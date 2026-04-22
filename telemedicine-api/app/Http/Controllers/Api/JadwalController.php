<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JadwalDokter;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class JadwalController extends Controller
{
    /**
     * GET /api/jadwal
     * Semua jadwal aktif untuk tanggal tertentu
     */
    public function index(Request $request)
    {
        $tanggal = $request->query('tanggal', today()->toDateString());
        $dt = \Carbon\Carbon::parse($tanggal);

        $hariMap = [
            0 => 'minggu',
            1 => 'senin',
            2 => 'selasa',
            3 => 'rabu',
            4 => 'kamis',
            5 => 'jumat',
            6 => 'sabtu',
        ];
        $hariEnum = $hariMap[$dt->dayOfWeek];

        $jadwal = JadwalDokter::with('dokter')
            ->where('hari', $hariEnum)
            ->where('is_aktif', true)
            ->orderBy('jam_mulai')
            ->get()
            ->map(fn($j) => [
                'id'          => $j->id,
                'dokter_id'   => $j->dokter_id,
                'jam_mulai'   => $j->jam_mulai,
                'jam_selesai' => $j->jam_selesai,
                'waktu'       => substr($j->jam_mulai, 0, 5) . ' - ' . substr($j->jam_selesai, 0, 5),
                'dokter'      => $j->dokter->name,
                'spesialisasi' => $j->dokter->spesialisasi ?? 'Dokter Umum',
            ]);

        return response()->json([
            'tanggal' => $tanggal,
            'hari'    => $hariEnum,
            'jadwal'  => $jadwal,
        ]);
    }

    /**
     * GET /api/jadwal/mingguan
     * Jadwal 5 hari (Senin–Jumat) untuk tampilan tabel
     * ⚠ Menyertakan dokter_id agar frontend bisa matching tanpa bergantung pada nama
     */
    public function mingguan()
    {
        $hariList = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];

        $jadwal = JadwalDokter::with('dokter')
            ->whereIn('hari', $hariList)
            ->where('is_aktif', true)
            ->orderBy('jam_mulai')
            ->get()
            ->groupBy('hari');

        $result = [];
        foreach ($hariList as $hari) {
            $result[$hari] = ($jadwal[$hari] ?? collect())->map(fn($j) => [
                'id'          => $j->id,
                'dokter_id'   => $j->dokter_id,          // ← PENTING: dibutuhkan frontend
                'jam_mulai'   => $j->jam_mulai,
                'jam_selesai' => $j->jam_selesai,
                // Format waktu bersih tanpa detik: "08:00 - 09:00"
                'waktu'       => substr($j->jam_mulai, 0, 5) . ' - ' . substr($j->jam_selesai, 0, 5),
                'dokter'      => $j->dokter->name,
            ]);
        }

        return response()->json($result);
    }

    /**
     * POST /api/jadwal
     */
    public function store(Request $request)
    {
        $dokterId = $request->user()->isDokter()
            ? $request->user()->id
            : $request->input('dokter_id');

        $request->validate([
            'dokter_id' => [
                Rule::requiredIf(!$request->user()->isDokter()),
                'integer',
                Rule::exists('users', 'id')->where(fn($q) => $q->where('role', 'dokter')),
            ],
            'hari'        => 'required|in:senin,selasa,rabu,kamis,jumat,sabtu,minggu',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'jam_mulai'   => [
                'required',
                'date_format:H:i',
                Rule::unique('jadwal_dokter')->where(fn($q) => $q
                    ->where('dokter_id', $dokterId)
                    ->where('hari', $request->input('hari'))),
            ],
        ], [
            'dokter_id.required' => 'Pilih dokter terlebih dahulu.',
            'dokter_id.exists'   => 'Dokter tidak ditemukan atau tidak aktif.',
            'jam_mulai.unique'   => 'Jadwal dokter di hari dan jam mulai tersebut sudah ada.',
        ]);

        $jadwal = JadwalDokter::create([
            'dokter_id'   => $dokterId,
            'hari'        => $request->hari,
            'jam_mulai'   => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'is_aktif'    => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil ditambahkan',
            'data'    => $jadwal->load('dokter'),
        ], 201);
    }

    /**
     * PUT /api/jadwal/{id}
     */
    public function update(Request $request, int $id)
    {
        $jadwal = JadwalDokter::findOrFail($id);

        if ($request->user()->isDokter() && $jadwal->dokter_id !== $request->user()->id) {
            throw ValidationException::withMessages([
                'jadwal' => ['Anda tidak diizinkan mengubah jadwal dokter lain.'],
            ]);
        }

        $request->validate([
            'is_aktif'    => 'boolean',
            'hari'        => 'sometimes|in:senin,selasa,rabu,kamis,jumat,sabtu,minggu',
            'jam_mulai'   => 'sometimes|date_format:H:i',
            'jam_selesai' => 'sometimes|date_format:H:i',
        ]);

        $jamMulai   = $request->input('jam_mulai',   $jadwal->jam_mulai);
        $jamSelesai = $request->input('jam_selesai', $jadwal->jam_selesai);

        if ($jamSelesai <= $jamMulai) {
            throw ValidationException::withMessages([
                'jam_selesai' => ['Jam selesai harus lebih besar dari jam mulai.'],
            ]);
        }

        $jadwal->update($request->only(['is_aktif', 'hari', 'jam_mulai', 'jam_selesai']));
        return response()->json(['success' => true, 'data' => $jadwal]);
    }

    /**
     * DELETE /api/jadwal/{id}
     */
    public function destroy(int $id)
    {
        JadwalDokter::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Jadwal dihapus.']);
    }
}