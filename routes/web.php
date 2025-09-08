<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;

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

// Laporan (PDF & Excel)
Route::get('/laporan/pdf', [PageController::class, 'cetakPdf'])->name('laporan.cetakPdf');
Route::get('/laporan/excel', [PageController::class, 'exportExcel'])->name('laporan.exportExcel');
