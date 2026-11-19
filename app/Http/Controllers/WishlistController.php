<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    // Halaman wishlist
    public function index()
    {
        $items = Wishlist::with('barang.kayu')
            ->where('user_id', Auth::id())
            ->latest()
            ->get()
            ->map(fn($w) => [
                'wishlist_id' => $w->id,
                'id'          => $w->barang->id,
                'name'        => $w->barang->nama_barang,
                'price'       => $w->barang->harga,
                'kategori'    => $w->barang->kategori,
                'stok'        => $w->barang->stok_barang,
                'kayu'        => $w->barang->kayu?->jenis_kayu,
                'image'       => $w->barang->gambar_url,
            ]);

        return view('wishlist', compact('items'));
    }

    // Toggle wishlist (tambah/hapus)
    public function toggle(int $barangId)
    {
        Barang::findOrFail($barangId);

        $existing = Wishlist::where('user_id', Auth::id())
            ->where('barang_id', $barangId)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json([
                'success'      => true,
                'in_wishlist'  => false,
                'message'      => 'Dihapus dari wishlist.',
            ]);
        }

        Wishlist::create([
            'user_id'   => Auth::id(),
            'barang_id' => $barangId,
        ]);

        return response()->json([
            'success'     => true,
            'in_wishlist' => true,
            'message'     => 'Ditambahkan ke wishlist.',
        ]);
    }

    // Cek status wishlist untuk beberapa produk sekaligus
    public function check(Request $request)
    {
        $ids = $request->input('ids', []);

        $wishlisted = Wishlist::where('user_id', Auth::id())
            ->whereIn('barang_id', $ids)
            ->pluck('barang_id')
            ->toArray();

        return response()->json(['success' => true, 'wishlisted' => $wishlisted]);
    }
}
