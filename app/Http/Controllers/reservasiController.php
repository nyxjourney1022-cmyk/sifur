<?php

namespace App\Http\Controllers;

use App\Models\Reservasi;
use App\Models\DetailReservasi;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class reservasiController extends Controller
{
    # meminta data reservasi
    public function index()
    {
        $reservasi = Reservasi::all();
        return response()->json([
            'success' => true,
            'message' => 'Suksess mengambil',
            'data' => $reservasi

        ]);
    }

    public function store(Request $request)
    {
        $validatedata = $request->validate([
            'nama' => 'required|string|max:255',
            'no_tlp' => 'required|string|max:255',
            'alamat' => 'required|string',

            'tanggal_reservasi' => 'required|date',
            'status' => 'nullable|in:Menunggu, Diproses, Selesai, Dibatalkan',

            'details' => 'required|array',
            'details.*.barang_id' => 'required|string',
            'details.*.jumlah' => 'required|int|min:1'
        ]);
        DB::beginTransaction();
        try {
            $pelanggans = Pelanggan::firstOrCreate(
                ['no_tlp' => $request->no_tlp],
                [
                    'nama' => $request->nama,
                    'alamat' => $request->alamat
                ]
            );
            $reservasi = Reservasi::create([
                'pelanggan_id' => $pelanggans->id,
                'tanggal_reservasi' => $request->tanggal,
                'status' => $request->status ?? 'Menunggu'
            ]);
            foreach ($request->details as $items) {
                DetailReservasi::create([
                    'reservasi_id' => $reservasi->id,
                    'barang_id' => $items['barang_id'],
                    'jumlah' => $items['jumlah'],
                ]);
            };
            DB::commit();
            return response()->json([
                'message'=>'Reservasi berhasil dibuat',
                'data_pelanggan'=> $pelanggans,
                'data_reservasi'=>$reservasi
            ],201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()-> json([
                'message'=> 'Reservasi gagal dibuat karena $e',
                'errors' => $e-> getMessage()
            ],500);
        }
    }
}
