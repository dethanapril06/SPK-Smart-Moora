<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Finalis {{ strtoupper($method) }}</title>
    <style>
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 10px; color: #333; }
        h2, p { margin: 0 0 6px; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 18px; }
        th, td { border: 1px solid #ccc; padding: 6px 4px; }
        th { background: {{ $method === 'smart' ? '#696CFF' : '#71DD37' }}; color: #fff; text-align: center; }
        .text-center { text-align: center; }
        .tingkat { background: #f1f1f1; font-weight: bold; }
        .footer { margin-top: 20px; text-align: right; color: #777; }
    </style>
</head>
<body>
    <h2>LAPORAN FINALIS {{ strtoupper($method) }}</h2>
    <p>Tahun Ajaran {{ $tahunAjaran->tahun_ajaran }} - {{ $tahunAjaran->semester }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tingkat</th>
                <th>Rank</th>
                <th>NISN</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Rank Kelas</th>
                <th>Skor</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($hasilList as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center tingkat">{{ $item->tingkat }}</td>
                    <td class="text-center">{{ $item->rank }}</td>
                    <td class="text-center">{{ $item->siswa->nisn ?? '-' }}</td>
                    <td>{{ $item->siswa->nama_siswa ?? '-' }}</td>
                    <td class="text-center">{{ $item->siswa->kelas->nama_kelas ?? '-' }}</td>
                    <td class="text-center">{{ $item->source_rank }}</td>
                    <td class="text-center">{{ number_format($item->skor, 4) }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center">Belum ada hasil finalis.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Dicetak pada {{ now()->format('d/m/Y H:i:s') }}</div>
</body>
</html>
