<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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

    public function showLogin()
    {
        return view('Login');
    }

    public function processLogin(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Cek user di tabel akun
        $akun = DB::table('akun')->where('username', $request->username)->first();

        if ($akun && Hash::check($request->password, $akun->password)) {
            // simpan session
            session(['user' => $akun->username]);

            // redirect ke dashboard karyawan
            return redirect()->route('karyawan.dashboard');
        }

        return back()->withErrors([
            'login' => 'Username atau password salah!',
        ]);
    }

    public function PresensiKaryawan()
    {
        return view('PresensiKaryawan');
    }

    // Data dummy (nanti ganti query DB)
    private function getData()
    {
        return collect([
            ['nik' => '72220535', 'nama' => 'Esra', 'divisi' => 'Keuangan', 'hadir' => 5, 'sakit' => 2, 'cuti' => 2],
            ['nik' => '72220536', 'nama' => 'Rudi', 'divisi' => 'HRD', 'hadir' => 4, 'sakit' => 1, 'cuti' => 3],
            ['nik' => '72220537', 'nama' => 'Sinta', 'divisi' => 'Marketing', 'hadir' => 6, 'sakit' => 0, 'cuti' => 1],
        ]);
    }

    // Cetak PDF
    public function cetakPdf()
{
    $data = $this->getData();
    $pdf = PDF::loadView('laporan_pdf', ['data' => $data]);
    return $pdf->download('laporan.pdf');
}

    // Export Excel
    public function exportExcel()
    {
        $data = $this->getData();

        return Excel::download(new class($data) implements \Maatwebsite\Excel\Concerns\FromCollection {
            private $data;
            public function __construct($data) { $this->data = $data; }
            public function collection()
            {
                $header = collect([['NIK','Nama','Divisi','Total Hari Kerja','Jumlah Hadir','Jumlah Sakit','Jumlah Cuti']]);
                $rows = $this->data->map(function($row){
                    return [
                        $row['nik'], $row['nama'], $row['divisi'],
                        0, // total hari kerja
                        $row['hadir'], $row['sakit'], $row['cuti']
                    ];
                });
                return $header->merge($rows);
            }
        }, 'laporan.xlsx');
    }
}
