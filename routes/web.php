<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PageController;
use App\Http\Controllers\Karyawan\DashboardController as KaryawanDashboardController;
use App\Http\Controllers\Karyawan\KaryawanController;
// use App\Http\Controllers\AbsensiController;


/*
|--------------------------------------------------------------------------
| Forgot & Reset Password
|--------------------------------------------------------------------------
*/
Route::get('/forgot-password', [PageController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [PageController::class, 'sendResetLink'])->name('password.email');

Route::get('/reset-password/{token}', [PageController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [PageController::class, 'resetPassword'])->name('password.update');


/*
|--------------------------------------------------------------------------
| Register
|--------------------------------------------------------------------------
*/
Route::get('/register', [PageController::class, 'showRegister'])->name('register.form');
Route::post('/register', [PageController::class, 'processRegister'])->name('register.store');


/*
|--------------------------------------------------------------------------
| Default Route
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('Login');
});


/*
|--------------------------------------------------------------------------
| Auth (Login & Logout)
|--------------------------------------------------------------------------
*/
Route::middleware('web')->group(function () {
    // Login
    // Login
Route::get('/login', [PageController::class, 'showLogin'])->name('login.form');
Route::post('/login', [PageController::class, 'processLogin'])->name('login.process');


    // Logout
    Route::post('/logout', [PageController::class, 'logout'])->name('logout');
    Route::post('/logout', [PageController::class, 'logoutUser'])->name('logout');
});


/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['role:admin'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [PageController::class, 'dashboard'])->name('dashboard');

    // Presensi Admin
    Route::get('/admin/presensi', [PageController::class, 'showPresensiAdmin'])->name('admin.presensi.form');
    Route::get('/admin/Masuk', [PageController::class, 'formMasuk'])->name('admin.Masuk');
    Route::post('/admin/Masuk', [PageController::class, 'storeMasuk'])->name('admin.storeMasuk');
    Route::get('/admin/Keluar', [PageController::class, 'formKeluar'])->name('admin.Keluar');
    Route::post('/admin/Keluar', [PageController::class, 'storeKeluar'])->name('admin.storeKeluar');

    // Laporan
    Route::get('/laporan', [PageController::class, 'laporan'])->name('laporan');
    Route::post('/laporan/simpan-catatan', [PageController::class, 'simpanCatatan'])->name('laporan.simpanCatatan');
    Route::get('/laporan/cetak-pdf', [PageController::class, 'cetakPdf'])->name('laporan.cetakPdf');
    Route::get('/laporan/export-excel', [PageController::class, 'exportExcel'])->name('laporan.exportExcel');

    // CRUD Karyawan
    Route::get('/daftarKaryawan', [PageController::class, 'daftarKaryawan'])->name('daftar.karyawan');
    Route::get('/karyawan/create', [PageController::class, 'createKaryawan'])->name('karyawan.create');
    Route::post('/karyawan/store', [PageController::class, 'storeKaryawan'])->name('karyawan.store');
    Route::get('/karyawan/{nik}/edit', [PageController::class, 'editKaryawan'])->name('karyawan.edit');
    Route::put('/karyawan/{nik}/update', [PageController::class, 'updateKaryawan'])->name('karyawan.update');
    Route::delete('/karyawan/{nik}', [PageController::class, 'deleteKaryawan'])->name('karyawan.delete');
    Route::get('/karyawan/{nik}/detail', [PageController::class, 'showKaryawan'])->name('karyawan.show');

    // Presensi CRUD
    Route::get('/daftarPresensi', [PageController::class, 'daftarPresensi'])->name('daftarPresensi');
    Route::get('/presensi/{id}/edit', [PageController::class, 'editPresensi'])->name('presensi.edit');
    Route::put('/presensi/{id}', [PageController::class, 'updatePresensi'])->name('presensi.update');
    Route::delete('/presensi/{id}', [PageController::class, 'deletePresensi'])->name('presensi.destroy');

    // Cuti
    Route::get('/cuti', 'PageController@cuti')->name('cuti.index');
    Route::post('/cuti', 'PageController@cutiStore')->name('cuti.store');
    Route::delete('/cuti/{id}', 'PageController@cutiDelete')->name('cuti.delete');

    // Profil
    Route::get('/profil', [PageController::class, 'profil'])->name('profil');
});


/*
|--------------------------------------------------------------------------
| KARYAWAN ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['role:karyawan'])->group(function () {
    // Dashboard Karyawan
    Route::get('/karyawan/dashboard', [PageController::class, 'dashboardKaryawan'])->name('karyawan.dashboard');

    // Presensi Karyawan
    Route::get('/PresensiKaryawan', [PageController::class, 'PresensiKaryawan']);
    Route::get('/riwayat', [PageController::class, 'riwayat'])->name('riwayat.index');

    // Upload Foto
    Route::post('/karyawan/upload-foto', [KaryawanController::class, 'uploadFoto'])->name('karyawan.uploadFoto');

    // Absensi Masuk & Keluar
    Route::get('/absensi/masuk', [PageController::class, 'showFormMasuk'])->name('absensi.formMasuk');
    Route::post('/absensi/masuk', [PageController::class, 'presensiMasuk'])->name('absensi.masuk');
    Route::get('/absensi/keluar', [PageController::class, 'showFormKeluar'])->name('absensi.formKeluar');
    Route::post('/absensi/keluar', [PageController::class, 'presensiKeluar'])->name('absensi.keluar');

    Route::get('/pengajuan-sakit', [PageController::class, 'formSakit'])->name('presensi.formSakit');
    Route::post('/pengajuan-sakit', [PageController::class, 'storeSakit'])->name('presensi.storeSakit');

    // form input tracking
    Route::get('/tracking-sales', [PageController::class, 'trackingSalesForm'])->name('tracking.form');

    // simpan data
    Route::post('/tracking-sales', [PageController::class, 'trackingSalesStore'])->name('tracking.store');

});


