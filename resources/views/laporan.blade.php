@extends('layout.master')

@section('title', 'Laporan')

@section('content')
<div class="mb-4">
    <h3 class="font-weight-bold">Laporan</h3>
</div>

{{-- Filter & Pencarian --}}
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="font-weight-bold mb-3">Filter dan Pencarian</h5>
        <div class="form-inline">
            <label class="mr-2">Mulai Tanggal</label>
            <input type="date" class="form-control mr-3" value="2023-08-06">

            <label class="mr-2">Sampai Tanggal</label>
            <input type="date" class="form-control mr-3" value="2023-09-07">

            <button class="btn btn-primary mr-2">Tampilkan</button>
            <button class="btn btn-primary">
                <i class="fa fa-print mr-1"></i> Print
            </button>
        </div>
    </div>
</div>

{{-- Data Transaksi --}}
<div class="card shadow-sm">
    <div class="card-body">
        <h5 class="font-weight-bold mb-3">Tabel Data Transaksi</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-light text-center">
                    <tr>
                        <th>No</th>
                        <th>NIM</th>
                        <th>Tahun Akademik</th>
                        <th>Instansi</th>
                        <th>Total Bayar</th>
                        <th>Tgl Transaksi</th>
                        <th>Kode VA</th>
                        <th>Status Bayar</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <tr>
                        <td>1</td>
                        <td>72220535</td>
                        <td>Semester Genap 2023</td>
                        <td>UKDW</td>
                        <td>Rp0</td>
                        <td>06/08/2023</td>
                        <td>00172220535</td>
                        <td><span class="badge badge-success px-3 py-2">Sudah Bayar</span></td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>72220545</td>
                        <td>Semester Genap 2023</td>
                        <td>UKDW</td>
                        <td>Rp1.500.000</td>
                        <td>10/08/2023</td>
                        <td>00172220545</td>
                        <td><span class="badge badge-danger px-3 py-2">Belum Bayar</span></td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>72220555</td>
                        <td>Semester Genap 2023</td>
                        <td>UKDW</td>
                        <td>Rp10.500.000</td>
                        <td>20/08/2023</td>
                        <td>00172220555</td>
                        <td><span class="badge badge-warning px-3 py-2">Pending</span></td>
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
