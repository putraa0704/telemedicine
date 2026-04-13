<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BookingJadwal;
use App\Models\JadwalDokter;
use App\Models\Konsultasi;
use App\Models\User;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    /**
     * GET /api/jadwal
     * Semua jadwal aktif untuk hari ini + 7 hari ke depan
     * Query params: ?tanggal=2026-04-12  (default: hari ini)
     */
    public function index(Request $request)
    {
        $tanggal = $request->query('tanggal', today()->toDateString());
        $hari    = strtolower(now()->parse($tanggal)->locale('id')->dayName);

        // Map nama hari Indonesia → enum
        $hariMap = [
            'senin'   => 'senin',
            'selasa'  => 'selasa',
            'rabu'    => 'rabu',
            'kamis'   => 'kamis',
            'jumat'   => 'jumat',
            'sabtu'   => 'sabtu',
            'minggu'  => 'minggu',
            'monday'    => 'senin',
            'tuesday'   => 'selasa',
            'wednesday' => 'rabu',
            'thursday'  => 'kamis',
            'friday'    => 'jumat',
            'saturday'  => 'sabtu',
            'sunday'    => 'minggu',
        ];
        $hariEnum = $hariMap[$hari] ?? 'senin';

        $jadwal = JadwalDokter::with('dokter')
            ->where('hari', $hariEnum)
            ->where('is_aktif', true)
            ->orderBy('jam_mulai')
            ->get()
            ->map(fn($j) => $this->formatSlot($j, $tanggal));

        return response()->json([
            'tanggal' => $tanggal,
            'hari'    => $hariEnum,
            'jadwal'  => $jadwal,
        ]);
    }

    /**
     * GET /api/jadwal/mingguan
     * Jadwal 5 hari ke depan (Senin–Jumat) untuk tampilan tabel
     */
    public function mingguan()
    {
        $hariList = ['senin','selasa','rabu','kamis','jumat'];

        $jadwal = JadwalDokter::with('dokter')
            ->whereIn('hari', $hariList)
            ->where('is_aktif', true)
            ->orderBy('jam_mulai')
            ->get()
            ->groupBy('hari');

        $result = [];
        foreach ($hariList as $hari) {
            $result[$hari] = ($jadwal[$hari] ?? collect())->map(fn($j) => [
                'id'         => $j->id,
                'jam_mulai'  => $j->jam_mulai,
                'jam_selesai'=> $j->jam_selesai,
                'dokter'     => $j->dokter->name,
            ]);
        }

        return response()->json($result);
    }

    /**
     * POST /api/jadwal/{id}/booking
     * Pasien booking slot jadwal
     */
    public function booking(Request $request, int $id)
    {
        $request->validate([
            'tanggal' => 'required|date|after_or_equal:today',
            'keluhan' => 'required|string|max:500',
        ]);

        $jadwal  = JadwalDokter::with('dokter')->findOrFail($id);
        $tanggal = $request->tanggal;

        // Cek slot sudah terisi
        if ($jadwal->sudahTerisi($tanggal)) {
            return response()->json([
                'success' => false,
                'message' => 'Slot jadwal ini sudah terisi untuk tanggal ' . $tanggal,
            ], 422);
        }

        // Buat konsultasi dulu
        $konsultasi = Konsultasi::create([
            'user_id'           => $request->user()->id,
            'dokter_id'         => $jadwal->dokter_id,
            'local_id'          => 'booking_' . uniqid(),
            'nama'              => $request->user()->name,
            'keluhan'           => $request->keluhan,
            'status'            => 'received',
            'client_created_at' => now(),
        ]);

        // Buat booking
        $booking = BookingJadwal::create([
            'jadwal_id'      => $jadwal->id,
            'pasien_id'      => $request->user()->id,
            'konsultasi_id'  => $konsultasi->id,
            'tanggal'        => $tanggal,
            'status'         => 'booked',
            'catatan'        => $request->keluhan,
        ]);

        return response()->json([
            'success'        => true,
            'message'        => 'Jadwal berhasil dipesan',
            'booking_id'     => $booking->id,
            'konsultasi_id'  => $konsultasi->id,
            'jadwal' => [
                'waktu'  => $jadwal->jam_mulai . ' - ' . $jadwal->jam_selesai,
                'dokter' => $jadwal->dokter->name,
                'tanggal'=> $tanggal,
            ],
        ], 201);
    }

    /**
     * GET /api/jadwal/booking-saya
     * Daftar booking milik pasien yang login
     */
    public function bookingSaya(Request $request)
    {
        $bookings = BookingJadwal::with('jadwal.dokter', 'konsultasi')
            ->where('pasien_id', $request->user()->id)
            ->latest()
            ->get()
            ->map(fn($b) => [
                'id'          => $b->id,
                'tanggal'     => $b->tanggal->toDateString(),
                'status'      => $b->status,
                'waktu'       => $b->jadwal->jam_mulai . ' - ' . $b->jadwal->jam_selesai,
                'dokter'      => $b->jadwal->dokter->name,
                'spesialisasi'=> $b->jadwal->dokter->spesialisasi,
                'catatan'     => $b->catatan,
                'konsultasi'  => $b->konsultasi ? [
                    'id'             => $b->konsultasi->id,
                    'status'         => $b->konsultasi->status,
                    'jawaban_dokter' => $b->konsultasi->jawaban_dokter,
                ] : null,
            ]);

        return response()->json($bookings);
    }

    /**
     * DELETE /api/jadwal/booking/{id}
     * Batalkan booking (hanya bisa jika masih 'booked')
     */
    public function batalBooking(Request $request, int $id)
    {
        $booking = BookingJadwal::where('pasien_id', $request->user()->id)->findOrFail($id);

        if ($booking->status !== 'booked') {
            return response()->json(['message' => 'Booking tidak bisa dibatalkan.'], 422);
        }

        $booking->update(['status' => 'dibatalkan']);

        // Hapus konsultasi terkait juga jika belum dijawab
        if ($booking->konsultasi && $booking->konsultasi->status === 'received') {
            $booking->konsultasi->delete();
        }

        return response()->json(['success' => true, 'message' => 'Booking berhasil dibatalkan.']);
    }

    // ── Admin/Dokter: kelola jadwal sendiri ──

    /**
     * POST /api/jadwal
     * Dokter tambah jadwal baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'hari'        => 'required|in:senin,selasa,rabu,kamis,jumat,sabtu,minggu',
            'jam_mulai'   => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ]);

        $dokter_id = $request->user()->isDokter()
            ? $request->user()->id
            : $request->input('dokter_id'); // admin bisa set dokter lain

        $jadwal = JadwalDokter::create([
            'dokter_id'   => $dokter_id,
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
     * Update jadwal (aktif/nonaktif)
     */
    public function update(Request $request, int $id)
    {
        $jadwal = JadwalDokter::findOrFail($id);

        $request->validate([
            'is_aktif'    => 'boolean',
            'jam_mulai'   => 'sometimes|date_format:H:i',
            'jam_selesai' => 'sometimes|date_format:H:i|after:jam_mulai',
        ]);

        $jadwal->update($request->only(['is_aktif', 'jam_mulai', 'jam_selesai']));

        return response()->json(['success' => true, 'data' => $jadwal]);
    }

    /**
     * DELETE /api/jadwal/{id}
     * Hapus jadwal (dokter/admin)
     */
    public function destroy(int $id)
    {
        $jadwal = JadwalDokter::findOrFail($id);
        $jadwal->delete();

        return response()->json(['success' => true, 'message' => 'Jadwal dihapus.']);
    }

    // ── Private helper ──
    private function formatSlot(JadwalDokter $j, string $tanggal): array
    {
        $terisi = $j->sudahTerisi($tanggal);
        return [
            'id'          => $j->id,
            'jam_mulai'   => $j->jam_mulai,
            'jam_selesai' => $j->jam_selesai,
            'waktu'       => $j->jam_mulai . ' - ' . $j->jam_selesai,
            'dokter_id'   => $j->dokter_id,
            'dokter'      => $j->dokter->name,
            'spesialisasi'=> $j->dokter->spesialisasi,
            'terisi'      => $terisi,
        ];
    }
}