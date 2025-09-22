@extends('layout.master')

@section('content')

<div style="overflow-y: auto; max-height: 80vh;"> <!-- Hanya konten ini yang bisa digeser -->
    <div class="d-flex justify-content-center mt-4 mb-4">
        <div class="card shadow-lg p-4" style="width: 800px; border-radius: 20px;">
        {{-- Bagian Profil --}}
        <div class="text-center mb-4"
             style="background: linear-gradient(90deg, #4a90e2, #357ABD); border-radius: 15px; padding: 20px;">
            <img src="https://via.placeholder.com/100"
                 alt="Foto Admin"
                 class="rounded-circle border border-3 border-white mb-2"
                 width="100" height="100">
            <h4 class="text-white mb-0">
                {{ session('karyawan')->nama_lengkap ?? 'Admin' }}
            </h4>
            <small class="text-light">
                {{ session('karyawan')->nama_divisi ?? 'Administrator' }}
            </small>
        </div>

        {{-- Live Attendance --}}
        <div class="text-center mb-4">
            <h6 class="fw-bold">Live Attendance</h6>
            <h2 id="clock" class="text-primary fw-bold"></h2>
            <p class="text-muted mb-0">{{ \Carbon\Carbon::now()->format('l, F d, Y') }}</p>
        </div>

        {{-- Tombol Masuk & Keluar --}}
        <div class="d-flex justify-content-between mt-3">
    @if(!$presensiHariIni)
        <!-- Belum absen sama sekali -->
        <a href="{{ route('admin.Masuk') }}" class="btn btn-primary btn-lg">Masuk</a>
        <button class="btn btn-danger btn-lg" disabled>Keluar</button>
    @elseif(!$presensiHariIni->jam_keluar)
        <!-- Sudah Masuk, Belum Keluar -->
        <button class="btn btn-primary btn-lg" disabled>Masuk</button>
        <a href="{{ route('admin.Keluar') }}" class="btn btn-danger btn-lg">Keluar</a>
    @else
        <!-- Sudah Masuk & Keluar -->
        <p class="text-success w-100 fw-bold">Anda sudah absen masuk & keluar hari ini ✅</p>
    @endif
</div>


<!-- Tabel Riwayat Presensi -->

    <h6 class="fw-bold mb-3">Riwayat Presensi</h6>

<div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
    <table class="table table-bordered table-striped">
        <thead class="table-light">
            <tr>
                <th>Tanggal</th>
                <th>Jam Masuk</th>
                <th>Lokasi Masuk</th>
                <th>Jam Keluar</th>
                <th>Lokasi Keluar</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($riwayat as $item)
                <tr class="{{ $item->jam_masuk > '08:30:00' ? 'bg-primary text-white' : '' }}">
                    <td>{{ \Carbon\Carbon::parse($item->tgl_presen)->format('D, d F Y') }}</td>
                    <td>{{ $item->jam_masuk ?? '--:--' }}</td>
                    <td>{{ $item->lokasi_masuk ?? '-' }}</td>
                    <td>{{ $item->jam_keluar ?? '--:--' }}</td>
                    <td>{{ $item->lokasi_keluar ?? '-' }}</td>
                    <td>
                        <span class="fw-semibold text-secondary">
                            {{ $item->status ?? '-' }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">Belum ada data presensi</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Jam Otomatis --}}
<script>
    function updateClock() {
        let now = new Date();
        let time = now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit', second:'2-digit'});
        document.getElementById('clock').innerText = time;
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>
@endsection
