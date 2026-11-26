<?php

namespace App\Http\Controllers;

use App\Models\Kayu;
use Illuminate\Http\Request;

class kayuController extends Controller
{
    // Mengambil seluruh jenis kayu beserta jumlah barangnya
    public function index()
    {
        $kayu = Kayu::withCount('barangs')->get();

        return response()->json([
            'message' => 'Berhasil mengambil data kayu',
            'success' => true,
            'data'    => $kayu,
        ]);
    }

    // Menambahkan jenis kayu baru
    public function store(Request $request)
    {
        $validate = $request->validate([
            'jenis_kayu' => 'required|string|max:100|unique:kayus,jenis_kayu',
        ]);

        $kayu = Kayu::create([
            'jenis_kayu' => $validate['jenis_kayu'],
        ]);

        return response()->json([
            'message' => 'Kayu berhasil ditambahkan',
            'success' => true,
            'data'    => $kayu,
        ], 201);
    }

    // Memperbarui jenis kayu
    public function update(Request $request, string $id)
    {
        $kayu = Kayu::find($id);

        if (!$kayu) {
            return response()->json([
                'message' => 'Jenis kayu tidak ditemukan',
                'success' => false,
            ], 404);
        }

        $validate = $request->validate([
            'jenis_kayu' => 'required|string|max:100|unique:kayus,jenis_kayu,' . $kayu->id,
        ]);

        $kayu->update([
            'jenis_kayu' => $validate['jenis_kayu'],
        ]);

        return response()->json([
            'message' => 'Kayu berhasil diupdate',
            'success' => true,
            'data'    => $kayu->fresh()->loadCount('barangs'),
        ], 200);
    }

    // Menghapus jenis kayu
    public function destroy(string $id)
    {
        $kayu = Kayu::withCount('barangs')->find($id);

        if (!$kayu) {
            return response()->json([
                'message' => 'Jenis kayu tidak ditemukan',
                'success' => false,
            ], 404);
        }

        // Mencegah penghapusan kayu yang masih digunakan barang
        if ($kayu->barangs_count > 0) {
            return response()->json([
                'message' => "Kayu '{$kayu->jenis_kayu}' tidak bisa dihapus karena masih digunakan oleh {$kayu->barangs_count} barang.",
                'success' => false,
            ], 422);
        }

        $kayu->delete();

        return response()->json([
            'message' => 'Kayu berhasil dihapus',
            'success' => true,
        ], 200);
    }
}
