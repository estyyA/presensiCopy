@extends('layout.master')

@section('title', 'Konfirmasi Saki')

@section('content')
    <div class="mb-4">
        <h3 class="font-weight-bold text-purple">📝 Konfirmasi Sakit</h3>
        <p class="text-muted">PT Madubaru</p>
    </div>

    {{-- ✅ Notifikasi --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
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
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengajuans as $i => $p)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $p->karyawan->nama_lengkap ?? '-' }}</td>
                                <td>{{ $p->karyawan->departement->nama_divisi ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($p->tgl_pengajuan)->format('d/m/Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($p->tgl_mulai)->format('d/m/Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($p->tgl_selesai)->format('d/m/Y') }}</td>
                                <td>{{ $p->keterangan }}</td>
                                <td>
                                    @if ($p->surat_dokter)
                                        <a href="{{ asset('storage/' . $p->surat_dokter) }}" target="_blank"
                                            class="btn btn-sm btn-info">
                                            <i class="fa fa-file-alt"></i> Lihat
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                
<td>
    <form action="{{ route('konfirsakit.updateStatus', $p->id) }}" method="POST">
        @csrf
        <select name="status_pengajuan"
            class="form-control form-control-sm"
            onchange="this.form.submit()">
            <option value="menunggu" {{ $p->status_pengajuan=='menunggu'?'selected':'' }}>Menunggu</option>
            <option value="disetujui" {{ $p->status_pengajuan=='disetujui'?'selected':'' }}>Disetujui</option>
            <option value="ditolak" {{ $p->status_pengajuan=='ditolak'?'selected':'' }}>Ditolak</option>
        </select>
    </form>
</td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-muted text-center">Belum ada pengajuan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-center mt-3">
                    {{ $pengajuans->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .bg-purple {
            background-color: #6f42c1 !important;
        }

        .text-purple {
            color: #6f42c1 !important;
        }
    </style>
@endpush
