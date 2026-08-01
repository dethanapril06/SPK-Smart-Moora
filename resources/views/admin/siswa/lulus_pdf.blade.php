<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Daftar Siswa Lulus</title>
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
            text-transform: uppercase;
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
            width: 100%;
        }

        .meta-info td {
            border: none;
            padding: 3px 5px;
            font-size: 11px;
        }

        .meta-info td.label {
            font-weight: bold;
            width: 120px;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            margin-top: 10px;
        }

        table.data-table thead th {
            background-color: #696CFF;
            color: #fff;
            padding: 8px 6px;
            text-align: center;
            font-size: 10px;
            border: 1px solid #5a5dd6;
        }

        table.data-table tbody td {
            padding: 6px 6px;
            border: 1px solid #ddd;
            font-size: 10px;
        }

        table.data-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .text-center {
            text-align: center;
        }

        .signature {
            margin-top: 40px;
            float: right;
            text-align: center;
            width: 220px;
        }

        .signature p {
            margin: 3px 0;
        }

        .signature .space {
            height: 60px;
        }

        .signature .line {
            font-weight: bold;
            border-top: 1px solid #333;
            padding-top: 5px;
        }

        .footer {
            margin-top: 30px;
            font-size: 9px;
            color: #888;
            clear: both;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Laporan Data Siswa Lulus (Alumni)</h2>
        <p>Sistem Pendukung Keputusan Pemilihan Siswa Berprestasi</p>
    </div>

    <div class="meta-info">
        <table>
            <tr>
                <td class="label">Tahun Lulus Filter:</td>
                <td>{{ $tahunLulus ? $tahunLulus : 'Semua Tahun Lulus' }}</td>
                <td class="label">Tanggal Cetak:</td>
                <td style="text-align: right;">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td class="label">Jenis Kelamin:</td>
                <td>{{ $jenisKelamin == 'L' ? 'Laki-Laki' : ($jenisKelamin == 'P' ? 'Perempuan' : 'Semua') }}</td>
                <td class="label">Pencarian:</td>
                <td style="text-align: right;">{{ $search ? '"' . $search . '"' : '-' }}</td>
            </tr>
            <tr>
                <td class="label">Total Siswa Lulus:</td>
                <td colspan="3"><strong>{{ count($siswaList) }} Siswa</strong></td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 90px;">NISN</th>
                <th>Nama Siswa</th>
                <th style="width: 80px;">Jenis Kelamin</th>
                <th style="width: 90px;">Tahun Lulus</th>
                <th>Alamat</th>
            </tr>
        </thead>
        <tbody>
            @forelse($siswaList as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center"><strong>{{ $item->nisn }}</strong></td>
                    <td>{{ $item->nama_siswa }}</td>
                    <td class="text-center">
                        {{ $item->jenis_kelamin == 'L' ? 'Laki-Laki' : 'Perempuan' }}
                    </td>
                    <td class="text-center">
                        <strong>{{ $item->tahun_lulus ?? '-' }}</strong>
                    </td>
                    <td>{{ $item->alamat ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px; color: #888;">
                        Tidak ada data siswa lulus yang sesuai dengan kriteria filter.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="signature">
        <p>Dicetak Pada: {{ date('d-m-Y') }}</p>
        <p>Mengetahui,</p>
        <p>Kepala Sekolah</p>
        <div class="space"></div>
        <p class="line">( .................................... )</p>
    </div>

    <div class="footer">
        <i>Dokumen ini dihasilkan secara otomatis oleh Sistem Pendukung Keputusan.</i>
    </div>
</body>

</html>
