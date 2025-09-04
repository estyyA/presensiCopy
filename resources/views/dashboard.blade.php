@extends('layout.master')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card card-stat shadow-sm">
            <i class="fa fa-user-circle text-primary"></i>
            <div>Jumlah Karyawan</div>
            <h3>20</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-stat shadow-sm">
            <i class="fa fa-user-check text-success"></i>
            <div>Karyawan Masuk</div>
            <h3>18</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-stat shadow-sm">
            <i class="fa fa-user-times text-danger"></i>
            <div>Karyawan Izin</div>
            <h3>2</h3>
        </div>
    </div>
</div>

<div class="card mt-4 shadow-sm">
    <div class="card-header font-weight-bold">Data Karyawan</div>
    <div class="card-body d-flex justify-content-center">
        {{-- Atur ukuran canvas --}}
        <div style="max-width: 300px; max-height: 300px;">
            <canvas id="chartKaryawan"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    var ctx = document.getElementById('chartKaryawan').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Masuk', 'Izin', 'Tidak Masuk'],
            datasets: [{
                data: [5, 3, 2],
                backgroundColor: ['#035d2dff', '#7f9cf5', '#d81017ff']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom', // biar legend rapi
                }
            }
        }
    });
</script>
@endpush

