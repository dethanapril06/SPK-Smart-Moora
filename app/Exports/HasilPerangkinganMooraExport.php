<?php

namespace App\Exports;

use App\Models\HasilAkhir;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class HasilPerangkinganMooraExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, WithEvents, ShouldAutoSize
{
    protected $filterTA;
    protected $filterSemester;
    protected $userId;
    protected $filterKelas;
    protected $tahunAjaran;
    protected $semester;
    protected $sourceName;
    protected $rowCount = 0;

    public function __construct($filterTA, $filterSemester, $userId, $filterKelas, $tahunAjaran, $semester, $sourceName)
    {
        $this->filterTA       = $filterTA;
        $this->filterSemester = $filterSemester;
        $this->userId         = $userId;
        $this->filterKelas    = $filterKelas;
        $this->tahunAjaran    = $tahunAjaran;
        $this->semester       = $semester;
        $this->sourceName     = $sourceName;
    }

    public function collection()
    {
        $data = HasilAkhir::with(['siswa.kelas'])
            ->where('id_ta', $this->filterTA)
            ->when($this->filterSemester, fn($q) => $q->where('id_semester', $this->filterSemester))
            ->whereNotNull('rank_moora')
            ->when($this->userId, fn($q) => $q->where('user_id', $this->userId))
            ->when($this->filterKelas, fn($q) => $q->whereHas('siswa', fn($sq) =>
                $sq->where('id_kelas', $this->filterKelas)
            ))
            ->get()
            ->sortBy('rank_moora')
            ->values();

        $this->rowCount = $data->count();
        return $data;
    }

    public function headings(): array
    {
        return ['No', 'NISN', 'Nama Siswa', 'Kelas', 'Skor MOORA', 'Rank MOORA'];
    }

    public function map($item): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $item->siswa->nisn ?? '-',
            $item->siswa->nama_siswa ?? '-',
            $item->siswa->kelas->nama_kelas ?? '-',
            number_format($item->skor_moora, 4),
            $item->rank_moora,
        ];
    }

    public function title(): string
    {
        return 'Hasil Perangkingan MOORA';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                // Hijau untuk MOORA
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '28A745']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->insertNewRowBefore(1, 3);

                $semLabel = $this->semester ? $this->semester->nama_semester : $this->tahunAjaran->semester;
                $sheet->setCellValue('A1', 'LAPORAN HASIL PERANGKINGAN SISWA — METODE MOORA');
                $sheet->setCellValue('A2', 'Tahun Ajaran: ' . $this->tahunAjaran->tahun_ajaran . ' - ' . $semLabel);
                $sheet->setCellValue('A3', 'Sumber: ' . $this->sourceName);

                foreach (['A1', 'A2', 'A3'] as $cell) {
                    $sheet->mergeCells($cell . ':F' . substr($cell, 1));
                }

                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getStyle('A2:A3')->applyFromArray([
                    'font'      => ['size' => 11],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $lastRow = $this->rowCount + 4;
                $sheet->getStyle('A4:F' . $lastRow)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                foreach (['A', 'D', 'F'] as $col) {
                    $sheet->getStyle($col . '5:' . $col . $lastRow)
                        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }
            },
        ];
    }
}