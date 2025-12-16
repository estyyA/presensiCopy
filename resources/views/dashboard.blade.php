@extends('layout.master')

@section('title', 'Dashboard')

@section('content')

{{-- ================== CARD STATISTIK ================== --}}
<div class="row justify-content-center">
    <div class="col-md-2 col-sm-6 mb-3">
        <div class="card card-stat shadow-sm p-3 text-center h-100">
            <i class="fa fa-users text-primary fa-2x mb-2"></i>
            <div>Total Karyawan</div>
            <h3>{{ $totalKaryawan }}</h3>
        </div>
    </div>

    <div class="col-md-2 col-sm-6 mb-3">
        <div class="card card-stat shadow-sm p-3 text-center h-100">
            <i class="fa fa-user-check text-success fa-2x mb-2"></i>
            <div>Masuk Hari Ini</div>
            <h3>{{ $harianMasuk }}</h3>
        </div>
    </div>

    <div class="col-md-2 col-sm-6 mb-3">
        <div class="card card-stat shadow-sm p-3 text-center h-100">
            <i class="fa fa-user-md text-info fa-2x mb-2"></i>
            <div>Sakit Hari Ini</div>
            <h3>{{ $harianSakit }}</h3>
        </div>
    </div>

    <div class="col-md-2 col-sm-6 mb-3">
        <div class="card card-stat shadow-sm p-3 text-center h-100">
            <i class="fa fa-user-slash text-danger fa-2x mb-2"></i>
            <div>Alpha Hari Ini</div>
            <h3>{{ $harianAlpha }}</h3>
        </div>
    </div>
</div>

{{-- ================== CHART ================== --}}
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
        <div style="max-width: 650px; width: 100%;">
            <canvas id="chartKaryawan"></canvas>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    var ctx = document.getElementById('chartKaryawan').getContext('2d');

    // ================== DATA TANPA IZIN & CUTI ==================
    var dataPeriode = {
        harian: [
            {{ $harianMasuk }},
            {{ $harianSakit }},
            {{ $harianAlpha }}
        ],
        mingguan: [
            {{ $mingguanMasuk }},
            {{ $mingguanSakit }},
            {{ $mingguanAlpha }}
        ],
        bulanan: [
            {{ $bulananMasuk }},
            {{ $bulananSakit }},
            {{ $bulananAlpha }}
        ],
    };

    var chartKaryawan = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Masuk', 'Sakit', 'Alpha'],
            datasets: [{
                label: 'Jumlah Karyawan',
                data: dataPeriode['harian'],
                backgroundColor: [
                    '#11ab59ff', // Masuk
                    '#0dcaf0',   // Sakit
                    '#d81017ff'  // Alpha
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                title: {
                    display: true,
                    text: 'Statistik Presensi Karyawan (Harian)'
                }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    document.getElementById('periodeFilter').addEventListener('change', function () {
        var periode = this.value;
        chartKaryawan.data.datasets[0].data = dataPeriode[periode];
        chartKaryawan.options.plugins.title.text =
            'Statistik Presensi Karyawan (' + periode.charAt(0).toUpperCase() + periode.slice(1) + ')';
        chartKaryawan.update();
    });
</script>
@endpush
