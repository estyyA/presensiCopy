@extends('layout.master')

@section('title', 'Daftar Cuti')

@section('content')
<div class="mb-4">
    <h3 class="font-weight-bold text-purple">📅 Data Cuti</h3>
    <p class="text-muted">PT Madubaru</p>
</div>

{{-- ✅ Tabel Daftar Cuti --}}
<div class="card shadow-lg border-0 rounded-lg">
    <div class="card-header text-white d-flex justify-content-between align-items-center"
         style="background: linear-gradient(90deg, #3f71dc, #3f71dc);">
        <span class="font-weight-bold"><i class="fa fa-list mr-2"></i> Daftar Cuti Karyawan</span>
        <button class="btn btn-light btn-sm rounded-pill px-3 shadow-sm" data-toggle="modal" data-target="#createCutiModal">
            <i class="fa fa-plus mr-1"></i> Tambah Cuti
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover">
                <thead class="thead-light text-center">
                    <tr>
                        <th>NIK</th>
                        <th>Nama</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @forelse ($cuti as $c)
                        <tr>
                            <td class="font-weight-bold">{{ $c->nik }}</td>
                            <td>{{ $c->karyawan->nama_lengkap ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($c->tanggal_mulai)->format('d M Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($c->tanggal_selesai)->format('d M Y') }}</td>
                            <td>
                                @php
                                    $ket = strtolower($c->keterangan);
                                    $badge = 'badge-info';
                                    if(str_contains($ket, 'tahunan')) $badge = 'badge-success';
                                    elseif(str_contains($ket, 'sakit')) $badge = 'badge-danger';
                                    elseif(str_contains($ket, 'pribadi')) $badge = 'badge-warning';
                                @endphp
                                <span class="badge {{ $badge }} px-3 py-2">{{ $c->keterangan }}</span>
                            </td>
                            <td>
                                <form action="{{ route('cuti.delete', $c->id) }}" method="POST"
                                      onsubmit="return confirm('Yakin hapus data cuti?')" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                        <i class="fa fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fa fa-calendar-times fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">Belum ada data cuti</p>
                                <small class="text-secondary">Klik tombol <b>Tambah Cuti</b> untuk menambahkan</small>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ✅ Modal Tambah Cuti --}}
<div class="modal fade" id="createCutiModal" tabindex="-1" role="dialog" aria-labelledby="createCutiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content shadow-lg border-0 rounded-lg">
            <form action="{{ route('cuti.store') }}" method="POST">
                @csrf
                <div class="modal-header text-white"
                     style="background: linear-gradient(90deg, #6f42c1, #d63384);">
                    <h5 class="modal-title"><i class="fa fa-plus-circle mr-2"></i> Tambah Cuti</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    {{-- Pilih Karyawan --}}
                    <div class="form-group">
                        <label for="nik" class="font-weight-bold">Karyawan</label>
                        <select name="nik" id="nik" class="form-control" required>
                            <option value="">-- Cari Karyawan --</option>
                            @foreach ($karyawan as $k)
                                <option value="{{ $k->NIK }}">{{ $k->NIK }} - {{ $k->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                    <hr>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="tanggal_mulai" class="font-weight-bold">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="tanggal_selesai" class="font-weight-bold">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control" required>
                        </div>
                    </div>
                    <hr>

                    <div class="form-group">
                        <label for="keterangan" class="font-weight-bold">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" rows="2" class="form-control"
                                  placeholder="Contoh: Cuti Tahunan, Cuti Sakit, Cuti Pribadi, dll"></textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">
                        <i class="fa fa-save mr-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

{{-- ✅ Select2 --}}
@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        $('#nik').select2({
            dropdownParent: $('#createCutiModal'),
            placeholder: "-- Cari Karyawan --",
            allowClear: true
        });
    });
</script>
@endpush

@push('styles')
<style>
    .bg-purple { background-color: #3f71dc !important; }
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
        transition: 0.2s;
    }
    .modal-content {
        border-radius: 1rem;
    }
</style>
@endpush
