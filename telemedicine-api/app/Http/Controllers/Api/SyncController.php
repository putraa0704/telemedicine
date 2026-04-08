<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Konsultasi;
use App\Models\SyncLog;
use App\Services\ConflictResolverService;
use Illuminate\Http\Request;

class SyncController extends Controller
{
    public function __construct(
        private ConflictResolverService $resolver
    ) {}

    public function sync(Request $request)
    {
        $request->validate([
            'local_id'   => 'required|string',
            'nama'       => 'required|string|max:255',
            'keluhan'    => 'required|string',
            'created_at' => 'nullable|string',
        ]);

        $payload = $request->only(['local_id', 'nama', 'keluhan', 'created_at']);

        // Jalankan conflict resolution
        $resolution = $this->resolver->resolve($payload);

        // Log semua sync attempt
        SyncLog::create([
            'local_id'        => $payload['local_id'],
            'action'          => 'create',
            'result'          => $resolution['status'] === 'ok' ? 'success' : 'conflict',
            'payload'         => $payload,
            'conflict_detail' => $resolution['status'] === 'conflict'
                                    ? $resolution['reason']
                                    : null,
            'ip_address'      => $request->ip(),
        ]);

        // Kalau conflict → return data yang sudah ada di server
        if ($resolution['status'] === 'conflict') {
            return response()->json([
                'success'   => false,
                'status'    => 'conflict',
                'message'   => 'Data sudah tersinkronisasi sebelumnya',
                'server_id' => $resolution['existing']->id,
            ], 409);
        }

        // Simpan ke database
        $konsultasi = Konsultasi::create([
            'user_id'           => $request->user()->id,
            'local_id'          => $payload['local_id'],
            'nama'              => $payload['nama'],
            'keluhan'           => $payload['keluhan'],
            'client_created_at' => $payload['created_at'] ?? now(),
        ]);

        return response()->json([
            'success'   => true,
            'status'    => 'synced',
            'server_id' => $konsultasi->id,
            'message'   => 'Konsultasi berhasil disimpan',
        ], 201);
    }
}