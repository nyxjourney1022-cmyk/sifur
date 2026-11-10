<?php

namespace App\Http\Controllers;

use App\Models\Kayu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class kayuController extends Controller
{
    public function index()
    {
        $kayu = Kayu::all();
        return response()->json([
            'message' => 'berhasil mengambil data kayu',
            'success' => true,
            'data' => $kayu
        ]);
    }
    public function store(Request $request)
    {
        $validate = $request->validate([
            'jenis_kayu' => 'required|string|max:100'
        ]);
        DB::beginTransaction();
        try {
            $kayu = Kayu::create(['kayu' => $validate['kayu']]);
            DB::commit();
            return response()->json([
                'message' => 'Kayu berhasil ditambahkan',
                'data' => $kayu
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Kayu gagal ditambahkan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function update(Request $request, string $id)
    {
        $kayu = Kayu::find($id);
        if (!$kayu) {
            return response()->json([
                'message' => 'Jenis Kayu tidak ditemukan',
                'success' => false
            ], 404);
        }
        $validate = $request->validate([
            'jenis_kayu' => 'required|string|max:100'
        ]);
        DB::beginTransaction();
        try {

            $kayu->update([
                'jenis_kayu' => $validate['jenis_kayu']
            ]);
            DB::commit();
            return response()->json([
                'message' => 'Berhasil diupdate',
                'success' => true,
                'data' => $kayu
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Kayu gagal diupdate',
                'error' => $e->getMessage()
            ], 500);
        }
    }
        public function destroy(string $id)
    {
        $kayu = Kayu::find($id);
        if (!$kayu) {
            return response()->json([
                'message' => 'kayu tidak ditemukan',
                'success' => false
            ], 404);
        }
        $kayu->delete();
        return response()->json([
            'message' => 'kayu berhasil dihapus',
            'success' => true
        ], 200);
}
}
