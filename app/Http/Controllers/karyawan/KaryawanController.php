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

    $karyawan = session('karyawan'); // ambil dari session
    if (!$karyawan) {
        return back()->withErrors(['auth' => 'User tidak ditemukan, silakan login ulang.']);
    }

    // Decode base64
    $data = $request->fotoBase64;
    list($type, $data) = explode(';', $data);
    list(, $data) = explode(',', $data);
    $data = base64_decode($data);

    // Simpan dengan nama unik berdasarkan NIK
    $namaFile = 'foto_' . $karyawan->NIK . '_' . time() . '.png';
    $folder = public_path('uploads');
    if (!file_exists($folder)) {
        mkdir($folder, 0777, true);
    }

    file_put_contents($folder . '/' . $namaFile, $data);

    // Update database
    \DB::table('karyawan')
        ->where('NIK', $karyawan->NIK)
        ->update(['foto' => $namaFile]);

    // Refresh session
    $karyawanBaru = \DB::table('karyawan')->where('NIK', $karyawan->NIK)->first();
    session(['karyawan' => $karyawanBaru]);

    return back()->with('success', 'Foto berhasil diperbarui!');
}

}
