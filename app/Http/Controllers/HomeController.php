<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Furniture;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Ambil nomor WhatsApp toko.
     * Prioritas: STORE_WHATSAPP di .env → no_tlp dari tabel furnitures → fallback default
     */
    public static function getWhatsapp(): string
    {
        // Dari .env
        $fromEnv = env('STORE_WHATSAPP');
        if (!empty($fromEnv)) {
            return preg_replace('/[^0-9]/', '', $fromEnv);
        }

        // Dari DB
        $furniture = \App\Models\Furniture::first();
        if ($furniture?->no_tlp) {
            return preg_replace('/[^0-9]/', '', $furniture->no_tlp);
        }

        return '62xxxxxxxxxx';
    }

    public function index()
    {
        $furniture = Furniture::first();

        // Ambil produk unggulan (8 terbaru atau stok > 0)
        $products = Barang::with('kayu')
            ->where('stok_barang', '>', 0)
            ->latest()
            ->take(8)
            ->get()
            ->map(fn($b) => [
                'id'       => $b->id,
                'name'     => $b->nama_barang,
                'price'    => $b->harga,
                'kategori' => $b->kategori,
                'stok'     => $b->stok_barang,
                'kayu'     => $b->kayu?->jenis_kayu,
                'image'    => $b->gambar_url,
            ]);

        // 4 kategori ruangan tetap — selalu tampil dengan gambar yang sesuai
        $categories = collect([
            ['name' => 'Ruang Tamu',  'room' => 'Living Room'],
            ['name' => 'Kamar Tidur', 'room' => 'Bedroom'],
            ['name' => 'Ruang Makan', 'room' => 'Dining Room'],
            ['name' => 'Ruang Kerja', 'room' => 'Home Office'],
        ]);

        $stats = [
            ['value' => Barang::count() . '+',           'label' => 'Produk Tersedia'],
            ['value' => '5+',                            'label' => 'Tahun Pengalaman'],
            ['value' => '500+',                          'label' => 'Pelanggan Puas'],
        ];

        return view('home', [
            'products'   => $products,
            'categories' => $categories,
            'stats'      => $stats,
            'about'      => 'Kami adalah toko furniture kayu pilihan yang telah berdiri sejak 2019. Setiap produk dibuat oleh pengrajin berpengalaman menggunakan kayu berkualitas tinggi untuk memastikan keindahan dan ketahanan jangka panjang.',
            'whatsapp'   => HomeController::getWhatsapp(),
            'address'    => $furniture?->alamat_toko ?? '[ALAMAT SHOWROOM]',
            'phone'      => $furniture?->no_tlp ?? '[NOMOR TELEPON]',
            'store_name' => $furniture?->nama_toko ?? config('app.name'),
        ]);
    }

    public function katalogData(Request $request)
    {
        $query = Barang::with('kayu')->where('stok_barang', '>', 0);

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }
        if ($request->filled('kayu')) {
            $query->whereHas('kayu', fn($q) => $q->where('jenis_kayu', $request->kayu));
        }

        match ($request->get('sort', 'terbaru')) {
            'harga_asc'  => $query->orderBy('harga', 'asc'),
            'harga_desc' => $query->orderBy('harga', 'desc'),
            'nama'       => $query->orderBy('nama_barang', 'asc'),
            default      => $query->latest(),
        };

        $products = $query->get()->map(fn($b) => [
            'id'       => $b->id,
            'name'     => $b->nama_barang,
            'price'    => $b->harga,
            'kategori' => $b->kategori,
            'stok'     => $b->stok_barang,
            'kayu'     => $b->kayu?->jenis_kayu,
            'image'    => $b->gambar_url,
        ]);

        $byCategory       = $products->groupBy('kategori');
        $selectedKategori = $request->get('kategori');
        $furniture        = Furniture::first();
        $whatsapp         = HomeController::getWhatsapp();

        $html = view('partials.katalog-content', compact(
            'products', 'byCategory', 'selectedKategori', 'whatsapp'
        ))->render();

        return response()->json([
            'html'  => $html,
            'count' => $products->count(),
        ]);
    }

    public function katalog(Request $request)
    {
        $query = Barang::with('kayu')->where('stok_barang', '>', 0);

        // Filter kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Filter kayu
        if ($request->filled('kayu')) {
            $query->whereHas('kayu', fn($q) => $q->where('jenis_kayu', $request->kayu));
        }

        // Sort
        match ($request->get('sort', 'terbaru')) {
            'harga_asc'  => $query->orderBy('harga', 'asc'),
            'harga_desc' => $query->orderBy('harga', 'desc'),
            'nama'       => $query->orderBy('nama_barang', 'asc'),
            default      => $query->latest(),
        };

        $products = $query->get()->map(fn($b) => [
            'id'       => $b->id,
            'name'     => $b->nama_barang,
            'price'    => $b->harga,
            'kategori' => $b->kategori,
            'stok'     => $b->stok_barang,
            'kayu'     => $b->kayu?->jenis_kayu,
            'image'    => $b->gambar_url,
        ]);

        // Produk dikelompokkan per kategori untuk horizontal scroll
        $byCategory = $products->groupBy('kategori');

        $categories = Barang::select('kategori')
            ->distinct()
            ->whereNotNull('kategori')
            ->pluck('kategori');

        $furniture  = Furniture::first();

        return view('katalog', [
            'products'         => $products,
            'byCategory'       => $byCategory,
            'categories'       => $categories,
            'selectedKategori' => $request->get('kategori'),
            'selectedKayu'     => $request->get('kayu'),
            'selectedSort'     => $request->get('sort', 'terbaru'),
            'store_name'       => $furniture?->nama_toko ?? config('app.name'),
            'whatsapp'         => HomeController::getWhatsapp(),
            'address'          => $furniture?->alamat_toko ?? '[ALAMAT SHOWROOM]',
            'phone'            => $furniture?->no_tlp ?? '[NOMOR TELEPON]',
        ]);
    }

    private function getRoomLabel(string $kategori): string
    {
        return match (strtolower($kategori)) {
            'ruang tamu'   => 'Living Room',
            'kamar tidur'  => 'Bedroom',
            'ruang makan'  => 'Dining Room',
            'ruang kerja'  => 'Home Office',
            'dapur'        => 'Kitchen',
            default        => 'All Rooms',
        };
    }
}
