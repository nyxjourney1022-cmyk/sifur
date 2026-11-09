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
        // Transaksi 1 - Pelanggan 1
        $t1 = Transaksi::create([
            'pelanggan_id'      => 1,
            'tanggal_transaksi' => '2026-11-02',
            'total_harga'       => 0,
        ]);
        $this->addDetail($t1, barang_id: 1, jumlah: 2);
        $this->addDetail($t1, barang_id: 5, jumlah: 1);
        $t1->update(['total_harga' => $t1->detailTransaksis()->sum('subtotal')]);

        // Transaksi 2 - Pelanggan 2
        $t2 = Transaksi::create([
            'pelanggan_id'      => 2,
            'tanggal_transaksi' => '2026-11-04',
            'total_harga'       => 0,
        ]);
        $this->addDetail($t2, barang_id: 3, jumlah: 1);
        $this->addDetail($t2, barang_id: 4, jumlah: 1);
        $t2->update(['total_harga' => $t2->detailTransaksis()->sum('subtotal')]);

        // Transaksi 3 - Pelanggan 5
        $t3 = Transaksi::create([
            'pelanggan_id'      => 5,
            'tanggal_transaksi' => '2026-11-06',
            'total_harga'       => 0,
        ]);
        $this->addDetail($t3, barang_id: 6, jumlah: 1);
        $this->addDetail($t3, barang_id: 7, jumlah: 2);
        $t3->update(['total_harga' => $t3->detailTransaksis()->sum('subtotal')]);
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
