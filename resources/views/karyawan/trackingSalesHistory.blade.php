@extends('layout.karyawan')

@section('content')
<div class="card shadow-lg border-0 mb-4 rounded-4 mt-2">
    <div class="card-body">
        <h5 class="fw-bold mb-3 text-primary">
            <i class="bi bi-geo-alt-fill me-2"></i> Riwayat Tracking Sales
        </h5>

        <!-- Filter Tanggal -->
        <form method="GET" action="{{ route('tracking.history') }}">
            <div class="d-flex align-items-end gap-3 flex-wrap">
                <div>
                    <label class="form-label fw-semibold">Mulai</label>
                    <input type="date" name="mulai"
                        class="form-control form-control-sm shadow-sm rounded"
                        style="width: 130px;"
                        value="{{ request('mulai') }}">
                </div>
                <div>
                    <label class="form-label fw-semibold">Sampai</label>
                    <input type="date" name="sampai"
                        class="form-control form-control-sm shadow-sm rounded"
                        style="width: 130px;"
                        value="{{ request('sampai') }}">
                </div>
                <div class="ms-auto">
                    <button type="submit"
                        class="btn btn-primary btn-sm shadow-sm rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 35px; height: 35px;"
                        title="Tampilkan">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
        </form>

        <!-- Tabel Riwayat -->
        <div class="table-responsive mt-4">
            <table class="table table-bordered table-striped shadow-sm rounded-4 overflow-hidden align-middle">
                <thead class="table-secondary">
                    <tr class="text-center">
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Lokasi</th>
                        <th>Divisi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tracking as $item)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal_sales)->translatedFormat('D, d M Y') }}</td>
                        <td>{{ $item->jam_sales }}</td>
                        <td>{{ $item->lokasi_sales }}</td>
                        <td>{{ $item->id_divisi }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">Belum ada data tracking sales</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-3">
            <!-- Tombol kiri -->
            <a href="{{ route('tracking.form') }}" class="btn btn-outline-primary btn-sm rounded-pill shadow-sm px-3">
                <i class="bi bi-pencil-square me-1"></i> Isi Form Tracking
            </a>

            <!-- Tombol kanan -->
            <a href="{{ route('karyawan.dashboard') }}" class="btn btn-primary btn-sm rounded-pill shadow-sm px-3">
                <i class="bi bi-speedometer2 me-1"></i> Kembali ke Dashboard
            </a>
        </div>

    </div>

</div>
</div>
@endsection