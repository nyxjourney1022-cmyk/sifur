<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{

    // GET /cart — ambil isi keranjang (JSON)
    public function index()
    {
        $cart  = Auth::user()->getOrCreateCart();
        $items = $cart->items()->with('barang.kayu')->get()->map(fn($item) => [
            'id'          => $item->id,
            'barang_id'   => $item->barang_id,
            'name'        => $item->barang->nama_barang,
            'kategori'    => $item->barang->kategori,
            'kayu'        => $item->barang->kayu?->jenis_kayu,
            'image'       => $item->barang->gambar_url,
            'harga'       => $item->barang->harga,
            'harga_fmt'   => 'Rp ' . number_format($item->barang->harga, 0, ',', '.'),
            'jumlah'      => $item->jumlah,
            'stok'        => $item->barang->stok_barang,
            'subtotal'    => $item->barang->harga * $item->jumlah,
            'subtotal_fmt'=> 'Rp ' . number_format($item->barang->harga * $item->jumlah, 0, ',', '.'),
        ]);

        $total = $items->sum('subtotal');

        return response()->json([
            'success'    => true,
            'items'      => $items,
            'total'      => $total,
            'total_fmt'  => 'Rp ' . number_format($total, 0, ',', '.'),
            'total_items'=> $items->sum('jumlah'),
        ]);
    }

    // POST /cart/add — tambah item (AJAX, no reload)
    public function add(Request $request)
    {
        $data = $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'jumlah'    => 'integer|min:1|max:100',
        ]);

        $barang = Barang::findOrFail($data['barang_id']);
        $jumlah = $data['jumlah'] ?? 1;

        if ($barang->stok_barang < $jumlah) {
            return response()->json(['success' => false, 'message' => 'Stok tidak cukup.'], 422);
        }

        $cart     = Auth::user()->getOrCreateCart();
        $cartItem = $cart->items()->where('barang_id', $barang->id)->first();

        if ($cartItem) {
            $newJumlah = $cartItem->jumlah + $jumlah;
            if ($newJumlah > $barang->stok_barang) {
                return response()->json(['success' => false, 'message' => 'Melebihi stok yang tersedia.'], 422);
            }
            $cartItem->update(['jumlah' => $newJumlah]);
        } else {
            $cart->items()->create([
                'barang_id' => $barang->id,
                'jumlah'    => $jumlah,
            ]);
        }

        $totalItems = $cart->items()->sum('jumlah');

        return response()->json([
            'success'     => true,
            'message'     => $barang->nama_barang . ' ditambahkan ke keranjang.',
            'total_items' => $totalItems,
        ]);
    }

    // PATCH /cart/item/{id} — update jumlah
    public function update(Request $request, int $id)
    {
        $data = $request->validate(['jumlah' => 'required|integer|min:1|max:100']);

        $cart     = Auth::user()->getOrCreateCart();
        $cartItem = $cart->items()->with('barang')->findOrFail($id);

        if ($data['jumlah'] > $cartItem->barang->stok_barang) {
            return response()->json(['success' => false, 'message' => 'Melebihi stok.'], 422);
        }

        $cartItem->update(['jumlah' => $data['jumlah']]);

        $subtotal = $cartItem->barang->harga * $data['jumlah'];
        $total    = $cart->items()->with('barang')->get()->sum(fn($i) => $i->barang->harga * $i->jumlah);

        return response()->json([
            'success'      => true,
            'subtotal_fmt' => 'Rp ' . number_format($subtotal, 0, ',', '.'),
            'total_fmt'    => 'Rp ' . number_format($total,    0, ',', '.'),
            'total_items'  => $cart->items()->sum('jumlah'),
        ]);
    }

    // DELETE /cart/item/{id} — hapus item
    public function remove(int $id)
    {
        $cart     = Auth::user()->getOrCreateCart();
        $cartItem = $cart->items()->findOrFail($id);
        $cartItem->delete();

        $total = $cart->items()->with('barang')->get()->sum(fn($i) => $i->barang->harga * $i->jumlah);

        return response()->json([
            'success'     => true,
            'total_fmt'   => 'Rp ' . number_format($total, 0, ',', '.'),
            'total_items' => $cart->items()->sum('jumlah'),
        ]);
    }

    // DELETE /cart — kosongkan semua
    public function clear()
    {
        $cart = Auth::user()->getOrCreateCart();
        $cart->items()->delete();

        return response()->json(['success' => true, 'total_items' => 0]);
    }
}
