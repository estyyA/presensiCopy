<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // contoh data dummy
        $nama_karyawan = "Budi Santoso";

        return view('karyawan.dashboard', compact('nama_karyawan'));
    }
}
