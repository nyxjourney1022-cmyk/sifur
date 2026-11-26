<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kayu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class barangController extends Controller
{
    public function index()
    {
        $barang = Barang::with('kayu')->get();
        return response()->json([
            'message' => 'Barang berhasil dimuat',
            'success' => true,
            'data'    => $barang,
        ]);
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'kategori'    => 'required|string|max:255',
            'stok_barang' => 'required|integer|min:0',   
            'harga'       => 'required|numeric|min:1',
            'kayu_id'     => 'required|integer|exists:kayus,id', 
            'gambar'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $pathGambar = null;
        if ($request->hasFile('gambar')) {
            $pathGambar = $request->file('gambar')->store('barang_image', 'public');
        }

        $barang = Barang::create([
            'nama_barang' => $validate['nama_barang'],
            'kategori'    => $validate['kategori'],
            'stok_barang' => $validate['stok_barang'],
            'harga'       => $validate['harga'],
            'kayu_id'     => $validate['kayu_id'],  
            'gambar'      => $pathGambar,
        ]);

        return response()->json([
            'message'    => 'Barang berhasil ditambahkan',
            'success'    => true,
            'data_barang' => $barang->load('kayu'),
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        $barang = Barang::find($id);
        if (!$barang) {
            return response()->json([
                'message' => 'Barang tidak ditemukan',
                'success' => false,
            ], 404);
        }

        $validate = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'kategori'    => 'required|string|max:255',
            'stok_barang' => 'required|integer|min:0',   
            'harga'       => 'required|numeric|min:1',
            'kayu_id'     => 'required|integer|exists:kayus,id', 
            'gambar'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Hanya update gambar kalau ada file baru
        $pathGambar = $barang->gambar;
        if ($request->hasFile('gambar')) {
            if ($barang->gambar) {
                Storage::disk('public')->delete($barang->gambar);
            }
            $pathGambar = $request->file('gambar')->store('barang_image', 'public');
        }

        $barang->update([
            'nama_barang' => $validate['nama_barang'],
            'kategori'    => $validate['kategori'],
            'stok_barang' => $validate['stok_barang'],
            'harga'       => $validate['harga'],
            'kayu_id'     => $validate['kayu_id'],
            'gambar'      => $pathGambar,
        ]);

        return response()->json([
            'message'    => 'Barang berhasil diupdate',
            'success'    => true,
            'data_barang' => $barang->fresh()->load('kayu'),
        ], 200);
    }

    public function destroy(string $id)
    {
        $barang = Barang::find($id);
        if (!$barang) {
            return response()->json([
                'message' => 'Barang tidak ditemukan',
                'success' => false,
            ], 404);
        }

        if ($barang->gambar) {
            Storage::disk('public')->delete($barang->gambar);
        }

        $barang->delete();

        return response()->json([
            'message' => 'Barang berhasil dihapus',
            'success' => true,
        ], 200);
    }
}
