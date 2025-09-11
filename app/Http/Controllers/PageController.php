<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Karyawan;
use App\Akun;
use App\Department;
use App\Jabatan;
use App\Absensi;


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
    $karyawan = Karyawan::paginate(10); // ambil 10 data per halaman
    return view('daftarKaryawan', compact('karyawan'));
}

    public function createKaryawan()
{
    return view('createKaryawan');
}

public function store(Request $request)
{
    DB::table('karyawan')->insert([
        'NIK' => $request->NIK,
        'nama_lengkap' => $request->nama_lengkap,
        'id_divisi' => $request->id_divisi,
        'username' => $request->username,
        'password' => Hash::make($request->password), // 🔑 bcrypt
        'no_hp' => $request->no_hp,
        'status' => $request->status,
    ]);

    return redirect('/daftarKaryawan')->with('success', 'Karyawan berhasil ditambahkan.');
}

public function editKaryawan($nik)
{
    $karyawan = DB::table('karyawan')->where('NIK', $nik)->first();
    $departements = DB::table('departement')->get();
    $jabatans = DB::table('jabatan')->get();

    return view('editKaryawan', compact('karyawan', 'departements', 'jabatans'));
}


public function updateKaryawan(Request $request, $NIK)
{
    // Ambil data karyawan
    $karyawan = DB::table('karyawan')->where('NIK', $NIK)->first();

    // Siapkan data update selain foto
    $dataUpdate = [
        'username'     => $request->username,
        'no_hp'        => $request->no_hp,
        'tgl_lahir'    => $request->tgl_lahir,
        'alamat'       => $request->alamat,
        'id_divisi'    => $request->id_divisi,
        'id_jabatan'   => $request->id_jabatan,
        'role'         => $request->role,
        'status'       => $request->status,
    ];

    // Jika ada upload foto baru
    if ($request->hasFile('foto')) {
        $fotoPath = $request->file('foto')->store('foto', 'public');

        // Simpan path ke database
        $dataUpdate['foto'] = $fotoPath;
    }

    // Update ke database
    DB::table('karyawan')->where('NIK', $NIK)->update($dataUpdate);

    return redirect('/daftarKaryawan')->with('success', 'Data karyawan berhasil diupdate.');
}



public function deleteKaryawan($nik)
{
    DB::table('karyawan')->where('NIK', $nik)->delete();
    return redirect('/daftarKaryawan')->with('success', 'Karyawan berhasil dihapus.');
}

public function showKaryawan($nik)
{
    $karyawan = DB::table('karyawan')
        ->leftJoin('departement', 'karyawan.id_divisi', '=', 'departement.id_divisi')
        ->leftJoin('jabatan', 'karyawan.id_jabatan', '=', 'jabatan.id_jabatan')
        ->select('karyawan.*', 'departement.nama_divisi', 'jabatan.nama_jabatan')
        ->where('karyawan.NIK', $nik)
        ->first();

    return view('showKaryawan', compact('karyawan'));
}



    public function laporan()
    {
        return view('laporan');
    }

    /** ---------------- LOGIN ---------------- */
    public function showLogin()
    {
        return view('login');
    }

    public function processLogin(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $akun = DB::table('akun')->where('username', $request->username)->first();

        if ($akun && Hash::check($request->password, $akun->password)) {
            session(['user' => $akun->username]);

            return redirect()->route('karyawan.dashboard');
        }

        return back()->withErrors([
            'login' => 'Username atau password salah!',
        ]);
    }

    /** ---------------- REGISTER ---------------- */
    public function showRegister()
    {
        return view('register'); // bikin view register.blade.php
    }

    public function processRegister(Request $request)
    {
        $request->validate([
            'nik'          => 'required|unique:karyawan,NIK',
            'username'     => 'required|unique:akun,username',
            'password'     => 'required|min:6',
            'id_divisi'    => 'required|integer',
            'id_jabatan'   => 'required|integer',
            'divisi'       => 'required|string',
            'nama_lengkap' => 'required|string',
            'no_hp'        => 'required|string',
            'tgl_lahir'    => 'required|date',
            'alamat'       => 'required|string',
            'role'         => 'required|string',
            'foto'         => 'nullable|image|max:2048',
        ]);

        // Simpan ke tabel akun
        DB::table('akun')->insert([
            'username' => $request->username,
            'NIK'      => $request->nik,
            'password' => Hash::make($request->password),
        ]);

        // Upload foto (jika ada)
        $fotoName = null;
        if ($request->hasFile('foto')) {
            $fotoName = time() . '.' . $request->foto->extension();
            $request->foto->move(public_path('uploads'), $fotoName);
        }

        // Simpan ke tabel karyawan
        DB::table('karyawan')->insert([
            'NIK'          => $request->nik,
            'username'     => $request->username,
            'id_divisi'    => $request->id_divisi,
            'id_jabatan'   => $request->id_jabatan,
            'divisi'       => $request->divisi,
            'nama_lengkap' => $request->nama_lengkap,
            'no_hp'        => $request->no_hp,
            'tgl_lahir'    => $request->tgl_lahir,
            'alamat'       => $request->alamat,
            'role'         => $request->role,
            'foto'         => $fotoName,
        ]);

        // ✅ Set session user agar langsung login
        session(['user' => $request->username]);

        // ✅ Redirect langsung ke dashboard karyawan
        return redirect()->route('karyawan.dashboard')
            ->with('success', 'Registrasi berhasil! Selamat datang, ' . $request->nama_lengkap);
    }

    /** ---------------- PRESENSI ---------------- */
    public function PresensiKaryawan()
    {
        return view('PresensiKaryawan');
    }

    private function getData()
    {
        return collect([
            ['nik' => '72220535', 'nama' => 'Esra', 'divisi' => 'Keuangan', 'hadir' => 5, 'sakit' => 2, 'cuti' => 2],
            ['nik' => '72220536', 'nama' => 'Rudi', 'divisi' => 'HRD', 'hadir' => 4, 'sakit' => 1, 'cuti' => 3],
            ['nik' => '72220537', 'nama' => 'Sinta', 'divisi' => 'Marketing', 'hadir' => 6, 'sakit' => 0, 'cuti' => 1],
        ]);
    }

    /** ---------------- CETAK PDF ---------------- */
    public function cetakPdf()
    {
        $data = $this->getData();
        $pdf = PDF::loadView('laporan_pdf', ['data' => $data]);
        return $pdf->download('laporan.pdf');
    }

    /** ---------------- EXPORT EXCEL ---------------- */
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
                        0,
                        $row['hadir'], $row['sakit'], $row['cuti']
                    ];
                });
                return $header->merge($rows);
            }
        }, 'laporan.xlsx');
    }

    /** ---------------- LOGOUT ---------------- */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Berhasil logout.');
    }
}
