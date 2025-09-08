<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KaryawanController extends Controller
{
    public function uploadFoto(Request $request)
    {
        $request->validate([
            'fotoBase64' => 'required|string',
        ]);

        $user = (object) ['foto' => 'img/profile.png']; // dummy user
        $folder = public_path('img');

        // Hapus foto lama jika ada dan bukan default
        if(!empty($user->foto) && file_exists(public_path($user->foto))){
            unlink(public_path($user->foto));
        }

        // Decode base64 dan simpan file baru
        $data = $request->fotoBase64;
        list($type, $data) = explode(';', $data);
        list(, $data) = explode(',', $data);
        $data = base64_decode($data);

        $namaFile = 'profile.png'; // selalu timpa file lama
        file_put_contents($folder.'/'.$namaFile, $data);

        return back()->with('success', 'Foto berhasil diupdate!');
    }
}
