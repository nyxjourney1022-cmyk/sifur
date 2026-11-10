<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kayu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class barangController extends Controller
{
    public function index()
    {
        $barang = Barang::all();
        return response()->json([
            'message' => 'Barang berhasil di muat',
            'success' => true,
            'data' => $barang
        ]);
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'stock_barang' => 'required|integer|min:1',
            'harga' => 'required|numeric|min:1',
            'jenis_kayu' => 'required|string|max:100'
        ]);
        DB::beginTransaction();
        try {
            $kayu = Kayu::firstOrCreate(
                ['jenis_kayu' => $validate['jenis_kayu']]
            );
            $barang = Barang::create([
                'nama_barang' => $validate['nama_barang'],
                'stock_barang' => $validate['stock_barang'],
                'harga' => $validate['harga'],
                'kayu_id' => $kayu->id
            ]);
            DB::commit();
            return response()->json([
                'message' => 'Berhasil dimasukan kedatabase',
                'data_barang' => $barang
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Barang tidak berhasil dimasukan kedatabase',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        $barang = Barang::find($id);
        if (!$barang) {
            return response()->json([
                'message' => 'barang tidak ditemukan',
                'success' => false
            ], 404);
        }
        $validate = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'stock_barang' => 'required|integer|min:1',
            'harga' => 'required|numeric|min:1',
            'jenis_kayu' => 'required|string|max:100'
        ]);
        DB::beginTransaction();
        try {
            $kayu = Kayu::firstOrCreate([
                'jenis_kayu' => $validate['jenis_kayu']
            ]);
            $barang->update([
                'nama_barang' => $validate['nama_barang'],
                'kategori' => $validate['kategori'],
                'stock_barang' => $validate['stock_barang'],
                'harga' => $validate['harga'],
                'kayu_id'=> $kayu->id
            ]);
            DB::commit();
            return response()->json([
                'message' => 'barang berhasil diupdate',
                'success' => true,
                'data_reservasi' => $barang
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'reservasi tidak berhasil diupdate',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        $barang = Barang::find($id);
        if (!$barang) {
            return response()->json([
                'message' => 'Barang tidak ditemukan',
                'success' => false
            ], 404);
        }
        $barang->delete();
        return response()->json([
            'message' => 'barang berhasil dihapus',
            'success' => true
        ], 200);
    }
}
