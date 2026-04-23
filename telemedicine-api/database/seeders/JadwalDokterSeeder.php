<?php

namespace Database\Seeders;

use App\Models\JadwalDokter;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class JadwalDokterSeeder extends Seeder
{
    public function run(): void
    {
        // Buat 4 akun dokter
        $dokterData = [
            [
                'name' => 'Dr. Hendra Wijaya',
                'email' => 'hendra@caremate.id',
                'password' => Hash::make('password123'),
                'role' => 'dokter',
                'spesialisasi' => 'Dokter Umum',
                'no_str' => 'STR-2024-001',
                'no_hp' => '081809386382',
                'jadwal' => [
                    ['hari' => 'selasa', 'jam_mulai' => '07:00', 'jam_selesai' => '12:00'],
                    ['hari' => 'rabu', 'jam_mulai' => '07:00', 'jam_selesai' => '12:00'],
                    ['hari' => 'sabtu', 'jam_mulai' => '07:00', 'jam_selesai' => '12:00'],
                ],
            ],
            [
                'name' => 'Dr. Sari Kusuma',
                'email' => 'sari@caremate.id',
                'password' => Hash::make('password123'),
                'role' => 'dokter',
                'spesialisasi' => 'Dokter Anak',
                'no_str' => 'STR-2024-002',
                'no_hp' => '08222222222',
                'jadwal' => [
                    ['hari' => 'selasa', 'jam_mulai' => '09:00', 'jam_selesai' => '10:00'],
                    ['hari' => 'selasa', 'jam_mulai' => '14:00', 'jam_selesai' => '15:00'],
                    ['hari' => 'kamis', 'jam_mulai' => '09:00', 'jam_selesai' => '10:00'],
                    ['hari' => 'kamis', 'jam_mulai' => '14:00', 'jam_selesai' => '15:00'],
                ],
            ],
            [
                'name' => 'Dr. Ahmad Ridho',
                'email' => 'ahmad@caremate.id',
                'password' => Hash::make('password123'),
                'role' => 'dokter',
                'spesialisasi' => 'Kardiologi',
                'no_str' => 'STR-2024-003',
                'no_hp' => '08333333333',
                'jadwal' => [
                    ['hari' => 'senin', 'jam_mulai' => '10:00', 'jam_selesai' => '11:00'],
                    ['hari' => 'selasa', 'jam_mulai' => '10:00', 'jam_selesai' => '11:00'],
                    ['hari' => 'kamis', 'jam_mulai' => '15:00', 'jam_selesai' => '16:00'],
                ],
            ],
            [
                'name' => 'Dr. Maya Putri',
                'email' => 'maya@CareMate.id',
                'password' => Hash::make('password123'),
                'role' => 'dokter',
                'spesialisasi' => 'Dermatologi',
                'no_str' => 'STR-2024-004',
                'no_hp' => '08444444444',
                'jadwal' => [
                    ['hari' => 'rabu', 'jam_mulai' => '13:00', 'jam_selesai' => '14:00'],
                    ['hari' => 'jumat', 'jam_mulai' => '08:00', 'jam_selesai' => '09:00'],
                    ['hari' => 'jumat', 'jam_mulai' => '15:00', 'jam_selesai' => '16:00'],
                ],
            ],
        ];

        foreach ($dokterData as $data) {
            $jadwalList = $data['jadwal'];
            unset($data['jadwal']);

            // Selalu sinkronkan data akun dokter agar kredensial tetap konsisten saat seed ulang.
            $dokter = User::updateOrCreate(['email' => $data['email']], $data);

            // Tambah jadwal
            foreach ($jadwalList as $jadwal) {
                JadwalDokter::firstOrCreate(
                    [
                        'dokter_id' => $dokter->id,
                        'hari' => $jadwal['hari'],
                        'jam_mulai' => $jadwal['jam_mulai'],
                    ],
                    [
                        'jam_selesai' => $jadwal['jam_selesai'],
                        'is_aktif' => true,
                    ]
                );
            }
        }

        $this->command->info('✅ Jadwal dokter selesai di-seed!');
    }
}