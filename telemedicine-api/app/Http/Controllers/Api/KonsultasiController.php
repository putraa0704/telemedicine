<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Konsultasi;
use Illuminate\Http\Request;

class KonsultasiController extends Controller
{
    /**
     * GET /api/konsultasi/saya
     * Semua konsultasi milik pasien yang sedang login
     */
    public function milikSaya(Request $request)
    {
        $konsultasi = Konsultasi::with('dokter')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get()
            ->map(fn($k) => $this->format($k));

        return response()->json($konsultasi);
    }

    /**
     * GET /api/konsultasi/{id}
     * Detail satu konsultasi (pasien hanya bisa lihat miliknya sendiri)
     */
    public function show(Request $request, int $id)
    {
        $konsultasi = Konsultasi::with('dokter', 'pasien')->findOrFail($id);

        if ($request->user()->isPasien() && $konsultasi->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        return response()->json($this->format($konsultasi));
    }

    /**
     * POST /api/konsultasi
     * Buat konsultasi baru — menyimpan dokter_id jika pasien memilih dokter
     */
    public function store(Request $request)
    {
        $request->validate([
            'keluhan'   => 'required|string',
            'nama'      => 'nullable|string|max:255',
            'local_id'  => 'nullable|string',
            // dokter_id opsional: dikirim dari form konsultasi jika pasien memilih dokter
            'dokter_id' => 'nullable|integer|exists:users,id',
        ]);

        // Cek duplikat local_id
        if ($request->local_id) {
            $existing = Konsultasi::where('local_id', $request->local_id)->first();
            if ($existing) {
                return response()->json([
                    'success'   => false,
                    'status'    => 'conflict',
                    'message'   => 'Data sudah tersinkronisasi sebelumnya',
                    'server_id' => $existing->id,
                ], 409);
            }
        }

        $konsultasi = Konsultasi::create([
            'user_id'           => $request->user()->id,
            'local_id'          => $request->local_id ?? ('direct_' . uniqid()),
            'nama'              => $request->nama ?? $request->user()->name,
            'keluhan'           => $request->keluhan,
            'status'            => 'received',
            // ← FIX: simpan dokter_id agar konsultasi terarah ke dokter yang dipilih
            'dokter_id'         => $request->dokter_id ?? null,
            'client_created_at' => $request->created_at ?? now(),
        ]);

        return response()->json([
            'success'   => true,
            'status'    => 'synced',
            'server_id' => $konsultasi->id,
            'message'   => 'Konsultasi berhasil disimpan',
            'data'      => $this->format($konsultasi),
        ], 201);
    }

    /**
     * DELETE /api/konsultasi/{id}
     * Batalkan konsultasi (hanya jika masih received / belum dijawab)
     */
    public function destroy(Request $request, int $id)
    {
        $konsultasi = Konsultasi::findOrFail($id);

        if ($konsultasi->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        if ($konsultasi->status === 'done') {
            return response()->json(['message' => 'Konsultasi yang sudah selesai tidak bisa dibatalkan.'], 422);
        }

        $konsultasi->delete();

        return response()->json(['success' => true, 'message' => 'Konsultasi berhasil dibatalkan.']);
    }

    // ── Private helper format response ──
    private function format(Konsultasi $k): array
    {
        return [
            'id'             => $k->id,
            'local_id'       => $k->local_id,
            'nama'           => $k->nama,
            'nama_pasien'    => $k->pasien?->name ?? $k->nama,
            'keluhan'        => $k->keluhan,
            'status'         => $k->status,
            'jawaban_dokter' => $k->jawaban_dokter,
            'dokter'         => $k->dokter ? [
                'id'           => $k->dokter->id,
                'name'         => $k->dokter->name,
                'spesialisasi' => $k->dokter->spesialisasi,
            ] : null,
            'created_at'     => $k->created_at,
            'dijawab_at'     => $k->dijawab_at,
        ];
    }
}