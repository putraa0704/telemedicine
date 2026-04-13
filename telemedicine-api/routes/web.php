<?php

use Illuminate\Support\Facades\Route;

Route::get('/',                    fn() => redirect('/login'));
Route::get('/login',               fn() => view('auth.login'));
Route::get('/register',            fn() => view('auth.register'));

// ── Pasien ──
Route::get('/pasien',              fn() => view('pasien.dashboard'));
Route::get('/konsultasi',          fn() => view('pasien.konsultasi'));
Route::get('/konsultasi/baru',     fn() => view('pasien.konsultasi_baru'));
Route::get('/riwayat',             fn() => view('pasien.riwayat'));

// ── Dokter ──
Route::get('/dokter',              fn() => view('dokter.dashboard'));

// ── Shared (semua role) ──
Route::get('/jadwal',              fn() => view('dokter.jadwal'));
Route::get('/tim',                 fn() => view('dokter.tim'));

// ── Admin ──
Route::get('/admin',               fn() => view('admin.dashboard'));
Route::get('/admin/dokter/tambah', fn() => view('admin.kelola_dokter'));