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
    <form id="formMasuk" action="{{ url('/absensi/masuk') }}" method="POST">
        @csrf
        <!-- Input jam masuk -->
        <div class="mb-3">
            <label for="jam_masuk" class="form-label fw-semibold">Jam Masuk</label>
            <input type="time" name="jam_masuk" id="jam_masuk" class="form-control" required>
        </div>
        <input type="hidden" name="lokasi_masuk" id="lokasi_masuk">


        <!-- Tombol Absen -->
        <button type="submit" id="btnMasuk" class="btn btn-success btn-lg w-100 text-center" disabled>
            <i class="bi bi-check-circle"></i> Absen Masuk
        </button>
        <p id="warning" class="text-center text-muted small mt-2"></p>
    </form>

    <!-- Info jam kantor -->
    <div class="card p-2 mt-3 text-center small">
        <span class="text-muted">Jam kerja: 07:00 - 15:30 WIB</span>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    let inputJam = document.getElementById("jam_masuk");
    let btnMasuk = document.getElementById("btnMasuk");
    let warning = document.getElementById("warning");

    // Isi jam otomatis
    let now = new Date();
    let hh = String(now.getHours()).padStart(2, '0');
    let mm = String(now.getMinutes()).padStart(2, '0');
    inputJam.value = `${hh}:${mm}`;

    // Cek apakah sudah jam >= 07:30
    if (now.getHours() > 7 || (now.getHours() === 7 && now.getMinutes() >= 30)) {
        btnMasuk.disabled = false;
        warning.innerText = "";
    } else {
        btnMasuk.disabled = true;
        warning.innerText = "❌ Absensi masuk hanya bisa dilakukan setelah jam 07:30.";
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

    // Ambil alamat (reverse geocoding pakai OpenStreetMap)
    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`)
        .then(response => response.json())
        .then(data => {
            let alamat = data.display_name || "Alamat tidak ditemukan";
            document.getElementById("alamat").innerText = alamat;

            // Simpan alamat + koordinat di input hidden
            document.getElementById("lokasi_masuk").value = `${alamat} | ${lat},${lon}`;
        })
        .catch(() => {
            document.getElementById("alamat").innerText = "Gagal memuat alamat";
            document.getElementById("lokasi_masuk").value = `${lat},${lon}`;
        });
}


function showError(error) {
    document.getElementById("alamat").innerText = "Tidak bisa mendapatkan lokasi.";
}
</script>
@endsection
