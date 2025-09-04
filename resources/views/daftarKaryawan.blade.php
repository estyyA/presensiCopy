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
                <thead class="thead-thead-light ">
                    <tr>
                        <th>No</th>
                        <th>NIK</th>
                        <th>Nama Karyawan</th>
                        <th>Bidang</th>
                        <th>Username</th>
                        <th>Password</th>
                        <th>No. HP</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>72220535</td>
                        <td>I Made Sugihantara</td>
                        <td>Direktur</td>
                        <td>Arjuna</td>
                        <td>Duryudana</td>
                        <td>082344441130</td>
                        <td><span class="badge badge-success">Aktif</span></td>
                        <td>
                            <button class="btn btn-sm btn-info"><i class="fa fa-folder-open"></i></button>
                            <button class="btn btn-sm btn-success"><i class="fa fa-pen"></i></button>
                            <button class="btn btn-sm btn-danger"><i class="fa fa-times"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>72220545</td>
                        <td>Imanuel Yayan L</td>
                        <td>Keuangan</td>
                        <td>Rainexx</td>
                        <td>nuel1234</td>
                        <td>081324567890</td>
                        <td><span class="badge badge-success">Aktif</span></td>
                        <td>
                            <button class="btn btn-sm btn-info"><i class="fa fa-folder-open"></i></button>
                            <button class="btn btn-sm btn-success"><i class="fa fa-pen"></i></button>
                            <button class="btn btn-sm btn-danger"><i class="fa fa-times"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>72220555</td>
                        <td>Esra Duwi Saputra</td>
                        <td>Keuangan</td>
                        <td>Dutaepepehe</td>
                        <td>duta1234</td>
                        <td>081324567896</td>
                        <td><span class="badge badge-success">Aktif</span></td>
                        <td>
                            <button class="btn btn-sm btn-info"><i class="fa fa-folder-open"></i></button>
                            <button class="btn btn-sm btn-success"><i class="fa fa-pen"></i></button>
                            <button class="btn btn-sm btn-danger"><i class="fa fa-times"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-center mt-3">
            <nav>
                <ul class="pagination">
                    <li class="page-item"><a class="page-link" href="#">&laquo;</a></li>
                    <li class="page-item"><a class="page-link" href="#">&lt;</a></li>
                    <li class="page-item"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">&gt;</a></li>
                    <li class="page-item"><a class="page-link" href="#">&raquo;</a></li>
                </ul>
            </nav>
        </div>

    </div>
</div>
<div>asyaa</div>
@endsection
