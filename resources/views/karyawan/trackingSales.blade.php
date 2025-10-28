<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracking Sales</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body class="bg-light">

    <div class="container py-4">
        <div class="card shadow-sm rounded-4 mx-auto" style="max-width: 500px;">
            <div class="card-body">
                <h4 class="fw-bold text-center mb-4">📍 Tracking Sales</h4>

                <!-- Notifikasi sukses -->
                @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form action="{{ route('tracking.store') }}" method="POST">
                    @csrf

                    <!-- Tanggal -->
                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal_sales" id="tanggal_sales" class="form-control rounded-3 shadow-sm" required>
                    </div>

                    <!-- Jam -->
                    <div class="mb-3">
                        <label class="form-label">Jam</label>
                        <input type="time" name="jam_sales" id="jam_sales" class="form-control rounded-3 shadow-sm" required>
                    </div>
                    <!-- Peta Lokasi -->
                    <div class="mb-3">
                        <iframe id="map-frame"
                            width="100%" height="250"
                            style="border:0; border-radius:10px;"
                            allowfullscreen="" loading="lazy">
                        </iframe>
                    </div>

                    <!-- Lokasi -->
                    <div class="mb-3">
                        <label class="form-label">Lokasi</label>
                        <input type="text" name="lokasi_sales" id="lokasi_sales" class="form-control rounded-3 shadow-sm" placeholder="Menunggu lokasi..." required>
                        <small id="alamat" class="text-muted"></small>
                    </div>

                    <!-- Tombol Simpan -->
                    <button type="submit" class="btn btn-primary w-100 mb-2 py-2 fw-bold rounded-pill shadow-sm">
                        <i class="bi bi-plus-circle me-2"></i> Simpan Tracking
                    </button>
                    <!-- Tombol Riwayat -->
                    <a href="{{ route('tracking.history') }}"
                        class="btn btn-outline-success w-100 mb-2 py-2 fw-bold rounded-pill shadow-sm">
                        <i class="bi bi-clock-history me-2"></i> Lihat Riwayat
                    </a>

                    <!-- Tombol Batal -->
                    <a href="{{ route('karyawan.dashboard') }}" class="btn btn-secondary w-100 py-2 rounded-pill">
                        <i class="bi bi-x-circle me-2"></i> Batal
                    </a>
                </form>
            </div>
        </div>
    </div>


    <!-- Script Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script otomatis tanggal, jam, dan lokasi -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Set tanggal hari ini otomatis
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('tanggal_sales').value = today;

            // Set jam sekarang otomatis
            const now = new Date();
            const hh = String(now.getHours()).padStart(2, '0');
            const mm = String(now.getMinutes()).padStart(2, '0');
            document.getElementById('jam_sales').value = `${hh}:${mm}`;

            // Ambil lokasi user
            const lokasiInput = document.getElementById('lokasi_sales');
            const alamatText = document.getElementById('alamat');
            const mapFrame = document.getElementById('map-frame');

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const lat = position.coords.latitude;
                        const lon = position.coords.longitude;

                        // Tampilkan peta di iframe
                        mapFrame.src = `https://www.google.com/maps?q=${lat},${lon}&hl=id&z=17&output=embed`;

                        // Isi input lokasi dengan koordinat
                        lokasiInput.value = `${lat},${lon}`;

                        // Reverse geocoding (ambil alamat)
                        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`)
                            .then(res => res.json())
                            .then(data => {
                                const alamat = data.display_name || "Alamat tidak ditemukan";
                                alamatText.innerText = alamat;
                                lokasiInput.value = `${alamat} | ${lat},${lon}`;
                            })
                            .catch(() => {
                                alamatText.innerText = "Gagal memuat alamat";
                            });
                    },
                    function() {
                        alamatText.innerText = "Tidak bisa mendapatkan lokasi.";
                    }
                );
            } else {
                alamatText.innerText = "Geolocation tidak didukung browser.";
            }
        });
    </script>
</body>

</html>