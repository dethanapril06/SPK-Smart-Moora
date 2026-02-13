<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Hasil Perangkingan</title>
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
            border-bottom: 3px double #696CFF;
            padding-bottom: 15px;
        }

        .header h2 {
            margin: 0;
            color: #696CFF;
            font-size: 18px;
        }

        .header p {
            margin: 3px 0;
            font-size: 12px;
            color: #555;
        }

        .meta-info {
            margin-bottom: 15px;
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
        }

        table.data-table thead th {
            background-color: #696CFF;
            color: #fff;
            padding: 8px 5px;
            text-align: center;
            font-size: 10px;
            border: 1px solid #5a5dd6;
        }

        table.data-table tbody td {
            padding: 6px 5px;
            border: 1px solid #ddd;
            font-size: 10px;
        }

        table.data-table tbody tr:nth-child(even) {
            background-color: #f8f8f8;
        }

        table.data-table tbody tr:nth-child(-n+3) td:first-child {
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #888;
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
    </style>
</head>

<body>
    <div class="header">
        <h2>LAPORAN HASIL PERANGKINGAN SISWA</h2>
        <p>Metode SMART & MOORA</p>
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
            @if (isset($filterKelas) && $filterKelas)
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
                <th style="width: 80px;">NISN</th>
                <th>Nama Siswa</th>
                <th style="width: 60px;">Kelas</th>
                <th style="width: 70px;">Skor SMART</th>
                <th style="width: 50px;">Rank SMART</th>
                <th style="width: 70px;">Skor MOORA</th>
                <th style="width: 50px;">Rank MOORA</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($hasilList->sortBy('rank_smart') as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center">{{ $item->siswa->nisn }}</td>
                    <td>{{ $item->siswa->nama_siswa }}</td>
                    <td class="text-center">{{ $item->siswa->kelas->nama_kelas ?? '-' }}</td>
                    <td class="text-center">{{ number_format($item->skor_smart, 4) }}</td>
                    <td class="text-center">{{ $item->rank_smart }}</td>
                    <td class="text-center">{{ number_format($item->skor_moora, 4) }}</td>
                    <td class="text-center">{{ $item->rank_moora }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature">
        <p>Mengetahui,<br>Kepala Sekolah</p>
        <div class="line">
            NIP. ........................
        </div>
    </div>

    <div style="clear: both;"></div>

    <div class="footer">
        Dicetak pada {{ now()->format('d/m/Y H:i:s') }} — SPK SMART & MOORA
    </div>
</body>

</html>
