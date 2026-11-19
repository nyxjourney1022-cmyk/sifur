<?php

namespace Database\Seeders;

use App\Models\Barang;
use Illuminate\Database\Seeder;

class BarangSeeder extends Seeder
{
    public function run(): void
    {
        $barangs = [
            ['nama_barang' => 'Kursi Makan',   'kategori' => 'Ruang Makan',  'stok_barang' => 20, 'harga' => 350000,  'kayu_id' => 1, 'gambar' => null],
            ['nama_barang' => 'Meja Makan',    'kategori' => 'Ruang Makan',  'stok_barang' => 10, 'harga' => 1500000, 'kayu_id' => 1, 'gambar' => null],
            ['nama_barang' => 'Lemari Pakaian','kategori' => 'Kamar Tidur',  'stok_barang' => 8,  'harga' => 2500000, 'kayu_id' => 2, 'gambar' => null],
            ['nama_barang' => 'Tempat Tidur',  'kategori' => 'Kamar Tidur',  'stok_barang' => 5,  'harga' => 3000000, 'kayu_id' => 2, 'gambar' => null],
            ['nama_barang' => 'Rak Buku',      'kategori' => 'Ruang Kantor', 'stok_barang' => 15, 'harga' => 750000,  'kayu_id' => 3, 'gambar' => null],
            ['nama_barang' => 'Meja Kerja',    'kategori' => 'Ruang Kantor', 'stok_barang' => 12, 'harga' => 1200000, 'kayu_id' => 4, 'gambar' => null],
            ['nama_barang' => 'Kursi Kerja',   'kategori' => 'Ruang Kantor', 'stok_barang' => 18, 'harga' => 850000,  'kayu_id' => 4, 'gambar' => null],
            ['nama_barang' => 'Meja Tamu',     'kategori' => 'Ruang Tamu',   'stok_barang' => 7,  'harga' => 1800000, 'kayu_id' => 5, 'gambar' => null],
        ];

        foreach ($barangs as $barang) {
            Barang::create($barang);
        }
    }
}
