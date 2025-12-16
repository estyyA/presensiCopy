<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pengajuan Izin Sakit</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #f9f9f9, #f1f1f1);
        }

        .card {
            border: none;
            border-radius: 16px;
            background: #fff;
        }
    </style>
</head>

<body>
<div class="container py-4">

    <div class="mb-3 text-center">
        <h5 class="fw-bold">📜 Riwayat Pengajuan Sakit</h5>
        <p class="text-muted mb-0">PT Madubaru</p>
    </div>

    @if (session('success'))
        <div class="alert alert-success text-center">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-2">

            @forelse($riwayat as $i => $r)
                <div class="border rounded p-3 mb-2">
                    <div class="small text-muted mb-1">
                        {{ \Carbon\Carbon::parse($r->tgl_pengajuan)->format('d/m/Y') }}
                    </div>

                    <div class="fw-semibold">
                        {{ \Carbon\Carbon::parse($r->tgl_mulai)->format('d/m/Y') }}
                        s/d
                        {{ \Carbon\Carbon::parse($r->tgl_selesai)->format('d/m/Y') }}
                    </div>

                    <div class="text-muted small">
                        {{ $r->keterangan ?? '-' }}
                    </div>

                    <div class="mt-2">
                        @if ($r->status_pengajuan == 'menunggu')
                            <span class="badge bg-warning">Menunggu</span>
                        @elseif ($r->status_pengajuan == 'disetujui')
                            <span class="badge bg-success">Disetujui</span>
                        @else
                            <span class="badge bg-danger">Ditolak</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center text-muted py-4">
                    Belum ada riwayat pengajuan
                </div>
            @endforelse

        </div>
    </div>

    <div class="mt-3 d-flex justify-content-center">
        {{ $riwayat->links() }}
    </div>

    <div class="mt-4 d-grid gap-2">
        <a href="{{ route('presensi.formSakit') }}" class="btn btn-warning">
            ➕ Ajukan Sakit
        </a>

        <a href="{{ route('karyawan.dashboard') }}" class="btn btn-secondary">
            ⬅ Kembali ke Dashboard
        </a>
    </div>

</div>
</body>
</html>
