<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi; // pastikan model Absensi sudah dibuat

class AbsensiController extends Controller
{
    /**
     * Tampilkan halaman absen masuk
     */
    public function masuk()
    {
        return view('absensi.masuk');
    }

    /**
     * Proses simpan absen masuk
     */
    public function storeMasuk(Request $request)
{
    $request->validate([
        'jam_masuk' => 'required',
    ]);

    Absensi::create([
        'user_id'    => auth()->id(),
        'jam_masuk'  => $request->jam_masuk,
        'keterangan' => $request->keterangan,
    ]);

    // Redirect ke dashboard karyawan
    return redirect()->route('karyawan.dashboard')
                     ->with('success', 'Berhasil absen masuk!');
}

    /**
     * Tampilkan halaman absen keluar
     */
    public function keluar()
    {
        return view('absensi.keluar');
    }

    /**
     * Proses simpan absen keluar
     */
    public function storeKeluar(Request $request)
    {
        $request->validate([
            'jam_keluar' => 'required',
        ]);

        $absensi = Absensi::where('user_id', auth()->id())
                          ->whereDate('created_at', now()->toDateString())
                          ->first();

        if ($absensi) {
            $absensi->update([
                'jam_keluar' => $request->jam_keluar,
                'catatan'    => $request->catatan,
            ]);
        } else {
            Absensi::create([
                'user_id'    => auth()->id(),
                'jam_keluar' => $request->jam_keluar,
                'catatan'    => $request->catatan,
            ]);
        }

        // Redirect ke dashboard karyawan
        return redirect()->route('karyawan.dashboard')
                         ->with('success', 'Berhasil absen keluar!');
    }
}
