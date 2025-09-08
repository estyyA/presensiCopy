<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Karyawan\DashboardController as KaryawanDashboardController;
use App\Http\Controllers\KaryawanController;


Route::get('/', function () {
    return view('Login');
});

// Login
Route::get('/Login', [PageController::class, 'Login'])->name('Login');
Route::post('/Login', [PageController::class, 'Login'])->name('Login');

// Dashboard & Halaman lain
Route::get('/dashboard', [PageController::class, 'dashboard']);
Route::get('/daftarPresensi', [PageController::class, 'daftarPresensi']);
Route::get('/daftarKaryawan', [PageController::class, 'daftarKaryawan']);
Route::get('/laporan', [PageController::class, 'laporan']);
Route::get('/PresensiKaryawan', [PageController::class, 'PresensiKaryawan']);

