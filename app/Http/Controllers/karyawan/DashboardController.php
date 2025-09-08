<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
   public function index()
    {
        // Dummy user untuk testing
        $user = (object) [
            'nama' => 'I Made Sugi Hantara',
            'nip'  => '72220562',
            'jabatan' => 'Senior UX Designer',
            'foto' => file_exists(public_path('img/profile.png')) ? 'img/profile.png' : 'img/default.png',
        ];

        return view('karyawan.dashboard', compact('user'));
    }
}
