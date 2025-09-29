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

    $karyawan = session('karyawan');
    if (!$karyawan) {
        return back()->withErrors(['auth' => 'User tidak ditemukan, silakan login ulang.']);
    }

    // Decode base64
    $data = $request->fotoBase64;
    list($type, $data) = explode(';', $data);
    list(, $data) = explode(',', $data);
    $data = base64_decode($data);

    // Simpan dengan nama unik
    $namaFile = 'foto_' . $karyawan->NIK . '_' . time() . '.png';
    $path = storage_path('app/public/foto');
    if (!file_exists($path)) {
        mkdir($path, 0777, true);
    }

    file_put_contents($path . '/' . $namaFile, $data);

    // Update database → simpan RELATIVE PATH
    \DB::table('karyawan')
        ->where('NIK', $karyawan->NIK)
        ->update(['foto' => 'foto/' . $namaFile]);

    // Refresh session (join lengkap)
    $karyawanBaru = \DB::table('karyawan')
        ->leftJoin('departement', 'karyawan.id_divisi', '=', 'departement.id_divisi')
        ->leftJoin('jabatan', 'karyawan.id_jabatan', '=', 'jabatan.id_jabatan')
        ->where('karyawan.NIK', $karyawan->NIK)
        ->select(
            'karyawan.*',
            'departement.nama_divisi',
            'jabatan.nama_jabatan'
        )
        ->first();

    session(['karyawan' => $karyawanBaru]);

    return back()->with('success', 'Foto berhasil diperbarui!');
}


}
