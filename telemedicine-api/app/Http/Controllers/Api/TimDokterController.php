<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class TimDokterController extends Controller
{
    /**
     * GET /api/tim-dokter
     * Daftar semua dokter aktif beserta info jadwal & jumlah pasien
     */
    public function index()
    {
        $dokterList = User::where('role', 'dokter')
            ->withCount([
                // Jumlah pasien aktif (konsultasi belum selesai)
                'konsultasi as pasien_aktif' => fn($q) => $q->whereIn('status', ['received','in_review']),
            ])
            ->with([
                'jadwalDokter' => fn($q) => $q->where('is_aktif', true)->orderBy('hari')->orderBy('jam_mulai'),
            ])
            ->get()
            ->map(fn($d) => $this->format($d));

        return response()->json($dokterList);
    }

    /**
     * GET /api/tim-dokter/{id}
     * Detail satu dokter + jadwal lengkapnya
     */
    public function show(int $id)
    {
        $dokter = User::where('role', 'dokter')
            ->withCount([
                'konsultasi as pasien_aktif'  => fn($q) => $q->whereIn('status', ['received','in_review']),
                'konsultasi as total_pasien',
            ])
            ->with([
                'jadwalDokter' => fn($q) => $q->where('is_aktif', true)->orderBy('hari')->orderBy('jam_mulai'),
            ])
            ->findOrFail($id);

        return response()->json($this->format($dokter, detail: true));
    }

    /**
     * PUT /api/tim-dokter/{id}/status
     * Admin: toggle status aktif/nonaktif dokter
     */
    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'is_aktif' => 'required|boolean',
        ]);

        $dokter = User::where('role', 'dokter')->findOrFail($id);

        // Nonaktifkan semua jadwal jika dokter dinonaktifkan
        if (!$request->is_aktif) {
            $dokter->jadwalDokter()->update(['is_aktif' => false]);
        }

        // Simpan flag di field baru (atau gunakan soft delete, sesuai kebutuhan)
        // Untuk sekarang kita simpan di field 'alamat' sebagai workaround
        // → Di production, tambahkan kolom 'is_aktif' ke tabel users
        $dokter->update(['alamat' => $request->is_aktif ? null : '__nonaktif__']);

        return response()->json([
            'success'  => true,
            'message'  => $request->is_aktif ? 'Dokter diaktifkan.' : 'Dokter dinonaktifkan.',
        ]);
    }

    // ── Private helper format response ──
    private function format(User $d, bool $detail = false): array
    {
        $hariMap = [
            'senin'=>'Senin','selasa'=>'Selasa','rabu'=>'Rabu',
            'kamis'=>'Kamis','jumat'=>'Jumat','sabtu'=>'Sabtu','minggu'=>'Minggu',
        ];

        // Kelompokkan jadwal per hari
        $jadwalPerHari = $d->jadwalDokter
            ->groupBy('hari')
            ->map(fn($slots, $hari) => [
                'hari'  => $hariMap[$hari] ?? $hari,
                'slots' => $slots->map(fn($s) => [
                    'id'          => $s->id,
                    'jam_mulai'   => $s->jam_mulai,
                    'jam_selesai' => $s->jam_selesai,
                    'waktu'       => $s->jam_mulai . ' - ' . $s->jam_selesai,
                ]),
            ])
            ->values();

        $hariAktif = $d->jadwalDokter->pluck('hari')->unique()->map(fn($h) => $hariMap[$h] ?? $h)->values();

        // Status: "sibuk" jika pasien aktif >= 10, "tersedia" jika ada jadwal
        $pasienAktif = $d->pasien_aktif ?? 0;
        $status = match(true) {
            $pasienAktif >= 10 => 'sibuk',
            default            => 'tersedia',
        };

        $base = [
            'id'           => $d->id,
            'nama'         => $d->name,
            'inisial'      => collect(explode(' ', $d->name))->map(fn($w) => strtoupper($w[0]))->take(2)->join(''),
            'spesialisasi' => $d->spesialisasi ?? 'Dokter Umum',
            'no_str'       => $d->no_str,
            'no_hp'        => $d->no_hp,
            'pasien_aktif' => $pasienAktif,
            'status'       => $status,
            'hari_praktik' => $hariAktif,
        ];

        if ($detail) {
            $base['jadwal']       = $jadwalPerHari;
            $base['total_pasien'] = $d->total_pasien ?? 0;
        }

        return $base;
    }
}