@extends('layout.karyawan')

@section('content')
<div class="card profile-card p-3 mb-3 text-center">
    <!-- Foto Profil -->
    <div class="d-flex flex-column align-items-center">
        <img src="https://via.placeholder.com/90"
             class="rounded-circle mb-2"
             width="90" height="90"
             alt="Foto Karyawan"
             style="object-fit: cover;">

        <h6 class="mb-0">I Made Sugi Hantara</h6>
        <small class="text-light">72220562 - Senior UX Designer</small>
    </div>

<!-- Jam & Absensi -->
<div class="card p-3 mb-3 text-center">
    <h5 class="fw-bold">Live Attendance</h5>
    <h2 class="text-primary">08:34 AM</h2>
    <p class="mb-1">Fri, 14 April 2023</p>
    <p class="text-muted small">Office Hours: 08:00 AM - 05:00 PM</p>

    <div class="d-flex justify-content-between">
        <a href="{{ url('/absensi/masuk') }}" class="btn btn-primary btn-lg">Masuk</a>
        <a href="{{ url('/absensi/keluar') }}" class="btn btn-danger btn-lg">Keluar</a>
    </div>
</div>

<!-- Riwayat Presensi -->
<div class="card p-3">
    <h6 class="fw-bold">Attendance History</h6>
    <ul class="list-unstyled mt-2 mb-0">
        <li class="d-flex justify-content-between small border-bottom py-2">
            <span>Fri, 14 April 2023</span>
            <span>08:00 AM - 05:00 PM</span>
        </li>
        <li class="d-flex justify-content-between small border-bottom py-2 text-danger">
            <span>Thu, 13 April 2023</span>
            <span>08:45 AM - 05:00 PM</span>
        </li>
        <li class="d-flex justify-content-between small border-bottom py-2">
            <span>Wed, 12 April 2023</span>
            <span>07:55 AM - 05:00 PM</span>
        </li>
    </ul>
</div>
@endsection
