<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Furniture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    // ── Helper: panggil Groq ─────────────────────────────────
    private function callGroq(array $messages, int $maxTokens = 300): ?string
    {
        $response = Http::withToken(env('LLM_API'))
            ->timeout(30)
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model'       => 'qwen/qwen3.8-27b',
                'messages'    => $messages,
                'max_tokens'  => $maxTokens,
                'temperature' => 0.7,
            ]);

        if ($response->failed()) {
            \Log::error('Groq API error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return null;
        }

        $reply = $response->json('choices.0.message.content') ?? '';
        // Strip Qwen3 thinking tags
        return trim(preg_replace('/<think>.*?<\/think>/s', '', $reply));
    }

    // ── CS Chat ──────────────────────────────────────────────
    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'history' => 'nullable|array|max:20',
        ]);

        $furniture   = Furniture::first();
        $storeName   = $furniture?->nama_toko   ?? config('app.name');
        $storeAddr   = $furniture?->alamat_toko ?? '-';
        $storeTlp    = $furniture?->no_tlp      ?? '-';
        $totalProduk = Barang::where('stok_barang', '>', 0)->count();
        $categories  = Barang::select('kategori')
            ->distinct()->whereNotNull('kategori')->pluck('kategori')->join(', ');

        $systemPrompt = <<<PROMPT
Kamu adalah asisten customer service ramah dari toko furniture bernama "{$storeName}".
Tugasmu membantu pelanggan dengan pertanyaan seputar produk, pemesanan, dan informasi toko.

Informasi toko:
- Nama toko: {$storeName}
- Alamat: {$storeAddr}
- Telepon/WA: {$storeTlp}
- Total produk tersedia: {$totalProduk} produk
- Kategori produk: {$categories}

Panduan:
- Jawab dalam Bahasa Indonesia yang ramah dan natural
- Untuk pertanyaan harga/detail produk spesifik, arahkan ke halaman Katalog atau WA
- Jangan menjawab hal di luar topik toko furniture ini
- Jawaban singkat dan to the point, maksimal 3-4 kalimat
- Jika tidak tahu, jujur dan sarankan menghubungi via WA
PROMPT;

        $messages = [['role' => 'system', 'content' => $systemPrompt]];

        foreach (($request->history ?? []) as $h) {
            if (in_array($h['role'] ?? '', ['user', 'assistant']) && !empty($h['content'])) {
                $messages[] = ['role' => $h['role'], 'content' => $h['content']];
            }
        }

        $messages[] = ['role' => 'user', 'content' => $request->message];

        try {
            $reply = $this->callGroq($messages, 300);
            if ($reply === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maaf, layanan sedang gangguan. Silakan hubungi kami via WhatsApp.',
                ], 500);
            }
            return response()->json(['success' => true, 'reply' => $reply]);
        } catch (\Exception $e) {
            \Log::error('CS Chat exception: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Koneksi gagal. Silakan coba lagi.'], 500);
        }
    }

    // ── Agent Rekomendasi Produk per Kategori ────────────────
    public function rekomendasiKategori(Request $request)
    {
        $request->validate(['kategori' => 'required|string|max:100']);

        $kategori = $request->kategori;

        $produkList = Barang::with('kayu')
            ->where('kategori', $kategori)
            ->where('stok_barang', '>', 0)
            ->get()
            ->map(fn($b) => sprintf(
                '- %s (Kayu: %s, Harga: Rp %s, Stok: %d)',
                $b->nama_barang,
                $b->kayu?->jenis_kayu ?? 'N/A',
                number_format($b->harga, 0, ',', '.'),
                $b->stok_barang
            ))
            ->join("\n");

        if (empty($produkList)) {
            return response()->json([
                'success' => true,
                'reply'   => 'Belum ada produk tersedia di kategori ini.',
                'produk'  => [],
            ]);
        }

        $prompt = <<<PROMPT
Kamu adalah interior design expert dan konsultan furniture profesional.

Daftar produk yang tersedia untuk kategori "{$kategori}":
{$produkList}

Tugasmu:
1. Pilih 2-3 produk yang paling direkomendasikan untuk {$kategori}
2. Berikan alasan singkat kenapa produk tersebut cocok untuk ruangan ini
3. Berikan 1 tips styling singkat untuk ruangan ini

Format jawaban (Bahasa Indonesia, friendly, 4-5 kalimat total):
Sebutkan nama produk yang direkomendasikan dan kenapa cocok, lalu tutup dengan tips singkat.
PROMPT;

        try {
            $reply = $this->callGroq([
                ['role' => 'system', 'content' => 'Kamu adalah interior design expert yang merekomendasikan furniture terbaik untuk setiap ruangan.'],
                ['role' => 'user',   'content' => $prompt],
            ], 400);

            if ($reply === null) {
                return response()->json(['success' => false, 'message' => 'Gagal memuat rekomendasi.'], 500);
            }

            $produkHighlight = Barang::with('kayu')
                ->where('kategori', $kategori)
                ->where('stok_barang', '>', 0)
                ->take(3)
                ->get()
                ->map(fn($b) => [
                    'id'    => $b->id,
                    'name'  => $b->nama_barang,
                    'price' => 'Rp ' . number_format($b->harga, 0, ',', '.'),
                    'kayu'  => $b->kayu?->jenis_kayu,
                    'image' => $b->gambar_url,
                ]);

            return response()->json([
                'success' => true,
                'reply'   => $reply,
                'produk'  => $produkHighlight,
            ]);
        } catch (\Exception $e) {
            \Log::error('Rekomendasi exception: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Koneksi gagal.'], 500);
        }
    }
}
