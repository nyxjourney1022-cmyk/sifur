<?php

namespace Database\Seeders;

use App\Models\Kayu;
use Illuminate\Database\Seeder;

class KayuSeeder extends Seeder
{
    public function run(): void
    {
        $kayus = [
            ['jenis_kayu' => 'Jati'],
            ['jenis_kayu' => 'Mahoni'],
            ['jenis_kayu' => 'Pinus'],
            ['jenis_kayu' => 'Meranti'],
            ['jenis_kayu' => 'Sonokeling'],
        ];

        foreach ($kayus as $kayu) {
            Kayu::create($kayu);
        }
    }
}
