@extends('layout.karyawan')

@section('content')
<div class="card p-3">
    <h5 class="mb-3 text-center fw-bold">Absensi Keluar</h5>

    <!-- Tombol Kembali -->
    <a href="{{ url('/dashboard-karyawan') }}" class="btn btn-secondary mb-3">
        <i class="bi bi-arrow-left-circle"></i> Kembali ke Dashboard
    </a>

    <!-- Lokasi Map -->
    <div class="mb-3" id="map-container">
        <iframe id="map-frame"
            width="100%" height="250"
            style="border:0; border-radius:10px;"
            allowfullscreen="" loading="lazy">
        </iframe>
        <p class="mt-2 small fw-semibold">Lokasi Anda: <span id="alamat">Mencari lokasi...</span></p>
    </div>

    <!-- Form Absensi Keluar -->
    <form action="{{ url('/absensi/keluar') }}" method="POST">
        @csrf
        <!-- Input jam keluar -->
        <div class="mb-3">
            <label for="jam_keluar" class="form-label fw-semibold">Jam Keluar</label>
            <input type="time" name="jam_keluar" id="jam_keluar" class="form-control" required>
        </div>

        <!-- Tombol Absen -->
        <button type="submit" class="btn btn-danger btn-lg w-100 text-center">
            <i class="bi bi-box-arrow-right"></i> Absen Keluar
        </button>
    </form>

    <!-- Info jam kantor -->
    <div class="card p-2 mt-3 text-center small">
        <span class="text-muted">Jam kerja: 07:30 - 16:30 WIB</span>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Isi jam otomatis
    let inputJam = document.getElementById("jam_keluar");
    if (inputJam) {
        let now = new Date();
        let hh = String(now.getHours()).padStart(2, '0');
        let mm = String(now.getMinutes()).padStart(2, '0');
        inputJam.value = `${hh}:${mm}`;
    }

    // Ambil lokasi user
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition, showError);
    } else {
        document.getElementById("alamat").innerText = "Geolocation tidak didukung browser.";
    }
});

function showPosition(position) {
    let lat = position.coords.latitude;
    let lon = position.coords.longitude;

    // Tampilkan peta
    let mapFrame = document.getElementById("map-frame");
    mapFrame.src = `https://www.google.com/maps?q=${lat},${lon}&hl=id&z=17&output=embed`;

    // Ambil alamat (reverse geocoding gratis pakai OpenStreetMap)
    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById("alamat").innerText = data.display_name || "Alamat tidak ditemukan";
        })
        .catch(() => {
            document.getElementById("alamat").innerText = "Gagal memuat alamat";
        });
}

function showError(error) {
    document.getElementById("alamat").innerText = "Tidak bisa mendapatkan lokasi.";
}
</script>
@endsection
