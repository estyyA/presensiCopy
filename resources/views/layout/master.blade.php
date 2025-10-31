<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Dashboard')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap 4 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- FontAwesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    {{-- Select2 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }

        .main-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* === SIDEBAR FIXED === */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 240px;
            height: 100vh;
            background: linear-gradient(180deg, #0d47a1, #001f54);
            color: white;
            padding: 20px 15px;
            overflow-y: auto;
            transition: all 0.3s ease;
        }
        .sidebar .brand {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 25px;
        }
        .sidebar .brand img {
            width: 50px;
            margin-right: 10px;
        }
        .sidebar .brand span {
            font-weight: bold;
            font-size: 18px;
            color: #fff;
        }
        .sidebar .nav-link {
            color: #dcdcdc;
            margin: 6px 0;
            font-weight: 500;
            padding: 10px 12px;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            transform: translateX(5px);
        }
        .sidebar .nav-link.active {
            background: rgba(255, 255, 255, 0.25);
            color: #fff;
        }
        .sidebar h6 {
            margin: 15px 0 8px;
            font-size: 12px;
            text-transform: uppercase;
            color: #aaa;
            letter-spacing: 1px;
        }

        /* === TOPBAR === */
        .topbar {
            height: 60px;
            background: #fff;
            border-bottom: 1px solid #eee;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .topbar .dropdown-toggle {
            font-weight: 600;
        }
        .topbar img {
            border: 2px solid #eee;
        }

        /* === MAIN CONTENT === */
        .main-content {
            margin-left: 240px;   /* geser karena sidebar fixed */
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 100vh;
        }

        .content {
            padding: 25px;
            background: #fff;
            width: 100%;
            flex: 1;
            box-sizing: border-box;
        }

        /* === FOOTER === */
        footer {
            background: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            border-top: 1px solid #ffffff;
            padding: 10px;
            text-align: center;
            font-size: 14px;
            color: #666;
        }

        /* === CARD STATS === */
        .card-stat {
            border-radius: 14px;
            text-align: center;
            padding: 25px 15px;
            font-weight: 600;
            transition: all 0.2s;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .card-stat:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .card-stat i {
            font-size: 28px;
            margin-bottom: 10px;
            color: #0d47a1;
        }

        /* Scrollbar Custom */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(0,0,0,0.2);
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>

<div class="main-wrapper">
    {{-- Sidebar --}}
    <div class="sidebar d-flex flex-column">
        <div class="brand">
            <img src="{{ asset('img/logo.png') }}" alt="Logo">
            <span>PT Madubaru</span>
        </div>

        <h6>Master Menu</h6>
        <a href="{{ url('/dashboard') }}" class="nav-link @if(Request::is('dashboard')) active @endif">
            <i class="fa fa-home mr-2"></i> Dashboard
        </a>
        <a href="{{ url('/daftarKaryawan') }}" class="nav-link @if(Request::is('daftarKaryawan')) active @endif">
            <i class="fa fa-users mr-2"></i> Data Karyawan
        </a>
        <a href="{{ url('/daftarPresensi') }}" class="nav-link @if(Request::is('daftarPresensi')) active @endif">
            <i class="fa fa-clipboard-list mr-2"></i> Data Presensi
        </a>
        <a href="{{ url('/laporan') }}" class="nav-link @if(Request::is('laporan*')) active @endif">
            <i class="fa fa-file-alt mr-2"></i> Laporan
        </a>
        <a href="{{ route('admin.presensi.form') }}" class="nav-link @if(Request::is('admin/presensi*')) active @endif">
            <i class="fa fa-clock mr-2"></i> Presensi
        </a>
        <a href="{{ route('konfirsakit') }}" class="nav-link @if(Request::is('konfirsakit*')) active @endif">
            <i class="fa fa-notes-medical mr-2"></i> Konfirmasi Sakit
        </a>
        <a href="{{ route('cuti.index') }}" class="nav-link @if(Request::is('cuti*')) active @endif">
            <i class="fa fa-calendar-check mr-2"></i> Input Cuti
        </a>
        <a href="{{ route('tracking.admin') }}"
        class="nav-link @if(Request::is('tracking/admin*')) active @endif">
            <i class="fa fa-map-marker-alt mr-2"></i> Pelacakan Lapangan
         </a>
    </div>

    {{-- Main Content --}}
    <div class="main-content">
        {{-- Topbar --}}
        <div class="topbar">
            <div class="dropdown">
                <a class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle"
                   href="#" id="userDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                   <span class="mr-2">
                        {{ session('karyawan')->nama_lengkap ?? 'Guest' }}
                   </span>

                   {{-- Ganti avatar bawaan dengan icon dari Icons8 --}}
                   <img src="https://img.icons8.com/ios-filled/40/0D8ABC/user.png"
                        class="rounded-circle bg-white p-1"
                        width="35" height="35" alt="user icon">

                </a>
                <div class="dropdown-menu dropdown-menu-right shadow" aria-labelledby="userDropdown">
                    <a class="dropdown-item" href="{{ route('profil') }}">
                        <i class="fa fa-user mr-2"></i> Profil
                    </a>
                    <div class="dropdown-divider"></div>
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="fa fa-sign-out-alt mr-2"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>


        {{-- Content --}}
        <div class="content">
            @yield('content')
        </div>

        {{-- Footer --}}
        <footer>
            <small>© {{ date('Y') }} PT Madubaru - Sistem Presensi Develop By Asya, Esty, Esra, Sugi, Yayan</small>
        </footer>
    </div>
</div>

{{-- Bootstrap JS --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

{{-- Select2 JS --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "-- Cari Karyawan --",
            allowClear: true,
            width: '100%'
        });
    });
</script>

@stack('scripts')

</body>
</html>
