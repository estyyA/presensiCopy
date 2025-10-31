<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Karyawan;
use App\Presensi;
use App\Akun;
use App\Department;
use App\Jabatan;
use App\CatatanLaporan;
use App\Cuti;
use App\Subdepartement;
use App\TrackingSales;
use App\Sakit;




class PageController extends Controller
{
    /** ---------------- DASHBOARD ---------------- */
    public function dashboard()
{
    $totalKaryawan = DB::table('karyawan')
    ->where('status', 'Aktif')
    ->count();

    $today = Carbon::today();
    $startWeek = Carbon::now()->startOfWeek();
    $endWeek   = Carbon::now()->endOfWeek();
    $startMonth = Carbon::now()->startOfMonth();
    $endMonth   = Carbon::now()->endOfMonth();

    /** ================= Harian ================= */
    $harianMasuk = DB::table('presensi')
        ->whereDate('tgl_presen', $today)
        ->where('status', 'hadir')
        ->count();

    $harianIzin = DB::table('presensi')
        ->whereDate('tgl_presen', $today)
        ->where('status', 'izin')
        ->count();

    $harianSakit = DB::table('presensi')
        ->whereDate('tgl_presen', $today)
        ->where('status', 'sakit')
        ->count();

    // 🔹 Cuti dibaca dari tabel cuti
    $harianCuti = DB::table('cuti')
        ->whereDate('tanggal_mulai', '<=', $today)
        ->whereDate('tanggal_selesai', '>=', $today)
        ->count();

    $harianAlpha = $totalKaryawan - ($harianMasuk + $harianIzin + $harianSakit + $harianCuti);

    /** ================= Mingguan ================= */
    $mingguanMasuk = DB::table('presensi')
        ->whereBetween('tgl_presen', [$startWeek, $endWeek])
        ->where('status', 'hadir')
        ->count();

    $mingguanIzin = DB::table('presensi')
        ->whereBetween('tgl_presen', [$startWeek, $endWeek])
        ->where('status', 'izin')
        ->count();

    $mingguanSakit = DB::table('presensi')
        ->whereBetween('tgl_presen', [$startWeek, $endWeek])
        ->where('status', 'sakit')
        ->count();

    $mingguanCuti = DB::table('cuti')
        ->where(function ($q) use ($startWeek, $endWeek) {
            $q->whereBetween('tanggal_mulai', [$startWeek, $endWeek])
              ->orWhereBetween('tanggal_selesai', [$startWeek, $endWeek])
              ->orWhere(function ($q2) use ($startWeek, $endWeek) {
                  $q2->where('tanggal_mulai', '<=', $startWeek)
                     ->where('tanggal_selesai', '>=', $endWeek);
              });
        })
        ->count();

    $mingguanAlpha = $totalKaryawan - ($mingguanMasuk + $mingguanIzin + $mingguanSakit + $mingguanCuti);

    /** ================= Bulanan ================= */
    $bulananMasuk = DB::table('presensi')
        ->whereBetween('tgl_presen', [$startMonth, $endMonth])
        ->where('status', 'hadir')
        ->count();

    $bulananIzin = DB::table('presensi')
        ->whereBetween('tgl_presen', [$startMonth, $endMonth])
        ->where('status', 'izin')
        ->count();

    $bulananSakit = DB::table('presensi')
        ->whereBetween('tgl_presen', [$startMonth, $endMonth])
        ->where('status', 'sakit')
        ->count();

    $bulananCuti = DB::table('cuti')
        ->where(function ($q) use ($startMonth, $endMonth) {
            $q->whereBetween('tanggal_mulai', [$startMonth, $endMonth])
              ->orWhereBetween('tanggal_selesai', [$startMonth, $endMonth])
              ->orWhere(function ($q2) use ($startMonth, $endMonth) {
                  $q2->where('tanggal_mulai', '<=', $startMonth)
                     ->where('tanggal_selesai', '>=', $endMonth);
              });
        })
        ->count();

    $bulananAlpha = $totalKaryawan - ($bulananMasuk + $bulananIzin + $bulananSakit + $bulananCuti);

    /** ================= Data Profil Karyawan ================= */
    $karyawan = null;
    if (Auth::check()) {
        $nik = Auth::user()->NIK; // sesuaikan field login kamu
        $karyawan = DB::table('karyawan')->where('NIK', $nik)->first();

        // simpan juga ke session biar konsisten
        session(['karyawan' => $karyawan]);
    }

    return view('dashboard', compact(
        'totalKaryawan',
        'harianMasuk','harianIzin','harianSakit','harianCuti','harianAlpha',
        'mingguanMasuk','mingguanIzin','mingguanSakit','mingguanCuti','mingguanAlpha',
        'bulananMasuk','bulananIzin','bulananSakit','bulananCuti','bulananAlpha',
        'karyawan' // 🔹 tambahan biar Blade dapet data
    ));
}





    /** ---------------- KARYAWAN ---------------- */
  public function daftarPresensi(Request $request)
{
    $query = DB::table('presensi')
        ->join('karyawan', 'presensi.NIK', '=', 'karyawan.NIK')
        ->leftJoin('departement', 'karyawan.id_divisi', '=', 'departement.id_divisi')
        ->select(
            'presensi.id_presen',
            'presensi.tgl_presen',
            'presensi.jam_masuk',
            'presensi.jam_keluar',
            'presensi.status',
            'presensi.surat', // ✅ tambahkan ini
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
        $query->whereDate('presensi.tgl_presen', $request->tanggal);
    }

    $presensis = $query->orderBy('presensi.tgl_presen', 'desc')
        ->paginate(10)
        ->appends($request->all());

    $departements = DB::table('departement')
        ->select('id_divisi', 'nama_divisi')
        ->get();

    return view('daftarPresensi', compact('presensis', 'departements'));
}





/** ---------------- DATA KARYAWAN ---------------- */

public function daftarKaryawan(Request $request)
{
    $query = DB::table('karyawan')
        ->leftJoin('departement', 'karyawan.id_divisi', '=', 'departement.id_divisi')
        ->leftJoin('jabatan', 'karyawan.id_jabatan', '=', 'jabatan.id_jabatan')
        ->select(
            'karyawan.*',
            'departement.nama_divisi',
            'jabatan.nama_jabatan'
        );

    // Filter nama
    if ($request->filled('nama')) {
        $query->where('karyawan.nama_lengkap', 'like', '%' . $request->nama . '%');
    }

    // Filter divisi
    if ($request->filled('divisi')) {
        $query->where('departement.id_divisi', $request->divisi);
    }

    // Filter pencarian umum
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('karyawan.NIK', 'like', "%$search%")
              ->orWhere('karyawan.nama_lengkap', 'like', "%$search%")
              ->orWhere('karyawan.username', 'like', "%$search%")
              ->orWhere('karyawan.no_hp', 'like', "%$search%");
        });
    }

    // ✅ Tambahkan filter hanya karyawan aktif
    $query->where('karyawan.status', 'aktif');

    // Ambil data dengan pagination
    $karyawan = $query->paginate(10)->appends($request->all());

    // Ambil semua divisi untuk dropdown filter
    $departements = DB::table('departement')->select('id_divisi', 'nama_divisi')->get();

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
            'role'         => 'karyawan', // 👈 FIXED
        ]);

        return redirect('/daftarKaryawan')->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function editKaryawan($nik)
    {
        $karyawan      = DB::table('karyawan')->where('NIK', $nik)->first();
        $departements  = DB::table('departement')->get();
        $jabatans      = DB::table('jabatan')->get();
        $subdepartements = DB::table('subdepartement')->get(); // ✅ Tambah ini

        return view('editKaryawan', compact('karyawan', 'departements', 'jabatans', 'subdepartements'));
    }

    public function updateKaryawan(Request $request, $NIK)
    {
        $dataUpdate = [
            'username'     => $request->username,
            'no_hp'        => $request->no_hp,
            'tgl_lahir'    => $request->tgl_lahir,
            'alamat'       => $request->alamat,
            'id_divisi'    => $request->id_divisi,
            'id_subdivisi' => $request->id_subdivisi, // ✅ Tambah ini
            'id_jabatan'   => $request->id_jabatan,
            'status'       => $request->status,
            'role'         => 'karyawan',
        ];


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
            ->leftJoin('subdepartement', 'karyawan.id_subdivisi', '=', 'subdepartement.id_subdivisi') // ✅ Tambahan join subdivisi
            ->leftJoin('jabatan', 'karyawan.id_jabatan', '=', 'jabatan.id_jabatan')
            ->select(
                'karyawan.*',
                'departement.nama_divisi',
                'subdepartement.nama_subdivisi', // ✅ Ambil nama_subdivisi
                'jabatan.nama_jabatan'
            )
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
    // Validasi input
    $request->validate([
        'username' => 'required',
        'password' => 'required',
    ]);

    // Ambil akun + data karyawan lengkap

    $akun = DB::table('akun')
        ->join('karyawan', 'akun.NIK', '=', 'karyawan.NIK')
        ->leftJoin('departement', 'karyawan.id_divisi', '=', 'departement.id_divisi')
        ->leftJoin('jabatan', 'karyawan.id_jabatan', '=', 'jabatan.id_jabatan')
        ->leftJoin('subdepartement', 'karyawan.id_subdivisi', '=', 'subdepartement.id_subdivisi')
        ->where('akun.username', $request->username)
        ->select(
            'akun.username',
            'akun.password',
            'karyawan.NIK',
            'karyawan.role',
            'karyawan.nama_lengkap',
            'departement.nama_divisi',
            'jabatan.nama_jabatan',
            'subdepartement.nama_subdivisi' // ✅ sesuai kolom di database
        )
        ->first();



    // Cek apakah user ada dan password benar
    if (!$akun || !Hash::check($request->password, $akun->password)) {
        return back()->withErrors(['login' => 'Username atau password salah.']);
    }

    // Simpan data lengkap karyawan ke session
    session(['karyawan' => $akun]);

    // Redirect sesuai role
    if ($akun->role === 'admin') {
        return redirect()->route('dashboard')
            ->with('success', 'Login berhasil sebagai Admin');
    }

    return redirect()->route('karyawan.dashboard')
        ->with('success', 'Login berhasil sebagai Karyawan');
}




    /** ---------------- REGISTER ---------------- */
    public function showRegister()
    {
        $departements = Department::all();
        $jabatans = Jabatan::all();

        return view('register', compact('departements', 'jabatans'));
    }

    public function processRegister(Request $request)
{
    $request->validate([
        'nik'          => 'required|unique:karyawan,NIK',
        'username'     => 'required|unique:akun,username',
        'password'     => 'required|min:6',
        'email'        => 'required|email|unique:karyawan,email',
        'id_divisi'    => 'required|integer',
        'id_subdivisi' => 'nullable|integer', // ✅ tambahkan ini
        'id_jabatan'   => 'required|integer',
        'nama_lengkap' => 'required|string',
        'no_hp'        => 'required|string',
        'tgl_lahir'    => 'required|date',
        'alamat'       => 'required|string',
        'role'         => 'required|string',
    ]);

    // Simpan data ke tabel karyawan
    DB::table('karyawan')->insert([
        'NIK'          => $request->nik,
        'username'     => $request->username,
        'email'        => $request->email,
        'id_divisi'    => $request->id_divisi,
        'id_subdivisi' => $request->id_subdivisi, // ✅ tambahkan ini
        'id_jabatan'   => $request->id_jabatan,
        'nama_lengkap' => $request->nama_lengkap,
        'no_hp'        => $request->no_hp,
        'tgl_lahir'    => $request->tgl_lahir,
        'alamat'       => $request->alamat,
        'role'         => $request->role,
        'status'       => 'Aktif',
    ]);

    // Simpan data ke tabel akun
    DB::table('akun')->insert([
        'username'   => $request->username,
        'NIK'        => $request->nik,
        'password'   => Hash::make($request->password),
    ]);

    return redirect()->route('login.form')->with('success', 'Registrasi berhasil! Silakan login.');
}


public function getSubdivisi($id_divisi)
{
    $subdivisi = \App\Subdepartement::where('id_divisi', $id_divisi)
        ->get(['id_subdivisi', 'nama_subdivisi']);

    return response()->json($subdivisi);
}



/** ---------------- LAPORAN ---------------- */
public function laporan(Request $request)
{
    $mulai    = $request->mulai;
    $sampai   = $request->sampai;
    $kategori = $request->kategori;

    // --- Ambil data presensi ---
    $presensi = DB::table('presensi')
        ->select(
            'NIK as nik',
            'tgl_presen as tanggal',
            'status',
            'surat',
            DB::raw('TIMESTAMPDIFF(MINUTE, jam_masuk, jam_keluar) as total_menit'),
            DB::raw('1 as total_hari')
        )
        ->when($mulai && $sampai, function ($q) use ($mulai, $sampai) {
            $q->whereBetween('tgl_presen', [$mulai, $sampai]);
        });

    // --- Ambil data cuti (hitung durasi harinya) ---
    $cuti = DB::table('cuti')
        ->select(
            'nik',
            DB::raw('tanggal_mulai as tanggal'),
            DB::raw('"cuti" as status'),
            DB::raw('NULL as surat'),
            DB::raw('0 as total_menit'),
            DB::raw('DATEDIFF(tanggal_selesai, tanggal_mulai) + 1 as total_hari')
        )
        ->when($mulai && $sampai, function ($q) use ($mulai, $sampai) {
            $q->where(function ($sub) use ($mulai, $sampai) {
                $sub->whereBetween('tanggal_mulai', [$mulai, $sampai])
                    ->orWhereBetween('tanggal_selesai', [$mulai, $sampai])
                    ->orWhere(function ($x) use ($mulai, $sampai) {
                        $x->where('tanggal_mulai', '<=', $mulai)
                          ->where('tanggal_selesai', '>=', $sampai);
                    });
            });
        });

    // Gabungkan presensi dan cuti
    $all = $presensi->unionAll($cuti);

    // --- Rekap per karyawan ---
    $rekap = DB::table(DB::raw("({$all->toSql()}) as logs"))
        ->mergeBindings($all)
        ->rightJoin('karyawan', 'logs.nik', '=', 'karyawan.NIK')
        ->leftJoin('departement', 'karyawan.id_divisi', '=', 'departement.id_divisi')
        ->leftJoin('jabatan', 'karyawan.id_jabatan', '=', 'jabatan.id_jabatan')
        ->groupBy('karyawan.NIK', 'karyawan.nama_lengkap', 'departement.nama_divisi', 'jabatan.nama_jabatan')
        ->select(
            'karyawan.NIK as nik',
            'karyawan.nama_lengkap as nama',
            'departement.nama_divisi as divisi',
            'jabatan.nama_jabatan as jabatan',
            DB::raw('GROUP_CONCAT(DISTINCT logs.surat SEPARATOR ", ") as surat'),
            DB::raw('SUM(CASE WHEN logs.status = "hadir" THEN 1 ELSE 0 END) as hadir'),
            DB::raw('SUM(CASE WHEN logs.status = "izin" THEN 1 ELSE 0 END) as izin'),
            DB::raw('SUM(CASE WHEN logs.status = "sakit" THEN 1 ELSE 0 END) as sakit'),
            DB::raw('SUM(CASE WHEN logs.status = "cuti" THEN logs.total_hari ELSE 0 END) as cuti'),
            DB::raw('SUM(CASE WHEN logs.status = "alpha" THEN 1 ELSE 0 END) as alpha_presensi'),
            DB::raw('SUM(CASE WHEN logs.status = "hadir" THEN logs.total_menit ELSE 0 END) as total_menit')
        )
        ->get();

    // --- Hitung total hari kerja ---
    if ($mulai && $sampai) {
        $period = new \DatePeriod(
            new \DateTime($mulai),
            new \DateInterval('P1D'),
            (new \DateTime($sampai))->modify('+1 day')
        );
        $totalHari = iterator_count($period);
    } else {
        $first = DB::table('presensi')->min('tgl_presen');
        $last  = DB::table('presensi')->max('tgl_presen');
        if ($first && $last) {
            $period = new \DatePeriod(new \DateTime($first), new \DateInterval('P1D'), (new \DateTime($last))->modify('+1 day'));
            $totalHari = iterator_count($period);
        } else {
            $totalHari = 0;
        }
    }

    // --- Tambahkan kolom alpha ---
    foreach ($rekap as $r) {
        $hadir = (int) $r->hadir;
        $izin  = (int) $r->izin;
        $sakit = (int) $r->sakit;
        $cuti  = (int) $r->cuti;
        $alpha_presensi = (int) $r->alpha_presensi;

        $alpha_kosong = max(0, $totalHari - ($hadir + $izin + $sakit + $cuti));
        $r->alpha = $alpha_presensi + $alpha_kosong;
    }

    // filter kategori
    if ($kategori && $kategori != 'semua') {
        $rekap = $rekap->filter(function ($r) use ($kategori) {
            return (int) ($r->{$kategori} ?? 0) > 0;
        })->values();
    }

    $catatan = CatatanLaporan::pluck('catatan', 'nik');

    return view('laporan', [
        'data' => $rekap,
        'catatan' => $catatan
    ]);
}

private function getData($mulai = null, $sampai = null)
{
    if (!$mulai || !$sampai) {
        $mulai  = DB::table('presensi')->min('tgl_presen');
        $sampai = DB::table('presensi')->max('tgl_presen');
    }

    // --- Ambil data presensi
    $presensi = DB::table('presensi')
        ->select(
            'NIK as nik',
            'tgl_presen as tanggal',
            'status',
            'surat',
            DB::raw('TIMESTAMPDIFF(MINUTE, jam_masuk, jam_keluar) as total_menit')
        )
        ->whereBetween('tgl_presen', [$mulai, $sampai])
        ->get();

    // --- Ambil data cuti (yang overlap rentang)
    $cutiRows = DB::table('cuti')
        ->select('nik', 'tanggal_mulai', 'tanggal_selesai')
        ->where(function ($q) use ($mulai, $sampai) {
            $q->whereBetween('tanggal_mulai', [$mulai, $sampai])
              ->orWhereBetween('tanggal_selesai', [$mulai, $sampai])
              ->orWhere(function ($x) use ($mulai, $sampai) {
                  $x->where('tanggal_mulai', '<=', $mulai)
                    ->where('tanggal_selesai', '>=', $sampai);
              });
        })
        ->get();

    // --- Expand cuti per hari (Senin–Sabtu)
    $cutiExpanded = collect();
    foreach ($cutiRows as $c) {
        $start = new \DateTime($c->tanggal_mulai);
        $end   = new \DateTime($c->tanggal_selesai);
        $period = new \DatePeriod($start, new \DateInterval('P1D'), $end->modify('+1 day'));

        foreach ($period as $d) {
            if ($d->format('N') < 7) { // Senin–Sabtu
                $cutiExpanded->push([
                    'nik' => $c->nik,
                    'tanggal' => $d->format('Y-m-d'),
                    'status' => 'cuti',
                    'surat' => null,
                    'total_menit' => 0,
                ]);
            }
        }
    }

    // --- Gabungkan presensi dan cuti
    $allLogs = $presensi->map(function ($p) {
        return (array) $p;
    })->merge($cutiExpanded);

    // --- Buat daftar tanggal kerja (Senin–Sabtu)
    $dates = collect();
    $period = new \DatePeriod(
        new \DateTime($mulai),
        new \DateInterval('P1D'),
        (new \DateTime($sampai))->modify('+1 day')
    );

    foreach ($period as $d) {
        if ($d->format('N') < 7) { // exclude Minggu
            $dates->push($d->format('Y-m-d'));
        }
    }

    // --- Ambil data karyawan
    $karyawan = DB::table('karyawan')->get();
    $namaField = Schema::hasColumn('karyawan', 'nama_lengkap') ? 'nama_lengkap' : 'nama_karyawan';

    // --- Rekap per karyawan
    $rekap = collect();
    foreach ($karyawan as $k) {
        $logPerKar = collect();

        foreach ($dates as $tgl) {
            $record = $allLogs->first(function ($x) use ($k, $tgl) {
                return isset($x['nik']) && isset($x['tanggal']) && $x['nik'] == $k->NIK && $x['tanggal'] == $tgl;
            });

            if ($record) {
                $logPerKar->push($record);
            } else {
                $logPerKar->push([
                    'nik' => $k->NIK,
                    'tanggal' => $tgl,
                    'status' => 'alpha',
                    'surat' => null,
                    'total_menit' => 0,
                ]);
            }
        }

        $rekap->push((object) [
            'nik'   => $k->NIK,
            'nama'  => $k->$namaField,
            'total_hari' => $dates->count(),
            'hadir' => $logPerKar->where('status', 'hadir')->count(),
            'izin'  => $logPerKar->where('status', 'izin')->count(),
            'sakit' => $logPerKar->where('status', 'sakit')->count(),
            'cuti'  => $logPerKar->where('status', 'cuti')->count(),
            'alpha' => $logPerKar->where('status', 'alpha')->count(),
            'surat' => $logPerKar->pluck('surat')->filter()->implode(', '),
            'total_menit' => $logPerKar->sum('total_menit'),
        ]);
    }

    return $rekap;
}




public function cetakPdf(Request $request)
{
    $mulai    = $request->mulai;
    $sampai   = $request->sampai;
    $kategori = $request->kategori;

    $data = $this->laporan($request)->getData()['data']; // ambil data dari fungsi laporan
    $catatan = CatatanLaporan::pluck('catatan', 'nik')->toArray();

    $pdf = PDF::loadView('laporan_pdf', [
        'data'     => $data,
        'catatan'  => $catatan,
        'mulai'    => $mulai,
        'sampai'   => $sampai,
        'kategori' => $kategori,
    ])->setPaper('a4', 'landscape');

    return $pdf->download("Laporan Presensi $mulai - $sampai.pdf");
}



public function exportExcel(Request $request)
{
    $mulai    = $request->mulai;
    $sampai   = $request->sampai;
    $kategori = $request->kategori;

    $data = $this->laporan($request)->getData()['data'];
    $catatan = CatatanLaporan::pluck('catatan', 'nik')->toArray();

    return Excel::download(new class($data, $catatan) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings
    {
        private $data, $catatan;
        public function __construct($data, $catatan)
        {
            $this->data = $data;
            $this->catatan = $catatan;
        }

        public function collection()
        {
            return collect($this->data)->map(function ($r) {
                return [
                    'NIK' => $r->nik,
                    'Nama' => $r->nama,
                    'Divisi' => $r->divisi,
                    'Jabatan' => $r->jabatan,
                    'Surat' => $r->surat,
                    'Hadir' => $r->hadir,
                    'Izin' => $r->izin,
                    'Sakit' => $r->sakit,
                    'Cuti' => $r->cuti,
                    'Alpha' => $r->alpha,
                    'Total Menit' => $r->total_menit,
                ];
            });
        }

        public function headings(): array
        {
            return ['NIK', 'Nama', 'Divisi', 'Jabatan', 'Surat', 'Hadir', 'Izin', 'Sakit', 'Cuti', 'Alpha', 'Total Menit'];
        }
    }, "Laporan Presensi $mulai - $sampai.xlsx");
}


public function simpanCatatan(Request $request)
{
    try {
        $catatan = $request->input('catatan', []);

        foreach ($catatan as $nik => $teks) {
            if (!empty($teks)) {
                DB::table('catatan_laporan')->updateOrInsert(
                    ['nik' => $nik],
                    ['catatan' => $teks, 'updated_at' => now()]
                );
            }
        }

        return redirect()->route('laporan')->with('success', 'Catatan berhasil disimpan!');
    } catch (\Exception $e) {
        return redirect()->route('laporan')->with('error', 'Gagal menyimpan catatan: ' . $e->getMessage());
    }
}

    /** ---------------- LOGOUT ---------------- */
    public function logout(Request $request)
    {
        $request->session()->forget(['user', 'karyawan']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Berhasil logout.');
    }

        /** ---------------- FORGOT PASSWORD ---------------- */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // cek apakah email ada di tabel karyawan
        $user = DB::table('karyawan')->where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak ditemukan.']);
        }

        // generate token
        $token = Str::random(60);

        // simpan ke tabel password_resets
        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            ['token' => $token, 'created_at' => Carbon::now()]
        );

        // kirim email (sementara pakai teks sederhana)
       Mail::raw("Klik link berikut untuk reset password: " . url('/reset-password/' . $token . '?email=' . $request->email), function ($message) use ($request) {
        $message->to($request->email);
        $message->subject('Reset Password');
     });


        return back()->with('success', 'Link reset password sudah dikirim ke email.');
    }

   public function showResetForm(Request $request, $token)
{
    $email = $request->query('email'); // ambil dari ?email=...
    return view('auth.reset-password', compact('token', 'email'));
}

   public function resetPassword(Request $request)
{
    $request->validate([
        'email' => 'required|email|exists:karyawan,email',
        'password' => 'required|min:6|confirmed',
        'token' => 'required'
    ]);

    $reset = DB::table('password_resets')
        ->where('email', $request->email)
        ->where('token', $request->token)
        ->first();

    if (!$reset) {
        return back()->withErrors(['email' => 'Token tidak valid.']);
    }

    // cek expired (60 menit)
    if (Carbon::parse($reset->created_at)->addMinutes(60)->isPast()) {
        return back()->withErrors(['email' => 'Token sudah kadaluarsa.']);
    }

    // update password di akun
    $karyawan = DB::table('karyawan')->where('email', $request->email)->first();
    if (!$karyawan) {
        return back()->withErrors(['email' => 'Karyawan tidak ditemukan.']);
    }

    DB::table('akun')->where('NIK', $karyawan->NIK)->update([
        'password' => Hash::make($request->password)
    ]);

    DB::table('password_resets')->where('email', $request->email)->delete();

    return redirect()->route('login.form')->with('success', 'Password berhasil direset. Silakan login.');
}


    public function logoutUser(Request $request)
{
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/login');
}

// =======================
    // DASHBOARD KARYAWAN
    // =======================
    public function dashboardKaryawan(Request $request)
    {
        $karyawan = session('karyawan');

    if (!$karyawan) {
        return redirect()->route('login.form');
    }

    // Presensi hari ini
    $presensiHariIni = DB::table('presensi')
        ->where('NIK', $karyawan->NIK)
        ->whereDate('tgl_presen', now()->toDateString())
        ->first();

    // Riwayat presensi 7 hari terakhir
    $riwayat = DB::table('presensi')
        ->where('NIK', $karyawan->NIK)
        ->orderBy('tgl_presen', 'desc');

    if ($request->filled('mulai') && $request->filled('sampai')) {
        $riwayat->whereBetween('tgl_presen', [$request->mulai, $request->sampai]);
    } else {
        $riwayat->limit(7);
    }

    $riwayat = $riwayat->get();

    return view('karyawan.dashboard', compact('karyawan', 'presensiHariIni', 'riwayat'));
    }


   // =======================
// FORM ABSEN MASUK
// =======================
public function presensiMasuk(Request $request)
{
    $karyawan = session('karyawan');
    if (!$karyawan) {
        return redirect()->route('login.form');
    }

    $tanggal = now('Asia/Jakarta')->toDateString(); // <-- pakai timezone Asia/Jakarta

    // Cek apakah sudah absen hari ini
    $sudahAbsen = DB::table('presensi')
        ->where('NIK', $karyawan->NIK)
        ->whereDate('tgl_presen', $tanggal)
        ->exists();

    if ($sudahAbsen) {
        return redirect()->route('karyawan.dashboard')
            ->with('warning', 'Anda sudah absen masuk hari ini!');
    }

    // Simpan data presensi
    DB::table('presensi')->insert([
        'NIK'           => $karyawan->NIK,
        'nama_karyawan' => $karyawan->nama_lengkap,
        'divisi'        => $karyawan->divisi ?? '-',
        'tgl_presen'    => $tanggal,
        'jam_masuk'     => now('Asia/Jakarta')->format('H:i:s'), // <-- pakai WIB
        'lokasi_masuk'  => $request->lokasi_masuk ?? null,
        'status'        => 'hadir',
    ]);

    return redirect()->route('karyawan.dashboard')->with('success', 'Absen masuk berhasil!');
}

// =======================
// FORM ABSEN KELUAR
// =======================
public function presensiKeluar(Request $request)
{
    $karyawan = session('karyawan');
    if (!$karyawan) {
        return redirect()->route('login.form');
    }

    DB::table('presensi')
        ->where('NIK', $karyawan->NIK)
        ->whereDate('tgl_presen', now('Asia/Jakarta')->toDateString()) // <-- pakai WIB
        ->update([
            'jam_keluar' => now('Asia/Jakarta')->format('H:i:s'), // <-- pakai WIB
            'lokasi_keluar' => $request->lokasi_keluar ?? null,
        ]);

    return redirect()->route('karyawan.dashboard')->with('success', 'Absen keluar berhasil!');
}



public function showFormMasuk()
{
    return view('absensi.masuk'); // pastikan file absensi/masuk.blade.php ada
}

public function showFormKeluar()
{
    return view('absensi.keluar'); // pastikan file absensi/keluar.blade.php ada
}


public function profil()
{
    $sessionKaryawan = session('karyawan');

    if (!$sessionKaryawan) {
        return redirect()->route('login.form')->withErrors(['login' => 'Silakan login dulu']);
    }

    $karyawan = DB::table('karyawan')
        ->leftJoin('departement', 'karyawan.id_divisi', '=', 'departement.id_divisi')
        ->leftJoin('subdepartement', 'karyawan.id_subdivisi', '=', 'subdepartement.id_subdivisi') // ✅ Tambahan join subdivisi
        ->leftJoin('jabatan', 'karyawan.id_jabatan', '=', 'jabatan.id_jabatan')
        ->select(
            'karyawan.*',
            'departement.nama_divisi',
            'subdepartement.nama_subdivisi', // ✅ Ambil nama_subdivisi
            'jabatan.nama_jabatan'
        )
        ->where('karyawan.NIK', $sessionKaryawan->NIK)
        ->first();

    return view('showKaryawan', [
        'karyawan' => $karyawan,
        'title'    => 'Profile'
    ]);
}
public function formSakit()
    {
        return view('karyawan.sakit'); // Sesuaikan path view
    }

public function storeSakit(Request $request)
{
    $karyawan = session('karyawan');
    if (!$karyawan) {
        return redirect()->route('login.form')->with('error', 'Silakan login terlebih dahulu.');
    }

    // Validasi input
    $request->validate([
        'tgl_pengajuan' => 'required|date',
        'tgl_mulai' => 'required|date',
        'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
        'keterangan' => 'nullable|string',
        'surat_dokter' => 'nullable|file|mimes:jpeg,jpg,png,heic,pdf|max:2048',
    ]);

    // Upload file jika ada
    $filePath = null;
    if ($request->hasFile('surat_dokter')) {
        $filePath = $request->file('surat_dokter')->store('surat_dokter', 'public');
    }

    // Simpan ke tabel sakit
    DB::table('sakit')->insert([
        'NIK' => $karyawan->NIK,
        'tgl_pengajuan' => $request->tgl_pengajuan,
        'tgl_mulai' => $request->tgl_mulai,
        'tgl_selesai' => $request->tgl_selesai,
        'keterangan' => $request->keterangan,
        'surat_dokter' => $filePath,
        'status_pengajuan' => 'menunggu',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return redirect()->route('karyawan.dashboard')
        ->with('success', 'Pengajuan sakit berhasil dikirim dan menunggu persetujuan admin.');
}

public function konfirSakit()
{
    $pengajuans = Sakit::with(['karyawan' => function($q) {
        $q->leftJoin('departement', 'karyawan.id_divisi', '=', 'departement.id_divisi')
          ->select('karyawan.*', 'departement.nama_divisi');
    }])
    ->orderByDesc('id')
    ->get();

    return view('konfirsakit', compact('pengajuans'));
}


public function setujuiSakit($id)
{
    $pengajuan = DB::table('sakit')->where('id', $id)->first();

    if (!$pengajuan) {
        return redirect()->back()->with('error', 'Data tidak ditemukan.');
    }

    DB::beginTransaction();
    try {
        // ✅ Ubah status pengajuan jadi disetujui + catat tanggal & admin
        DB::table('sakit')->where('id', $id)->update([
            'status_pengajuan' => 'disetujui',
            'tanggal_disetujui' => now(),
            'disetujui_oleh' => session('admin')->nama ?? 'Admin',
            'updated_at' => now(),
        ]);

        // ✅ Tambahkan data ke tabel presensi sesuai rentang tanggal
        $mulai = \Carbon\Carbon::parse($pengajuan->tgl_mulai);
        $selesai = \Carbon\Carbon::parse($pengajuan->tgl_selesai);

        for ($tgl = $mulai; $tgl->lte($selesai); $tgl->addDay()) {
            DB::table('presensi')->updateOrInsert(
                [
                    'NIK' => $pengajuan->NIK,
                    'tgl_presen' => $tgl->toDateString(),
                ],
                [
                    'jam_masuk' => null,
                    'jam_keluar' => null,
                    'lokasi_masuk' => null,
                    'lokasi_keluar' => null,
                    'status' => 'sakit',
                    'surat' => $pengajuan->surat_dokter,
                ]
            );
        }

        DB::commit();
        return redirect()->back()->with('success', 'Pengajuan sakit disetujui dan presensi diperbarui.');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Gagal menyetujui: ' . $e->getMessage());
    }
}


public function tolakSakit($id)
{
    $pengajuan = DB::table('sakit')->where('id', $id)->first();
    if (!$pengajuan) {
        return redirect()->back()->with('error', 'Data tidak ditemukan.');
    }

    DB::table('sakit')->where('id', $id)->update([
        'status_pengajuan' => 'ditolak',
        'updated_at' => now(),
    ]);

    return redirect()->back()->with('success', 'Pengajuan sakit ditolak.');
}


// tampilkan form
public function trackingSalesForm()
{
    // Ambil data karyawan dari session
    $karyawan = session('karyawan');
    if (!$karyawan) {
        return redirect()->route('login.form')->with('error', 'Silahkan login terlebih dahulu.');
    }

    return view('karyawan.trackingSales', compact('karyawan'));
}

public function trackingSalesStore(Request $request)
{
    $request->validate([
        'tanggal_sales' => 'required|date',
        'jam_sales' => 'required',
        'lokasi_sales' => 'required|string|max:255',
    ]);

    // Ambil data karyawan dari session
    $karyawan = session('karyawan');
    if (!$karyawan) {
        return redirect()->route('login.form')->with('error', 'Silahkan login terlebih dahulu.');
    }

    // 🔍 Pastikan id_divisi benar-benar ada di database
    $divisi = DB::table('karyawan')
        ->where('NIK', $karyawan->NIK)
        ->value('id_divisi');

    if (!$divisi) {
        return back()->with('error', 'ID divisi karyawan tidak ditemukan.');
    }

    // Simpan data ke tabel tracking_sales
    TrackingSales::create([
        'NIK' => $karyawan->NIK,
        'id_divisi' => $divisi,
        'tanggal_sales' => $request->tanggal_sales,
        'jam_sales' => $request->jam_sales,
        'lokasi_sales' => $request->lokasi_sales,
    ]);

    // ⬇️ Setelah simpan, langsung kembali ke dashboard karyawan
    return redirect()->route('tracking.history')
                     ->with('success', 'Data tracking berhasil disimpan!');
}

public function trackingSalesHistory(Request $request)
{
    $karyawan = session('karyawan');

    if (!$karyawan) {
        return redirect()->route('login.form')->with('error', 'Silahkan login terlebih dahulu.');
    }

    $query = TrackingSales::with('departement')
    ->where('NIK', $karyawan->NIK)->orderBy('tanggal_sales', 'desc');

    if ($request->filled('mulai') && $request->filled('sampai')) {
        $query->whereBetween('tanggal_sales', [$request->mulai, $request->sampai]);
    }

    $tracking = $query->paginate(5);

    return view('karyawan.trackingSalesHistory', compact('tracking'));
}
public function trackingAdmin()
{
    // Ambil data tracking sales dengan relasi karyawan
    $trackings = TrackingSales::with('karyawan')
        ->orderBy('tanggal_sales', 'desc')
        ->paginate(5);

    return view('admin.trackingSalesAdmin', compact('trackings'));
}




// Hapus Presensi
public function deletePresensi($id)
{
    $deleted = DB::table('presensi')->where('id_presen', $id)->delete();

    if ($deleted) {
        return redirect()->route('daftarPresensi')->with('success', 'Presensi berhasil dihapus.');
    } else {
        return redirect()->route('daftarPresensi')->with('error', 'Presensi gagal dihapus.');
    }
}


public function updatePresensi(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:hadir,sakit,izin,cuti,alpha',
    ]);

    DB::table('presensi')
        ->where('id_presen', $id)
        ->update([
            'status' => $request->status,
        ]);

    return redirect()->back()->with('success', 'Status presensi berhasil diperbarui!');
}

// =======================
// PRESENSI ADMIN
// =======================

// Halaman utama presensi (riwayat)
public function showPresensiAdmin()
{
    $admin = session('karyawan');
    if (!$admin || $admin->role !== 'admin') {
        return redirect()->route('login.form');
    }

    $nik = $admin->NIK;
    $riwayat = DB::table('presensi')
        ->where('NIK', $admin->NIK)
        ->orderBy('tgl_presen', 'desc')
        ->take(10)
        ->get();

    $presensiHariIni = DB::table('presensi')
        ->where('NIK', $admin->NIK)
        ->whereDate('tgl_presen', now('Asia/Jakarta')->toDateString())
        ->first();

    return view('admin.presensi', compact('admin','riwayat', 'presensiHariIni'));
}

// FORM MASUK
public function formMasuk()
{
    return view('admin.Masuk'); // tampilkan Masuk.blade.php
}

// SIMPAN MASUK
public function storeMasuk(Request $request)
{
   $admin = session('karyawan');
    if (!$admin || $admin->role !== 'admin') {
        return redirect()->route('login.form');
    }

    $nik = $admin->NIK;

    $tanggal = now('Asia/Jakarta')->toDateString();

    $sudahAbsen = DB::table('presensi')
        ->where('NIK', $admin->NIK)
        ->whereDate('tgl_presen', $tanggal)
        ->exists();

    if ($sudahAbsen) {
        return redirect()->route('admin.presensi.form')
            ->with('warning', 'Anda sudah absen masuk hari ini!');
    }

    DB::table('presensi')->insert([
        'NIK'           => $admin->NIK,
        'nama_karyawan' => $admin->nama_lengkap,
        'divisi'        => $admin->divisi ?? '-',
        'tgl_presen'    => $tanggal,
        'jam_masuk'     => now('Asia/Jakarta')->format('H:i:s'),
        'lokasi_masuk'  => $request->lokasi_masuk ?? null,
        'status'        => 'hadir',
    ]);

    return redirect()->route('admin.presensi.form')->with('success', 'Absen masuk berhasil!');
}

// FORM KELUAR
public function formKeluar()
{
    return view('admin.Keluar'); // tampilkan Keluar.blade.php
}

// SIMPAN KELUAR
public function storeKeluar(Request $request)
{
    $admin = session('karyawan');
    if (!$admin || $admin->role !== 'admin') {
        return redirect()->route('login.form');
    }

    $nik = $admin->NIK;

    DB::table('presensi')
        ->where('NIK', $admin->NIK)
        ->whereDate('tgl_presen', now('Asia/Jakarta')->toDateString())
        ->update([
            'jam_keluar'    => now('Asia/Jakarta')->format('H:i:s'),
            'lokasi_keluar' => $request->lokasi_keluar ?? null,
        ]);

    return redirect()->route('admin.presensi.form')->with('success', 'Absen keluar berhasil!');
}

//CUTI//
public function cuti()
{
    $cuti = Cuti::with('karyawan')->latest()->get();

    // hanya ambil karyawan aktif untuk dropdown
    $karyawan = Karyawan::where('status', 'Aktif')
        ->orderBy('nama_lengkap')
        ->get();

    return view('cuti', compact('cuti', 'karyawan'));
}

// Menyimpan cuti baru
public function cutiStore(Request $request)
{
    $validated = $request->validate([
        'nik' => 'required|exists:karyawan,NIK',
        'tanggal_mulai' => 'required|date',
        'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        'keterangan' => 'nullable|string',
    ]);

    // simpan cuti
    Cuti::create([
        'nik' => $validated['nik'],
        'tanggal_mulai' => $validated['tanggal_mulai'],
        'tanggal_selesai' => $validated['tanggal_selesai'],
        'keterangan' => $validated['keterangan'] ?? null,
    ]);

    return redirect()->route('cuti.index')->with('success', '✅ Data cuti berhasil ditambahkan');
}

// Hapus cuti
public function cutiDelete($id)
{
    $cuti = Cuti::findOrFail($id);
    $cuti->delete();

    return redirect()->route('cuti.index')->with('success', '🗑️ Data cuti berhasil dihapus');
}

}



