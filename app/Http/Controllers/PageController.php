<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PageController extends Controller
{
    /** ---------------- DASHBOARD ---------------- */
    public function dashboard()
    {
        // Ambil data karyawan dari session
        $karyawan = session('karyawan');
        return view('dashboard', compact('karyawan'));
    }

    /** ---------------- KARYAWAN ---------------- */
    public function daftarPresensi()
    {
        return view('daftarPresensi');
    }

    public function daftarKaryawan()
    {
        $karyawan = DB::table('karyawan')->paginate(10);
        return view('daftarKaryawan', compact('karyawan'));
    }

    public function createKaryawan()
    {
        return view('createKaryawan');
    }

    public function store(Request $request)
    {
        DB::table('karyawan')->insert([
            'NIK'          => $request->NIK,
            'nama_lengkap' => $request->nama_lengkap,
            'id_divisi'    => $request->id_divisi,
            'username'     => $request->username,
            'password'     => Hash::make($request->password),
            'no_hp'        => $request->no_hp,
            'status'       => $request->status,
        ]);

        return redirect('/daftarKaryawan')->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function editKaryawan($NIK)
    {
        $karyawan     = DB::table('karyawan')->where('NIK', $NIK)->first();
        $departements = DB::table('departement')->get();
        $jabatans     = DB::table('jabatan')->get();

        return view('editKaryawan', compact('karyawan', 'departements', 'jabatans'));
    }

    public function updateKaryawan(Request $request, $NIK)
    {
        $dataUpdate = [
            'username'   => $request->username,
            'no_hp'      => $request->no_hp,
            'tgl_lahir'  => $request->tgl_lahir,
            'alamat'     => $request->alamat,
            'id_divisi'  => $request->id_divisi,
            'id_jabatan' => $request->id_jabatan,
            'role'       => $request->role,
            'status'     => $request->status,
        ];

        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('foto', 'public');
            $dataUpdate['foto'] = $fotoPath;
        }

        DB::table('karyawan')->where('NIK', $NIK)->update($dataUpdate);

        return redirect('/daftarKaryawan')->with('success', 'Data karyawan berhasil diupdate.');
    }

    public function deleteKaryawan($NIK)
    {
        DB::table('karyawan')->where('NIK', $NIK)->delete();
        return redirect('/daftarKaryawan')->with('success', 'Karyawan berhasil dihapus.');
    }

    public function showKaryawan($NIK)
    {
        $karyawan = DB::table('karyawan')
            ->leftJoin('departement', 'karyawan.id_divisi', '=', 'departement.id_divisi')
            ->leftJoin('jabatan', 'karyawan.id_jabatan', '=', 'jabatan.id_jabatan')
            ->select('karyawan.*', 'departement.nama_divisi', 'jabatan.nama_jabatan')
            ->where('karyawan.NIK', $NIK)
            ->first();

        return view('showKaryawan', compact('karyawan'));
    }

    /** ---------------- LAPORAN ---------------- */
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

        // Cari akun
        $akun = DB::table('akun')
            ->where('username', $request->username)
            ->first();

        if (!$akun || !Hash::check($request->password, $akun->password)) {
            return back()->withErrors(['login' => 'Username atau password salah.']);
        }

        // Ambil data karyawan
        $karyawan = DB::table('karyawan')
            ->where('NIK', $akun->NIK)
            ->first();

        if (!$karyawan) {
            return back()->withErrors(['login' => 'Data karyawan tidak ditemukan.']);
        }

        // Simpan ke session
        session([
            'username' => $akun->username,
            'role'     => $karyawan->role,
            'karyawan' => $karyawan,
        ]);

        // Redirect sesuai role
        if ($karyawan->role === 'admin') {
            return redirect()->route('dashboard')->with('success', 'Login berhasil sebagai Admin');
        }

        return redirect()->route('karyawan.dashboard')->with('success', 'Login berhasil sebagai Karyawan');
    }

    /** ---------------- REGISTER ---------------- */
    public function showRegister()
    {
        return view('register');
    }

    public function processRegister(Request $request)
    {
        $request->validate([
            'NIK'          => 'required|unique:karyawan,NIK',
            'username'     => 'required|unique:akun,username',
            'password'     => 'required|min:6',
            'id_divisi'    => 'required|integer',
            'id_jabatan'   => 'required|integer',
            'nama_lengkap' => 'required|string',
            'no_hp'        => 'required|string',
            'tgl_lahir'    => 'required|date',
            'alamat'       => 'required|string',
            'role'         => 'required|string',
            'foto'         => 'nullable|image|max:2048',
        ]);

        $fotoName = null;
        if ($request->hasFile('foto')) {
            $fotoName = time() . '.' . $request->foto->extension();
            $request->foto->move(public_path('uploads'), $fotoName);
        }

        DB::table('karyawan')->insert([
            'NIK'          => $request->NIK,
            'username'     => $request->username,
            'id_divisi'    => $request->id_divisi,
            'id_jabatan'   => $request->id_jabatan,
            'nama_lengkap' => $request->nama_lengkap,
            'no_hp'        => $request->no_hp,
            'tgl_lahir'    => $request->tgl_lahir,
            'alamat'       => $request->alamat,
            'role'         => $request->role,
            'foto'         => $fotoName,
        ]);

        DB::table('akun')->insert([
            'username' => $request->username,
            'NIK'      => $request->NIK,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('login.form')->with('success', 'Registrasi berhasil! Silakan login.');
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

    public function cetakPdf()
    {
        $data = $this->getData();
        $pdf  = PDF::loadView('laporan_pdf', ['data' => $data]);
        return $pdf->download('laporan.pdf');
    }

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
        $request->session()->forget(['username','role','karyawan']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Berhasil logout.');
    }

    // public function karyawanDashboard()
    // {
    //     // Ambil data karyawan dari session
    //     $karyawan = session('karyawan');

    //     if (!$karyawan) {
    //         // Kalau belum login, arahkan ke halaman login
    //         return redirect()->route('login')->with('error', 'Silakan login dulu.');
    //     }

    //     // Ambil data absensi milik karyawan ini dari tabel absensi
    //     $absensi = DB::table('absensi')
    //         ->where('NIK', $karyawan->NIK)   // sesuaikan nama kolom NIK di database Anda
    //         ->orderBy('tanggal', 'desc')
    //         ->get();

    //     // Kirim data ke view
    //     return view('karyawan.dashboard', compact('karyawan', 'absensi'));
    // }
}
