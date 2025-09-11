@extends('layout.master')

@section('title', 'Data Karyawan')

@section('content')
<div class="card shadow-sm">
    <div class="card-header font-weight-bold">
        Data Karyawan
        <small class="text-muted d-block">PT Madubaru</small>
    </div>
    <div class="card-body">

        {{-- Filter dan Pencarian --}}
        <div class="mb-3 p-3 border rounded bg-light">
            <h6 class="font-weight-bold">Filter dan Pencarian</h6>
            <div class="form-row">
                <div class="col-md-4 mb-2">
                    <input type="text" class="form-control" placeholder="Nama Karyawan">
                </div>
                <div class="col-md-3 mb-2">
                    <select class="form-control">
                        <option value="">Bidang Pekerjaan</option>
                        <option>Direktur</option>
                        <option>Keuangan</option>
                        <option>HRD</option>
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <input type="text" class="form-control" placeholder="Search...">
                </div>
                <div class="col-md-2 mb-2">
                    <button class="btn btn-primary btn-block">
                        <i class="fa fa-search"></i> Cari
                    </button>
                </div>
            </div>
        </div>

        {{-- Tabel Data Karyawan --}}
        <div class="table-responsive">
            <table class="table table-bordered table-striped text-center">
                <thead>
                    <tr>
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
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $row->NIK }}</td>
                            <td>{{ $row->nama_lengkap }}</td>
                            <td>{{ $row->departement->nama_divisi ?? '-' }}</td>
                            <td>{{ $row->username }}</td>
                            <td>{{ $row->no_hp }}</td>
                            <td>
                                @if($row->status == 'Aktif')
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-secondary">{{ $row->status }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('karyawan.show', $row->NIK) }}" class="btn btn-sm btn-info">
                                    <i class="fa fa-folder-open"></i>
                                </a>
                                <a href="{{ route('karyawan.edit', $row->NIK) }}" class="btn btn-sm btn-success">
                                    <i class="fa fa-pen"></i>
                                </a>
                                <form action="{{ route('karyawan.delete', $row->NIK) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin mau hapus data ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">Tidak ada data karyawan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination (kalau pakai paginate) --}}
        <div class="d-flex justify-content-center mt-3">
            {{ $karyawan->links() ?? '' }}
        </div>

    </div>
</div>
@endsection
