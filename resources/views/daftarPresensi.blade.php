    @extends('layout.master')

    @section('title', 'Data Presensi')

    @section('content')
    <div class="mb-4">
        <h3 class="font-weight-bold text-purple">📊 Data Presensi</h3>
        <p class="text-muted">PT Madubaru</p>
    </div>

    {{-- 🔔 Notifikasi --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            ✅ {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span>&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            ⚠️ {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span>&times;</span>
            </button>
        </div>
    @endif

    {{-- 🔍 Filter & Pencarian --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <h5 class="font-weight-bold mb-3 text-purple">Filter & Pencarian</h5>
            <form method="GET" action="{{ url()->current() }}" class="filter">
                <div class="form-row align-items-center">
                    {{-- Nama --}}
                    <div class="col-md-4 mb-2">
                        <input type="text" name="nama" class="form-control"
                            placeholder="🔎 Cari nama karyawan..."
                            value="{{ request('nama') }}">
                    </div>
                    {{-- Divisi --}}
                    <div class="col-md-3 mb-2">
                        <select name="divisi" class="form-control">
                            <option value="">🏢 Semua Divisi</option>
                            @foreach($departements as $dept)
                                <option value="{{ $dept->id_divisi }}"
                                    {{ request('divisi') == $dept->id_divisi ? 'selected' : '' }}>
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

    {{-- 📋 Data Presensi --}}
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h5 class="font-weight-bold mb-3 text-purple">Data Presensi Karyawan</h5>

            <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
                <table class="table table-bordered table-striped text-center">
                    <thead class="sticky-top">
                        <tr style="background:#3f71dc; color:#ffffff; text-align:center;">
                            <th>No</th>
                            <th>NIK</th>
                            <th>Nama Karyawan</th>
                            <th>Divisi</th>
                            <th>Tanggal</th>
                            <th>Jam Masuk</th>
                            <th>Jam Keluar</th>
                            <th>Status</th>
                            <th>Aksi</th>
                            <th>Surat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($presensis as $i => $p)
                            <tr>
                                <td>{{ $presensis->firstItem() + $i }}</td>
                                <td>{{ $p->NIK }}</td>
                                <td>{{ $p->nama_lengkap }}</td>
                                <td>{{ $p->nama_divisi ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($p->tgl_presen)->format('d/m/Y') }}</td>
                                <td>{{ $p->jam_masuk ?? '-' }}</td>
                                <td>{{ $p->jam_keluar ?? '-' }}</td>
                                <td>
                                    @switch(strtolower($p->status))
                                        @case('hadir') <span class="badge badge-success px-3 py-1">Hadir</span> @break
                                        @case('sakit') <span class="badge badge-info px-3 py-1">Sakit</span> @break
                                        @case('izin') <span class="badge badge-warning px-3 py-1">Izin</span> @break
                                        @case('cuti') <span class="badge badge-primary px-3 py-1">Cuti</span> @break
                                        @default <span class="badge badge-danger px-3 py-1">Alpha</span>
                                    @endswitch
                                </td>
                                <td>
                                    <button type="button"
                                            class="btn btn-warning btn-sm btn-edit-presensi"
                                            title="Edit"
                                            data-presen='@json($p)'>
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <form action="{{ route('presensi.destroy', $p->id_presen) }}"
                                        method="POST" class="d-inline"
                                        onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm" title="Hapus">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    @if($p->surat)
                                    <a href="{{ asset('storage/' . $p->surat) }}" target="_blank" class="btn btn-sm btn-info">
                                        <i class="fa fa-file-alt"></i> Lihat Surat
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
</td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">⚠️ Belum ada data presensi</td>
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

    {{-- ✏️ Modal Edit Presensi --}}
    <div class="modal fade" id="editPresensiModal" tabindex="-1" role="dialog" aria-labelledby="editPresensiLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form method="POST" id="editPresensiForm">
                @csrf
                @method('PUT')
                <div class="modal-content rounded-lg">
                    <div class="modal-header bg-purple text-white">
                        <h5 class="modal-title">Edit Presensi</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
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
                                <option value="cuti">Cuti</option>
                                <option value="alpha">Alpha</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light border" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-purple">Simpan</button>
                    </div>
                </div>
            </form>
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
        .action-btns .btn { width: 36px; height: 36px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; margin: 2px; }
        .card .table th { font-weight: 600; }
        .badge { font-size: 0.85rem; }
        .table thead th {
            position: sticky;
            top: 0;
            z-index: 10;
            background: #3f71dc;
            color: #fff;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        // Tombol Edit
        $('.btn-edit-presensi').click(function() {
            let presen = $(this).data('presen');

            $('#id_presen').val(presen.id_presen);
            $('#jam_masuk').val(presen.jam_masuk);
            $('#jam_keluar').val(presen.jam_keluar);

            let status = (presen.status || '').toLowerCase().trim();
            $('#status').val(status);

            $('#editPresensiForm').attr('action', '/presensi/' + presen.id_presen);
            $('#editPresensiModal').modal('show');
        });
    </script>
    @endpush
