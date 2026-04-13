<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Konsultasi;
use Illuminate\Http\Request;

class DokterController extends Controller
{
    /**
     * GET /api/dokter/konsultasi
     * Semua konsultasi yang masuk (dokter & admin)
     * Query: ?status=received|in_review|done  ?limit=20
     */
    public function index(Request $request)
    {
        $query = Konsultasi::with('pasien', 'dokter')->latest();

        // Filter status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Jika dokter (bukan admin), hanya tampilkan yang ditugaskan padanya
        // atau yang belum ada dokternya
        if ($request->user()->isDokter()) {
            $query->where(function ($q) use ($request) {
                $q->where('dokter_id', $request->user()->id)
                  ->orWhereNull('dokter_id');
            });
        }

        $konsultasi = $query->limit($request->query('limit', 50))
            ->get()
            ->map(fn($k) => [
                'id'          => $k->id,
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
     * Dokter menjawab konsultasi
     */
    public function jawab(Request $request, int $id)
    {
        $request->validate([
            'jawaban' => 'required|string',
        ]);

        $konsultasi = Konsultasi::findOrFail($id);

        $konsultasi->update([
            'jawaban_dokter' => $request->jawaban,
            'dokter_id'      => $request->user()->id,
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
     * Dokter update status konsultasi (in_review, dll)
     */
    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:received,in_review,done',
        ]);

        $konsultasi = Konsultasi::findOrFail($id);
        $konsultasi->update([
            'status'    => $request->status,
            'dokter_id' => $request->user()->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status diperbarui',
            'data'    => $konsultasi,
        ]);
    }
}