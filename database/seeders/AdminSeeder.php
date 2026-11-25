<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil konfigurasi admin
        $name = config('services.sifur_admin.name');
        $email = config('services.sifur_admin.email');
        $password = config('services.sifur_admin.password');

        // Pastikan konfigurasi sudah lengkap
        if (! $name || ! $email || ! $password) {
            throw new \RuntimeException(
                'Konfigurasi ADMIN_NAME, ADMIN_EMAIL, dan ADMIN_PASSWORD wajib diisi di .env.'
            );
        }

        // Cari akun berdasarkan email atau buat akun baru
        $user = User::firstOrCreate(
            [
                'email' => $email,
            ],
            [
                'name' => $name,
                'password' => Hash::make($password),
            ]
        );

        // Berikan role admin hanya untuk akun yang baru dibuat
        if ($user->wasRecentlyCreated) {
            $user->role = 'admin';
            $user->save();
        } 
        // Jangan mengubah akun pelanggan yang sudah ada
        elseif ($user->role !== 'admin') {
            throw new \RuntimeException(
                'Email admin sudah digunakan oleh akun non-admin. Gunakan email khusus admin.'
            );
        }
    }
}
