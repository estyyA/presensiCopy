@extends('layout.master')

@section('content')
<div class="d-flex justify-content-center mt-4">
    <div class="card shadow-lg p-4" style="width: 500px; border-radius: 20px;">

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
        <div class="d-flex justify-content-center gap-3 mb-4">
            <a href="{{ route('admin.Masuk') }}" class="btn btn-primary btn-lg px-4">
                Masuk
            </a>
            <a href="{{ route('admin.Keluar') }}" class="btn btn-danger btn-lg px-4">
                Keluar
            </a>
        </div>

        {{-- Riwayat Presensi --}}
        <div>
            <h6 class="fw-bold mb-3">Attendance History</h6>
            @if(isset($riwayat) && $riwayat->isNotEmpty())
                <ul class="list-group">
                    @foreach($riwayat as $presensi)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>
                                {{ $presensi->tgl_presen }} —
                                @if($presensi->jam_masuk)
                                    Masuk: {{ $presensi->jam_masuk }}
                                @endif
                                @if($presensi->jam_keluar)
                                    , Pulang: {{ $presensi->jam_keluar }}
                                @endif
                            </span>
                            <span class="fw-semibold text-secondary">
                                {{ $presensi->status }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-muted">Belum ada data presensi</p>
            @endif
        </div>
    </div>
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
