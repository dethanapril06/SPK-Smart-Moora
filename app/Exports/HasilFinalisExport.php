<?php

namespace App\Exports;

use App\Models\HasilFinalis;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HasilFinalisExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    private int $number = 0;

    public function __construct(
        private readonly int $tahunAjaranId,
        private readonly ?int $semesterId,
        private readonly int $adminUserId,
        private readonly string $method
    ) {
    }

    public function collection()
    {
        return HasilFinalis::with('siswa.kelas')
            ->where('id_ta', $this->tahunAjaranId)
            ->when($this->semesterId, fn($q) => $q->where('id_semester', $this->semesterId))
            ->where('user_id', $this->adminUserId)
            ->where('metode', $this->method)
            ->orderByRaw("FIELD(tingkat, 'X', 'XI', 'XII')")
            ->orderBy('rank')
            ->get();
    }

    public function headings(): array
    {
        return ['No', 'Tingkat', 'Rank', 'NISN', 'Nama Siswa', 'Kelas', 'Asal Rank Kelas', 'Skor ' . strtoupper($this->method)];
    }

    public function map($item): array
    {
        return [
            ++$this->number,
            $item->tingkat,
            $item->rank,
            $item->siswa->nisn ?? '-',
            $item->siswa->nama_siswa ?? '-',
            $item->siswa->kelas->nama_kelas ?? '-',
            $item->source_rank,
            number_format($item->skor, 4),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->setTitle('Finalis ' . strtoupper($this->method));

        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $this->method === 'smart' ? '696CFF' : '71DD37']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}
