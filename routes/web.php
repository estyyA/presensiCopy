<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PageController;

Route::get('/', function () {
    return view('Login');
});

//bagian register&login admin
//Route::get('/registeradmin', [PageController::class, 'registerAdminform'])->name('registeradmin');
//Route::post('/registeradmin', [PageController::class, 'registerAdmin'])->name('register.admin');


// tampilkan form login karyawan
Route::get('/Login', [PageController::class, 'Login'])->name('Login');

// proses login karyawan (sementara kosong dulu)
Route::post('/Login', [PageController::class, 'Login'])->name('Login');

// arahkan ke PageController bagian dashboard&lain-lain
Route::get('/dashboard', [PageController::class, 'dashboard']);
Route::get('/daftarPresensi', [PageController::class, 'daftarPresensi']);
Route::get('/daftarKaryawan', [PageController::class, 'daftarKaryawan']);
Route::get('/laporan', [PageController::class, 'laporan']);


// untuk presensi karyawan
Route::get('/PresensiKaryawan', [PageController::class, 'PresensiKaryawan']);