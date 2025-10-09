@extends('layout.master')

@section('content')
<div class="container mt-5">
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
        <!-- Header -->
        <div class="card-header text-white py-3"
             style="background: linear-gradient(135deg, #4facfe, #0e3132);">
            <h4 class="mb-0 fw-bold">
                <i class="fa fa-id-card me-2"></i> Detail Karyawan
            </h4>
        </div>

        <!-- Body -->
        <div class="card-body p-5">
            <div class="row">
                <!-- Kolom Foto -->
                <div class="col-md-4 text-center mb-4 mb-md-0">
                    @if($karyawan->foto)
                        <img src="{{ asset('storage/' . $karyawan->foto) }}"
                             alt="Foto {{ $karyawan->nama_lengkap }}"
                             class="rounded-circle shadow-lg border border-3 border-white mb-3"
                             style="width: 200px; height: 200px; object-fit: cover;">
                    @else
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-3"
                             style="width: 200px; height: 200px; margin: auto;">
                            <i class="fa fa-user text-muted" style="font-size: 60px;"></i>
                        </div>
                    @endif

                    <h4 class="fw-bold">{{ $karyawan->nama_lengkap }}</h4>
                    <p class="text-muted mb-1">
                        <i class="fa fa-briefcase me-1 text-primary"></i> {{ $karyawan->nama_jabatan ?? '-' }}
                    </p>
                    <p class="text-muted">
                        <i class="fa fa-user-shield me-1 text-success"></i> {{ ucfirst($karyawan->role) }}
                    </p>
                </div>

                <!-- Kolom Detail -->
                <div class="col-md-8">
                    <table class="table table-borderless align-middle">
                        <tr>
                            <th width="30%"><i class="fa fa-id-badge text-primary me-2"></i> NIK</th>
                            <td>{{ $karyawan->NIK }}</td>
                        </tr>
                        <tr>
                            <th><i class="fa fa-user text-info me-2"></i> Username</th>
                            <td>{{ $karyawan->username ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th><i class="fa fa-phone text-success me-2"></i> No HP</th>
                            <td>{{ $karyawan->no_hp ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th><i class="fa fa-calendar text-danger me-2"></i> Tanggal Lahir</th>
                            <td>{{ $karyawan->tgl_lahir ? \Carbon\Carbon::parse($karyawan->tgl_lahir)->format('d-m-Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <th><i class="fa fa-home text-warning me-2"></i> Alamat</th>
                            <td>{{ $karyawan->alamat ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th><i class="fa fa-building text-secondary me-2"></i> Divisi</th>
                            <td>{{ $karyawan->nama_divisi ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th><i class="fa fa-sitemap text-purple me-2"></i> Subdivisi</th>
                            <td>{{ $karyawan->nama_subdivisi ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th><i class="fa fa-briefcase text-primary me-2"></i> Jabatan</th>
                            <td>{{ $karyawan->nama_jabatan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th><i class="fa fa-user-shield text-success me-2"></i> Role</th>
                            <td>{{ ucfirst($karyawan->role) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Tombol -->
            <div class="mt-4 text-end">
                <a href="{{ url('/daftarKaryawan') }}" class="btn btn-outline-secondary px-4 me-2">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
                <a href="{{ route('karyawan.edit', $karyawan->NIK) }}" class="btn btn-primary px-4"
                   style="background: linear-gradient(135deg, #4facfe, #00f2fe); border: none;">
                    <i class="fa fa-pen"></i> Edit
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
