<?php

namespace Database\Seeders;

use App\Models\Pelanggan;
use Illuminate\Database\Seeder;

class PelangganSeeder extends Seeder
{
    public function run(): void
    {
        $pelanggans = [
            ['nama' => 'Budi Santoso',    'no_tlp' => '081234567890', 'alamat' => 'Jl. Mawar No. 5, Surabaya'],
            ['nama' => 'Siti Rahayu',     'no_tlp' => '082345678901', 'alamat' => 'Jl. Melati No. 12, Bandung'],
            ['nama' => 'Ahmad Fauzi',     'no_tlp' => '083456789012', 'alamat' => 'Jl. Kenanga No. 8, Jakarta'],
            ['nama' => 'Dewi Lestari',    'no_tlp' => '084567890123', 'alamat' => 'Jl. Anggrek No. 3, Yogyakarta'],
            ['nama' => 'Rudi Hermawan',   'no_tlp' => '085678901234', 'alamat' => 'Jl. Dahlia No. 17, Semarang'],
        ];

        foreach ($pelanggans as $pelanggan) {
            Pelanggan::create($pelanggan);
        }
    }
}
