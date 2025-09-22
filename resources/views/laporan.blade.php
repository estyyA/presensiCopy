@extends('layout.master')

@section('title', 'Laporan')

@section('content')
<div class="mb-4">
    <h3 class="font-weight-bold text-purple">📊 Laporan</h3>
    <p class="text-muted">PT Madubaru</p>
</div>

{{-- 🔔 Notifikasi --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show auto-close shadow-sm" role="alert">
        <i class="fa fa-check-circle mr-2"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show auto-close shadow-sm" role="alert">
        <i class="fa fa-exclamation-circle mr-2"></i> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

{{-- 🔎 Filter & Pencarian --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <h5 class="font-weight-bold mb-3 text-purple">Filter & Pencarian</h5>
        <form method="GET" action="{{ route('laporan') }}" class="form-inline flex-wrap">
            <div class="form-group mr-3 mb-2">
                <label class="mr-2 font-weight-bold">Mulai</label>
                <input type="date" name="mulai" class="form-control" value="{{ request('mulai') }}">
            </div>

            <div class="form-group mr-3 mb-2">
                <label class="mr-2 font-weight-bold">Sampai</label>
                <input type="date" name="sampai" class="form-control" value="{{ request('sampai') }}">
            </div>

            <button type="submit" class="btn btn-purple mr-2 mb-2">
                <i class="fa fa-search mr-1"></i> Tampilkan
            </button>

            <a href="{{ route('laporan.cetakPdf', ['mulai' => request('mulai'), 'sampai' => request('sampai')]) }}"
               class="btn btn-danger mr-2 mb-2" target="_blank">
                <i class="fa fa-file-pdf mr-1"></i> PDF
            </a>

            <a href="{{ route('laporan.exportExcel', ['mulai' => request('mulai'), 'sampai' => request('sampai')]) }}"
               class="btn btn-success mb-2">
                <i class="fa fa-file-excel mr-1"></i> Excel
            </a>
        </form>
    </div>
</div>

{{-- 📋 Data Presensi --}}
<div class="card shadow-sm border-0">
    <div class="card-body">
        <h5 class="font-weight-bold mb-3 text-purple">📑 Rekapitulasi Presensi</h5>
        <div class="table-responsive">
            <form method="POST" action="{{ route('laporan.simpanCatatan') }}">
                @csrf
                <table class="table table-bordered table-hover text-center">
                    <thead style="background:#3f71dc; color:#fff;">
                        <tr>
                            <th>No</th>
                            <th>NIK</th>
                            <th>Nama</th>
                            <th>Divisi</th>
                            <th>Total Hari</th>
                            <th>Hadir</th>
                            <th>Sakit</th>
                            <th>Izin</th>
                            <th>Cuti</th>
                            <th>Alpha</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $i => $row)
                            <tr>
                                <td>{{ $i+1 }}</td>
                                <td>{{ $row->nik }}</td>
                                <td>{{ $row->nama }}</td>
                                <td>{{ $row->divisi ?? '-' }}</td>
                                <td><span class="badge badge-dark px-3">{{ $row->total_hari }}</span></td>
                                <td><span class="badge badge-success px-3">{{ $row->hadir }}</span></td>
                                <td><span class="badge badge-info px-3">{{ $row->sakit }}</span></td>
                                <td><span class="badge badge-warning px-3">{{ $row->izin }}</span></td>
                                <td><span class="badge badge-primary px-3">{{ $row->cuti }}</span></td>
                                <td><span class="badge badge-danger px-3">{{ $row->alpha }}</span></td>
                                <td>
                                    <textarea name="catatan[{{ $row->nik }}]"
                                              class="form-control rounded"
                                              rows="2"
                                              placeholder="Tambahkan catatan...">{{ $catatan[$row->nik] ?? '' }}</textarea>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-muted">⚠️ Tidak ada data presensi</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Tombol Simpan --}}
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
    .bg-purple { background-color: #6f42c1 !important; }
    .text-purple { color: #6f42c1 !important; }
    .btn-purple { background-color: #6f42c1; color: #fff; border-radius: 6px; }
    .btn-purple:hover { background-color: #59309a; }
    .badge { font-size: 0.85rem; }
    textarea { resize: none; }
</style>
@endpush

@push('scripts')
<script>
    // Auto-close alert
    document.addEventListener("DOMContentLoaded", function() {
        setTimeout(() => {
            document.querySelectorAll('.alert.auto-close').forEach(el => $(el).alert('close'));
        }, 3000);
    });
</script>
@endpush
