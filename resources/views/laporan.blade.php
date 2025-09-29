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

            <a href="{{ route('laporan.cetakPdf', ['mulai' => request('mulai'), 'sampai' => request('sampai'), 'kategori' => request('kategori')]) }}"
               class="btn btn-danger mr-2 mb-2" target="_blank">
                <i class="fa fa-file-pdf mr-1"></i> PDF
            </a>

            <a href="{{ route('laporan.exportExcel', ['mulai' => request('mulai'), 'sampai' => request('sampai'), 'kategori' => request('kategori')]) }}"
               class="btn btn-success mb-2">
                <i class="fa fa-file-excel mr-1"></i> Excel
            </a>
        </form>
    </div>
</div>

{{-- 📌 Menu Kategori Laporan --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <h5 class="font-weight-bold mb-3 text-purple">📌 Pilih Kategori Laporan</h5>
        <ul class="nav nav-pills mb-3">
            {{-- Tambahan menu Semua --}}
            <li class="nav-item">
                <a class="nav-link {{ request('kategori')==null ? 'active bg-purple text-white' : '' }}"
                   href="{{ route('laporan', array_merge(request()->all(), ['kategori'=>null])) }}">
                   Semua
                </a>
            </li>

            @foreach(['hadir','izin','sakit','cuti','alpha'] as $kategori)
                <li class="nav-item">
                    <a class="nav-link {{ request('kategori')==$kategori ? 'active bg-purple text-white' : '' }}"
                       href="{{ route('laporan', array_merge(request()->all(), ['kategori'=>$kategori])) }}">
                       {{ ucfirst($kategori) }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</div>

{{-- 📋 Data Presensi --}}
<div class="card shadow-sm border-0">
    <div class="card-body">
        <h5 class="font-weight-bold mb-3 text-purple">📑 Rekapitulasi Presensi
            @if(request('kategori'))
                - {{ ucfirst(request('kategori')) }}
            @else
                - Semua
            @endif
        </h5>
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
                            <th>Jabatan</th>
                            <th>Total Hari</th>

                            {{-- ✅ Tampilkan kolom sesuai kategori --}}
                            @if(request('kategori') == 'hadir')
                                <th>Hadir</th>
                            @elseif(request('kategori') == 'sakit')
                                <th>Sakit</th>
                            @elseif(request('kategori') == 'izin')
                                <th>Izin</th>
                            @elseif(request('kategori') == 'cuti')
                                <th>Cuti</th>
                            @elseif(request('kategori') == 'alpha')
                                <th>Alpha</th>
                            @else
                                {{-- Jika pilih semua --}}
                                <th>Hadir</th>
                                <th>Izin</th>
                                <th>Sakit</th>
                                <th>Cuti</th>
                                <th>Alpha</th>
                            @endif

                            {{-- Kolom Total Jam Kerja di ujung sebelum catatan (hanya hadir & semua) --}}
                            @if(request('kategori') == 'hadir' || request('kategori') == null)
                                <th>Total Jam Kerja</th>
                            @endif

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
                                <td>{{ $row->jabatan ?? '-' }}</td>

                                {{-- ✅ Total Hari sesuai kategori --}}
                                <td>
                                    @php
                                        $kategori = request('kategori');
                                        $totalHari = 0;

                                        if ($kategori == 'hadir') {
                                            $totalHari = $row->hadir ?? 0;
                                        } elseif ($kategori == 'sakit') {
                                            $totalHari = $row->sakit ?? 0;
                                        } elseif ($kategori == 'izin') {
                                            $totalHari = $row->izin ?? 0;
                                        } elseif ($kategori == 'cuti') {
                                            $totalHari = $row->cuti ?? 0;
                                        } elseif ($kategori == 'alpha') {
                                            $totalHari = $row->alpha ?? 0;
                                        } else {
                                            $totalHari = ($row->hadir ?? 0) + ($row->izin ?? 0) + ($row->sakit ?? 0) + ($row->cuti ?? 0) + ($row->alpha ?? 0);
                                        }
                                    @endphp
                                    <span class="badge badge-dark px-3">{{ $totalHari }}</span>
                                </td>

                                {{-- ✅ Kolom sesuai kategori --}}
                                @if(request('kategori') == 'hadir')
                                    <td><span class="badge badge-success px-3">{{ $row->hadir ?? 0 }}</span></td>
                                @elseif(request('kategori') == 'sakit')
                                    <td><span class="badge badge-info px-3">{{ $row->sakit ?? 0 }}</span></td>
                                @elseif(request('kategori') == 'izin')
                                    <td><span class="badge badge-warning px-3">{{ $row->izin ?? 0 }}</span></td>
                                @elseif(request('kategori') == 'cuti')
                                    <td><span class="badge badge-primary px-3">{{ $row->cuti ?? 0 }}</span></td>
                                @elseif(request('kategori') == 'alpha')
                                    <td><span class="badge badge-danger px-3">{{ $row->alpha ?? 0 }}</span></td>
                                @else
                                    {{-- Jika pilih semua --}}
                                    <td><span class="badge badge-success px-3">{{ $row->hadir ?? 0 }}</span></td>
                                    <td><span class="badge badge-warning px-3">{{ $row->izin ?? 0 }}</span></td>
                                    <td><span class="badge badge-info px-3">{{ $row->sakit ?? 0 }}</span></td>
                                    <td><span class="badge badge-primary px-3">{{ $row->cuti ?? 0 }}</span></td>
                                    <td><span class="badge badge-danger px-3">{{ $row->alpha ?? 0 }}</span></td>
                                @endif

                                {{-- ✅ Total Jam Kerja di ujung --}}
                                @if(request('kategori') == 'hadir' || request('kategori') == null)
                                    <td>
                                        @php
                                            $totalMenit = (int) ($row->total_menit ?? 0);
                                            $jam = intdiv($totalMenit, 60);
                                            $menit = $totalMenit % 60;
                                        @endphp
                                        <span class="badge badge-secondary px-3">
                                            @if($jam > 0 && $menit > 0)
                                                {{ $jam }} Jam {{ $menit }} Menit
                                            @elseif($jam > 0)
                                                {{ $jam }} Jam
                                            @elseif($menit > 0)
                                                {{ $menit }} Menit
                                            @else
                                                0 Menit
                                            @endif
                                        </span>
                                    </td>
                                @endif

                                <td>
                                    <textarea name="catatan[{{ $row->nik }}]"
                                              class="form-control rounded"
                                              rows="2"
                                              placeholder="Tambahkan catatan...">{{ $catatan[$row->nik] ?? '' }}</textarea>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ request('kategori') == 'hadir' ? 9 : (request('kategori') ? 8 : 12) }}" class="text-muted">
                                    ⚠️ Tidak ada data presensi
                                </td>
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
    .nav-pills .nav-link { border-radius: 6px; }
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
