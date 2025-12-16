<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Sakit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f9f9f9, #f1f1f1);
        }

        .card {
            border: none;
            border-radius: 20px;
            background: #fff;
        }

        .form-label {
            font-weight: 600;
        }

        .btn-warning {
            font-weight: 600;
            border-radius: 12px;
        }

        .btn-secondary {
            border-radius: 12px;
        }
    </style>
</head>

<body>

    <div class="container py-5">
        <div class="card shadow-lg mx-auto p-4" style="max-width: 500px;">
            <div class="text-center mb-4">
                <i class="bi bi-file-medical-fill text-warning" style="font-size: 3rem;"></i>
                <h3 class="fw-bold mt-2">Form Pengajuan Sakit</h3>
                <p class="text-muted">Silakan isi data berikut untuk absen sakit</p>
            </div>

            @if(session('success'))
            <div class="alert alert-success text-center rounded-3 shadow-sm">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            </div>
            @endif

        <form action="{{ route('presensi.storeSakit') }}" method="POST" enctype="multipart/form-data">
            @csrf
             <!-- Tanggal Pengajuan Otomatis (hidden) -->
            <input type="hidden" name="tgl_pengajuan" value="{{ date('Y-m-d') }}">
            <div class="mb-3">
                <label class="form-label"><i class="bi bi-calendar-event me-2"></i>Tanggal Mulai Sakit</label>
                <input type="date" name="tgl_mulai" class="form-control rounded-3 shadow-sm" required>
            </div>
            <div class="mb-3">
                <label class="form-label"><i class="bi bi-calendar-event me-2"></i>Tanggal Selesai Sakit</label>
                <input type="date" name="tgl_selesai" class="form-control rounded-3 shadow-sm" required>
            </div>
            <div class="mb-3">
                <label class="form-label"><i class="bi bi-upload me-2"></i>Upload Surat Dokter (Foto/PDF)</label>
                <input type="file" name="surat_dokter" class="form-control rounded-3 shadow-sm" accept="image/*,application/pdf">
            </div>
            <div class="mb-3">
                <label class="form-label"><i class="bi bi-chat-dots me-2"></i>Keterangan</label>
                <textarea name="keterangan" class="form-control rounded-3 shadow-sm" rows="3"
                    placeholder="Contoh: Demam tinggi, istirahat atas saran dokter"></textarea>
            </div>
            <button type="submit" class="btn btn-warning w-100 py-2 shadow-sm mb-2">
                <i class="bi bi-send-fill me-2"></i>Kirim Pengajuan Sakit
            </button>
        </form>
        <div class="d-grid gap-3 mt-2">
              <a href="{{ route('karyawan.dashboard') }}" class="btn btn-secondary w-100 py-2 shadow-sm">
                <i class="bi bi-arrow-left-circle me-2"></i>Batal
            </a>
            <a href="{{ route('presensi.riwayatSakit') }}"
            class="btn btn-info w-100 py-2 shadow-sm">
                <i class="bi bi-clock-history me-2"></i>
                Riwayat Pengajuan Izin Sakit
            </a>
        </div>
    </div>
</div>





    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
