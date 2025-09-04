<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PageController;

Route::get('/', function () {
    return view('registerAdmin');
});

//bagian register&login admin
Route::get('/registeradmin', [PageController::class, 'registerAdminform'])->name('registeradmin');
Route::post('/registeradmin', [PageController::class, 'registerAdmin'])->name('register.admin');


// tampilkan form login karyawan
Route::get('/loginadmin', [PageController::class, 'loginAdmin'])->name('login.admin.form');

// proses login karyawan (sementara kosong dulu)
Route::post('/loginadmin', [PageController::class, 'login'])->name('login.admin');

// arahkan ke PageController bagian dashboard&lain-lain
Route::get('/dashboard', [PageController::class, 'dashboard']);
Route::get('/daftarPresensi', [PageController::class, 'daftarPresensi']);
Route::get('/daftarKaryawan', [PageController::class, 'daftarKaryawan']);
Route::get('/laporan', [PageController::class, 'laporan']);