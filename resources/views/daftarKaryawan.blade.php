@extends('layout.master')

@section('title', 'Data Karyawan')

@section('content')
    <div class="mb-4">
        <h3 class="font-weight-bold text-purple">📊 Data Karyawan</h3>
        <p class="text-muted">PT Madubaru</p>
    </div>

    {{-- 🔔 Notifikasi --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            ✅ {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span>&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            ⚠️ {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span>&times;</span>
            </button>
        </div>
    @endif

    {{-- 🔍 Filter dan Pencarian --}}
    <div class="mb-4 p-3 border rounded bg-light">
        <h6 class="font-weight-bold mb-3">Filter & Pencarian</h6>
        <form method="GET" action="{{ route('daftar.karyawan') }}">
            <div class="form-row">
                {{-- Nama --}}
                <div class="col-md-4 mb-2">
                    <input type="text" name="nama" class="form-control" placeholder="Cari nama karyawan..."
                        value="{{ request('nama') }}">
                </div>
                {{-- Divisi --}}
                <div class="col-md-3 mb-2">
                    <select name="divisi" class="form-control">
                        <option value="">🏢 Semua Divisi</option>
                        @foreach ($departements as $dept)
                            <option value="{{ $dept->id_divisi }}"
                                {{ request('divisi') == $dept->id_divisi ? 'selected' : '' }}>
                                {{ $dept->nama_divisi }}
                            </option>
                        @endforeach
                    </select>
                </div>
                {{-- Search umum --}}
                <div class="col-md-3 mb-2">
                    <input type="text" name="search" class="form-control" placeholder="🔎 Username / No. HP"
                        value="{{ request('search') }}">
                </div>
                {{-- Tombol --}}
                <div class="col-md-2 mb-2">
                    <button class="btn btn-primary btn-block">
                        <i class="fa fa-search mr-1"></i> Cari
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- 📋 Tabel Data Karyawan --}}
    <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
        <table class="table table-bordered table-hover text-center">
            <thead class="sticky-top">
                <tr style="background:#3f71dc; color:#fff;">
                    <th>No</th>
                    <th>NIK</th>
                    <th>Nama Karyawan</th>
                    <th>Divisi</th>
                    <th>Username</th>
                    <th>No. HP</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($karyawan as $index => $row)
                    <tr>
                        <td>{{ $karyawan->firstItem() + $index }}</td>
                        <td>{{ $row->NIK }}</td>
                        <td class="text-left">{{ $row->nama_lengkap }}</td>
                        <td>{{ $row->nama_divisi ?? '-' }}</td>
                        <td>{{ $row->username }}</td>
                        <td>{{ $row->no_hp }}</td>
                        <td>
                            @if ($row->status == 'Aktif')
                                <span class="badge badge-success px-3 py-1">Aktif</span>
                            @else
                                <span class="badge badge-secondary px-3 py-1">{{ $row->status }}</span>
                            @endif
                        </td>
                        <td class="action-btns">
                            <a href="{{ route('karyawan.show', $row->NIK) }}" class="btn btn-sm btn-info" title="Detail">
                                <i class="fa fa-folder-open"></i>
                            </a>
                            <a href="{{ route('karyawan.edit', $row->NIK) }}" class="btn btn-sm btn-success"
                                title="Edit">
                                <i class="fa fa-pen"></i>
                            </a>
                            <form action="{{ route('karyawan.delete', $row->NIK) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Yakin mau hapus data ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                    <i class="fa fa-times"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-muted">⚠️ Tidak ada data karyawan</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- 📑 Pagination --}}
    <div class="d-flex justify-content-center mt-3">
        {{ $karyawan->links() ?? '' }}
    </div>
@endsection

@push('styles')
    <style>
        .action-btns .btn {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 2px;
            border-radius: 6px;
        }

        .badge {
            font-size: 0.85rem;
        }

        .table thead th {
            vertical-align: middle;
            font-weight: 600;
            position: sticky;
            top: 0;
            z-index: 10;
            background: #3f71dc;
            color: #fff;
        }
    </style>
@endpush
