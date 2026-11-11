<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Furniture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'message'  => 'required|string|max:1000',
            'history'  => 'nullable|array|max:20',
        ]);

        // Konteks toko dari DB
        $furniture   = Furniture::first();
        $storeName   = $furniture?->nama_toko   ?? config('app.name');
        $storeAddr   = $furniture?->alamat_toko ?? '-';
        $storeTlp    = $furniture?->no_tlp      ?? '-';

        $totalProduk = Barang::where('stok_barang', '>', 0)->count();
        $categories  = Barang::select('kategori')->distinct()->whereNotNull('kategori')->pluck('kategori')->join(', ');

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

        // Susun messages dari history + pesan baru
        $messages = [['role' => 'system', 'content' => $systemPrompt]];

        foreach (($request->history ?? []) as $h) {
            if (in_array($h['role'] ?? '', ['user', 'assistant']) && !empty($h['content'])) {
                $messages[] = ['role' => $h['role'], 'content' => $h['content']];
            }
        }

        $messages[] = ['role' => 'user', 'content' => $request->message];

        try {
            $response = Http::withToken(env('LLM_API'))
                ->timeout(30)
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model'       => 'qwen/qwen3.8-27b',
                    'messages'    => $messages,
                    'max_tokens'  => 300,
                    'temperature' => 0.7,
                ]);

            if ($response->failed()) {
                \Log::error('Groq API error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Maaf, layanan sedang gangguan. Silakan hubungi kami via WhatsApp.',
                ], 500);
            }

            $reply = $response->json('choices.0.message.content') ?? 'Maaf, tidak ada respons.';

            // Strip Qwen3 thinking tags kalau ada
            $reply = preg_replace('/<think>.*?<\/think>/s', '', $reply);

            return response()->json([
                'success' => true,
                'reply'   => trim($reply),
            ]);
        } catch (\Exception $e) {
            \Log::error('Groq exception: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Koneksi gagal. Silakan coba lagi.',
            ], 500);
        }
    }
}
