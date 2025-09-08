<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PageController;
use App\Http\Controllers\Karyawan\DashboardController as KaryawanDashboardController;
use App\Http\Controllers\Karyawan\KaryawanController;


Route::get('/', function () {
    return view('Login');
});

// Login
Route::get('/Login', [PageController::class, 'Login'])->name('Login');
Route::post('/Login', [PageController::class, 'Login'])->name('Login');

// Dashboard & halaman utama
Route::get('/dashboard', [PageController::class, 'dashboard']);
Route::get('/daftarPresensi', [PageController::class, 'daftarPresensi']);
Route::get('/daftarKaryawan', [PageController::class, 'daftarKaryawan']);
Route::get('/laporan', [PageController::class, 'laporan'])->name('laporan');

// simpan catatan laporan (POST)
Route::post('/laporan/simpan-catatan', [PageController::class, 'simpanCatatan'])->name('laporan.simpanCatatan');

// Presensi Karyawan
Route::get('/PresensiKaryawan', [PageController::class, 'PresensiKaryawan']);

// Karyawan
Route::post('/karyawan/upload-foto', [KaryawanController::class, 'uploadFoto'])
    ->name('karyawan.uploadFoto');


// Dashboard karyawan
Route::get('/karyawan/dashboard', function () {
    return view('karyawan.dashboard');
})->name('karyawan.dashboard');

// Absensi masuk & keluar
Route::get('/absensi/masuk', function () {
    return view('absensi.masuk');
})->name('absensi.masuk');

Route::get('/absensi/keluar', function () {
    return view('absensi.keluar');
})->name('absensi.keluar');

// Laporan (PDF & Excel)
Route::get('/laporan/pdf', [PageController::class, 'cetakPdf'])->name('laporan.cetakPdf');
Route::get('/laporan/excel', [PageController::class, 'exportExcel'])->name('laporan.exportExcel');
