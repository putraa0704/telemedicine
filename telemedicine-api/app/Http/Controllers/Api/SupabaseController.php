<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class SupabaseController extends Controller
{
    public function timDokter(): JsonResponse
    {
        $baseUrl = rtrim((string) config('services.supabase.url'), '/');
        $apiKey = (string) config('services.supabase.key');

        if (!$baseUrl || !$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'Konfigurasi Supabase belum diatur.',
            ], 503);
        }

        try {
            $headers = [
                'apikey' => $apiKey,
                'Authorization' => 'Bearer ' . $apiKey,
                'Accept' => 'application/json',
            ];

            $usersResponse = Http::withHeaders($headers)
                ->timeout(12)
                ->get($baseUrl . '/rest/v1/users', [
                    'select' => 'id,name,spesialisasi,no_str,no_hp,role',
                    'role' => 'eq.dokter',
                    'order' => 'name.asc',
                ]);

            if (!$usersResponse->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil data dokter dari Supabase.',
                    'detail' => $usersResponse->json('message') ?: $usersResponse->body(),
                ], $usersResponse->status());
            }

            $jadwalResponse = Http::withHeaders($headers)
                ->timeout(12)
                ->get($baseUrl . '/rest/v1/jadwal_dokter', [
                    'select' => 'id,dokter_id,hari,jam_mulai,jam_selesai,is_aktif',
                    'is_aktif' => 'eq.true',
                    'order' => 'hari.asc,jam_mulai.asc',
                ]);

            $jadwalRows = $jadwalResponse->successful() ? $jadwalResponse->json() : [];

            $hariLabel = [
                'senin' => 'Senin',
                'selasa' => 'Selasa',
                'rabu' => 'Rabu',
                'kamis' => 'Kamis',
                'jumat' => 'Jumat',
                'sabtu' => 'Sabtu',
                'minggu' => 'Minggu',
            ];

            $jadwalByDokter = collect($jadwalRows)
                ->groupBy('dokter_id')
                ->map(function ($rows) use ($hariLabel) {
                    return collect($rows)
                        ->groupBy('hari')
                        ->map(function ($slots, $hari) use ($hariLabel) {
                            return [
                                'hari_key' => $hari,
                                'hari' => $hariLabel[$hari] ?? ucfirst($hari),
                                'slots' => collect($slots)->map(function ($slot) {
                                    return [
                                        'id' => $slot['id'],
                                        'jam_mulai' => $slot['jam_mulai'],
                                        'jam_selesai' => $slot['jam_selesai'],
                                        'waktu' => $slot['jam_mulai'] . ' - ' . $slot['jam_selesai'],
                                    ];
                                })->values(),
                            ];
                        })
                        ->values();
                });

            $data = collect($usersResponse->json())
                ->map(function ($doctor) use ($jadwalByDokter, $hariLabel) {
                    $nama = $doctor['name'] ?? 'Dokter';
                    $slots = $jadwalByDokter->get($doctor['id'], collect());
                    $hariPraktik = collect($slots)
                        ->pluck('hari_key')
                        ->unique()
                        ->map(fn($hari) => $hariLabel[$hari] ?? ucfirst((string) $hari))
                        ->values();

                    return [
                        'id' => $doctor['id'],
                        'nama' => $nama,
                        'inisial' => collect(explode(' ', $nama))
                            ->filter()
                            ->map(fn($word) => strtoupper(substr($word, 0, 1)))
                            ->take(2)
                            ->join(''),
                        'spesialisasi' => $doctor['spesialisasi'] ?? 'Dokter Umum',
                        'no_str' => $doctor['no_str'] ?? null,
                        'no_hp' => $doctor['no_hp'] ?? null,
                        'pasien_aktif' => 0,
                        'status' => 'tersedia',
                        'hari_praktik' => $hariPraktik,
                        'jadwal' => $slots,
                    ];
                })
                ->values();

            return response()->json($data);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Supabase tidak dapat diakses.',
                'detail' => $e->getMessage(),
            ], 502);
        }
    }
}
