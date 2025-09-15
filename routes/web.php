<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PageController;
use App\Http\Controllers\Karyawan\KaryawanController; // dipakai untuk upload-foto

// Home / Login / Register
Route::get('/', function () { return view('Login'); });

Route::get('/register', [PageController::class, 'showRegister'])->name('register.form');
Route::post('/register', [PageController::class, 'processRegister'])->name('register.store');

Route::get('/login', [PageController::class, 'showLogin'])->name('login.form');
Route::post('/login', [PageController::class, 'processLogin'])->name('login.process');
Route::post('/logout', [PageController::class, 'logout'])->name('logout');

// Dashboard
Route::get('/dashboard', [PageController::class, 'dashboard'])->name('dashboard');
Route::get('/karyawan/dashboard', function () { return view('karyawan.dashboard'); })->name('karyawan.dashboard');

// Presensi
Route::get('/daftarPresensi', [PageController::class, 'daftarPresensi'])->name('daftar.presensi');
Route::get('/PresensiKaryawan', [PageController::class, 'PresensiKaryawan'])->name('presensi.karyawan');

// Absensi (ganti fn() => ke function(){} biar kompatibel)
Route::get('/absensi/masuk', function () { return view('absensi.masuk'); })->name('absensi.masuk');
Route::get('/absensi/keluar', function () { return view('absensi.keluar'); })->name('absensi.keluar');

// Daftar Karyawan (-> sesuai method di PageController)
Route::get('/daftarKaryawan', [PageController::class, 'daftarKaryawan'])->name('daftar.karyawan');

// CRUD Karyawan (gunakan nama method yang ada di PageController)
Route::get('/karyawan/create', [PageController::class, 'createKaryawan'])->name('karyawan.create');
Route::post('/karyawan/store', [PageController::class, 'store'])->name('karyawan.store');

Route::get('/karyawan/{nik}/detail', [PageController::class, 'showKaryawan'])->name('karyawan.show');
Route::get('/karyawan/{nik}/edit', [PageController::class, 'editKaryawan'])->name('karyawan.edit');
Route::put('/karyawan/{nik}/update', [PageController::class, 'updateKaryawan'])->name('karyawan.update');
Route::delete('/karyawan/{nik}', [PageController::class, 'deleteKaryawan'])->name('karyawan.delete');

// Upload foto (controller terpisah)
Route::post('/karyawan/upload-foto', [KaryawanController::class, 'uploadFoto'])->name('karyawan.uploadFoto');

// Laporan
Route::get('/laporan', [PageController::class, 'laporan'])->name('laporan');
Route::get('/laporan/pdf', [PageController::class, 'cetakPdf'])->name('laporan.cetakPdf');
Route::get('/laporan/excel', [PageController::class, 'exportExcel'])->name('laporan.exportExcel');
Route::post('/laporan/simpan-catatan', [PageController::class, 'simpanCatatan'])->name('laporan.simpanCatatan');
