@extends('layout.master')

@section('title', 'Dashboard')

@section('content')
    <div class="row">
        <div class="col-md-2">
            <div class="card card-stat shadow-sm p-3 text-center">
                <i class="fa fa-users text-primary fa-2x mb-2"></i>
                <div>Total Karyawan</div>
                <h3>{{ $totalKaryawan }}</h3>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card card-stat shadow-sm p-3 text-center">
                <i class="fa fa-user-check text-success fa-2x mb-2"></i>
                <div>Masuk Hari Ini</div>
                <h3>{{ $harianMasuk }}</h3>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card card-stat shadow-sm p-3 text-center">
                <i class="fa fa-user-clock text-warning fa-2x mb-2"></i>
                <div>Izin Hari Ini</div>
                <h3>{{ $harianIzin }}</h3>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card card-stat shadow-sm p-3 text-center">
                <i class="fa fa-user-md text-info fa-2x mb-2"></i>
                <div>Sakit Hari Ini</div>
                <h3>{{ $harianSakit }}</h3>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card card-stat shadow-sm p-3 text-center">
                <i class="fa fa-plane-departure text-primary fa-2x mb-2"></i>
                <div>Cuti Hari Ini</div>
                <h3>{{ $harianCuti }}</h3>
            </div>
        </div>
        <div class="col-md-2">
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
            <div style="max-width: 700px; width: 100%;">
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
            harian: [{{ $harianMasuk }}, {{ $harianIzin }}, {{ $harianSakit }}, {{ $harianCuti }},
                {{ $harianAlpha }}
            ],
            mingguan: [{{ $mingguanMasuk }}, {{ $mingguanIzin }}, {{ $mingguanSakit }}, {{ $mingguanCuti }},
                {{ $mingguanAlpha }}
            ],
            bulanan: [{{ $bulananMasuk }}, {{ $bulananIzin }}, {{ $bulananSakit }}, {{ $bulananCuti }},
                {{ $bulananAlpha }}
            ],
        };

        // Buat chart
        var chartKaryawan = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Masuk', 'Izin', 'Sakit', 'Cuti', 'Alpha'],
                datasets: [{
                    label: 'Jumlah Karyawan',
                    data: dataPeriode['harian'],
                    backgroundColor: [
                        '#11ab59ff', // Masuk
                        '#f39c12', // Izin
                        '#0dcaf0', // Sakit
                        '#0d6efd', // Cuti
                        '#d81017ff' // Alpha
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Statistik Presensi Karyawan (Harian)'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Event listener saat dropdown berubah
        document.getElementById('periodeFilter').addEventListener('change', function() {
            var periode = this.value;
            chartKaryawan.data.datasets[0].data = dataPeriode[periode];
            chartKaryawan.options.plugins.title.text =
                'Statistik Presensi Karyawan (' + periode.charAt(0).toUpperCase() + periode.slice(1) + ')';
            chartKaryawan.update();
        });
    </script>
@endpush
