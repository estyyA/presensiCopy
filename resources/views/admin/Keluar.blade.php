@extends('layout.master')

@section('content')
<div class="card p-3">
    <h5 class="mb-3 text-center fw-bold">Absensi Keluar</h5>

        {{-- Peta Lokasi --}}
        <div class="mb-3" id="map-container">
            <iframe id="map-frame"
                width="100%" height="250"
                style="border:0; border-radius:10px;"
                allowfullscreen="" loading="lazy">
            </iframe>
            <p class="mt-2 small fw-semibold">
                <strong>Lokasi Anda:</strong> <span id="alamat">Mencari lokasi...</span>
            </p>
        </div>

        {{-- Jam Pulang --}}
        <form id="formKeluar" method="POST" action="{{ url('/admin/Keluar') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Jam Pulang</label>
                <input type="time" name="jam_keluar" id="jam_keluar" class="form-control" required>
            </div>

            {{-- Hidden input untuk lokasi --}}
            <input type="hidden" name="lokasi_keluar" id="lokasi_keluar">

            <button type="submit" id="btnKeluar" class="btn btn-success btn-lg w-100 text-center">
                <i class="bi bi-check-circle"></i>Absen Pulang
            </button>
        </form>

        <div class="text-center mt-3 text-muted">
            Jam kerja: 07:00 - 15:30 WIB
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    let inputJam = document.getElementById("jam_keluar");
    let now = new Date();
    let hh = String(now.getHours()).padStart(2, '0');
    let mm = String(now.getMinutes()).padStart(2, '0');
    inputJam.value = `${hh}:${mm}`;

    // Ambil lokasi user
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition, showError);
    } else {
        document.getElementById("alamat").innerText = "Geolocation tidak didukung browser.";
        document.getElementById("lokasi_keluar").value = "Geolocation tidak didukung";
    }
});

function showPosition(position) {
    let lat = position.coords.latitude;
    let lon = position.coords.longitude;

    // Tampilkan peta
    document.getElementById("map-frame").src = `https://www.google.com/maps?q=${lat},${lon}&hl=id&z=17&output=embed`;

    // Ambil alamat lengkap (reverse geocoding)
    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`)
        .then(response => response.json())
        .then(data => {
            let alamat = data.display_name || "Alamat tidak ditemukan";
            document.getElementById("alamat").innerText = alamat;
            document.getElementById("lokasi_keluar").value = `${alamat} (${lat}, ${lon})`;
        })
        .catch(() => {
            document.getElementById("alamat").innerText = "Gagal memuat alamat";
            document.getElementById("lokasi_keluar").value = `(${lat}, ${lon})`;
        });
}

function showError(error) {
    document.getElementById("alamat").innerText = "Tidak bisa mendapatkan lokasi.";
    document.getElementById("lokasi_keluar").value = "Lokasi tidak tersedia";
}
</script>
@endsection
