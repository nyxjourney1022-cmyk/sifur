<?php

namespace Database\Seeders;

use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\Barang;
use Illuminate\Database\Seeder;

class TransaksiSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // [pelanggan_id, tanggal, [[barang_id, jumlah], ...]]
            [1, '2026-11-02', [[1, 2], [5, 1]]],
            [2, '2026-11-04', [[3, 1], [4, 1]]],
            [5, '2026-11-06', [[6, 1], [7, 2]]],
            [3, '2026-11-08', [[2, 1], [8, 1]]],
            [4, '2026-11-10', [[1, 4], [3, 1]]],
            [1, '2026-11-12', [[6, 2], [5, 3]]],
            [2, '2026-11-14', [[4, 1], [7, 1], [8, 1]]],
            [3, '2026-11-16', [[1, 6], [2, 2]]],
            [5, '2026-11-18', [[3, 2], [6, 1]]],
            [4, '2026-11-20', [[5, 2], [7, 3], [8, 1]]],
        ];

        foreach ($data as [$pelanggan_id, $tanggal, $items]) {
            $transaksi = Transaksi::create([
                'pelanggan_id'      => $pelanggan_id,
                'tanggal_transaksi' => $tanggal,
                'total_harga'       => 0,
            ]);

            foreach ($items as [$barang_id, $jumlah]) {
                $this->addDetail($transaksi, $barang_id, $jumlah);
            }

            $transaksi->update([
                'total_harga' => $transaksi->detailTransaksis()->sum('subtotal'),
            ]);
        }
    }

    private function addDetail($transaksi, int $barang_id, int $jumlah): void
    {
        $barang  = Barang::find($barang_id);
        $subtotal = $barang->harga * $jumlah;

        DetailTransaksi::create([
            'transaksi_id' => $transaksi->id,
            'barang_id'    => $barang_id,
            'jumlah'       => $jumlah,
            'harga_satuan' => $barang->harga,
            'subtotal'     => $subtotal,
        ]);
    }
}
