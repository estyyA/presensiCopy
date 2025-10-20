@extends('layout.karyawan')

@section('content')
<style>
    .btn-danger:hover {
        background-color: #c82333 !important;
        transform: scale(1.05);
    }

    /* Gaya tombol titik tiga */
    .menu-btn {
        background: transparent;
        border: none;
        font-size: 1.3rem;
        color: #6c757d;
        cursor: pointer;
    }

    .menu-btn:hover {
        color: #000;
        transform: scale(1.1);
    }

    .dropdown-menu {
        border-radius: 10px;
        box-shadow: 0px 4px 12px rgba(0,0,0,0.15);
    }
</style>

<!-- Card Profil Karyawan -->
<div class="card profile-card p-3 mb-3 text-center position-relative">

    <!-- Tombol Titik 3 -->
    <div class="dropdown position-absolute top-0 end-0 m-2">
        <button class="menu-btn" type="button" id="menuDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-three-dots-vertical"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="menuDropdown">
            <li>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger fw-semibold d-flex align-items-center">
                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                    </button>
                </form>
            </li>
        </ul>
    </div>

    <!-- Isi Profil -->
    <div class="d-flex flex-column align-items-center position-relative">
        <!-- Foto Profil -->
        <img id="previewFoto"
             src="{{ $karyawan->foto
                    ? asset('storage/'.$karyawan->foto)
                    : asset('img/profile.png') }}"
             class="rounded-circle mb-2"
             width="90" height="90"
             alt="Foto Karyawan"
             style="object-fit: cover;">

        <!-- Tombol Edit Foto -->
        <input type="file" id="inputFoto" class="d-none" accept="image/*">
        <label for="inputFoto"
               class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-1"
               style="cursor: pointer;">
            <i class="bi bi-pencil-fill"></i>
        </label>

        <!-- Nama & Divisi -->
        <h6 class="mb-0 mt-2">{{ $karyawan->nama_lengkap ?? 'Nama Karyawan' }}</h6>
        <small class="text-muted">{{ $karyawan->nama_divisi ?? 'Divisi' }}</small>
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
    let hours = now.getHours();
    let minutes = now.getMinutes();
    let seconds = now.getSeconds();
    let ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12 || 12;

    let timeString = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')} ${ampm}`;
    document.getElementById('liveClock').textContent = timeString;

    let options = { weekday: 'short', year: 'numeric', month: 'long', day: 'numeric' };
    document.getElementById('liveDate').textContent = now.toLocaleDateString('en-US', options);
}

updateClock();
setInterval(updateClock, 1000);
</script>

<div class="d-flex justify-content-between mt-4">
    @if(!$presensiHariIni)
        <a href="{{ route('absensi.formMasuk') }}"
           class="btn btn-success btn-lg px-4 shadow-sm d-flex align-items-center">
            <i class="bi bi-box-arrow-in-right me-2"></i> Masuk
        </a>
        <button class="btn btn-secondary btn-lg px-4 shadow-sm" disabled>
            <i class="bi bi-box-arrow-left me-2"></i> Keluar
        </button>
    @elseif(!$presensiHariIni->jam_keluar)
        <button class="btn btn-secondary btn-lg px-4 shadow-sm" disabled>
            <i class="bi bi-box-arrow-in-right me-2"></i> Masuk
        </button>
        <a href="{{ route('absensi.formKeluar') }}"
           class="btn btn-danger btn-lg px-4 shadow-sm d-flex align-items-center">
            <i class="bi bi-box-arrow-left me-2"></i> Keluar
        </a>
    @else
        <div class="alert alert-success text-center w-100 fw-bold shadow-sm rounded-3">
            ✅ Anda sudah absen masuk & keluar hari ini
        </div>
    @endif
</div>

<div class="mt-3">
    <a href="{{ route('presensi.formSakit') }}"
       class="btn btn-warning btn-lg w-100 shadow-sm d-flex align-items-center justify-content-center">
        <i class="bi bi-file-medical-fill me-2"></i> Ajukan Sakit
    </a>
</div>
<div class="mt-3">
    <a href="{{ route('tracking.form') }}"
       class="btn btn-primary btn-lg w-100 shadow-sm d-flex align-items-center justify-content-center">
        <i class="bi bi-geo-alt-fill me-2"></i> Tracking Sales
    </a>
</div>

<!-- Card Riwayat Presensi -->
<div class="card shadow-lg border-0 mb-4 rounded-4 mt-2">
    <div class="card-body">
        <h5 class="fw-bold mb-3 text-purple">
            <i class="bi-calendar-check-fill me-2"></i> Riwayat Presensi
        </h5>

        <form method="GET" action="{{ route('karyawan.dashboard') }}">
            <div class="d-flex align-items-end gap-3 flex-wrap">
                <div>
                    <label class="form-label fw-semibold">Mulai</label>
                    <input type="date" name="mulai"
                           class="form-control form-control-sm shadow-sm rounded"
                           style="width: 115px;"
                           value="{{ request('mulai') }}">
                </div>
                <div>
                    <label class="form-label fw-semibold">Sampai</label>
                    <input type="date" name="sampai"
                           class="form-control form-control-sm shadow-sm rounded"
                           style="width: 115px;"
                           value="{{ request('sampai') }}">
                </div>
                <div class="ms-auto">
                    <button type="submit"
                            class="btn btn-purple btn-sm shadow-sm rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 35px; height: 35px;"
                            title="Tampilkan">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
        </form>

        <div class="table-responsive mt-4">
            <table class="table table-bordered table-striped shadow-sm rounded-4 overflow-hidden">
                <thead class="table-secondary">
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
        <!-- Tombol Logout -->
        <div class="mt-4">
            <form action="{{ route('logout') }}" method="POST" class="d-inline w-100">
                @csrf
                <button type="submit"
                        class="btn btn-danger btn-lg w-100 shadow-sm d-flex align-items-center justify-content-center rounded-pill">
                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
