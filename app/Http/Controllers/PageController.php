<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Karyawan;
use App\Department;
use App\Jabatan;

class PageController extends Controller
{
    /** ---------------- DASHBOARD ---------------- */
    public function dashboard()
    {
        $karyawan = session('karyawan');
        return view('dashboard', compact('karyawan'));
    }

    /** ---------------- PRESENSI ---------------- */
    public function daftarPresensi()
    {
        return view('daftarPresensi');
    }

    /** ---------------- KARYAWAN ---------------- */
    public function daftarKaryawan(Request $request)
    {
        $query = Karyawan::with(['departement', 'jabatan']);

        if ($request->filled('nama')) {
            $query->where('nama_lengkap', 'like', '%' . $request->nama . '%');
        }

        if ($request->filled('divisi')) {
            $query->whereHas('departement', function ($q) use ($request) {
                $q->where('nama_divisi', $request->divisi);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('NIK', 'like', "%$search%")
                    ->orWhere('nama_lengkap', 'like', "%$search%")
                    ->orWhere('username', 'like', "%$search%")
                    ->orWhere('no_hp', 'like', "%$search%");
            });
        }

        $karyawan = $query->paginate(10)->appends($request->all());
        $departements = Department::all();

        return view('daftarKaryawan', compact('karyawan', 'departements'));
    }

    public function createKaryawan()
    {
        $departements = Department::all();
        $jabatans = Jabatan::all();
        return view('createKaryawan', compact('departements', 'jabatans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'NIK'          => 'required|unique:karyawan,NIK',
            'nama_lengkap' => 'required|string',
            'id_divisi'    => 'required|exists:departement,id_divisi',
            'id_jabatan'   => 'required|exists:jabatan,id_jabatan',
            'username'     => 'required|unique:karyawan,username',
            'password'     => 'required|min:6',
            'no_hp'        => 'required|string',
            'status'       => 'required|string',
        ]);

        Karyawan::create([
            'NIK'          => $request->NIK,
            'nama_lengkap' => $request->nama_lengkap,
            'id_divisi'    => $request->id_divisi,
            'id_jabatan'   => $request->id_jabatan,
            'username'     => $request->username,
            'password'     => Hash::make($request->password),
            'no_hp'        => $request->no_hp,
            'status'       => $request->status,
        ]);

        return redirect()->route('daftar.karyawan')->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function editKaryawan($nik)
    {
        $karyawan = Karyawan::findOrFail($nik);
        $departements = Department::all();
        $jabatans = Jabatan::all();

        return view('editKaryawan', compact('karyawan', 'departements', 'jabatans'));
    }

    public function updateKaryawan(Request $request, $NIK)
    {
        $karyawan = Karyawan::findOrFail($NIK);

        $request->validate([
            'nama_lengkap' => 'required|string',
            'id_divisi'    => 'required|exists:departement,id_divisi',
            'id_jabatan'   => 'required|exists:jabatan,id_jabatan',
            'username'     => 'required|unique:karyawan,username,' . $NIK . ',NIK',
            'no_hp'        => 'required|string',
            'status'       => 'required|string',
        ]);

        $dataUpdate = $request->only(['nama_lengkap', 'username', 'no_hp', 'tgl_lahir', 'alamat', 'id_divisi', 'id_jabatan', 'role', 'status']);

        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('foto', 'public');
            $dataUpdate['foto'] = $fotoPath;
        }

        $karyawan->update($dataUpdate);

        return redirect()->route('daftar.karyawan')->with('success', 'Data karyawan berhasil diupdate.');
    }

    public function deleteKaryawan($nik)
    {
        Karyawan::destroy($nik);
        return redirect()->route('daftar.karyawan')->with('success', 'Karyawan berhasil dihapus.');
    }

    public function showKaryawan($nik)
    {
        $karyawan = Karyawan::with(['departement', 'jabatan'])->findOrFail($nik);
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

        $akun = DB::table('akun')->where('username', $request->username)->first();

        if (!$akun || !Hash::check($request->password, $akun->password)) {
            return back()->withErrors(['login' => 'Username atau password salah.']);
        }

        $karyawan = Karyawan::find($akun->NIK);
        if (!$karyawan) {
            return back()->withErrors(['login' => 'Data karyawan tidak ditemukan.']);
        }

        session([
            'username' => $akun->username,
            'role'     => $karyawan->role,
            'karyawan' => $karyawan,
        ]);

        if ($karyawan->role === 'admin') {
            return redirect()->route('dashboard')->with('success', 'Login berhasil sebagai Admin');
        } else {
            return redirect()->route('karyawan.dashboard')->with('success', 'Login berhasil sebagai Karyawan');
        }
    }

    /** ---------------- REGISTER ---------------- */
    public function showRegister()
    {
        return view('register');
    }

    public function processRegister(Request $request)
    {
        $request->validate([
            'nik'         => 'required|unique:karyawan,NIK',
            'username'    => 'required|unique:akun,username',
            'password'    => 'required|min:6',
            'id_divisi'   => 'required|integer|exists:departement,id_divisi',
            'id_jabatan'  => 'required|integer|exists:jabatan,id_jabatan',
            'nama_lengkap'=> 'required|string',
            'no_hp'       => 'required|string',
            'tgl_lahir'   => 'required|date',
            'alamat'      => 'required|string',
            'role'        => 'required|string',
            'foto'        => 'nullable|image|max:2048',
        ]);

        $fotoName = null;
        if ($request->hasFile('foto')) {
            $fotoName = time() . '.' . $request->foto->extension();
            $request->foto->move(public_path('uploads'), $fotoName);
        }

        Karyawan::create([
            'NIK'          => $request->nik,
            'username'     => $request->username,
            'id_divisi'    => $request->id_divisi,
            'id_jabatan'   => $request->id_jabatan,
            'nama_lengkap' => $request->nama_lengkap,
            'no_hp'        => $request->no_hp,
            'tgl_lahir'    => $request->tgl_lahir,
            'alamat'       => $request->alamat,
            'role'         => $request->role,
            'foto'         => $fotoName,
            'status'       => 'Aktif',
            'password'     => Hash::make($request->password),
        ]);

        DB::table('akun')->insert([
            'username' => $request->username,
            'NIK'      => $request->nik,
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
            ['nik' => '72220535', 'nama' => 'Esra', 'divisi' => 'Keuangan',  'hadir' => 5, 'sakit' => 2, 'cuti' => 2],
            ['nik' => '72220536', 'nama' => 'Rudi', 'divisi' => 'HRD',       'hadir' => 4, 'sakit' => 1, 'cuti' => 3],
            ['nik' => '72220537', 'nama' => 'Sinta','divisi' => 'Marketing', 'hadir' => 6, 'sakit' => 0, 'cuti' => 1],
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
                $rows = $this->data->map(function ($row) {
                    return [$row['nik'],$row['nama'],$row['divisi'],0,$row['hadir'],$row['sakit'],$row['cuti']];
                });
                return $header->merge($rows);
            }
        }, 'laporan.xlsx');
    }

    /** ---------------- LOGOUT ---------------- */
    public function logout(Request $request)
    {
        $request->session()->forget(['user', 'karyawan']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->with('success', 'Berhasil logout.');
    }
}
