<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Hasil Perangkingan MOORA</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px double #28A745;
            padding-bottom: 15px;
        }

        .header h2 {
            margin: 0;
            color: #28A745;
            font-size: 18px;
        }

        .header p {
            margin: 3px 0;
            font-size: 12px;
            color: #555;
        }

        .meta-info table {
            border: none;
        }

        .meta-info td {
            border: none;
            padding: 2px 5px;
            font-size: 11px;
        }

        .meta-info td:first-child {
            font-weight: bold;
            width: 130px;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            margin-top: 15px;
        }

        table.data-table thead th {
            background-color: #28A745;
            color: #fff;
            padding: 8px 5px;
            text-align: center;
            font-size: 10px;
            border: 1px solid #1e7e34;
        }

        table.data-table tbody td {
            padding: 6px 5px;
            border: 1px solid #ddd;
            font-size: 10px;
        }

        table.data-table tbody tr:nth-child(even) {
            background-color: #f8f8f8;
        }

        .text-center {
            text-align: center;
        }

        .signature {
            margin-top: 50px;
            float: right;
            text-align: center;
            width: 200px;
        }

        .signature .line {
            border-top: 1px solid #333;
            margin-top: 60px;
            padding-top: 5px;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #888;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>LAPORAN HASIL PERANGKINGAN SISWA</h2>
        <p>Metode MOORA (Multi-Objective Optimization on the basis of Ratio Analysis)</p>
    </div>

    <div class="meta-info">
        <table>
            <tr>
                <td>Tahun Ajaran</td>
                <td>: {{ $tahunAjaran->tahun_ajaran }} — {{ $tahunAjaran->semester }}</td>
            </tr>
            <tr>
                <td>Sumber Perhitungan</td>
                <td>: {{ $sourceName }}</td>
            </tr>
            @if (!empty($filterKelas))
                <tr>
                    <td>Kelas</td>
                    <td>: {{ $filterKelas }}</td>
                </tr>
            @endif
            <tr>
                <td>Tanggal Cetak</td>
                <td>: {{ now()->format('d F Y H:i') }}</td>
            </tr>
            <tr>
                <td>Jumlah Siswa</td>
                <td>: {{ $hasilList->count() }} siswa</td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 90px;">NISN</th>
                <th>Nama Siswa</th>
                <th style="width: 70px;">Kelas</th>
                <th style="width: 80px;">Skor MOORA</th>
                <th style="width: 60px;">Rank</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($hasilList as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center">{{ $item->siswa->nisn }}</td>
                    <td>{{ $item->siswa->nama_siswa }}</td>
                    <td class="text-center">{{ $item->siswa->kelas->nama_kelas ?? '-' }}</td>
                    <td class="text-center">{{ number_format($item->skor_moora, 4) }}</td>
                    <td class="text-center">{{ $item->rank_moora }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature">
        <p>Mengetahui,<br>Kepala Sekolah</p>
        <div class="line">NIP. ........................</div>
    </div>

    <div style="clear: both;"></div>
    <div class="footer">Dicetak pada {{ now()->format('d/m/Y H:i:s') }} — SPK MOORA</div>
</body>

</html>
