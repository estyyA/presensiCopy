@extends('layout.master')

@section('title', 'Data Presensi')

@section('content')
<div class="mb-4">
    <h3 class="font-weight-bold">Data Presensi</h3>
    <p class="text-muted">PT Madubaru</p>
</div>

{{-- Filter & Pencarian --}}
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="font-weight-bold mb-3">Filter & Pencarian</h5>
        <form method="GET" action="{{ url()->current() }}" class="filter">
            <div class="form-row align-items-center">
                {{-- Nama Karyawan: input text biasa --}}
                <div class="col-md-4 mb-2">
                    <input
                        type="text"
                        name="nama"
                        class="form-control"
                        placeholder="Ketik nama karyawan (contoh: 'Resty')"
                        value="{{ request('nama') }}"
                    >
                </div>

                {{-- Divisi --}}
                <div class="col-md-3 mb-2">
                    <select name="divisi" class="form-control">
                        <option value="">Pilih Divisi</option>
                        @foreach($departements as $dept)
                            <option value="{{ $dept->id_divisi }}" {{ request('divisi') == $dept->id_divisi ? 'selected' : '' }}>
                                {{ $dept->nama_divisi }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Tanggal --}}
                <div class="col-md-3 mb-2">
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

{{-- Data Presensi --}}
<div class="card shadow-sm">
    <div class="card-body">
        <h5 class="font-weight-bold mb-3">Data Presensi Karyawan</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm">
                <thead>
                    <tr>
                        <th class="text-center bg-purple text-white">No</th>
                        <th class="text-center bg-purple text-white">NIK</th>
                        <th class="bg-purple text-white">Nama Karyawan</th>
                        <th class="text-center bg-purple text-white">Divisi</th>
                        <th class="text-center bg-purple text-white">Tanggal</th>
                        <th class="text-center bg-purple text-white">Jam Masuk</th>
                        <th class="text-center bg-purple text-white">Jam Pulang</th>
                        <th class="text-center bg-purple text-white">Status Kehadiran</th>
                        <th class="text-center bg-purple text-white">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($presensis as $i => $p)
                        <tr>
                            <td class="text-center">{{ $presensis->firstItem() + $i }}</td>
                            <td class="text-center">{{ $p->NIK }}</td>
                            <td>{{ $p->nama_lengkap }}</td>
                            <td class="text-center">{{ $p->nama_divisi ?? '-' }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($p->tgl_presen)->format('d/m/Y') }}</td>
                            <td class="text-center">{{ $p->jam_masuk ?? '-' }}</td>
                            <td class="text-center">{{ $p->jam_keluar ?? '-' }}</td>
                            <td class="text-center">
                                @if(strtolower($p->status) == 'hadir')
                                    <span class="badge badge-success">Hadir</span>
                                @elseif(strtolower($p->status) == 'sakit')
                                    <span class="badge badge-info">Sakit</span>
                                @elseif(strtolower($p->status) == 'izin')
                                    <span class="badge badge-warning">Izin</span>
                                @else
                                    <span class="badge badge-danger">Alpha</span>
                                @endif
                            </td>
                            <td class="text-center action-btns">
                                {{-- ganti route jika beda nama, atau hapus jika belum ada --}}
                                <a href="{{ route('presensi.edit', $p->id_presen) }}" class="btn btn-warning btn-sm" title="Edit">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <form action="{{ route('presensi.destroy', $p->id_presen) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin ingin menghapus presensi ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm" title="Hapus"><i class="fa fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">Belum ada data presensi</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-center mt-3">
            {{ $presensis->appends(request()->all())->links() }}
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .bg-purple { background-color: #6f42c1 !important; color: #fff !important; }
    .btn-purple { background-color: #6f42c1 !important; color: #fff !important; border: none; }
    .btn-purple:hover { background-color: #59309a !important; }
    .filter .form-control, .filter .btn { height: 44px; }
    .action-btns .btn { width: 38px; height: 38px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; margin-left: 4px; }
    .card .table td, .card .table th { vertical-align: middle; }
    .table-responsive { overflow-x: auto; }
    .filter .form-control::placeholder { color: #999; }
    .page-link { border-radius: 6px !important; margin: 0 3px; }
    @media (max-width: 576px) {
        .filter .form-control, .filter .btn { height: 42px; }
        .action-btns .btn { width: 34px; height: 34px; }
    }
</style>
@endpush
