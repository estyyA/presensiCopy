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
            <a href="{{ route('laporan.cetakPdf') }}" class="btn btn-danger mr-2" target="_blank">
                <i class="fa fa-file-pdf mr-1"></i> Export PDF
            </a>
            <a href="{{ route('laporan.exportExcel') }}" class="btn btn-success">
                <i class="fa fa-file-excel mr-1"></i> Export Excel
            </a>
        </div>
    </div>
</div>

{{-- Data Karyawan --}}
<div class="card shadow-sm">
    <div class="card-body">
        <h5 class="font-weight-bold mb-3">Tabel Data Transaksi</h5>
        <div class="table-responsive">
            <form method="POST" action="#">
                @csrf
                <table class="table table-bordered table-hover">
                    <thead class="thead-light text-center">
                        <tr>
                            <th>No</th>
                            <th>NIK</th>
                            <th>Nama</th>
                            <th>Divisi</th>
                            <th>Total Hari Kerja</th>
                            <th>Jumlah Hadir</th>
                            <th>Jumlah Sakit</th>
                            <th>Jumlah Cuti</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        <tr>
                            <td>1</td>
                            <td>72220535</td>
                            <td>Esra</td>
                            <td>Keuangan</td>
                            <td>0</td>
                            <td>5</td>
                            <td>2</td>
                            <td>2</td>
                            <td>
                                <textarea name="catatan[1]" class="form-control" rows="2" placeholder="Opsional..."></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>72220536</td>
                            <td>Rudi</td>
                            <td>HRD</td>
                            <td>0</td>
                            <td>4</td>
                            <td>1</td>
                            <td>3</td>
                            <td>
                                <textarea name="catatan[2]" class="form-control" rows="2" placeholder="Opsional..."></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>72220537</td>
                            <td>Sinta</td>
                            <td>Marketing</td>
                            <td>0</td>
                            <td>6</td>
                            <td>0</td>
                            <td>1</td>
                            <td>
                                <textarea name="catatan[3]" class="form-control" rows="2" placeholder="Opsional..."></textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>

                {{-- Tombol Simpan Catatan --}}
                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save mr-1"></i> Simpan Catatan
                    </button>
                </div>
            </form>
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
