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
        <h5 class="font-weight-bold mb-3">Filter dan Pencarian</h5>
        <div class="d-flex align-items-center">
            <button class="btn btn-light border mr-2">Nama Karyawan</button>
            <button class="btn btn-light border mr-2">Bidang Pekerjaan</button>
            <button class="btn btn-light border mr-2">Hari</button>
            <button class="btn btn-primary ml-auto">
                <i class="fa fa-user"></i>
            </button>
        </div>
    </div>
</div>

{{-- Data Karyawan --}}
<div class="card shadow-sm">
    <div class="card-body">
        <h5 class="font-weight-bold mb-3">Data Karyawan</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-light text-center">
                    <tr>
                        <th>No</th>
                        <th>NIK</th>
                        <th>Nama Karyawan</th>
                        <th>Divisi</th>
                        <th>Tanggal</th>
                        <th>Jam Masuk</th>
                        <th>Jam Pulang</th>
                        <th>Status Kehadiran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <tr>
                        <td>1</td>
                        <td>72220535</td>
                        <td class="text-left">I Made Sugihantara</td>
                        <td>Keuangan</td>
                        <td>01/09/2023</td>
                        <td>07.00</td>
                        <td>15.30</td>
                        <td><span class="badge badge-success px-3 py-2">Hadir</span></td>
                        <td>
                            <button class="btn btn-success btn-sm"><i class="fa fa-edit"></i></button>
                            <button class="btn btn-danger btn-sm"><i class="fa fa-times"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>72220545</td>
                        <td class="text-left">Imanuel Yayan L</td>
                        <td>Keuangan</td>
                        <td>09/09/2023</td>
                        <td>07.00</td>
                        <td>15.30</td>
                        <td><span class="badge badge-success px-3 py-2">Hadir</span></td>
                        <td>
                            <button class="btn btn-success btn-sm"><i class="fa fa-edit"></i></button>
                            <button class="btn btn-danger btn-sm"><i class="fa fa-times"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>72220555</td>
                        <td class="text-left">Esra Duwi Saputra</td>
                        <td>Keuangan</td>
                        <td>09/09/2024</td>
                        <td>07.00</td>
                        <td>15.30</td>
                        <td><span class="badge badge-success px-3 py-2">Hadir</span></td>
                        <td>
                            <button class="btn btn-success btn-sm"><i class="fa fa-edit"></i></button>
                            <button class="btn btn-danger btn-sm"><i class="fa fa-times"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-center mt-3">
            <nav>
                <ul class="pagination">
                    <li class="page-item"><a class="page-link bg-purple text-white border-0" href="#">&laquo;</a></li>
                    <li class="page-item"><a class="page-link bg-purple text-white border-0" href="#">1</a></li>
                    <li class="page-item"><a class="page-link bg-purple text-white border-0" href="#">2</a></li>
                    <li class="page-item"><a class="page-link bg-purple text-white border-0" href="#">3</a></li>
                    <li class="page-item"><a class="page-link bg-purple text-white border-0" href="#">&raquo;</a></li>
                </ul>
            </nav>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .bg-purple {
        background-color: #800080 !important;
    }
    .page-link {
        border-radius: 6px !important;
        margin: 0 3px;
    }
</style>
@endpush
