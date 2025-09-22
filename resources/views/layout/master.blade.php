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
            background-color: #f9f7f7;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .sidebar {
            height: 100vh;
            background: linear-gradient(180deg, #0d47a1, #001f54);
            color: white;
            padding: 20px;
        }
        .sidebar .nav-link {
            color: #dcdcdc;
            margin: 8px 0;
            font-weight: 500;
        }
        .sidebar .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 8px;
        }
        .sidebar h6 {
            margin-top: 20px;
            font-size: 14px;
            text-transform: uppercase;
            color: #aaa;
        }
        .topbar {
            height: 60px;
            background: #fff;
            border-bottom: 1px solid #eee;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }
        .content {
            padding: 20px;
        }
        .card-stat {
            border-radius: 12px;
            text-align: center;
            padding: 20px;
            font-weight: 600;
        }
        .card-stat i {
            font-size: 28px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="d-flex">
    {{-- Sidebar --}}
    <div class="sidebar d-flex flex-column">
        <div class="text-center mb-4">
            <img src="{{ asset('img/logo.png') }}" alt="Logo" width="100">
        </div>
        <h6>MASTER MENU</h6>
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
        <a href="{{ route('admin.presensi.form') }}"
        class="nav-link @if(Request::is('admin/presensi*')) active @endif">
            <i class="fa fa-clock mr-2"></i> Presensi
        </a>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('cuti.index') }}">
                <i class="fa fa-calendar-check"></i>
                <span>Data Cuti</span>
            </a>
        </li>
    </div>

    {{-- Main Content --}}
    <div class="flex-fill">
        {{-- Topbar --}}
        <div class="topbar">
            <div class="dropdown">
                <a class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle"
                   href="#" id="userDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                   <span class="mr-2">
                    {{ session('karyawan')->nama_lengkap ?? 'Guest' }}
                </span>
                <img src="https://ui-avatars.com/api/?name={{ urlencode(session('karyawan')->nama_lengkap ?? 'User') }}&background=0D8ABC&color=fff"
                     class="rounded-circle" width="35" height="35" alt="avatar">
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow" aria-labelledby="userDropdown">
                    <a class="dropdown-item" href="{{ route('profil') }}">
                        <i class="fa fa-user"></i> Profil
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
        // inisialisasi select2 untuk semua select dengan class .select2
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
