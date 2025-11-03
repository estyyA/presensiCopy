<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Presensi</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }

        th {
            background: #f2f2f2;
        }

        h3 {
            text-align: center;
            margin-bottom: 4px;
        }

        p {
            text-align: center;
            margin-top: 0;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <h3>Laporan Presensi Karyawan</h3>
    <p>
        Periode: {{ $mulai ? date('d-m-Y', strtotime($mulai)) : '-' }}
        s/d {{ $sampai ? date('d-m-Y', strtotime($sampai)) : '-' }}<br>
        Kategori: {{ ucfirst($kategori ?? 'Semua') }}
    </p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIK</th>
                <th>Nama</th>
                <th>Divisi</th>
                <th>Jabatan</th>
                <th>Total Hari</th>

                {{-- ✅ Kolom dinamis sesuai kategori --}}
                @if ($kategori == 'hadir')
                    <th>Hadir</th>
                    <th>Total Jam Kerja</th>
                @elseif($kategori == 'sakit')
                    <th>Sakit</th>
                @elseif($kategori == 'izin')
                    <th>Izin</th>
                @elseif($kategori == 'cuti')
                    <th>Cuti</th>
                @elseif($kategori == 'alpha')
                    <th>Alpha</th>
                @else
                    <th>Hadir</th>
                    <th>Sakit</th>
                    <th>Izin</th>
                    <th>Cuti</th>
                    <th>Alpha</th>
                    <th>Total Jam Kerja</th>
                @endif

                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $i => $row)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $row->nik }}</td>
                    <td>{{ $row->nama }}</td>
                    <td>{{ $row->divisi ?? '-' }}</td>
                    <td>{{ $row->jabatan ?? '-' }}</td>

                    {{-- ✅ Total Hari SELALU menjumlah semua kategori (sinkron dgn Excel) --}}
                    <td>
                        {{
                            ($row->hadir ?? 0) +
                            ($row->sakit ?? 0) +
                            ($row->izin ?? 0) +
                            ($row->cuti ?? 0) +
                            ($row->alpha ?? 0)
                        }}
                    </td>

                    {{-- ✅ Kolom presensi sesuai kategori --}}
                    @if ($kategori == 'hadir')
                        <td>{{ $row->hadir ?? 0 }}</td>
                        <td>{{ $row->durasi_jam_kerja ?? '0 Jam 0 Menit' }}</td>
                    @elseif($kategori == 'sakit')
                        <td>{{ $row->sakit ?? 0 }}</td>
                    @elseif($kategori == 'izin')
                        <td>{{ $row->izin ?? 0 }}</td>
                    @elseif($kategori == 'cuti')
                        <td>{{ $row->cuti ?? 0 }}</td>
                    @elseif($kategori == 'alpha')
                        <td>{{ $row->alpha ?? 0 }}</td>
                    @else
                        <td>{{ $row->hadir ?? 0 }}</td>
                        <td>{{ $row->sakit ?? 0 }}</td>
                        <td>{{ $row->izin ?? 0 }}</td>
                        <td>{{ $row->cuti ?? 0 }}</td>
                        <td>{{ $row->alpha ?? 0 }}</td>
                        <td>{{ $row->durasi_jam_kerja ?? '0 Jam 0 Menit' }}</td>
                    @endif

                    <td>{{ $catatan[$row->nik] ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
