<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Reservasi;
use App\Models\DetailReservasi;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class reservasiController extends Controller
{
    // GET /api/reservasi
    public function index()
    {
        $reservasi = Reservasi::with(['pelanggan', 'detailReservasis.barang'])
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil data reservasi',
            'data'    => $reservasi,
        ]);
    }

    // POST /api/reservasi
    public function store(Request $request)
    {
        $validate = $request->validate([
            'nama'                        => 'required|string|max:255',
            'no_tlp'                      => 'required|string|max:255',
            'alamat'                      => 'required|string',
            'tanggal_reservasi'           => 'required|date',
            'status'                      => 'nullable|in:Menunggu,Diproses,Selesai,Dibatalkan',
            'details'                     => 'required|array|min:1',
            'details.*.barang_id'         => 'required|integer|distinct|exists:barangs,id',
            'details.*.jumlah'            => 'required|integer|min:1',
        ]);

        $reservasi = DB::transaction(function () use ($validate) {

            $barangIds = collect($validate['details'])
                ->pluck('barang_id')->unique()->sort()->values();

            $barangs = Barang::whereIn('id', $barangIds)
                ->orderBy('id')->lockForUpdate()->get()->keyBy('id');

            $status           = $validate['status'] ?? 'Menunggu';
            $perluKurangiStok = $status !== 'Dibatalkan';

            if ($perluKurangiStok) {
                foreach ($validate['details'] as $item) {
                    $barang = $barangs->get($item['barang_id']);
                    if (!$barang || $barang->stok_barang < $item['jumlah']) {
                        throw ValidationException::withMessages([
                            'details' => ['Stok barang ' . ($barang->nama_barang ?? 'yang dipilih') . ' tidak mencukupi.'],
                        ]);
                    }
                }
            }

            $pelanggan = Pelanggan::firstOrCreate(
                ['no_tlp' => $validate['no_tlp']],
                ['nama' => $validate['nama'], 'alamat' => $validate['alamat']]
            );

            $reservasi = Reservasi::create([
                'pelanggan_id'      => $pelanggan->id,
                'tanggal_reservasi' => $validate['tanggal_reservasi'],
                'status'            => $status,
                'stok_dikurangi'    => $perluKurangiStok,
            ]);

            foreach ($validate['details'] as $item) {
                $barang = $barangs->get($item['barang_id']);
                if ($perluKurangiStok) {
                    $barang->decrement('stok_barang', $item['jumlah']);
                }
                DetailReservasi::create([
                    'reservasi_id' => $reservasi->id,
                    'barang_id'    => $item['barang_id'],
                    'jumlah'       => $item['jumlah'],
                ]);
            }

            return $reservasi;
        });

        return response()->json([
            'success'        => true,
            'message'        => 'Reservasi berhasil dibuat',
            'data_reservasi' => $reservasi->load(['pelanggan', 'detailReservasis.barang']),
        ], 201);
    }

    // PUT /api/reservasi/{id}
    public function update(Request $request, string $id)
    {
        $validate = $request->validate([
            'tanggal_reservasi'   => 'required|date',
            'status'              => 'nullable|in:Menunggu,Diproses,Selesai,Dibatalkan',
            'details'             => 'required|array|min:1',
            'details.*.barang_id' => 'required|integer|distinct|exists:barangs,id',
            'details.*.jumlah'    => 'required|integer|min:1',
        ]);

        $reservasi = DB::transaction(function () use ($validate, $id) {

            $reservasi = Reservasi::whereKey($id)->lockForUpdate()->first();
            if (!$reservasi) return null;

            $detailLama = DetailReservasi::where('reservasi_id', $reservasi->id)->get();

            $barangIds = $detailLama->pluck('barang_id')
                ->merge(collect($validate['details'])->pluck('barang_id'))
                ->unique()->sort()->values();

            $barangs = Barang::whereIn('id', $barangIds)
                ->orderBy('id')->lockForUpdate()->get()->keyBy('id');

            if ($reservasi->stok_dikurangi) {
                foreach ($detailLama as $detail) {
                    $barang = $barangs->get($detail->barang_id);
                    if ($barang) $barang->increment('stok_barang', $detail->jumlah);
                }
            }

            $statusBaru       = $validate['status'] ?? $reservasi->status;
            $perluKurangiStok = $statusBaru !== 'Dibatalkan';

            if ($perluKurangiStok) {
                foreach ($validate['details'] as $item) {
                    $barang = $barangs->get($item['barang_id']);
                    if (!$barang || $barang->stok_barang < $item['jumlah']) {
                        throw ValidationException::withMessages([
                            'details' => ['Stok barang ' . ($barang->nama_barang ?? 'yang dipilih') . ' tidak mencukupi.'],
                        ]);
                    }
                }
            }

            $reservasi->update([
                'tanggal_reservasi' => $validate['tanggal_reservasi'],
                'status'            => $statusBaru,
                'stok_dikurangi'    => $perluKurangiStok,
            ]);

            DetailReservasi::where('reservasi_id', $reservasi->id)->delete();

            foreach ($validate['details'] as $item) {
                $barang = $barangs->get($item['barang_id']);
                if (!$barang) throw new \Exception('Barang tidak ditemukan.');
                if ($perluKurangiStok) $barang->decrement('stok_barang', $item['jumlah']);
                DetailReservasi::create([
                    'reservasi_id' => $reservasi->id,
                    'barang_id'    => $item['barang_id'],
                    'jumlah'       => $item['jumlah'],
                ]);
            }

            return $reservasi;
        });

        if (!$reservasi) {
            return response()->json(['success' => false, 'message' => 'Reservasi tidak ditemukan'], 404);
        }

        return response()->json([
            'success'        => true,
            'message'        => 'Reservasi berhasil diupdate',
            'data_reservasi' => $reservasi->load(['pelanggan', 'detailReservasis.barang']),
        ]);
    }

    // DELETE /api/reservasi/{id}
    public function destroy(string $id)
    {
        $berhasil = DB::transaction(function () use ($id) {

            $reservasi = Reservasi::whereKey($id)->lockForUpdate()->first();
            if (!$reservasi) return false;

            if ($reservasi->stok_dikurangi) {
                $detailLama = DetailReservasi::where('reservasi_id', $reservasi->id)->get();
                $barangIds  = $detailLama->pluck('barang_id')->unique()->sort()->values();
                $barangs    = Barang::whereIn('id', $barangIds)->orderBy('id')->lockForUpdate()->get()->keyBy('id');

                foreach ($detailLama as $detail) {
                    $barang = $barangs->get($detail->barang_id);
                    if ($barang) $barang->increment('stok_barang', $detail->jumlah);
                }
            }

            DetailReservasi::where('reservasi_id', $reservasi->id)->delete();
            $reservasi->delete();

            return true;
        });

        if (!$berhasil) {
            return response()->json(['success' => false, 'message' => 'Reservasi tidak ditemukan'], 404);
        }

        return response()->json(['success' => true, 'message' => 'Reservasi berhasil dihapus']);
    }
}
