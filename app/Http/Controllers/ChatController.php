<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Furniture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    // ── Helper: panggil Groq ─────────────────────────────────────────
    private function callGroq(array $messages, int $maxTokens = 400): ?string
    {
        $response = Http::withToken(config('services.llm_api'))
            ->timeout(30)
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model'       => 'qwen/qwen3.8-27b',
                'messages'    => $messages,
                'max_tokens'  => $maxTokens,
                'temperature' => 0.7,
            ]);

        if ($response->failed()) {
            \Log::error('Groq error', ['status' => $response->status(), 'body' => $response->body()]);
            return null;
        }

        // Strip Qwen3 <think> tags
        return trim(preg_replace('/<think>.*?<\/think>/s', '', $response->json('choices.0.message.content') ?? ''));
    }

    // ── Helper: info toko dari DB ─────────────────────────────────────
    private function storeInfo(): array
    {
        $f    = Furniture::first();
        $cats = Barang::select('kategori')->distinct()->whereNotNull('kategori')->pluck('kategori')->join(', ');
        $tot  = Barang::where('stok_barang', '>', 0)->count();

        return [
            'nama'       => $f?->nama_toko   ?? config('app.name'),
            'alamat'     => $f?->alamat_toko ?? '-',
            'telepon'    => $f?->no_tlp      ?? '-',
            'whatsapp'   => HomeController::getWhatsapp(),
            'total'      => $tot,
            'kategori'   => $cats,
        ];
    }

    // ── CS Chat ──────────────────────────────────────────────────────
    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'history' => 'nullable|array|max:12',
        ]);

        $s   = $this->storeInfo();
        $msg = $request->message;

        // Deteksi apakah user minta rekomendasi produk
        $isRekomendasi = $this->isRekomendasiRequest($msg);

        // Kalau rekomendasi, inject daftar produk ke system prompt
        $produkContext = '';
        $produkHighlight = [];

        if ($isRekomendasi) {
            $semuaProduk = Barang::with('kayu')
                ->where('stok_barang', '>', 0)
                ->get();

            $produkContext = "\n\n**Daftar Produk Tersedia di Toko:**\n" .
                $semuaProduk->map(fn($b) => sprintf(
                    '- %s | Kategori: %s | Kayu: %s | Harga: Rp %s | Stok: %d',
                    $b->nama_barang,
                    $b->kategori ?? '-',
                    $b->kayu?->jenis_kayu ?? '-',
                    number_format($b->harga, 0, ',', '.'),
                    $b->stok_barang
                ))->join("\n");

            $produkContext .= "\n\nRekomendasikan produk yang paling sesuai dengan pertanyaan user dari daftar di atas. Sebutkan nama produknya secara spesifik.";
        }

        $system = <<<PROMPT
Kamu adalah customer service toko furniture bernama "{$s['nama']}".

**Informasi Toko:**
- Nama: {$s['nama']}
- Alamat: {$s['alamat']}
- Telepon/WA: {$s['telepon']}
- Produk tersedia: {$s['total']} produk
- Kategori: {$s['kategori']}

**Keahlianmu:**
Kamu paham mendalam tentang furniture kayu — jenis kayu (jati, mahoni, trembesi, oak, dll), teknik sambungan, finishing, perawatan, ukuran standar, dan tips styling ruangan.

**Panduan Menjawab:**
- Bahasa Indonesia yang ramah dan natural
- Jawaban singkat, to the point, maksimal 3-4 kalimat
- Jangan menjawab hal di luar topik furniture dan toko
- Jika tidak tahu → jujur dan sarankan hubungi via WA{$produkContext}
PROMPT;

        $messages = [['role' => 'system', 'content' => $system]];

        $history = array_slice($request->history ?? [], -6);
        foreach ($history as $h) {
            if (in_array($h['role'] ?? '', ['user', 'assistant']) && !empty($h['content'])) {
                $messages[] = ['role' => $h['role'], 'content' => $h['content']];
            }
        }
        $messages[] = ['role' => 'user', 'content' => $msg];

        $reply = $this->callGroq($messages, 400);

        if ($reply === null) {
            return response()->json(['success' => false, 'message' => 'Maaf, layanan sedang gangguan. Silakan hubungi kami via WhatsApp.'], 500);
        }

        // Kalau rekomendasi, cari produk yang disebutkan AI untuk jadi card
        if ($isRekomendasi) {
            $semuaProduk = Barang::with('kayu')->where('stok_barang', '>', 0)->get();
            $produkHighlight = $semuaProduk
                ->filter(fn($b) => str_contains(strtolower($reply), strtolower($b->nama_barang)))
                ->take(3)
                ->map(fn($b) => [
                    'id'    => $b->id,
                    'name'  => $b->nama_barang,
                    'price' => 'Rp ' . number_format($b->harga, 0, ',', '.'),
                    'kayu'  => $b->kayu?->jenis_kayu,
                    'image' => $b->gambar_url,
                ])->values();

            // Kalau tidak ada yang cocok, ambil top 3 relevan dari kategori
            if ($produkHighlight->isEmpty()) {
                $produkHighlight = $semuaProduk->take(3)->map(fn($b) => [
                    'id'    => $b->id,
                    'name'  => $b->nama_barang,
                    'price' => 'Rp ' . number_format($b->harga, 0, ',', '.'),
                    'kayu'  => $b->kayu?->jenis_kayu,
                    'image' => $b->gambar_url,
                ])->values();
            }
        }

        return response()->json([
            'success' => true,
            'reply'   => $reply,
            'produk'  => $produkHighlight,
        ]);
    }

    // ── Deteksi apakah pesan minta rekomendasi ────────────────────────
    private function isRekomendasiRequest(string $msg): bool
    {
        $keywords = [
            'rekomen', 'sarankan', 'suggest', 'pilihkan', 'carikan',
            'apa yang bagus', 'yang cocok', 'yang sesuai', 'mana yang',
            'produk apa', 'furniture apa', 'kursi apa', 'meja apa',
            'lemari apa', 'tempat tidur apa', 'sofa apa', 'ada apa',
            'apa saja', 'tolong pilih', 'bantu pilih', 'pilih mana',
        ];

        $lower = strtolower($msg);
        foreach ($keywords as $kw) {
            if (str_contains($lower, $kw)) return true;
        }
        return false;
    }

    // ── Agent Rekomendasi per Kategori ────────────────────────────────
    // Dipanggil saat user klik filter kategori di katalog.
    // Kalau kategori tersebut KOSONG di DB, AI tetap merekomendasikan
    // produk dari semua stok yang ada yang paling cocok untuk ruangan itu.
    public function rekomendasiKategori(Request $request)
    {
        $request->validate(['kategori' => 'required|string|max:100']);

        $kategori = $request->kategori;
        $s        = $this->storeInfo();

        // Coba ambil produk sesuai kategori
        $produkKategori = Barang::with('kayu')
            ->where('kategori', $kategori)
            ->where('stok_barang', '>', 0)
            ->get();

        $kosong = $produkKategori->isEmpty();

        if ($kosong) {
            // Kalau kosong — ambil semua produk tersedia dan minta AI pilih yang cocok
            $semuaProduk = Barang::with('kayu')
                ->where('stok_barang', '>', 0)
                ->get();

            if ($semuaProduk->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'reply'   => 'Produk sedang habis. Silakan hubungi kami via WhatsApp untuk informasi lebih lanjut.',
                    'produk'  => [],
                    'kosong'  => true,
                ]);
            }

            $daftarSemua = $semuaProduk->map(fn($b) => sprintf(
                '- %s (Kayu: %s, Harga: Rp %s, Stok: %d)',
                $b->nama_barang,
                $b->kayu?->jenis_kayu ?? 'N/A',
                number_format($b->harga, 0, ',', '.'),
                $b->stok_barang
            ))->join("\n");

            $prompt = <<<PROMPT
Kamu adalah interior design expert dari toko furniture "{$s['nama']}".

Pelanggan sedang mencari furniture untuk **{$kategori}**, tapi kategori ini belum tersedia.

Berikut semua produk yang ada di toko:
{$daftarSemua}

Tugasmu:
1. Pilih 2-3 produk dari daftar di atas yang paling bisa digunakan atau cocok untuk {$kategori}
2. Jelaskan kenapa produk tersebut tetap bisa dipakai atau relevan untuk ruangan itu
3. Berikan 1 tips kreatif agar produk tersebut cocok di {$kategori}
4. Tutup dengan info bahwa kami membuka pesanan custom

Format: Bahasa Indonesia, ramah, 4-5 kalimat total.
PROMPT;

            $reply = $this->callGroq([
                ['role' => 'system', 'content' => 'Kamu adalah interior design expert yang kreatif dan membantu pelanggan menemukan solusi furniture terbaik.'],
                ['role' => 'user',   'content' => $prompt],
            ], 450);

            if ($reply === null) {
                return response()->json(['success' => false, 'message' => 'Gagal memuat rekomendasi.'], 500);
            }

            // Ambil top 3 produk yang ada sebagai highlight
            $highlight = $semuaProduk->take(3)->map(fn($b) => [
                'id'    => $b->id,
                'name'  => $b->nama_barang,
                'price' => 'Rp ' . number_format($b->harga, 0, ',', '.'),
                'kayu'  => $b->kayu?->jenis_kayu,
                'image' => $b->gambar_url,
            ])->values();

            return response()->json([
                'success' => true,
                'reply'   => $reply,
                'produk'  => $highlight,
                'kosong'  => true,
            ]);
        }

        // Kategori ada isinya — rekomendasi normal
        $daftarProduk = $produkKategori->map(fn($b) => sprintf(
            '- %s (Kayu: %s, Harga: Rp %s, Stok: %d)',
            $b->nama_barang,
            $b->kayu?->jenis_kayu ?? 'N/A',
            number_format($b->harga, 0, ',', '.'),
            $b->stok_barang
        ))->join("\n");

        $prompt = <<<PROMPT
Kamu adalah interior design expert dari toko furniture "{$s['nama']}".

Produk tersedia untuk **{$kategori}**:
{$daftarProduk}

Tugasmu:
1. Pilih 2-3 produk yang paling direkomendasikan untuk {$kategori}
2. Jelaskan singkat kenapa cocok untuk ruangan ini
3. Berikan 1 tips styling untuk {$kategori}

Format: Bahasa Indonesia, ramah, 4-5 kalimat total.
PROMPT;

        $reply = $this->callGroq([
            ['role' => 'system', 'content' => 'Kamu adalah interior design expert yang merekomendasikan furniture terbaik untuk setiap ruangan.'],
            ['role' => 'user',   'content' => $prompt],
        ], 400);

        if ($reply === null) {
            return response()->json(['success' => false, 'message' => 'Gagal memuat rekomendasi.'], 500);
        }

        $highlight = $produkKategori->take(3)->map(fn($b) => [
            'id'    => $b->id,
            'name'  => $b->nama_barang,
            'price' => 'Rp ' . number_format($b->harga, 0, ',', '.'),
            'kayu'  => $b->kayu?->jenis_kayu,
            'image' => $b->gambar_url,
        ])->values();

        return response()->json([
            'success' => true,
            'reply'   => $reply,
            'produk'  => $highlight,
            'kosong'  => false,
        ]);
    }
}
