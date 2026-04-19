<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DokterController;
use App\Http\Controllers\Api\JadwalController;
use App\Http\Controllers\Api\KonsultasiController;
use App\Http\Controllers\Api\SyncController;
use App\Http\Controllers\Api\TimDokterController;
use Illuminate\Support\Facades\Route;

// Health check
Route::get('/ping', fn() => response()->json(['status' => 'ok', 'time' => now()]));

// Auth public
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Protected routes
Route::middleware(['auth:sanctum', 'check.token'])->group(function () {

    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Tim Dokter
    Route::get('/tim-dokter', [TimDokterController::class, 'index']);
    Route::get('/tim-dokter/{id}', [TimDokterController::class, 'show']);

    // Jadwal
    Route::get('/jadwal', [JadwalController::class, 'index']);
    Route::get('/jadwal/mingguan', [JadwalController::class, 'mingguan']);

    // Konsultasi Pasien
    Route::get('/konsultasi/saya', [KonsultasiController::class, 'milikSaya']);
    Route::get('/konsultasi/{id}', [KonsultasiController::class, 'show']);
    Route::delete('/konsultasi/{id}', [KonsultasiController::class, 'destroy']);

    // Sync offline
    Route::post('/konsultasi', [SyncController::class, 'sync']);

    // Dokter & Admin only
    Route::middleware('role:dokter,admin')->group(function () {
        Route::get('/dokter/konsultasi', [DokterController::class, 'index']);
        Route::post('/dokter/konsultasi/{id}/jawab', [DokterController::class, 'jawab']);
        Route::put('/dokter/konsultasi/{id}/status', [DokterController::class, 'updateStatus']);

        Route::post('/jadwal', [JadwalController::class, 'store']);
        Route::put('/jadwal/{id}', [JadwalController::class, 'update']);
        Route::delete('/jadwal/{id}', [JadwalController::class, 'destroy']);
    });

    // Admin only
    Route::middleware('role:admin')->group(function () {
        Route::post('/auth/register-dokter', [AuthController::class, 'registerDokter']);
        Route::put('/tim-dokter/{id}/status', [TimDokterController::class, 'updateStatus']);
        Route::get('/konsultasi', fn() => response()->json(\App\Models\Konsultasi::latest()->get()));
        Route::get('/sync-logs', fn() => response()->json(\App\Models\SyncLog::latest()->get()));
    });
});