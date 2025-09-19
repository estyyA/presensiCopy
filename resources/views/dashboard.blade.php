@extends('layout.master')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="card card-stat shadow-sm p-3 text-center">
            <i class="fa fa-users text-primary fa-2x mb-2"></i>
            <div>Jumlah Karyawan</div>
            <h3>{{ $totalKaryawan }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stat shadow-sm p-3 text-center">
            <i class="fa fa-user-check text-success fa-2x mb-2"></i>
            <div>Karyawan Masuk Hari Ini</div>
            <h3>{{ $harianMasuk }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stat shadow-sm p-3 text-center">
            <i class="fa fa-user-times text-warning fa-2x mb-2"></i>
            <div>Izin/Sakit Hari Ini</div>
            <h3>{{ $harianIzinSakit }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stat shadow-sm p-3 text-center">
            <i class="fa fa-user-slash text-danger fa-2x mb-2"></i>
            <div>Alpha Hari Ini</div>
            <h3>{{ $harianAlpha }}</h3>
        </div>
    </div>
</div>

<div class="card mt-4 shadow-sm">
    <div class="card-header font-weight-bold d-flex justify-content-between align-items-center">
        <span>Statistik Presensi Karyawan</span>
        <select id="periodeFilter" class="form-select form-select-sm w-auto">
            <option value="harian" selected>Harian</option>
            <option value="mingguan">Mingguan</option>
            <option value="bulanan">Bulanan</option>
        </select>
    </div>
    <div class="card-body d-flex justify-content-center">
        <div style="max-width: 600px; width: 100%;">
            <canvas id="chartKaryawan"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    var ctx = document.getElementById('chartKaryawan').getContext('2d');

    // Data dari controller
    var dataPeriode = {
        harian: [{{ $harianMasuk }}, {{ $harianIzinSakit }}, {{ $harianCuti }}, {{ $harianAlpha }}],
        mingguan: [{{ $mingguanMasuk }}, {{ $mingguanIzinSakit }}, {{ $mingguanCuti }}, {{ $mingguanAlpha }}],
        bulanan: [{{ $bulananMasuk }}, {{ $bulananIzinSakit }}, {{ $bulananCuti }}, {{ $bulananAlpha }}],
    };

    // Buat chart
    var chartKaryawan = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Masuk', 'Izin/Sakit', 'Cuti', 'Alpha'],
            datasets: [{
                label: 'Jumlah Karyawan',
                data: dataPeriode['harian'],
                backgroundColor: ['#11ab59ff', '#f39c12', '#0d6efd', '#d81017ff']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                title: { display: true, text: 'Statistik Presensi Karyawan (Harian)' }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Event listener saat dropdown berubah
    document.getElementById('periodeFilter').addEventListener('change', function () {
        var periode = this.value;
        chartKaryawan.data.datasets[0].data = dataPeriode[periode];
        chartKaryawan.options.plugins.title.text =
            'Statistik Presensi Karyawan (' + periode.charAt(0).toUpperCase() + periode.slice(1) + ')';
        chartKaryawan.update();
    });
</script>
@endpush
