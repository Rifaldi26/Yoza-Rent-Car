<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin utama
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'              => 'Admin Yoza Rent Car',
                'no_hp'             => '081234567890',
                'password'          => Hash::make('admin123'),
                'role'              => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Akun demo pelanggan (opsional, untuk testing)
        User::updateOrCreate(
            ['email' => 'pelanggan@gmail.com'],
            [
                'name'              => 'Budi Santoso',
                'no_hp'             => '082345678901',
                'password'          => Hash::make('user123'),
                'role'              => 'user',
                'email_verified_at' => now(),
            ]
        );
    }
}