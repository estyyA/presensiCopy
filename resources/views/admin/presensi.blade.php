@extends('layout.master')

@section('content')

<style>
    .foto-profil {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        display: block;
    }
</style>

<div style="overflow-y: auto; max-height: 80vh;">
  <div class="d-flex flex-column align-items-center gap-10">

    {{-- Bagian Profil --}}
    <div class="text-center mb-3"
         style="background: linear-gradient(90deg, #4a90e2, #357ABD);
                width: 800px; border-radius: 20px; padding: 20px;">


    {{-- Live Attendance --}}
    <div class="card shadow-lg p-4 mb-3"
         style="width: 800px; border-radius: 20px;">
        <div class="text-center mb-4">
            <h6 class="fw-bold">Live Attendance</h6>
            <h2 id="clock" class="text-primary fw-bold"></h2>
            <p class="text-muted mb-0">
                {{ \Carbon\Carbon::now()->format('l, F d, Y') }}
            </p>
        </div>

        {{-- Tombol Masuk & Keluar --}}
        <div class="d-flex justify-content-center">
            @if(!$presensiHariIni)
                <a href="{{ route('admin.Masuk') }}" class="btn btn-primary btn-lg mx-2">Masuk</a>
                <button class="btn btn-danger btn-lg mx-2" disabled>Keluar</button>
            @elseif(!$presensiHariIni->jam_keluar)
                <button class="btn btn-primary btn-lg mx-2" disabled>Masuk</button>
                <a href="{{ route('admin.Keluar') }}" class="btn btn-danger btn-lg mx-2">Keluar</a>
            @else
                <p class="text-success fw-bold text-center w-100">
                    Anda sudah absen masuk & keluar hari ini ✅
                </p>
            @endif
        </div>
    </div>

    {{-- Riwayat Presensi --}}
    <div class="card shadow-lg p-4" style="width: 800px; border-radius: 20px;">
        <h6 class="fw-bold mb-3 mt-5">Riwayat Presensi</h6>
        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
            <table class="table table-bordered table-striped align-middle text-center"
                   style="table-layout: fixed; width: 100%;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 130px;">Tanggal</th>
                        <th style="width: 100px;">Jam Masuk</th>
                        <th style="width: 200px;">Lokasi Masuk</th>
                        <th style="width: 100px;">Jam Keluar</th>
                        <th style="width: 200px;">Lokasi Keluar</th>
                        <th style="width: 100px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $item)
                        <tr class="{{ $item->jam_masuk > '08:30:00' ? 'bg-light text-dark' : '' }}">
                            <td>{{ \Carbon\Carbon::parse($item->tgl_presen)->format('D, d F Y') }}</td>
                            <td>{{ $item->jam_masuk ?? '--:--' }}</td>
                            <td class="text-start" style="white-space: normal; word-wrap: break-word;">
                                {{ $item->lokasi_masuk ?? '-' }}
                            </td>
                            <td>{{ $item->jam_keluar ?? '--:--' }}</td>
                            <td class="text-start" style="white-space: normal; word-wrap: break-word;">
                                {{ $item->lokasi_keluar ?? '-' }}
                            </td>
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
