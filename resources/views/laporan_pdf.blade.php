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
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }
        th {
            background: #f2f2f2;
        }
    </style>
</head>
<body>
    <h3 align="center">Laporan Presensi Karyawan</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIK</th>
                <th>Nama</th>
                <th>Divisi</th>
                <th>Total Hari Kerja</th>
                <th>Jumlah Hadir</th>
                <th>Jumlah Sakit</th>
                <th>Jumlah Cuti</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $i => $row)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ $row['nik'] }}</td>
                    <td>{{ $row['nama'] }}</td>
                    <td>{{ $row['divisi'] }}</td>
                    <td>0</td>
                    <td>{{ $row['hadir'] }}</td>
                    <td>{{ $row['sakit'] }}</td>
                    <td>{{ $row['cuti'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
