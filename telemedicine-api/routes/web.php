<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect('/login'));
Route::get('/login', fn() => view('auth.login'));
Route::get('/register', fn() => view('auth.register'));

// Vercel runtime may resolve /api/* into /* for PHP entrypoints.
// Provide auth/ping fallbacks under API middleware to keep login/register functional.
Route::middleware('api')->group(function () {
    Route::get('/ping', fn() => response()->json(['status' => 'ok', 'time' => now()]));
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::middleware(['auth:sanctum', 'check.token'])->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);
    });
});

// ── Pasien ──
Route::get('/welcome', fn() => view('pasien.welcome'));   // ← halaman chat baru
Route::get('/pasien', fn() => view('pasien.dashboard'));
Route::get('/obat-obatan', fn() => view('pasien.obat'));
Route::get('/konsultasi', fn() => view('pasien.konsultasi'));
Route::get('/konsultasi/baru', fn() => view('pasien.konsultasi_baru'));
Route::get('/riwayat', fn() => view('pasien.riwayat'));

// ── Dokter ──
Route::get('/dokter', fn() => view('dokter.dashboard'));
Route::get('/dokter/konsultasi', fn() => view('dokter.konsultasi'));
Route::get('/dokter/riwayat', fn() => view('dokter.riwayat'));
Route::get('/dokter/jadwal-saya', fn() => view('dokter.jadwal_saya'));

// ── Shared ──
Route::get('/jadwal', fn() => view('dokter.jadwal'));
Route::get('/tim', fn() => view('dokter.tim'));

// ── Admin ──
Route::get('/admin', fn() => view('admin.dashboard'));
Route::get('/admin/dokter/tambah', fn() => view('admin.kelola_dokter'));