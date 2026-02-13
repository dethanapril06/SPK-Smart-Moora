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

class HasilPerangkinganExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, WithEvents, ShouldAutoSize
{
    protected $filterTA;
    protected $userId;
    protected $filterKelas;
    protected $tahunAjaran;
    protected $sourceName;
    protected $rowCount = 0;

    public function __construct($filterTA, $userId, $filterKelas, $tahunAjaran, $sourceName)
    {
        $this->filterTA = $filterTA;
        $this->userId = $userId;
        $this->filterKelas = $filterKelas;
        $this->tahunAjaran = $tahunAjaran;
        $this->sourceName = $sourceName;
    }

    public function collection()
    {
        $data = HasilAkhir::with(['siswa.kelas'])
            ->where('id_ta', $this->filterTA)
            ->when($this->userId, function ($q) {
                $q->where('user_id', $this->userId);
            })
            ->when($this->filterKelas, function ($q) {
                $q->whereHas('siswa', function ($sq) {
                    $sq->where('id_kelas', $this->filterKelas);
                });
            })
            ->get()
            ->sortBy('rank_smart')
            ->values();

        $this->rowCount = $data->count();
        return $data;
    }

    public function headings(): array
    {
        return [
            'No',
            'NISN',
            'Nama Siswa',
            'Kelas',
            'Skor SMART',
            'Rank SMART',
            'Skor MOORA',
            'Rank MOORA',
        ];
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
            number_format($item->skor_smart, 4),
            $item->rank_smart,
            number_format($item->skor_moora, 4),
            $item->rank_moora,
        ];
    }

    public function title(): string
    {
        return 'Hasil Perangkingan';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '696CFF'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $this->rowCount + 1;

                // Title rows above data
                $sheet->insertNewRowBefore(1, 3);

                $sheet->setCellValue('A1', 'LAPORAN HASIL PERANGKINGAN SISWA');
                $sheet->setCellValue('A2', 'Tahun Ajaran: ' . $this->tahunAjaran->tahun_ajaran . ' - ' . $this->tahunAjaran->semester);
                $sheet->setCellValue('A3', 'Sumber: ' . $this->sourceName);

                $sheet->mergeCells('A1:H1');
                $sheet->mergeCells('A2:H2');
                $sheet->mergeCells('A3:H3');

                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getStyle('A2:A3')->applyFromArray([
                    'font' => ['size' => 11],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Border for data area
                $dataRange = 'A4:H' . ($lastRow + 3);
                $sheet->getStyle($dataRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);

                // Center align specific columns
                $centerCols = ['A', 'D', 'F', 'H'];
                foreach ($centerCols as $col) {
                    $sheet->getStyle($col . '5:' . $col . ($lastRow + 3))->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }
            },
        ];
    }
}
