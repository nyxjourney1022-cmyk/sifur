<?php

namespace Database\Seeders;

use App\Models\Reservasi;
use App\Models\DetailReservasi;
use Illuminate\Database\Seeder;

class ReservasiSeeder extends Seeder
{
    public function run(): void
    {
        // Reservasi 1 - Pelanggan 1
        $r1 = Reservasi::create([
            'pelanggan_id'      => 1,
            'tanggal_reservasi' => '2026-11-01',
            'status'            => 'Selesai',
        ]);
        DetailReservasi::create(['reservasi_id' => $r1->id, 'barang_id' => 1, 'jumlah' => 4]);
        DetailReservasi::create(['reservasi_id' => $r1->id, 'barang_id' => 2, 'jumlah' => 1]);

        // Reservasi 2 - Pelanggan 2
        $r2 = Reservasi::create([
            'pelanggan_id'      => 2,
            'tanggal_reservasi' => '2026-11-03',
            'status'            => 'Diproses',
        ]);
        DetailReservasi::create(['reservasi_id' => $r2->id, 'barang_id' => 3, 'jumlah' => 1]);
        DetailReservasi::create(['reservasi_id' => $r2->id, 'barang_id' => 5, 'jumlah' => 2]);

        // Reservasi 3 - Pelanggan 3
        $r3 = Reservasi::create([
            'pelanggan_id'      => 3,
            'tanggal_reservasi' => '2026-11-05',
            'status'            => 'Menunggu',
        ]);
        DetailReservasi::create(['reservasi_id' => $r3->id, 'barang_id' => 6, 'jumlah' => 2]);

        // Reservasi 4 - Pelanggan 4
        $r4 = Reservasi::create([
            'pelanggan_id'      => 4,
            'tanggal_reservasi' => '2026-11-06',
            'status'            => 'Dibatalkan',
        ]);
        DetailReservasi::create(['reservasi_id' => $r4->id, 'barang_id' => 7, 'jumlah' => 3]);
    }
}
