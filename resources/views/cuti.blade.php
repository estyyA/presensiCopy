@extends('layout.master')

@section('title', 'Daftar Cuti')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="font-weight-bold">📅 Daftar Cuti</h3>
    <button class="btn btn-primary" data-toggle="modal" data-target="#createCutiModal">
        + Tambah Cuti
    </button>
</div>

{{-- ✅ Tabel Daftar Cuti --}}
<div class="card shadow-sm border-0">
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>NIK</th>
                    <th>Nama</th>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Selesai</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($cuti as $c)
                    <tr>
                        <td>{{ $c->nik }}</td>
                        <td>{{ $c->karyawan->nama_lengkap ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($c->tanggal_mulai)->format('d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($c->tanggal_selesai)->format('d/m/Y') }}</td>
                        <td>{{ $c->keterangan }}</td>
                        <td>
                            <form action="{{ route('cuti.delete', $c->id) }}" method="POST" onsubmit="return confirm('Yakin hapus data cuti?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">Belum ada data cuti</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>


{{-- ✅ Modal Tambah Cuti --}}
<div class="modal fade" id="createCutiModal" tabindex="-1" role="dialog" aria-labelledby="createCutiModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('cuti.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createCutiModalLabel">Tambah Cuti</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    {{-- Pilih Karyawan --}}
                    <div class="form-group">
                        <label for="nik">Karyawan</label>
                        <select name="nik" id="nik" class="form-control" required>
                            <option value="">-- Cari Karyawan --</option>
                            @foreach ($karyawan as $k)
                                <option value="{{ $k->NIK }}">{{ $k->NIK }} - {{ $k->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Tanggal Mulai --}}
                    <div class="form-group">
                        <label for="tanggal_mulai">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" required>
                    </div>

                    {{-- Tanggal Selesai --}}
                    <div class="form-group">
                        <label for="tanggal_selesai">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control" required>
                    </div>

                    {{-- Keterangan --}}
                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" rows="2" class="form-control"></textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


{{-- ✅ Tambah Script Select2 --}}
@push('scripts')
    {{-- CDN Select2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#nik').select2({
                dropdownParent: $('#createCutiModal'), // biar bisa dipakai di modal
                placeholder: "-- Cari Karyawan --",
                allowClear: true
            });
        });
    </script>
@endpush
