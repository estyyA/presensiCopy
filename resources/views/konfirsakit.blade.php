@extends('layout.master')

@section('title', 'Konfirmasi Saki')

@section('content')
<div class="mb-4">
    <h3 class="font-weight-bold text-purple">📝 Konfirmasi Sakit</h3>
    <p class="text-muted">PT Madubaru</p>
</div>

{{-- ✅ Notifikasi --}}
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped text-center">
                <thead style="background-color: #3f71dc; color: white; font-weight: 600;">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Divisi</th>
                        <th>Tgl Pengajuan</th>
                        <th>Tgl Mulai</th>
                        <th>Tgl Selesai</th>
                        <th>Keterangan</th>
                        <th>Surat</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengajuans as $i => $p)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $p->karyawan->nama_lengkap }}</td>
                           <td>{{ $p->karyawan->departement->nama_divisi ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($p->tgl_pengajuan)->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($p->tgl_mulai)->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($p->tgl_selesai)->format('d/m/Y') }}</td>
                            <td>{{ $p->keterangan }}</td>
                            <td>
                                @if($p->surat_dokter)
                                    <a href="{{ asset('storage/' . $p->surat_dokter) }}" target="_blank" class="btn btn-sm btn-info">
                                        <i class="fa fa-file-alt"></i> Lihat
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($p->status_pengajuan == 'menunggu')
                                    <span class="badge badge-warning">Menunggu</span>
                                @elseif($p->status_pengajuan == 'disetujui')
                                    <span class="badge badge-success">Disetujui</span>
                                @else
                                    <span class="badge badge-danger">Ditolak</span>
                                @endif
                            </td>
                            <td>
                                @if($p->status_pengajuan == 'menunggu')
                                    <form action="{{ route('konfirsakit.setujui', $p->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-success btn-sm" onclick="return confirm('Setujui pengajuan ini?')">✔</button>
                                    </form>
                                    <form action="{{ route('konfirsakit.tolak', $p->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-danger btn-sm" onclick="return confirm('Tolak pengajuan ini?')">✖</button>
                                    </form>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-muted text-center">Belum ada pengajuan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.bg-purple { background-color: #6f42c1 !important; }
.text-purple { color: #6f42c1 !important; }
</style>
@endpush
