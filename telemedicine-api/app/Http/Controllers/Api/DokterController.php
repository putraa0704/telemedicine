<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Konsultasi;
use Illuminate\Http\Request;

class DokterController extends Controller
{
    // Lihat semua konsultasi yang masuk
    public function index()
    {
        $konsultasi = Konsultasi::with('pasien')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id'           => $item->id,
                    'nama_pasien'  => $item->pasien?->name ?? $item->nama,
                    'keluhan'      => $item->keluhan,
                    'status'       => $item->status,
                    'jawaban'      => $item->jawaban_dokter,
                    'created_at'   => $item->created_at,
                    'dijawab_at'   => $item->dijawab_at,
                ];
            });

        return response()->json($konsultasi);
    }

    // Beri jawaban
    public function jawab(Request $request, $id)
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
            'data'    => $konsultasi,
        ]);
    }
}