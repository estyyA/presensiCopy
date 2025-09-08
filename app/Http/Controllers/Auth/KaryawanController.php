<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KaryawanController extends Controller
{
    public function uploadFoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Simpan ke folder public/image
        $namaFile = time() . '.' . $request->foto->extension();
        $request->foto->move(public_path('image'), $namaFile);

        // Simpan nama file ke database (contoh pakai auth user)
        // auth()->user()->update(['foto' => $namaFile]);

        return redirect()->back()->with('success', 'Foto berhasil diupload!');
    }
}
