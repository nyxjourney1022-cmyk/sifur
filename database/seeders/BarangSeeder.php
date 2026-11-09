<?php

namespace Database\Seeders;

use App\Models\Barang;
use Illuminate\Database\Seeder;

class BarangSeeder extends Seeder
{
    public function run(): void
    {
        $barangs = [
            ['nama_barang' => 'Kursi Makan',      'stok_barang' => 20, 'harga' => 350000,  'kayu_id' => 1],
            ['nama_barang' => 'Meja Makan',        'stok_barang' => 10, 'harga' => 1500000, 'kayu_id' => 1],
            ['nama_barang' => 'Lemari Pakaian',    'stok_barang' => 8,  'harga' => 2500000, 'kayu_id' => 2],
            ['nama_barang' => 'Tempat Tidur',      'stok_barang' => 5,  'harga' => 3000000, 'kayu_id' => 2],
            ['nama_barang' => 'Rak Buku',          'stok_barang' => 15, 'harga' => 750000,  'kayu_id' => 3],
            ['nama_barang' => 'Meja Kerja',        'stok_barang' => 12, 'harga' => 1200000, 'kayu_id' => 4],
            ['nama_barang' => 'Kursi Kerja',       'stok_barang' => 18, 'harga' => 850000,  'kayu_id' => 4],
            ['nama_barang' => 'Meja Tamu',         'stok_barang' => 7,  'harga' => 1800000, 'kayu_id' => 5],
        ];

        foreach ($barangs as $barang) {
            Barang::create($barang);
        }
    }
}
