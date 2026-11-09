<?php

namespace Database\Seeders;

use App\Models\Furniture;
use Illuminate\Database\Seeder;

class FurnitureSeeder extends Seeder
{
    public function run(): void
    {
        Furniture::create([
            'nama_toko'   => 'Toko Sifur Furniture',
            'alamat_toko' => 'Jl. Raya Furniture No. 10, Jakarta',
            'no_tlp'      => '021-12345678',
        ]);
    }
}
