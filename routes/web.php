<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PageController;
use App\Http\Controllers\Karyawan\DashboardController as KaryawanDashboardController;
use App\Http\Controllers\Karyawan\KaryawanController;


//Forgot Password
// Forgot Password
Route::get('/forgot-password', [PageController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [PageController::class, 'sendResetLink'])->name('password.email');

// Reset Password
Route::get('/reset-password/{token}', [PageController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [PageController::class, 'resetPassword'])->name('password.update');




// Form Registrasi
Route::get('/register', [PageController::class, 'showRegister'])->name('register.form');

// Proses Registrasi
Route::post('/register', [PageController::class, 'processRegister'])->name('register.store');




Route::get('/', function () {
    return view('Login');
});
Route::middleware('web')->group(function () {
// Login
Route::get('/login', [PageController::class, 'showLogin'])->name('login.form');
Route::post('/login', [PageController::class, 'processLogin'])->name('login.process');

//Logout
Route::post('/logout', [PageController::class, 'logout'])->name('logout');
Route::post('/logout', [PageController::class, 'logoutUser'])->name('logout');



// Dashboard & halaman utama
Route::get('/dashboard', [PageController::class, 'dashboard']);
});
Route::get('/daftarPresensi', [PageController::class, 'daftarPresensi']);
Route::get('/daftarKaryawan', [PageController::class, 'daftarKaryawan'])->name('daftar.karyawan');
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

// Dashboard admin
Route::get('/dashboard', [PageController::class, 'dashboard'])->name('dashboard');


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

// CRUD Karyawan (admin)
Route::get('/karyawan/create', [PageController::class, 'createKaryawan'])->name('karyawan.create');
Route::post('/karyawan/store', [PageController::class, 'storeKaryawan'])->name('karyawan.store');

Route::get('/karyawan/{nik}/edit', [PageController::class, 'editKaryawan'])->name('karyawan.edit');
Route::put('/karyawan/{nik}/update', [PageController::class, 'updateKaryawan'])->name('karyawan.update');

Route::delete('/karyawan/{nik}', [PageController::class, 'deleteKaryawan'])->name('karyawan.delete');

Route::get('/karyawan/{nik}/detail', [PageController::class, 'showKaryawan'])->name('karyawan.show');

Route::get('/profil', [App\Http\Controllers\PageController::class, 'profil'])->name('profil');


