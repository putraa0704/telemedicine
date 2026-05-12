<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Konsultasi;
use Illuminate\Http\Request;

class DokterController extends Controller
{
    /**
     * GET /api/dokter/konsultasi
     * Daftar konsultasi yang relevan untuk dokter/admin.
     *
     * LOGIKA ROUTING KONSULTASI:
     * - Admin  → lihat semua konsultasi (bisa filter per status)
     * - Dokter → lihat hanya konsultasi yang:
     *     (a) memang ditujukan kepada dokter ini (dokter_id = saya), ATAU
     *     (b) belum punya dokter sama sekali DAN belum ditangani siapapun
     *         (status masih 'received') — supaya bisa diambil
     *
     * Dengan begitu konsultasi pasien yang memilih Dokter A TIDAK akan
     * muncul di inbox Dokter B, tapi tetap terlihat oleh admin.
     */
    public function index(Request $request)
    {
        $query = Konsultasi::with('pasien', 'dokter')->oldest();

        // Filter status opsional
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->user()->isDokter()) {
            $myId = $request->user()->id;

            $query->where(function ($q) use ($myId) {
                // (a) konsultasi yang memang diarahkan ke saya
                $q->where('dokter_id', $myId)
                  // (b) konsultasi tanpa dokter yang belum diklaim siapapun
                  ->orWhere(function ($q2) {
                      $q2->whereNull('dokter_id')
                         ->where('status', 'received');
                  });
            });
        }

        $konsultasi = $query
            ->limit($request->query('limit', 50))
            ->get()
            ->map(fn($k) => [
                'id'            => $k->id,
                'nomor_antrian' => $k->nomor_antrian,
                'nama_pasien' => $k->pasien?->name ?? $k->nama,
                'keluhan'     => $k->keluhan,
                'status'      => $k->status,
                'jawaban'     => $k->jawaban_dokter,
                'dokter'      => $k->dokter?->name,
                'created_at'  => $k->created_at,
                'dijawab_at'  => $k->dijawab_at,
            ]);

        return response()->json($konsultasi);
    }

    /**
     * POST /api/dokter/konsultasi/{id}/jawab
     * Dokter menjawab konsultasi — sekaligus mengklaim (assign) konsultasi ke dirinya.
     */
    public function jawab(Request $request, int $id)
    {
        $request->validate([
            'jawaban' => 'required|string',
        ]);

        $konsultasi = Konsultasi::findOrFail($id);

        // Pastikan dokter tidak bisa menjawab konsultasi milik dokter lain
        if ($request->user()->isDokter()
            && $konsultasi->dokter_id !== null
            && $konsultasi->dokter_id !== $request->user()->id
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Konsultasi ini sudah ditangani oleh dokter lain.',
            ], 403);
        }

        $konsultasi->update([
            'jawaban_dokter' => $request->jawaban,
            // Klaim konsultasi ke dokter yang menjawab (jika belum ada)
            'dokter_id'      => $konsultasi->dokter_id ?? $request->user()->id,
            'status'         => 'done',
            'dijawab_at'     => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Jawaban berhasil disimpan',
            'data'    => $konsultasi->fresh('dokter', 'pasien'),
        ]);
    }

    /**
     * PUT /api/dokter/konsultasi/{id}/status
     * Dokter update status konsultasi — sekaligus mengklaim jika belum ada dokternya.
     */
    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:received,in_review,done',
        ]);

        $konsultasi = Konsultasi::findOrFail($id);

        // Pastikan dokter tidak bisa mengubah status konsultasi milik dokter lain
        if ($request->user()->isDokter()
            && $konsultasi->dokter_id !== null
            && $konsultasi->dokter_id !== $request->user()->id
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Konsultasi ini sudah ditangani oleh dokter lain.',
            ], 403);
        }

        $konsultasi->update([
            'status'    => $request->status,
            // Klaim konsultasi ke dokter yang mengubah status (jika belum ada)
            'dokter_id' => $konsultasi->dokter_id ?? $request->user()->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status diperbarui',
            'data'    => $konsultasi,
        ]);
    }
}