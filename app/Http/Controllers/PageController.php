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
    /** ---------------- DASHBOARD ---------------- */
    public function dashboard()
    {
        // Ambil data karyawan dari session
        $karyawan = session('karyawan');
        return view('dashboard', compact('karyawan'));
    }

    /** ---------------- KARYAWAN ---------------- */
    public function daftarPresensi(Request $request)
    {
        $query = DB::table('presensi')
            ->join('karyawan', 'presensi.nik', '=', 'karyawan.NIK')
            ->leftJoin('departement', 'karyawan.id_divisi', '=', 'departement.id_divisi')
            ->select(
                'presensi.id',
                'presensi.tanggal',
                'presensi.jam_masuk',
                'presensi.jam_pulang',
                'presensi.status',
                'karyawan.NIK',
                'karyawan.nama_lengkap',
                'departement.nama_divisi'
            );

        // Nama (partial match)
        if ($request->filled('nama')) {
            $query->where('karyawan.nama_lengkap', 'like', '%' . $request->nama . '%');
        }

        // Filter Divisi (by id)
        if ($request->filled('divisi')) {
            $query->where('departement.id_divisi', $request->divisi);
        }

        // Filter Tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('presensi.tanggal', $request->tanggal);
        }

        // Urut terbaru dulu, paginate, dan pertahankan query string (Laravel 6 pakai appends)
        $presensis = $query->orderBy('presensi.tanggal', 'desc')->paginate(10)->appends($request->all());

        // Hanya kirim departement untuk dropdown divisi — jangan kirim karyawanList
        $departements = DB::table('departement')->select('id_divisi', 'nama_divisi')->get();

        return view('daftarPresensi', compact('presensis', 'departements'));
    }


    public function daftarKaryawan(Request $request)
{
    $query = Karyawan::query();

    if ($request->filled('nama')) {
        $query->where('nama_lengkap', 'like', '%' . $request->nama . '%');
    }

    if ($request->filled('divisi')) {
        $query->whereHas('departement', function($q) use ($request) {
            $q->where('nama_divisi', $request->divisi);
        });
    }

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('NIK', 'like', "%$search%")
              ->orWhere('nama_lengkap', 'like', "%$search%")
              ->orWhere('username', 'like', "%$search%")
              ->orWhere('no_hp', 'like', "%$search%");
        });
    }

    $karyawan = $query->paginate(10)->appends($request->all());

    // Ambil semua divisi untuk dropdown filter
    $departements = Department::all();

    return view('daftarKaryawan', compact('karyawan', 'departements'));
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

    public function editKaryawan($nik)
    {
        $karyawan    = DB::table('karyawan')->where('NIK', $nik)->first();
        $departements = DB::table('departement')->get();
        $jabatans     = DB::table('jabatan')->get();

        return view('editKaryawan', compact('karyawan', 'departements', 'jabatans'));
    }

    public function updateKaryawan(Request $request, $NIK)
    {
        $dataUpdate = [
            'username'  => $request->username,
            'no_hp'     => $request->no_hp,
            'tgl_lahir' => $request->tgl_lahir,
            'alamat'    => $request->alamat,
            'id_divisi' => $request->id_divisi,
            'id_jabatan'=> $request->id_jabatan,
            'role'      => $request->role,
            'status'    => $request->status,
        ];

        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('foto', 'public');
            $dataUpdate['foto'] = $fotoPath;
        }

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

    // Ambil data karyawan berdasarkan NIK dari tabel akun
    $karyawan = DB::table('karyawan')
        ->where('NIK', $akun->NIK)
        ->first();

    if (!$karyawan) {
        return back()->withErrors(['login' => 'Data karyawan tidak ditemukan.']);
    }

    // Simpan ke session
    session([
        'username' => $akun->username,
        'role'     => $karyawan->role,   // role dari tabel karyawan
        'karyawan' => $karyawan,
    ]);

    // Redirect sesuai role
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
            'nik'        => 'required|unique:karyawan,NIK',
            'username'   => 'required|unique:akun,username',
            'password'   => 'required|min:6',
            'id_divisi'  => 'required|integer',
            'id_jabatan' => 'required|integer',
            'divisi'     => 'required|string',
            'nama_lengkap'=> 'required|string',
            'no_hp'      => 'required|string',
            'tgl_lahir'  => 'required|date',
            'alamat'     => 'required|string',
            'role'       => 'required|string',
            'foto'       => 'nullable|image|max:2048',
        ]);

        $fotoName = null;
        if ($request->hasFile('foto')) {
            $fotoName = time() . '.' . $request->foto->extension();
            $request->foto->move(public_path('uploads'), $fotoName);
        }

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

    public function getData($startDate = null, $endDate = null)
    {
        $query = DB::table('presensi')
            ->join('karyawan', 'presensi.NIK', '=', 'karyawan.NIK')
            ->leftJoin('departement', 'karyawan.id_divisi', '=', 'departement.id_divisi')
            ->select(
                'karyawan.NIK as nik',
                'karyawan.nama_lengkap as nama',
                'departement.nama_divisi as divisi',
                DB::raw('COUNT(DISTINCT presensi.tgl_presen) as total_hari'),
                DB::raw('SUM(CASE WHEN presensi.status = "hadir" THEN 1 ELSE 0 END) as hadir'),
                DB::raw('SUM(CASE WHEN presensi.status = "sakit" THEN 1 ELSE 0 END) as sakit'),
                DB::raw('SUM(CASE WHEN presensi.status = "izin" THEN 1 ELSE 0 END) as izin'),
                DB::raw('SUM(CASE WHEN presensi.status = "alpha" THEN 1 ELSE 0 END) as alpha')
            )
            ->groupBy('karyawan.NIK', 'karyawan.nama_lengkap', 'departement.nama_divisi');

        if ($startDate && $endDate) {
            $query->whereBetween('presensi.tgl_presen', [$startDate, $endDate]);
        }

        return $query->get();
    }


    public function laporan(Request $request)
    {
        $data = $this->getData($request->mulai, $request->sampai);
        return view('laporan', compact('data'));
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

            public function __construct($data)
            {
                $this->data = $data;
            }

            public function collection()
            {
                $header = collect([['NIK','Nama','Divisi','Total Hari Kerja','Jumlah Hadir','Jumlah Sakit','Jumlah Cuti']]);

                $rows = $this->data->map(function ($row) {
                    return [
                        $row['nik'],
                        $row['nama'],
                        $row['divisi'],
                        0,
                        $row['hadir'],
                        $row['sakit'],
                        $row['cuti'],
                    ];
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
