<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PageController;
use App\Http\Controllers\Karyawan\DashboardController as KaryawanDashboardController;
use App\Http\Controllers\Karyawan\KaryawanController;
// use App\Http\Controllers\AbsensiController;


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


// Presensi Karyawan
Route::get('/PresensiKaryawan', [PageController::class, 'PresensiKaryawan']);

// Karyawan
Route::post('/karyawan/upload-foto', [KaryawanController::class, 'uploadFoto'])
    ->name('karyawan.uploadFoto');


// Dashboard karyawan
Route::get('/karyawan/dashboard', [PageController::class, 'dashboardKaryawan'])
    ->name('karyawan.dashboard');

// Dashboard admin
Route::get('/dashboard', [PageController::class, 'dashboard'])->name('dashboard');
// Presensi Admin
Route::get('/admin/presensi', [PageController::class, 'showPresensiAdmin'])->name('admin.presensi.form');
Route::post('/admin/presensi', [PageController::class, 'submitPresensiAdmin'])->name('admin.presensi.submit');
// Halaman form absensi masuk
Route::get('/absensi/masuk', [PageController::class, 'masuk'])->name('absensi.masuk');
// Simpan absen masuk
Route::post('/absensi/masuk', [PageController::class, 'storeMasuk'])->name('absensi.storeMasuk');

// Halaman form absensi keluar
Route::get('/absensi/keluar', [PageController::class, 'keluar'])->name('absensi.keluar');
// Simpan absen keluar
Route::post('/absensi/keluar', [PageController::class, 'storeKeluar'])->name('absensi.storeKeluar');

// Karyawan
// Absensi masuk & keluar
Route::get('/absensi/masuk', function () {
    return view('absensi.masuk');
})->name('absensi.masuk');

// Form absen masuk & keluar

// Form Absen Masuk
Route::get('/absensi/masuk', [PageController::class, 'showFormMasuk'])->name('absensi.formMasuk');
Route::post('/absensi/masuk', [PageController::class, 'presensiMasuk'])->name('absensi.masuk');

// Form Absen Keluar
Route::get('/absensi/keluar', [PageController::class, 'showFormKeluar'])->name('absensi.formKeluar');
Route::post('/absensi/keluar', [PageController::class, 'presensiKeluar'])->name('absensi.keluar');

// GET untuk halaman laporan
Route::get('/laporan', [PageController::class, 'laporan'])->name('laporan');

// POST untuk simpan catatan
Route::post('/laporan/simpan-catatan', [PageController::class, 'simpanCatatan'])->name('laporan.simpanCatatan');

// (opsional kalau ada)
Route::get('/laporan/cetak-pdf', [PageController::class, 'cetakPdf'])->name('laporan.cetakPdf');
Route::get('/laporan/export-excel', [PageController::class, 'exportExcel'])->name('laporan.exportExcel');


// CRUD Karyawan (admin)
Route::get('/karyawan/create', [PageController::class, 'createKaryawan'])->name('karyawan.create');
Route::post('/karyawan/store', [PageController::class, 'storeKaryawan'])->name('karyawan.store');

Route::get('/karyawan/{nik}/edit', [PageController::class, 'editKaryawan'])->name('karyawan.edit');
Route::put('/karyawan/{nik}/update', [PageController::class, 'updateKaryawan'])->name('karyawan.update');

Route::delete('/karyawan/{nik}', [PageController::class, 'deleteKaryawan'])->name('karyawan.delete');

Route::get('/karyawan/{nik}/detail', [PageController::class, 'showKaryawan'])->name('karyawan.show');
Route::get('/profil', [PageController::class, 'profil'])->name('profil');

Route::get('/profil', [PageController::class, 'profil'])->name('profil');

// Edit Presensi
Route::get('/presensi/{id}/edit', [PageController::class, 'editPresensi'])->name('presensi.edit');

// Hapus Presensi
Route::delete('/presensi/{id}', [PageController::class, 'deletePresensi'])->name('presensi.destroy');

// Edit Presensi sudah ada GET untuk modal, tambahkan PUT untuk update
Route::put('/presensi/{id}', [PageController::class, 'updatePresensi'])->name('presensi.update');

Route::resource('presensi', PageController::class);

// ganti baris lama (tanpa ->name(...)) jadi seperti ini:
Route::get('/daftarPresensi', [PageController::class, 'daftarPresensi'])->name('daftarPresensi');

//
