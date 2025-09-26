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
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Karyawan;
use App\Presensi;
use App\Akun;
use App\Department;
use App\Jabatan;
use App\CatatanLaporan;
use App\Cuti;



class PageController extends Controller
{
    /** ---------------- DASHBOARD ---------------- */
    public function dashboard()
    {
        $totalKaryawan = DB::table('karyawan')->count();

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

        return view('dashboard', compact(
            'totalKaryawan',
            'harianMasuk','harianIzin','harianSakit','harianCuti','harianAlpha',
            'mingguanMasuk','mingguanIzin','mingguanSakit','mingguanCuti','mingguanAlpha',
            'bulananMasuk','bulananIzin','bulananSakit','bulananCuti','bulananAlpha'
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

    // Urut terbaru dulu, paginate, dan pertahankan query string
    $presensis = $query->orderBy('presensi.tgl_presen', 'desc')
        ->paginate(10)
        ->appends($request->all());

    // Data dropdown divisi
    $departements = DB::table('departement')
        ->select('id_divisi', 'nama_divisi')
        ->get();

    return view('daftarPresensi', compact('presensis', 'departements'));
}


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

    // Ambil data dengan pagination
    $karyawan = $query->paginate(10)->appends($request->all());

    // Ambil semua divisi untuk dropdown filter
    $departements = DB::table('departement')->select('id_divisi', 'nama_divisi')->get();

    return view('daftarKaryawan', compact('karyawan', 'departements'));
}

/** ---------------- DATA KARYAWAN ---------------- */
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
        $karyawan    = DB::table('karyawan')->where('NIK', $nik)->first();
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
            'status'     => $request->status,
            'role'       => 'karyawan', // 👈 dipaksa tetap karyawan
        ];

        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $namaFile = time() . '_' . $foto->getClientOriginalName();
            $foto->move(public_path('uploads'), $namaFile);
            $dataUpdate['foto'] = $namaFile;
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

        // Ambil akun + role karyawan
        $akun = DB::table('akun')
        ->join('karyawan', 'akun.NIK', '=', 'karyawan.NIK')
        ->where('akun.username', $request->username)
        ->select(
            'akun.username',
            'akun.password',
            'karyawan.NIK',
            'karyawan.role',
            'karyawan.nama_lengkap',
            'karyawan.id_divisi',
            'karyawan.id_jabatan',
            'karyawan.foto' // ✅ tambahkan ini
        )
        ->first();

        if (!$akun || !Hash::check($request->password, $akun->password)) {
            return back()->withErrors(['login' => 'Username atau password salah.']);
        }

        // Simpan ke session (selalu satu key saja)
        session(['karyawan' => $akun]);

        // Redirect sesuai role
        if ($akun->role === 'admin') {
            return redirect()->route('dashboard')->with('success', 'Login berhasil sebagai Admin');
        }

        return redirect()->route('karyawan.dashboard')->with('success', 'Login berhasil sebagai Karyawan');
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
        'nik'         => 'required|unique:karyawan,NIK',
        'username'    => 'required|unique:akun,username',
        'password'    => 'required|min:6',
        'email'       => 'required|email|unique:karyawan,email',
        'id_divisi'   => 'required|integer',
        'id_jabatan'  => 'required|integer',
        'nama_lengkap'=> 'required|string',
        'no_hp'       => 'required|string',
        'tgl_lahir'   => 'required|date',
        'alamat'      => 'required|string',
        'role'        => 'required|string',
        'foto'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

  // default foto kosong
    $fotoPath = null;

    // kalau ada file yang diupload
    if ($request->hasFile('foto')) {
        // simpan ke storage/app/public/foto
        $fotoPath = $request->file('foto')->store('foto', 'public');
    }

    // Simpan data ke tabel karyawan (tambahkan status default = Aktif)
    DB::table('karyawan')->insert([
        'NIK'          => $request->nik,
        'username'     => $request->username,
        'email'        => $request->email,
        'id_divisi'    => $request->id_divisi,
        'id_jabatan'   => $request->id_jabatan,
        'nama_lengkap' => $request->nama_lengkap,
        'no_hp'        => $request->no_hp,
        'tgl_lahir'    => $request->tgl_lahir,
        'alamat'       => $request->alamat,
        'role'         => $request->role,
        'foto'         => $fotoPath,
        'status'       => 'Aktif', // <- tambahkan ini
        // 'created_at'   => now(),
        // 'updated_at'   => now(),
    ]);

    // Simpan data ke tabel akun
    DB::table('akun')->insert([
        'username'   => $request->username,
        'NIK'        => $request->nik,
        'password'   => Hash::make($request->password),
        // 'created_at' => now(),
        // 'updated_at' => now(),
    ]);

    return redirect()->route('login.form')->with('success', 'Registrasi berhasil! Silakan login.');
}


    /** ---------------- PRESENSI ---------------- */
    public function PresensiKaryawan()
    {
        return view('PresensiKaryawan');
    }



 /** ---------------- LAPORAN ---------------- */
 public function laporan(Request $request)
 {
     $mulai    = $request->mulai;
     $sampai   = $request->sampai;
     $kategori = $request->kategori;

     // Ambil data presensi
     $presensi = DB::table('presensi')
         ->select(
             'NIK as nik',
             'tgl_presen as tanggal',
             'status',
             DB::raw('TIMESTAMPDIFF(MINUTE, jam_masuk, jam_keluar) as durasi_menit')
         )
         ->when($mulai && $sampai, function ($q) use ($mulai, $sampai) {
             $q->whereBetween('tgl_presen', [$mulai, $sampai]);
         });

     // Ambil data cuti
     $cuti = DB::table('cuti')
         ->select(
             'nik',
             'tanggal_mulai as tanggal',
             DB::raw('"cuti" as status'),
             DB::raw('0 as durasi_menit')
         )
         ->when($mulai && $sampai, function ($q) use ($mulai, $sampai) {
             $q->where(function ($sub) use ($mulai, $sampai) {
                 $sub->whereBetween('tanggal_mulai', [$mulai, $sampai])
                     ->orWhereBetween('tanggal_selesai', [$mulai, $sampai]);
             });
         });

     // Gabungkan presensi + cuti
     $all = $presensi->unionAll($cuti);

     // Hitung rekap awal
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
             DB::raw('SUM(CASE WHEN logs.status = "cuti" THEN 1 ELSE 0 END) as cuti'),
             DB::raw('SUM(CASE WHEN logs.status = "hadir" AND logs.durasi_menit IS NOT NULL
                           THEN logs.durasi_menit ELSE 0 END) as total_menit')
         )
         ->get();

     // Hitung total hari kerja (bisa exclude weekend kalau mau)
     $totalHari = 0;
     if ($mulai && $sampai) {
         $period = new \DatePeriod(
             new \DateTime($mulai),
             new \DateInterval('P1D'),
             (new \DateTime($sampai))->modify('+1 day')
         );

         foreach ($period as $date) {
             // Kalau mau exclude Sabtu & Minggu, uncomment baris ini:
             // if (in_array($date->format('N'), [6, 7])) continue;

             $totalHari++;
         }
     }

     // Tambahkan perhitungan alpha
     foreach ($data as $item) {
         $item->alpha = $totalHari - ($item->hadir + $item->izin + $item->sakit + $item->cuti);
         if ($item->alpha < 0) {
             $item->alpha = 0; // jaga-jaga biar nggak minus
         }
     }

     // Filter kategori kalau dipilih
     if ($kategori && $kategori != 'semua') {
         $data = $data->filter(function ($row) use ($kategori) {
             return $row->{$kategori} > 0;
         })->values();
     }

     $catatan = CatatanLaporan::pluck('catatan', 'nik');

     return view('laporan', compact('data', 'catatan'));
 }

 private function getData($mulai = null, $sampai = null)
 {
     // 1. Buat daftar tanggal kerja
     $dates = collect();
     if ($mulai && $sampai) {
         $period = new \DatePeriod(
             new \DateTime($mulai),
             new \DateInterval('P1D'),
             (new \DateTime($sampai))->modify('+1 day')
         );
         foreach ($period as $date) {
             $dates->push($date->format('Y-m-d'));
         }
     }

     // 2. Ambil semua karyawan
     $karyawan = DB::table('karyawan')->get();

     // Pastikan ada kolom nama_lengkap atau nama
     $namaField = Schema::hasColumn('karyawan', 'nama_lengkap') ? 'nama_lengkap' : 'nama';

     // 3. Buat kombinasi karyawan × tanggal
     $combination = collect();
     foreach ($karyawan as $k) {
         foreach ($dates as $d) {
             $combination->push([
                 'nik'     => $k->NIK,
                 'tanggal' => $d
             ]);
         }
     }

     // 4. Ambil data presensi
     $presensi = DB::table('presensi')
         ->select(
             'NIK as nik',
             'tgl_presen as tanggal',
             'status',
             DB::raw('TIMESTAMPDIFF(MINUTE, jam_masuk, jam_keluar) as durasi_menit')
         )
         ->when($mulai && $sampai, function ($q) use ($mulai, $sampai) {
             $q->whereBetween('tgl_presen', [$mulai, $sampai]);
         })
         ->get();

     // 5. Ambil data cuti → expand ke tanggal per hari
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

     // 6. Gabungkan presensi + cuti
     $allLogs = $presensi->map(function ($p) {
         return (array) $p;
     })->merge($cutiExpanded);

     // 7. Masukkan ke dalam combination (cek data yang kosong → alpha)
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

     // 8. Rekap hasilnya
     $query = $logs->groupBy('nik')->map(function ($items, $nik) use ($karyawan, $namaField) {
         $kar = $karyawan->firstWhere('NIK', $nik);

         return (object) [
             'nik'        => $nik,
             'nama'       => $kar ? $kar->$namaField : '-',
             'total_hari' => $items->count(),
             'hadir'      => $items->where('status', 'hadir')->count(),
             'sakit'      => $items->where('status', 'sakit')->count(),
             'izin'       => $items->where('status', 'izin')->count(),
             'cuti'       => $items->where('status', 'cuti')->count(),
             'alpha'      => $items->where('status', 'alpha')->count(),
             'total_menit'=> $items->where('status', 'hadir')->sum('durasi_menit'),
         ];
     })->values();

     return $query;
 }


public function cetakPdf(Request $request)
{
    $data = $this->getData($request->mulai, $request->sampai);

    if ($request->kategori) {
        $kategori = $request->kategori;
        $data = $data->filter(function ($row) use ($kategori) {
            return isset($row->{$kategori}) && (int) $row->{$kategori} > 0;
        })->values();
    }

    $catatan = CatatanLaporan::pluck('catatan', 'nik')->toArray();

    // 🔹 Tambahkan format jam hanya jika kategori = hadir
    foreach ($data as $row) {
        if ($request->kategori == 'hadir') {
            $totalMenit = (int) ($row->total_menit ?? 0);
            $jam = intdiv($totalMenit, 60);
            $menit = $totalMenit % 60;

            if ($jam > 0 && $menit > 0) {
                $row->total_jam = "{$jam} Jam {$menit} Menit";
            } elseif ($jam > 0) {
                $row->total_jam = "{$jam} Jam";
            } elseif ($menit > 0) {
                $row->total_jam = "{$menit} Menit";
            } else {
                $row->total_jam = "0 Menit";
            }
        } else {
            $row->total_jam = null; // ✅ kosong kalau bukan hadir
        }
    }

    $pdf = PDF::loadView('laporan_pdf', [
        'data'     => $data,
        'catatan'  => $catatan,
        'kategori' => $request->kategori ?? null,
    ]);

    return $pdf->download('laporan.pdf');
}




public function exportExcel(Request $request)
{
    $data = $this->getData($request->mulai, $request->sampai);

    if ($request->kategori) {
        $kategori = $request->kategori;
        $data = $data->filter(function ($row) use ($kategori) {
            return isset($row->{$kategori}) && (int) $row->{$kategori} > 0;
        })->values();
    }

    $catatan = CatatanLaporan::pluck('catatan', 'nik')->toArray();
    $kategori = $request->kategori;

    return Excel::download(new class($data, $catatan, $kategori) implements \Maatwebsite\Excel\Concerns\FromCollection {
        private $data;
        private $catatan;
        private $kategori;

        public function __construct($data, $catatan, $kategori)
        {
            $this->data = $data;
            $this->catatan = $catatan;
            $this->kategori = $kategori;
        }

        public function collection()
        {
            // 🔹 Header dinamis
            $header = [
                'NIK','Nama','Divisi','Total Hari Kerja',
                'Jumlah Hadir','Jumlah Sakit','Jumlah Izin',
                'Jumlah Cuti','Jumlah Alpha'
            ];

            if ($this->kategori == 'hadir') {
                $header[] = 'Total Jam Kerja';
            }

            $header[] = 'Catatan';

            $rows = $this->data->map(function ($row) {
                $base = [
                    $row->nik,
                    $row->nama,
                    $row->divisi ?? '-',
                    $row->total_hari,
                    $row->hadir,
                    $row->sakit,
                    $row->izin,
                    $row->cuti,
                    $row->alpha,
                ];

                // 🔹 Tambahkan total jam hanya untuk kategori hadir
                if ($this->kategori == 'hadir') {
                    $totalMenit = (int) ($row->total_menit ?? 0);
                    $jam = intdiv($totalMenit, 60);
                    $menit = $totalMenit % 60;

                    if ($jam > 0 && $menit > 0) {
                        $totalJam = "{$jam} Jam {$menit} Menit";
                    } elseif ($jam > 0) {
                        $totalJam = "{$jam} Jam";
                    } elseif ($menit > 0) {
                        $totalJam = "{$menit} Menit";
                    } else {
                        $totalJam = "0 Menit";
                    }

                    $base[] = $totalJam;
                }

                $base[] = $this->catatan[$row->nik] ?? '-';

                return $base;
            });

            return collect([$header])->merge($rows);
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
        $sessionKaryawan = session('karyawan');

        if (!$sessionKaryawan) {
            return redirect()->route('login.form');
        }

        // Ambil data lengkap dari DB (termasuk foto)
        $karyawan = DB::table('karyawan')
            ->leftJoin('departement', 'karyawan.id_divisi', '=', 'departement.id_divisi')
            ->leftJoin('jabatan', 'karyawan.id_jabatan', '=', 'jabatan.id_jabatan')
            ->select('karyawan.*', 'departement.nama_divisi', 'jabatan.nama_jabatan')
            ->where('karyawan.NIK', $sessionKaryawan->NIK)
            ->first();

        // Presensi hari ini
        $presensiHariIni = DB::table('presensi')
            ->where('NIK', $karyawan->NIK)
            ->whereDate('tgl_presen', now()->toDateString())
            ->first();

        // Riwayat presensi 7 hari terakhir
        $riwayat = DB::table('presensi')
            ->where('NIK', $karyawan->NIK)
            ->orderBy('tgl_presen', 'desc');

        // Cek apakah ada filter tanggal
        if ($request->filled('mulai') && $request->filled('sampai')) {
            $riwayat->whereBetween('tgl_presen', [$request->mulai, $request->sampai]);
        } else {
            // Default: tampilkan 7 hari terakhir
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
        ->leftJoin('jabatan', 'karyawan.id_jabatan', '=', 'jabatan.id_jabatan')
        ->select('karyawan.*', 'departement.nama_divisi', 'jabatan.nama_jabatan')
        ->where('karyawan.NIK', $sessionKaryawan->NIK)
        ->first();

    return view('showKaryawan', [
        'karyawan' => $karyawan,
        'title'    => 'Profile'
    ]);
}

// Edit Presensi
public function editPresensi($id)
{
    $presensi = DB::table('presensi')
        ->join('karyawan', 'presensi.NIK', '=', 'karyawan.NIK')
        ->select('presensi.*', 'karyawan.nama_lengkap')
        ->where('presensi.id_presen', $id)
        ->first();

    if (!$presensi) {
        return redirect()->route('daftarPresensi')->with('error', 'Data presensi tidak ditemukan.');
    }

    return view('editPresensi', compact('presensi'));
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
    $karyawan = Karyawan::orderBy('nama_lengkap')->get(); // urutkan biar rapi
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



