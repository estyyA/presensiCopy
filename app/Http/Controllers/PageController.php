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



public function laporan(Request $request)
{
    $mulai    = $request->mulai;
    $sampai   = $request->sampai;
    $kategori = $request->kategori;

    // --- Ambil data presensi + cuti (union builder) ---
    $presensi = DB::table('presensi')
        ->select(
            'presensi.NIK as nik',
            'presensi.tgl_presen as tanggal',
            'presensi.status',
            'presensi.surat',
            DB::raw('TIMESTAMPDIFF(MINUTE, jam_masuk, jam_keluar) as total_menit')
        )
        ->when($mulai && $sampai, function ($q) use ($mulai, $sampai) {
            $q->whereBetween('presensi.tgl_presen', [$mulai, $sampai]);
        });

    $cuti = DB::table('cuti')
        ->select(
            'cuti.nik as nik',
            DB::raw('cuti.tanggal_mulai as tanggal'),
            DB::raw('"cuti" as status'),
            DB::raw('NULL as surat'),
            DB::raw('0 as total_menit')
        )
        ->when($mulai && $sampai, function ($q) use ($mulai, $sampai) {
            $q->where(function ($sub) use ($mulai, $sampai) {
                $sub->whereBetween('cuti.tanggal_mulai', [$mulai, $sampai])
                    ->orWhereBetween('cuti.tanggal_selesai', [$mulai, $sampai]);
            });
        });

    $all = $presensi->unionAll($cuti);

    // --- Rekap: hitung semua status termasuk status='alpha' dari DB (alpha_presensi) ---
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
            DB::raw('SUM(CASE WHEN logs.status = "hadir" THEN 1 ELSE 0 END) as hadir'),
            DB::raw('SUM(CASE WHEN logs.status = "izin" THEN 1 ELSE 0 END) as izin'),
            DB::raw('SUM(CASE WHEN logs.status = "sakit" THEN 1 ELSE 0 END) as sakit'),
            DB::raw('SUM(CASE WHEN logs.status = "cuti" THEN 1 ELSE 0 END) as cuti'),
            // penting: hitung alpha yang memang tercatat di tabel presensi
            DB::raw('SUM(CASE WHEN logs.status = "alpha" THEN 1 ELSE 0 END) as alpha_presensi'),
            DB::raw('SUM(CASE WHEN logs.status = "hadir" THEN logs.total_menit ELSE 0 END) as total_menit'),
            DB::raw('MAX(logs.surat) as surat')
        )
        ->get();

    // --- Hitung total hari kerja pada periode ---
    // Jika user memberikan tanggal, gunakan periode itu. Jika tidak, kita gunakan
    // jumlah tanggal unik di tabel presensi pada bulan berjalan (fallback).
    $totalHari = 0;
    if ($mulai && $sampai) {
        $period = new \DatePeriod(
            new \DateTime($mulai),
            new \DateInterval('P1D'),
            (new \DateTime($sampai))->modify('+1 day')
        );
        foreach ($period as $d) {
            // opsional: exclude weekend (contoh exclude Sundays)
            // jika mau hitung semua hari, hapus if
            if ($d->format('N') != 7) {
                $totalHari++;
            }
        }
    } else {
        // fallback: hitung jumlah distinct tgl_presen di tabel presensi untuk bulan ini
        $startMonth = date('Y-m-01');
        $endMonth = date('Y-m-t');
        $totalHari = DB::table('presensi')
            ->when(true, function($q) use ($startMonth, $endMonth) {
                $q->whereBetween('tgl_presen', [$startMonth, $endMonth]);
            })
            ->distinct()
            ->count('tgl_presen');

        // jika tidak ada pencatatan sama sekali, bisa set default 0 atau 1 — kita pakai 0
        $totalHari = (int)$totalHari;
    }

    // --- Gabungkan alpha dari presensi (alpha_presensi) + alpha kosong (perhitungan hari kerja) ---
    // alpha_kosong = totalHari - (hadir + izin + sakit + cuti)
    foreach ($rekap as $r) {
        $hadir = (int) $r->hadir;
        $izin  = (int) $r->izin;
        $sakit = (int) $r->sakit;
        $cuti  = (int) $r->cuti;
        $alpha_presensi = (int) $r->alpha_presensi;

        $alpha_kosong = max(0, $totalHari - ($hadir + $izin + $sakit + $cuti));
        $r->alpha = $alpha_presensi + $alpha_kosong;

        // pastikan kolom lain ada sesuai view
        $r->total_menit = (int) $r->total_menit;
    }

    // --- Filter kategori setelah alpha terset ---
    if ($kategori && $kategori != 'semua') {
        $rekap = $rekap->filter(function ($row) use ($kategori) {
            // jika kategori = alpha, gunakan kolom 'alpha' (sudah dikalkulasi)
            return (int) ($row->{$kategori} ?? 0) > 0;
        })->values();
    }

    $catatan = CatatanLaporan::pluck('catatan', 'nik');

    // kirim ke view — view kamu mengakses $data, jadi kita tetap kirim 'data'
    return view('laporan', [
        'data' => $rekap,
        'catatan' => $catatan
    ]);
}


private function getData($mulai = null, $sampai = null)
{
    // 1. Tentukan range tanggal
    if (!$mulai || !$sampai) {
        $first = DB::table('presensi')->min('tgl_presen');
        $last  = DB::table('presensi')->max('tgl_presen');

        // Jika database kosong
        if (!$first || !$last) {
            return collect();
        }

        $mulai  = $mulai ?? $first;
        $sampai = $sampai ?? $last;
    }

    // 2. Buat daftar tanggal antara mulai dan sampai
    $dates = collect();
    $period = new \DatePeriod(
        new \DateTime($mulai),
        new \DateInterval('P1D'),
        (new \DateTime($sampai))->modify('+1 day')
    );
    foreach ($period as $date) {
        $dates->push($date->format('Y-m-d'));
    }

    // 3. Ambil semua karyawan
    $karyawan = DB::table('karyawan')->get();

    // Pastikan nama field benar
    $namaField = Schema::hasColumn('karyawan', 'nama_lengkap') ? 'nama_lengkap' : 'nama_karyawan';

    // 4. Buat kombinasi setiap karyawan × tanggal
    $combination = collect();
    foreach ($karyawan as $k) {
        foreach ($dates as $d) {
            $combination->push([
                'nik'     => $k->NIK,
                'tanggal' => $d
            ]);
        }
    }

    // 5. Ambil data presensi
    $presensi = DB::table('presensi')
        ->select(
            'NIK as nik',
            'tgl_presen as tanggal',
            'status',
            DB::raw('TIMESTAMPDIFF(MINUTE, jam_masuk, jam_keluar) as durasi_menit')
        )
        ->whereBetween('tgl_presen', [$mulai, $sampai])
        ->get();

    // 6. Ambil data cuti lalu expand ke setiap tanggal
    $cutiRows = DB::table('cuti')
        ->select('nik', 'tanggal_mulai', 'tanggal_selesai')
        ->when($mulai && $sampai, function ($q) use ($mulai, $sampai) {
            $q->where(function ($sub) use ($mulai, $sampai) {
                $sub->whereBetween('tanggal_mulai', [$mulai, $sampai])
                    ->orWhereBetween('tanggal_selesai', [$mulai, $sampai])
                    ->orWhere(function ($sub2) use ($mulai, $sampai) {
                        $sub2->where('tanggal_mulai', '<=', $mulai)
                             ->where('tanggal_selesai', '>=', $sampai);
                    });
            });
        })
        ->get();

    $cutiExpanded = collect();
    foreach ($cutiRows as $c) {
        if (!$c->tanggal_mulai || !$c->tanggal_selesai) continue;

        $start = $mulai ? max($mulai, $c->tanggal_mulai) : $c->tanggal_mulai;
        $end   = $sampai ? min($sampai, $c->tanggal_selesai) : $c->tanggal_selesai;

        $period = new \DatePeriod(
            new \DateTime($start),
            new \DateInterval('P1D'),
            (new \DateTime($end))->modify('+1 day')
        );

        foreach ($period as $d) {
            $cutiExpanded->push([
                'nik'          => $c->nik,
                'tanggal'      => $d->format('Y-m-d'),
                'status'       => 'cuti',
                'durasi_menit' => 0
            ]);
        }
    }

    // 7. Gabungkan presensi + cuti
    $allLogs = $presensi->map(function ($p) {
        return (array) $p;
    })->merge($cutiExpanded);

    // 8. Masukkan ke kombinasi, isi yang kosong sebagai alpha
    $logs = collect();
    foreach ($combination as $c) {
        $record = $allLogs->first(function ($item) use ($c) {
            return $item['nik'] == $c['nik'] && $item['tanggal'] == $c['tanggal'];
        });

        if ($record) {
            $logs->push($record);
        } else {
            $logs->push([
                'nik'          => $c['nik'],
                'tanggal'      => $c['tanggal'],
                'status'       => 'alpha',
                'durasi_menit' => 0,
            ]);
        }
    }

    // 9. Rekap hasil akhir per karyawan
    $query = $logs->groupBy('nik')->map(function ($items, $nik) use ($karyawan, $namaField) {
        $kar = $karyawan->firstWhere('NIK', $nik);

        return (object) [
            'nik'         => $nik,
            'nama'        => $kar ? $kar->$namaField : '-',
            'total_hari'  => $items->count(),
            'hadir'       => $items->where('status', 'hadir')->count(),
            'sakit'       => $items->where('status', 'sakit')->count(),
            'izin'        => $items->where('status', 'izin')->count(),
            'cuti'        => $items->where('status', 'cuti')->count(),
            'alpha'       => $items->where('status', 'alpha')->count(),
            'total_menit' => $items->where('status', 'hadir')->sum('durasi_menit'),
        ];
    })->values();

    return $query;
}
public function cetakPdf(Request $request)
{
    $mulai    = $request->mulai;
    $sampai   = $request->sampai;
    $kategori = $request->kategori;

    // --- ambil data presensi + cuti ---
    $presensi = DB::table('presensi')
        ->select(
            'nik',
            DB::raw('tgl_presen as tanggal'),
            'status',
            DB::raw('TIMESTAMPDIFF(MINUTE, jam_masuk, jam_keluar) as durasi_menit'),
            DB::raw('1 as durasi_hari')
        )
        ->when($mulai && $sampai, function ($q) use ($mulai, $sampai) {
            $q->whereBetween('tgl_presen', [$mulai, $sampai]);
        });

    $cuti = DB::table('cuti')
        ->select(
            'nik',
            DB::raw('tanggal_mulai as tanggal'),
            DB::raw('"cuti" as status'),
            DB::raw('0 as durasi_menit'),
            DB::raw('DATEDIFF(tanggal_selesai, tanggal_mulai) + 1 as durasi_hari')
        )
        ->when($mulai && $sampai, function ($q) use ($mulai, $sampai) {
            $q->where(function ($sub) use ($mulai, $sampai) {
                $sub->whereBetween('tanggal_mulai', [$mulai, $sampai])
                    ->orWhereBetween('tanggal_selesai', [$mulai, $sampai]);
            });
        });

    $all = $presensi->unionAll($cuti);

    $data = DB::table(DB::raw("({$all->toSql()}) as logs"))
        ->mergeBindings($all)
        ->join('karyawan', 'logs.nik', '=', 'karyawan.NIK')
        ->leftJoin('departement', 'karyawan.id_divisi', '=', 'departement.id_divisi')
        ->leftJoin('jabatan', 'karyawan.id_jabatan', '=', 'jabatan.id_jabatan')
        ->groupBy('karyawan.NIK', 'karyawan.nama_lengkap', 'departement.nama_divisi', 'jabatan.nama_jabatan')
        ->select(
            'karyawan.NIK as nik',
            'karyawan.nama_lengkap as nama',
            'departement.nama_divisi as divisi',
            'jabatan.nama_jabatan as jabatan',
            DB::raw('COUNT(DISTINCT logs.tanggal) as total_hari'),
            DB::raw('SUM(CASE WHEN logs.status = "hadir" THEN 1 ELSE 0 END) as hadir'),
            DB::raw('SUM(CASE WHEN logs.status = "sakit" THEN 1 ELSE 0 END) as sakit'),
            DB::raw('SUM(CASE WHEN logs.status = "izin" THEN 1 ELSE 0 END) as izin'),
            DB::raw('SUM(CASE WHEN logs.status = "cuti" THEN logs.durasi_hari ELSE 0 END) as cuti'),
            DB::raw('SUM(CASE WHEN logs.status = "hadir" THEN logs.durasi_menit ELSE 0 END) as total_menit')
        )
        ->get();

    // total hari kerja
    $totalHari = Carbon::parse($mulai)->diffInDays(Carbon::parse($sampai)) + 1;

    // hitung alpha dan total jam kerja
    foreach ($data as $item) {
        $hadir = DB::table('presensi')
            ->where('NIK', $item->nik)
            ->whereBetween('tgl_presen', [$mulai, $sampai])
            ->where('status', 'Hadir')
            ->count();

        $izin = DB::table('presensi')
            ->where('NIK', $item->nik)
            ->whereBetween('tgl_presen', [$mulai, $sampai])
            ->where('status', 'Izin')
            ->count();

        $sakit = DB::table('presensi')
            ->where('NIK', $item->nik)
            ->whereBetween('tgl_presen', [$mulai, $sampai])
            ->where('status', 'Sakit')
            ->count();

        $cuti = DB::table('cuti')
            ->where('nik', $item->nik)
            ->whereBetween('tanggal_mulai', [$mulai, $sampai])
            ->orWhereBetween('tanggal_selesai', [$mulai, $sampai])
            ->count();

        $alpha = $totalHari - ($hadir + $izin + $sakit + $cuti);
        $item->alpha = max($alpha, 0);

        // hitung total menit kerja dari jam_masuk dan jam_keluar
        $totalMenit = DB::table('presensi')
            ->where('NIK', $item->nik)
            ->whereBetween('tgl_presen', [$mulai, $sampai])
            ->selectRaw('SUM(TIMESTAMPDIFF(MINUTE, jam_masuk, jam_keluar)) as total_menit')
            ->value('total_menit');

        $jam = intdiv($totalMenit ?? 0, 60);
        $menit = ($totalMenit ?? 0) % 60;
        $item->total_jam = ($jam > 0 || $menit > 0)
            ? ($jam > 0 ? "{$jam} Jam {$menit} Menit" : "{$menit} Menit")
            : "0 Menit";
    }

    // filter kategori
    if ($kategori && $kategori != 'semua') {
        $data = $data->filter(function ($row) use ($kategori) {
            if ($kategori === 'alpha') {
                return $row->alpha > 0;
            }
            return isset($row->{$kategori}) && (int) $row->{$kategori} > 0;
        })->values();
    }

    $catatan = CatatanLaporan::pluck('catatan', 'nik')->toArray();

    $pdf = PDF::loadView('laporan_pdf', [
        'data'     => $data,
        'catatan'  => $catatan,
        'kategori' => $kategori ?? null,
        'mulai'    => $mulai,
        'sampai'   => $sampai,
    ])->setPaper('a4', 'portrait');

    return $pdf->download('laporan.pdf');
}


public function exportExcel(Request $request)
{
    $mulai    = $request->mulai;
    $sampai   = $request->sampai;
    $kategori = $request->kategori;

    $presensi = DB::table('presensi')
        ->select(
            'nik',
            DB::raw('tgl_presen as tanggal'),
            'status',
            DB::raw('TIMESTAMPDIFF(MINUTE, jam_masuk, jam_keluar) as durasi_menit'),
            DB::raw('1 as durasi_hari')
        )
        ->when($mulai && $sampai, function ($q) use ($mulai, $sampai) {
            $q->whereBetween('tgl_presen', [$mulai, $sampai]);
        });

    $cuti = DB::table('cuti')
        ->select(
            'nik',
            DB::raw('tanggal_mulai as tanggal'),
            DB::raw('"cuti" as status'),
            DB::raw('0 as durasi_menit'),
            DB::raw('DATEDIFF(tanggal_selesai, tanggal_mulai) + 1 as durasi_hari')
        )
        ->when($mulai && $sampai, function ($q) use ($mulai, $sampai) {
            $q->where(function ($sub) use ($mulai, $sampai) {
                $sub->whereBetween('tanggal_mulai', [$mulai, $sampai])
                    ->orWhereBetween('tanggal_selesai', [$mulai, $sampai]);
            });
        });

    $all = $presensi->unionAll($cuti);

    $data = DB::table(DB::raw("({$all->toSql()}) as logs"))
        ->mergeBindings($all)
        ->join('karyawan', 'logs.nik', '=', 'karyawan.NIK')
        ->leftJoin('departement', 'karyawan.id_divisi', '=', 'departement.id_divisi')
        ->leftJoin('jabatan', 'karyawan.id_jabatan', '=', 'jabatan.id_jabatan')
        ->groupBy('karyawan.NIK', 'karyawan.nama_lengkap', 'departement.nama_divisi', 'jabatan.nama_jabatan')
        ->select(
            'karyawan.NIK as nik',
            'karyawan.nama_lengkap as nama',
            'departement.nama_divisi as divisi',
            'jabatan.nama_jabatan as jabatan',
            DB::raw('COUNT(DISTINCT logs.tanggal) as total_hari'),
            DB::raw('SUM(CASE WHEN logs.status = "hadir" THEN 1 ELSE 0 END) as hadir'),
            DB::raw('SUM(CASE WHEN logs.status = "sakit" THEN 1 ELSE 0 END) as sakit'),
            DB::raw('SUM(CASE WHEN logs.status = "izin" THEN 1 ELSE 0 END) as izin'),
            DB::raw('SUM(CASE WHEN logs.status = "cuti" THEN logs.durasi_hari ELSE 0 END) as cuti'),
            DB::raw('SUM(CASE WHEN logs.status = "hadir" THEN logs.durasi_menit ELSE 0 END) as total_menit')
        )
        ->get();

    $totalHari = Carbon::parse($mulai)->diffInDays(Carbon::parse($sampai)) + 1;

    foreach ($data as $item) {
        $hadir = DB::table('presensi')->where('NIK', $item->nik)->whereBetween('tgl_presen', [$mulai, $sampai])->where('status', 'Hadir')->count();
        $izin  = DB::table('presensi')->where('NIK', $item->nik)->whereBetween('tgl_presen', [$mulai, $sampai])->where('status', 'Izin')->count();
        $sakit = DB::table('presensi')->where('NIK', $item->nik)->whereBetween('tgl_presen', [$mulai, $sampai])->where('status', 'Sakit')->count();
        $cuti  = DB::table('cuti')->where('nik', $item->nik)->whereBetween('tanggal_mulai', [$mulai, $sampai])->orWhereBetween('tanggal_selesai', [$mulai, $sampai])->count();

        $alpha = $totalHari - ($hadir + $izin + $sakit + $cuti);
        $item->alpha = max($alpha, 0);

        $totalMenit = DB::table('presensi')
            ->where('NIK', $item->nik)
            ->whereBetween('tgl_presen', [$mulai, $sampai])
            ->selectRaw('SUM(TIMESTAMPDIFF(MINUTE, jam_masuk, jam_keluar)) as total_menit')
            ->value('total_menit');

        $jam = intdiv($totalMenit ?? 0, 60);
        $menit = ($totalMenit ?? 0) % 60;
        $item->total_jam = ($jam > 0 || $menit > 0)
            ? ($jam > 0 ? "{$jam} Jam {$menit} Menit" : "{$menit} Menit")
            : "0 Menit";
    }

    if ($kategori && $kategori != 'semua') {
        $data = $data->filter(function ($row) use ($kategori) {
            if ($kategori === 'alpha') {
                return $row->alpha > 0;
            }
            return isset($row->{$kategori}) && (int) $row->{$kategori} > 0;
        })->values();
    }

    $catatan = CatatanLaporan::pluck('catatan', 'nik')->toArray();

    return Excel::download(new class($data, $catatan) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
        private $data;
        private $catatan;
        public function __construct($data, $catatan)
        {
            $this->data = $data;
            $this->catatan = $catatan;
        }

        public function collection()
        {
            return $this->data->map(function ($row) {
                return [
                    'NIK'        => $row->nik,
                    'Nama'       => $row->nama,
                    'Divisi'     => $row->divisi ?? '-',
                    'Jabatan'    => $row->jabatan ?? '-',
                    'Total Hari' => $row->total_hari,
                    'Hadir'      => $row->hadir,
                    'Sakit'      => $row->sakit,
                    'Izin'       => $row->izin,
                    'Cuti'       => $row->cuti,
                    'Alpha'      => $row->alpha,
                    'Total Jam'  => $row->total_jam,
                    'Catatan'    => $this->catatan[$row->nik] ?? '-',
                ];
            });
        }

        public function headings(): array
        {
            return ['NIK','Nama','Divisi','Jabatan','Total Hari','Hadir','Sakit','Izin','Cuti','Alpha','Total Jam','Catatan'];
        }
    }, 'laporan.xlsx');
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
        return redirect()->route('login.form')->with('error', 'Silahkan login terlebih dahulu.');
    }

    $request->validate([
        'tgl_presen' => 'required|date',
        'surat' => 'nullable|file|mimes:jpeg,jpg,png,heic,pdf|max:2048',
    ]);

    $filePath = null;
    if ($request->hasFile('surat')) {
        $filePath = $request->file('surat')->store('surat', 'public');
    }

    // pakai DB::table agar konsisten dengan style di controllermu
    DB::table('presensi')->insert([
        'NIK' => $karyawan->NIK,
        'nama_karyawan' => $karyawan->nama_lengkap,
        'divisi' => $karyawan->nama_divisi ?? ($karyawan->divisi ?? '-'),
        'tgl_presen' => $request->tgl_presen,
        'jam_masuk' => null,
        'lokasi_masuk' => null,
        'jam_keluar' => null,
        'lokasi_keluar' => null,
        'status' => 'sakit',
        'surat' => $filePath,
    ]);

    return redirect()->route('karyawan.dashboard')->with('success', 'Pengajuan sakit berhasil dikirim!');
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

    $query = TrackingSales::where('NIK', $karyawan->NIK)->orderBy('tanggal_sales', 'desc');

    if ($request->filled('mulai') && $request->filled('sampai')) {
        $query->whereBetween('tanggal_sales', [$request->mulai, $request->sampai]);
    }

    $tracking = $query->get();

    return view('karyawan.trackingSalesHistory', compact('tracking'));
}
public function trackingAdmin()
{
    // Ambil data tracking sales dengan relasi karyawan
    $trackings = TrackingSales::with('karyawan')
        ->orderBy('tanggal_sales', 'desc')
        ->paginate(10);

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



