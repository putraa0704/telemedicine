<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DokterSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(['email' => 'dokter@caremate.id'], [
            'name' => 'Dr. Budi Santoso',
            'email' => 'dokter@caremate.id',
            'password' => Hash::make('password123'),
            'role' => 'dokter',
            'spesialisasi' => 'Dokter Umum',
            'no_str' => 'STR-2024-001',
            'no_hp' => '08123456789',
        ]);

        User::updateOrCreate(['email' => 'admin@caremate.id'], [
            'name' => 'Admin',
            'email' => 'admin@caremate.id',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'no_hp' => '08000000000',
        ]);
    }
}