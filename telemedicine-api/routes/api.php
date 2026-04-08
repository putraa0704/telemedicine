<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DokterController;
use App\Http\Controllers\Api\SyncController;
use Illuminate\Support\Facades\Route;

// Health check
Route::get('/ping', fn() => response()->json(['status' => 'ok', 'time' => now()]));

// ── Auth (public) ──────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);
});

// ── Protected routes ───────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me',      [AuthController::class, 'me']);

    // Pasien — kirim konsultasi
    Route::post('/konsultasi', [SyncController::class, 'sync']);

    // Pasien — lihat konsultasi milik sendiri
    Route::get('/konsultasi/saya', function (Illuminate\Http\Request $req) {
        return response()->json(
            \App\Models\Konsultasi::where('user_id', $req->user()->id)
                ->latest()->get()
        );
    });

    // Dokter only
    Route::middleware('role:dokter,admin')->group(function () {
        Route::get('/dokter/konsultasi',          [DokterController::class, 'index']);
        Route::post('/dokter/konsultasi/{id}/jawab', [DokterController::class, 'jawab']);
    });

    // Debug (hapus saat production)
    Route::get('/konsultasi', fn() =>
        response()->json(\App\Models\Konsultasi::latest()->get())
    );
    Route::get('/sync-logs', fn() =>
        response()->json(\App\Models\SyncLog::latest()->get())
    );
});