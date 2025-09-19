@extends('layout.master')

@section('title', 'Laporan')

@section('content')
<div class="mb-4">
    <h3 class="font-weight-bold">Laporan</h3>
</div>

{{-- 🔔 Notifikasi --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show auto-close" role="alert">
        <i class="fa fa-check-circle mr-2"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show auto-close" role="alert">
        <i class="fa fa-exclamation-circle mr-2"></i> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

{{-- Filter & Pencarian --}}
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="font-weight-bold mb-3">Filter dan Pencarian</h5>

        {{-- ✅ Form Filter GET --}}
        <form method="GET" action="{{ route('laporan') }}" class="form-inline mb-0">
            <label class="mr-2">Mulai Tanggal</label>
            <input type="date" name="mulai" class="form-control mr-3" value="{{ request('mulai') }}">

            <label class="mr-2">Sampai Tanggal</label>
            <input type="date" name="sampai" class="form-control mr-3" value="{{ request('sampai') }}">

            <button type="submit" class="btn btn-primary mr-2">Tampilkan</button>

            {{-- Export PDF & Excel --}}
            <a href="{{ route('laporan.cetakPdf', ['mulai' => request('mulai'), 'sampai' => request('sampai')]) }}"
               class="btn btn-danger mr-2" target="_blank">
                <i class="fa fa-file-pdf mr-1"></i> Export PDF
            </a>
            <a href="{{ route('laporan.exportExcel', ['mulai' => request('mulai'), 'sampai' => request('sampai')]) }}"
               class="btn btn-success">
                <i class="fa fa-file-excel mr-1"></i> Export Excel
            </a>
        </form>
    </div>
</div>

{{-- Data Karyawan --}}
<div class="card shadow-sm">
    <div class="card-body">
        <h5 class="font-weight-bold mb-3">Tabel Data Presensi</h5>
        <div class="table-responsive">

            {{-- ✅ Form POST untuk simpan catatan --}}
            <form method="POST" action="{{ route('laporan.simpanCatatan') }}">
                @csrf
                <table class="table table-bordered table-hover">
                    <thead class="thead-light text-center">
                        <tr>
                            <th>No</th>
                            <th>NIK</th>
                            <th>Nama</th>
                            <th>Divisi</th>
                            <th>Total Hari Kerja</th>
                            <th>Jumlah Hadir</th>
                            <th>Jumlah Sakit</th>
                            <th>Jumlah Izin</th>
                            <th>Jumlah Alpha</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @forelse($data as $i => $row)
                            <tr>
                                <td>{{ $i+1 }}</td>
                                <td>{{ $row->nik }}</td>
                                <td>{{ $row->nama }}</td>
                                <td>{{ $row->divisi ?? '-' }}</td>
                                <td>{{ $row->total_hari }}</td>
                                <td>{{ $row->hadir }}</td>
                                <td>{{ $row->sakit }}</td>
                                <td>{{ $row->izin }}</td>
                                <td>{{ $row->alpha }}</td>
                                <td>
                                    <textarea name="catatan[{{ $row->nik }}]"
                                              class="form-control"
                                              rows="2"
                                              placeholder="Opsional...">{{ $catatan[$row->nik] ?? '' }}</textarea>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" style="color: #777;">Tidak ada data presensi</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Tombol Simpan Catatan --}}
                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save mr-1"></i> Simpan Catatan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .bg-purple {
        background-color: #800080 !important;
    }
    .page-link {
        border-radius: 6px !important;
        margin: 0 3px;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        setTimeout(function() {
            let alerts = document.querySelectorAll('.alert.auto-close');
            alerts.forEach(function(alert) {
                $(alert).alert('close');
            });
        }, 3000); // Auto-close setelah 3 detik
    });
</script>
@endpush
