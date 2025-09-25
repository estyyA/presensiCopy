@extends('layout.karyawan')

@section('content')
<style>
    .btn-danger:hover {
        background-color: #c82333 !important;
        transform: scale(1.05);
    }
</style>

<!-- Card Profil Karyawan -->
<div class="card profile-card p-3 mb-3 text-center">
    <div class="d-flex flex-column align-items-center position-relative">

<!-- Foto Profil -->
<img id="previewFoto"
     src="{{ $karyawan->foto ? asset('uploads/'.$karyawan->foto) : asset('img/profile.png') }}"
     class="rounded-circle mb-2"
     width="90" height="90"
     alt="Foto Karyawan"
     style="object-fit: cover;">


        <!-- Tombol Edit Foto -->
        <input type="file" id="inputFoto" class="d-none" accept="image/*">
        <label for="inputFoto" class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-1" style="cursor: pointer;">
            <i class="bi bi-pencil-fill"></i>
        </label>

        <h6 class="mb-0 mt-2">{{ session('karyawan')->nama_lengkap ?? $karyawan->nama_lengkap ?? 'Nama Karyawan' }}</h6>
        <small class="text-muted">{{ session('karyawan')->nama_divisi ?? $karyawan->nama_divisi ?? 'Divisi' }}</small>
    </div>
</div>

<!-- Modal Preview Foto -->
<div class="modal fade" id="modalPreview" tabindex="-1" aria-labelledby="modalPreviewLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="{{ route('karyawan.uploadFoto') }}" method="POST" enctype="multipart/form-data" id="formFoto">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="modalPreviewLabel">Preview Foto</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <img id="modalFoto" src="" class="rounded-circle" width="150" height="150" style="object-fit: cover;">
          <input type="hidden" name="fotoBase64" id="fotoBase64">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.getElementById('inputFoto').addEventListener('change', function(e) {
    let file = e.target.files[0];
    if(file){
        let reader = new FileReader();
        reader.onload = function(e){
            document.getElementById('modalFoto').src = e.target.result;
            document.getElementById('fotoBase64').value = e.target.result;
            var modal = new bootstrap.Modal(document.getElementById('modalPreview'));
            modal.show();
        };
        reader.readAsDataURL(file);
    }
});
</script>

<!-- Jam & Absensi -->
<div class="card p-3 mb-3 text-center">
    <h5 class="fw-bold">Live Attendance</h5>
    <h2 class="text-primary" id="liveClock">--:-- --</h2>
    <p class="mb-1" id="liveDate"></p>

<script>
function updateClock() {
    let now = new Date();

    // Format waktu (hh:mm:ss AM/PM)
    let hours = now.getHours();
    let minutes = now.getMinutes();
    let seconds = now.getSeconds();
    let ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12 || 12;

    let timeString = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')} ${ampm}`;
    document.getElementById('liveClock').textContent = timeString;

    // Format tanggal (Fri, 19 September 2025)
    let options = { weekday: 'short', year: 'numeric', month: 'long', day: 'numeric' };
    document.getElementById('liveDate').textContent = now.toLocaleDateString('en-US', options);
}

updateClock();
setInterval(updateClock, 1000);


updateClock();
setInterval(updateClock, 1000);
</script>

    <div class="d-flex justify-content-between mt-3">
    @if(!$presensiHariIni)
        <!-- Belum Absen Masuk -->
        <a href="{{ route('absensi.formMasuk') }}" class="btn btn-primary btn-lg">Masuk</a>
        <button class="btn btn-danger btn-lg" disabled>Keluar</button>

        @elseif(!$presensiHariIni->jam_keluar)
            <!-- Sudah Masuk, Belum Keluar -->
            <button class="btn btn-primary btn-lg" disabled>Masuk</button>
            <a href="{{ route('absensi.formKeluar') }}" class="btn btn-danger btn-lg">Keluar</a>
        @else
            <!-- Sudah Masuk & Keluar -->
            <p class="text-success w-100 fw-bold">Anda sudah absen masuk & keluar hari ini ✅</p>
        @endif
    </div>
</div>

<!-- Tabel Riwayat Presensi -->
<div class="card shadow-lg border-0 mb-4">
    <div class="card-body">
        <h5 class="fw-bold mb-4 text-purple">
            <i class="bi bi-funnel-fill me-2"></i> Filter Presensi
        </h5>

        <form method="GET" action="{{ route('karyawan.dashboard') }}">
            <div class="row g-3 align-items-end">
                <!-- Input Mulai -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Mulai</label>
                    <input type="date" name="mulai" class="form-control shadow-sm"
                           value="{{ request('mulai') }}">
                </div>

                <!-- Input Sampai -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Sampai</label>
                    <input type="date" name="sampai" class="form-control shadow-sm"
                           value="{{ request('sampai') }}">
                </div>

                <!-- Tombol -->
                <div class="col-md-4 d-flex">
                    <button type="submit" class="btn btn-purple ms-auto px-4 shadow-sm">
                        <i class="fa fa-search me-1"></i> Tampilkan
                    </button>
                </div>
            </div>
        </form>
    </div>

<div class="table-responsive">
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
                <tr class="{{ $item->jam_masuk > '07:30:00' ? 'bg-primary text-white' : '' }}">
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
</div>






<!-- Tombol Logout -->
<form action="{{ route('logout') }}" method="POST" class="mt-4 d-flex justify-content-end">
    @csrf
    <button type="submit" class="btn btn-danger btn-lg px-4 rounded-pill shadow-sm d-flex align-items-center justify-content-center gap-2" style="transition: 0.3s;">
        <i class="bi bi-box-arrow-right"></i>
        <span>Logout</span>
    </button>
</form>

{{-- @if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif --}}


@endsection
