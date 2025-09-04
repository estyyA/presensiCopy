<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;


class PageController extends Controller
{
    

    public function dashboard()
    {
        return view('dashboard');
    }

    
    public function daftarPresensi()
    {
        return view('daftarPresensi');
    }

    
    public function daftarKaryawan()
    {
        return view('daftarKaryawan');
    }

     public function laporan()
    {
        return view('laporan');
    }

    public function registerAdminform()
    {
        return view('registerAdmin');
    }
    public function registerAdmin(Request $request)
    {
        // validasi input
        $request->validate([
            'fullname' => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // simpan ke database
        User::create([
            'name'     => $request->fullname,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return redirect()->route('login.admin')->with('success', 'Akun berhasil dibuat, silakan login.');
    }


    public function loginAdmin()
    {
        return view('loginAdmin');
    }

    public function PresensiKaryawan()
    {
        return view('PresensiKaryawan');
    }



}
