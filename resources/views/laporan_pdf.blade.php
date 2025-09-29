<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Presensi</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: center; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>
    <h3 align="center">Laporan Presensi Karyawan</h3>
    <p align="center">
        Periode: {{ request('mulai') }} s/d {{ request('sampai') }}
    </p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIK</th>
                <th>Nama</th>
                <th>Divisi</th>
                <th>Total Hari</th>

                {{-- ✅ Tampilkan kolom sesuai kategori --}}
                @if(request('kategori') == 'hadir')
                    <th>Hadir</th>
                    <th>Total Jam Kerja</th>
                @elseif(request('kategori') == 'sakit')
                    <th>Sakit</th>
                @elseif(request('kategori') == 'izin')
                    <th>Izin</th>
                @elseif(request('kategori') == 'cuti')
                    <th>Cuti</th>
                @elseif(request('kategori') == 'alpha')
                    <th>Alpha</th>
                @else
                    {{-- Jika pilih semua --}}
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
            @foreach($data as $i => $row)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ $row->nik }}</td>
                    <td>{{ $row->nama }}</td>
                    <td>{{ $row->divisi ?? '-' }}</td>

                    {{-- ✅ Total Hari sesuai kategori --}}
                    <td>
                        @php
                            $kategori = request('kategori');
                            $totalHari = 0;

                            if ($kategori == 'hadir') {
                                $totalHari = $row->hadir ?? 0;
                            } elseif ($kategori == 'sakit') {
                                $totalHari = $row->sakit ?? 0;
                            } elseif ($kategori == 'izin') {
                                $totalHari = $row->izin ?? 0;
                            } elseif ($kategori == 'cuti') {
                                $totalHari = $row->cuti ?? 0;
                            } elseif ($kategori == 'alpha') {
                                $totalHari = $row->alpha ?? 0;
                            } else {
                                $totalHari = ($row->hadir ?? 0) + ($row->sakit ?? 0) + ($row->izin ?? 0) + ($row->cuti ?? 0) + ($row->alpha ?? 0);
                            }
                        @endphp
                        {{ $totalHari }}
                    </td>

                    {{-- ✅ Tampilkan kolom sesuai kategori --}}
                    @if(request('kategori') == 'hadir')
                        <td>{{ $row->hadir ?? 0 }}</td>
                        <td>{{ $row->total_jam ?? 0 }}</td>
                    @elseif(request('kategori') == 'sakit')
                        <td>{{ $row->sakit ?? 0 }}</td>
                    @elseif(request('kategori') == 'izin')
                        <td>{{ $row->izin ?? 0 }}</td>
                    @elseif(request('kategori') == 'cuti')
                        <td>{{ $row->cuti ?? 0 }}</td>
                    @elseif(request('kategori') == 'alpha')
                        <td>{{ $row->alpha ?? 0 }}</td>
                    @else
                        <td>{{ $row->hadir ?? 0 }}</td>
                        <td>{{ $row->sakit ?? 0 }}</td>
                        <td>{{ $row->izin ?? 0 }}</td>
                        <td>{{ $row->cuti ?? 0 }}</td>
                        <td>{{ $row->alpha ?? 0 }}</td>
                        <td>{{ $row->total_jam ?? 0 }}</td>
                    @endif

                    <td>{{ $catatan[$row->nik] ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
