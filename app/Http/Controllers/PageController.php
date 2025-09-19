<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Karyawan;
use App\Akun;
use App\Department;
use App\Jabatan;
use App\Absensi;
use App\CatatanLaporan;



class PageController extends Controller
{
    /** ---------------- DASHBOARD ---------------- */
   public function dashboard()
{
    $totalKaryawan = DB::table('karyawan')->count();

    // ================= Harian =================
    $harianMasuk = DB::table('presensi')
        ->whereDate('tgl_presen', Carbon::today())
        ->where('status', 'hadir')
        ->count();

    $harianIzin = DB::table('presensi')
        ->whereDate('tgl_presen', Carbon::today())
        ->where('status', 'izin')
        ->count();

    $harianCuti = DB::table('presensi')
        ->whereDate('tgl_presen', Carbon::today())
        ->where('status', 'cuti')
        ->count();

    $harianSakit = DB::table('presensi')
        ->whereDate('tgl_presen', Carbon::today())
        ->where('status', 'sakit')
        ->count();

    // Alpha = total karyawan - (hadir + izin + cuti + sakit)
    $harianAlpha = $totalKaryawan - ($harianMasuk + $harianIzin + $harianCuti + $harianSakit);

    // ================= Mingguan =================
    $mingguanMasuk = DB::table('presensi')
        ->whereBetween('tgl_presen', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
        ->where('status', 'hadir')
        ->count();

    $mingguanIzin = DB::table('presensi')
        ->whereBetween('tgl_presen', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
        ->where('status', 'izin')
        ->count();

    $mingguanCuti = DB::table('presensi')
        ->whereBetween('tgl_presen', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
        ->where('status', 'cuti')
        ->count();

    $mingguanSakit = DB::table('presensi')
        ->whereBetween('tgl_presen', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
        ->where('status', 'sakit')
        ->count();

    $mingguanAlpha = $totalKaryawan - ($mingguanMasuk + $mingguanIzin + $mingguanCuti + $mingguanSakit);

    // ================= Bulanan =================
    $bulananMasuk = DB::table('presensi')
        ->whereMonth('tgl_presen', Carbon::now()->month)
        ->whereYear('tgl_presen', Carbon::now()->year)
        ->where('status', 'hadir')
        ->count();

    $bulananIzin = DB::table('presensi')
        ->whereMonth('tgl_presen', Carbon::now()->month)
        ->whereYear('tgl_presen', Carbon::now()->year)
        ->where('status', 'izin')
        ->count();

    $bulananCuti = DB::table('presensi')
        ->whereMonth('tgl_presen', Carbon::now()->month)
        ->whereYear('tgl_presen', Carbon::now()->year)
        ->where('status', 'cuti')
        ->count();

    $bulananSakit = DB::table('presensi')
        ->whereMonth('tgl_presen', Carbon::now()->month)
        ->whereYear('tgl_presen', Carbon::now()->year)
        ->where('status', 'sakit')
        ->count();

    $bulananAlpha = $totalKaryawan - ($bulananMasuk + $bulananIzin + $bulananCuti + $bulananSakit);

    return view('dashboard', compact(
        'totalKaryawan',
        'harianMasuk', 'harianIzin', 'harianCuti', 'harianSakit', 'harianAlpha',
        'mingguanMasuk', 'mingguanIzin', 'mingguanCuti', 'mingguanSakit', 'mingguanAlpha',
        'bulananMasuk', 'bulananIzin', 'bulananCuti', 'bulananSakit', 'bulananAlpha'
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
    $foto = $request->file('foto');
    $namaFile = time() . '_' . $foto->getClientOriginalName();
    $foto->move(public_path('uploads'), $namaFile);
    $dataUpdate['foto'] = $namaFile; // simpan nama file ke DB
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

    // Ambil data karyawan + nama_divisi + nama_jabatan
    $karyawan = DB::table('karyawan')
    ->leftJoin('departement', 'karyawan.id_divisi', '=', 'departement.id_divisi')
    ->where('karyawan.NIK', $akun->NIK)
    ->select('karyawan.*', 'departement.nama_divisi')
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
        'foto'        => 'nullable|image|max:2048',
    ]);

    $fotoName = null;
    if ($request->hasFile('foto')) {
        $fotoName = time() . '.' . $request->foto->extension();
        $request->foto->move(public_path('uploads'), $fotoName);
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
        'foto'         => $fotoName,
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

 /** ---------------- LAPORAN ---------------- */
 public function laporan(Request $request)
 {
     $data = $this->getData($request->mulai, $request->sampai);

     // Ambil catatan yang sudah ada berdasarkan NIK
     $catatan = CatatanLaporan::pluck('catatan', 'nik')->toArray();

     return view('laporan', compact('data', 'catatan'));
 }

 public function cetakPdf(Request $request)
 {
     $data = $this->getData($request->mulai, $request->sampai);
     $catatan = CatatanLaporan::pluck('catatan', 'nik')->toArray();

     $pdf  = PDF::loadView('laporan_pdf', [
         'data'    => $data,
         'catatan' => $catatan
     ]);

     return $pdf->download('laporan.pdf');
 }

 public function exportExcel(Request $request)
 {
     $data = $this->getData($request->mulai, $request->sampai);
     $catatan = CatatanLaporan::pluck('catatan', 'nik')->toArray();

     return Excel::download(new class($data, $catatan) implements \Maatwebsite\Excel\Concerns\FromCollection {
         private $data;
         private $catatan;

         public function __construct($data, $catatan)
         {
             $this->data = $data;
             $this->catatan = $catatan;
         }

         public function collection()
         {
             $header = collect([[
                 'NIK','Nama','Divisi','Total Hari Kerja',
                 'Jumlah Hadir','Jumlah Sakit','Jumlah Izin',
                 'Jumlah Alpha','Catatan'
             ]]);

             $rows = $this->data->map(function ($row) {
                 return [
                     $row->nik,
                     $row->nama,
                     $row->divisi ?? '-',
                     $row->total_hari,
                     $row->hadir,
                     $row->sakit,
                     $row->izin,
                     $row->alpha,
                     $this->catatan[$row->nik] ?? '-',
                 ];
             });

             return $header->merge($rows);
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
        Mail::raw("Klik link berikut untuk reset password: " . url('/reset-password/' . $token), function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Reset Password');
        });

        return back()->with('success', 'Link reset password sudah dikirim ke email.');
    }

    public function showResetForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
            'token' => 'required'
        ]);

        $reset = DB::table('password_resets')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$reset) {
            return back()->withErrors(['email' => 'Token tidak valid atau sudah kadaluarsa.']);
        }

        // update password di tabel akun (pakai NIK dari karyawan)
        $karyawan = DB::table('karyawan')->where('email', $request->email)->first();
        if (!$karyawan) {
            return back()->withErrors(['email' => 'Karyawan tidak ditemukan.']);
        }

        DB::table('akun')->where('NIK', $karyawan->NIK)->update([
            'password' => Hash::make($request->password)
        ]);

        // hapus token biar tidak bisa dipakai lagi
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
    public function dashboardKaryawan()
{
    $karyawan = session('karyawan'); // ambil dari session
    if (!$karyawan) {
        return redirect()->route('login.form');
    }

    $nik = $karyawan->NIK;

    $presensiHariIni = DB::table('presensi')
        ->where('NIK', $nik)
        ->whereDate('tgl_presen', now()->toDateString())
        ->first();

    $riwayat = DB::table('presensi')
        ->where('NIK', $nik)
        ->orderBy('tgl_presen', 'desc')
        ->limit(7)
        ->get();

    // 🔑 kirim $karyawan ke view
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
        'status' => 'required|in:hadir,sakit,izin,alpha',
    ]);

    DB::table('presensi')
        ->where('id_presen', $id)
        ->update([
            'status'     => $request->status,
            // 'updated_at' => now(),
        ]);

    return redirect()->back()->with('success', 'Status presensi berhasil diupdate!');
}

public function showPresensiAdmin()
{
    $riwayat = DB::table('presensi')
        ->orderBy('tgl_presen', 'desc')
        ->take(10) // ambil 10 riwayat terakhir
        ->get();

    return view('admin.presensi', compact('riwayat'));
}


}
