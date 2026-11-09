<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name'  => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Urutan penting: master data dulu, baru transaksi
        $this->call([
            KayuSeeder::class,
            FurnitureSeeder::class,
            BarangSeeder::class,
            PelangganSeeder::class,
            ReservasiSeeder::class,
            TransaksiSeeder::class,
        ]);
    }
}
