@extends('layout.master')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">{{ $title ?? 'Detail Karyawan' }}</h2>

    <div class="card shadow-lg border-0">
        <div class="card-body p-4">
            <div class="row">
                <!-- Kolom Foto -->
                <div class="col-md-4 text-center">
                    @if($karyawan->foto)
                        <img src="{{ asset('uploads/' . $karyawan->foto) }}"
                             alt="Foto {{ $karyawan->nama_lengkap }}"
                             class="img-fluid rounded-circle shadow-sm mb-3"
                             style="max-width: 200px; max-height:200px; object-fit:cover;">
                    @else
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-3"
                             style="width: 200px; height: 200px; margin: auto;">
                            <span class="text-muted">Tidak ada foto</span>
                        </div>
                    @endif
                    <h4 class="fw-bold">{{ $karyawan->nama_lengkap }}</h4>
                    <p class="text-muted">
                        Jabatan: {{ $karyawan->nama_jabatan ?? '-' }} <br>
                        Role: {{ ucfirst($karyawan->role) }}
                    </p>
                </div>

                <!-- Kolom Detail -->
                <div class="col-md-8">
                    <table class="table table-borderless">
                        <tr>
                            <th width="30%">NIK</th>
                            <td>: {{ $karyawan->NIK }}</td>
                        </tr>
                        <tr>
                            <th>Username</th>
                            <td>: {{ $karyawan->username ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>No HP</th>
                            <td>: {{ $karyawan->no_hp ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Lahir</th>
                            <td>: {{ $karyawan->tgl_lahir ? \Carbon\Carbon::parse($karyawan->tgl_lahir)->format('d-m-Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Alamat</th>
                            <td>: {{ $karyawan->alamat ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Divisi</th>
                            <td>: {{ $karyawan->nama_divisi ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Jabatan</th>
                            <td>: {{ $karyawan->nama_jabatan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Role</th>
                            <td>: {{ ucfirst($karyawan->role) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="mt-4 text-end">
                <a href="{{ url('/daftarKaryawan') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
                <a href="{{ route('karyawan.edit', $karyawan->NIK) }}" class="btn btn-success">
                    <i class="fa fa-pen"></i> Edit
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
