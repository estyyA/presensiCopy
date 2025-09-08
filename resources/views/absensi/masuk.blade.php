@extends('layout.karyawan')

@section('content')
<div class="card p-3">
    <h5 class="mb-3 text-center fw-bold">Absensi Masuk</h5>

    <!-- Lokasi Map -->
    <div class="mb-3" id="map-container">
        <iframe id="map-frame"
            width="100%" height="250"
            style="border:0; border-radius:10px;"
            allowfullscreen="" loading="lazy">
        </iframe>
        <p class="mt-2 small fw-semibold">Lokasi Anda: <span id="alamat">Mencari lokasi...</span></p>
    </div>

    <!-- Form Absensi -->
    <form action="{{ url('/absensi/masuk') }}" method="POST">
        @csrf
        <!-- Input jam masuk -->
        <div class="mb-3">
            <label for="jam_masuk" class="form-label fw-semibold">Jam Masuk</label>
            <input type="time" name="jam_masuk" id="jam_masuk" class="form-control" required>
        </div>

        <!-- Keterangan Absensi -->
<div class="btn btn-success btn-lg w-100 text-center">
    <i class="bi bi-check-circle"></i> Absen Masuk
</div>

<!-- Info jam kantor -->
<div class="card p-2 mt-3 text-center small">
    <span class="text-muted">Jam kerja: 07:30 - 16:30 WIB</span>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Isi jam otomatis
    let inputJam = document.getElementById("jam_masuk");
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
