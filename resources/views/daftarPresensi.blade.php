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
                {{-- Nama Karyawan --}}
                <div class="col-md-4 mb-2">
                    <input type="text" name="nama" class="form-control" placeholder="Ketik nama karyawan (contoh: 'Resty')" value="{{ request('nama') }}">
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
                        <th class="text-center">No</th>
                        <th class="text-center">NIK</th>
                        <th>Nama Karyawan</th>
                        <th class="text-center">Divisi</th>
                        <th class="text-center">Tanggal</th>
                        <th class="text-center">Jam Masuk</th>
                        <th class="text-center">Jam Keluar</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
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
                                @elseif(strtolower($p->status) == 'cuti')
                                    <span class="badge badge-primary">Cuti</span>
                                @else
                                    <span class="badge badge-danger">Alpha</span>
                                @endif
                            </td>
                            <td class="text-center action-btns">
                                {{-- Tombol Edit (Modal) --}}
                                <button type="button"
                                        class="btn btn-warning btn-sm btn-edit-presensi"
                                        title="Edit"
                                        data-presen='@json($p)'>
                                    <i class="fa fa-edit"></i>
                                </button>

                                {{-- Tombol Hapus --}}
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

{{-- Modal Edit Presensi --}}
<div class="modal fade" id="editPresensiModal" tabindex="-1" role="dialog" aria-labelledby="editPresensiLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" id="editPresensiForm">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header bg-purple text-white">
                    <h5 class="modal-title" id="editPresensiLabel">Edit Presensi</h5>
                    <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_presen" id="id_presen">

                    <div class="mb-3">
                        <label for="jam_masuk">Jam Masuk</label>
                        <input type="time" class="form-control" name="jam_masuk" id="jam_masuk" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="jam_keluar">Jam Keluar</label>
                        <input type="time" class="form-control" name="jam_keluar" id="jam_keluar" disabled>
                    </div>

                    <div class="mb-3">
                        <label for="status">Status Kehadiran</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="hadir">Hadir</option>
                            <option value="sakit">Sakit</option>
                            <option value="izin">Izin</option>
                            <option value="cuti">Cuti</option> {{-- ✅ tambahan --}}
                            <option value="alpha">Alpha</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-purple">Simpan</button>
                </div>
            </div>
        </form>
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

@push('scripts')
<script>
    // Tombol Edit Presensi
    $('.btn-edit-presensi').click(function() {
        let presen = $(this).data('presen');

        $('#id_presen').val(presen.id_presen);
        $('#jam_masuk').val(presen.jam_masuk);
        $('#jam_keluar').val(presen.jam_keluar);

        // Normalisasi status -> lowercase + trim
        let status = (presen.status || '').toLowerCase().trim();
        $('#status').val(status);

        // set action form sesuai id presensi
        $('#editPresensiForm').attr('action', '/presensi/' + presen.id_presen);

        $('#editPresensiModal').modal('show');
    });
</script>
@endpush

