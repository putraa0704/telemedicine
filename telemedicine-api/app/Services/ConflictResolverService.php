<?php

namespace App\Services;

use App\Models\Konsultasi;

class ConflictResolverService
{
    /**
     * Cek apakah data dari client konflik dengan data di server
     * Strategy: jika local_id sudah ada → duplicate, tolak dengan info
     */
    public function resolve(array $payload): array
    {
        $existing = Konsultasi::where('local_id', $payload['local_id'])->first();

        // Tidak ada di server → aman, simpan
        if (!$existing) {
            return ['status' => 'ok', 'existing' => null];
        }

        // Sudah ada dengan local_id yang sama → duplicate/conflict
        return [
            'status'   => 'conflict',
            'existing' => $existing,
            'reason'   => 'local_id already exists on server',
        ];
    }
}