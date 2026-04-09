<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect('/login'));
Route::get('/login',    fn() => view('auth.login'));
Route::get('/register', fn() => view('auth.register'));
Route::get('/pasien',   fn() => view('pasien.dashboard'));
Route::get('/dokter',   fn() => view('dokter.dashboard'));