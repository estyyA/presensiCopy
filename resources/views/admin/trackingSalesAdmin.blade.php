@extends('layout.master')

@section('title', 'Tracking Sales Admin')

@section('content')
<div class="mb-4">
    <h3 class="font-weight-bold text-purple">📊 Tracking Sales</h3>
    <p class="text-muted">PT Madubaru</p>
</div>

{{-- 🔔 Notifikasi --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        ✅ {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        ⚠️ {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
@endif

{{-- 🔍 Filter --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <h5 class="font-weight-bold mb-3 text-purple">Filter & Pencarian</h5>
        <form method="GET" action="{{ url()->current() }}" class="filter">
            <div class="form-row align-items-center">
                {{-- Nama --}}
                <div class="col-md-4 mb-2">
                    <input type="text" name="nama" class="form-control"
                        placeholder="🔎 Cari nama karyawan..." value="{{ request('nama') }}">
                </div>
                {{-- Tanggal --}}
                <div class="col-md-4 mb-2">
                    <input type="date" name="tanggal" class="form-control" value="{{ request('tanggal') }}">
                </div>
                {{-- Tombol Cari --}}
                <div class="col-md-2 mb-2">
                    <button type="submit" class="btn btn-purple btn-block">
                        <i class="fa fa-search mr-1"></i> Cari
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- 📋 Data Tracking Sales --}}
<div class="card shadow-sm border-0">
    <div class="card-body">
        <h5 class="font-weight-bold mb-3 text-purple">Data Tracking Sales</h5>

        <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
            <table class="table table-bordered table-striped text-center">
                <thead class="sticky-top">
                    <tr style="background:#3f71dc; color:#ffffff;">
                        <th>No</th>
                        <th>NIK</th>
                        <th>Nama Karyawan</th>
                        <th>Tanggal Sales</th>
                        <th>Jam Sales</th>
                        <th>Lokasi Sales</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($trackings as $i => $t)
                        <tr>
                            <td>{{ $trackings->firstItem() + $i }}</td>
                            <td>{{ $t->NIK }}</td>
                            <td>{{ $t->karyawan->nama_lengkap ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($t->tanggal_sales)->format('d/m/Y') }}</td>
                            <td>{{ $t->jam_sales ?? '-' }}</td>
                            <td>{{ $t->lokasi_sales ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">⚠️ Belum ada data tracking sales</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-center mt-3">
            {{ $trackings->appends(request()->all())->links() }}
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .bg-purple { background-color: #6f42c1 !important; }
    .text-purple { color: #6f42c1 !important; }
    .btn-purple { background-color: #6f42c1 !important; color: #fff !important; border: none; }
    .btn-purple:hover { background-color: #59309a !important; }
    .filter .form-control, .filter .btn { height: 44px; }
    .table thead th { font-weight: 600; position: sticky; top: 0; z-index: 10; background: #3f71dc; color: #fff; }
</style>
@endpush
