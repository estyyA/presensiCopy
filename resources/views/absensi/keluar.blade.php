@extends('layout.karyawan')

@section('content')
<div class="card p-3">
    <h5 class="mb-3 text-center fw-bold">Absensi Keluar</h5>

<<<<<<< Updated upstream
    <!-- Tombol Kembali -->
    <a href="{{ route('karyawan.dashboard') }}" class="btn btn-secondary mb-3">
        <i class="bi bi-arrow-left-circle"></i> Kembali ke Dashboard
    </a>

=======
>>>>>>> Stashed changes
    <!-- Lokasi Map -->
    <div class="mb-3" id="map-container">
        <iframe id="map-frame"
            width="100%" height="250"
            style="border:0; border-radius:10px;"
            allowfullscreen="" loading="lazy">
        </iframe>
        <p class="mt-2 small fw-semibold">Lokasi Anda: <span id="alamat">Mencari lokasi...</span></p>
    </div>

<<<<<<< Updated upstream
    <!-- Form Absensi Keluar -->
    <form action="{{ url('/absensi/keluar') }}" method="POST">
=======
    <!-- Form Absensi -->
    <form id="formKeluar" action="{{ route('absensi.keluar') }}" method="POST">
>>>>>>> Stashed changes
        @csrf
        <!-- Input jam keluar -->
        <div class="mb-3">
            <label for="jam_keluar" class="form-label fw-semibold">Jam Keluar</label>
            <input type="time" name="jam_keluar" id="jam_keluar" class="form-control" required>
        </div>

<<<<<<< Updated upstream
        <!-- Tombol Absen Keluar -->
        <button type="submit" class="btn btn-danger btn-lg w-100 text-center">
            <i class="bi bi-box-arrow-right"></i> Absen Keluar
        </button>
=======
        <!-- Tombol Absen -->
        <button type="submit" id="btnKeluar" class="btn btn-danger btn-lg w-100" disabled>
            <i class="bi bi-box-arrow-right"></i> Absen Keluar
        </button>
        <p id="warning" class="text-center text-muted small mt-2"></p>
>>>>>>> Stashed changes
    </form>

    <!-- Info jam kantor -->
    <div class="card p-2 mt-3 text-center small">
        <span class="text-muted">Jam kerja: 07:30 - 16:30 WIB</span>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
<<<<<<< Updated upstream
    // Isi jam otomatis
    let inputJam = document.getElementById("jam_keluar");
    if (inputJam) {
        let now = new Date();
        let hh = String(now.getHours()).padStart(2, '0');
        let mm = String(now.getMinutes()).padStart(2, '0');
        inputJam.value = `${hh}:${mm}`;
=======
    let inputJam = document.getElementById("jam_keluar");
    let btnKeluar = document.getElementById("btnKeluar");
    let warning = document.getElementById("warning");

    // Atur jam keluar otomatis
    let now = new Date();
    let hh = String(now.getHours()).padStart(2, '0');
    let mm = String(now.getMinutes()).padStart(2, '0');
    inputJam.value = `${hh}:${mm}`;

    // Cek apakah sudah lewat jam 15:30
    if (now.getHours() > 15 || (now.getHours() === 15 && now.getMinutes() >= 30)) {
        btnKeluar.disabled = false;
        warning.innerText = "";
    } else {
        btnKeluar.disabled = true;
        warning.innerText = "❌ Absensi keluar hanya bisa dilakukan setelah jam 15:30.";
>>>>>>> Stashed changes
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

<<<<<<< Updated upstream
    // Tampilkan peta
    let mapFrame = document.getElementById("map-frame");
    mapFrame.src = `https://www.google.com/maps?q=${lat},${lon}&hl=id&z=17&output=embed`;

    // Ambil alamat (reverse geocoding gratis pakai OpenStreetMap)
=======
    let mapFrame = document.getElementById("map-frame");
    mapFrame.src = `https://www.google.com/maps?q=${lat},${lon}&hl=id&z=17&output=embed`;

>>>>>>> Stashed changes
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
