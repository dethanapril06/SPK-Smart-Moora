<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            $now = now();

            $affectedIds = DB::table('tb_kriteria')
                ->whereIn('kode_kriteria', ['C3', 'C4', 'C5', 'C6', 'C7'])
                ->pluck('id_kriteria');

            if ($affectedIds->isNotEmpty()) {
                DB::table('tb_penilaian')->whereIn('id_kriteria', $affectedIds)->delete();
                DB::table('tb_subkriteria')->whereIn('id_kriteria', $affectedIds)->delete();
            }

            $this->upsertKriteria('C3', 'Sikap Spiritual', 'Benefit', 3, $now);
            $this->upsertKriteria('C4', 'Sikap Sosial', 'Benefit', 3, $now);
            $this->upsertKriteria('C5', 'Ekstrakulikuler', 'Benefit', 3, $now);
            $this->upsertKriteria('C6', 'Jumlah poin pelanggaran', 'Cost', 2, $now);
            $this->upsertKriteria('C7', 'Absensi', 'Cost', 3, $now);

            $this->insertSubKriteria('C3', $this->sikapRanges(), $now);
            $this->insertSubKriteria('C4', $this->sikapRanges(), $now);
            $this->insertSubKriteria('C5', $this->benefitRanges(), $now);
            $this->insertSubKriteria('C6', $this->costRanges(), $now);
            $this->insertSubKriteria('C7', $this->costRanges(), $now);
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            $now = now();

            $affectedIds = DB::table('tb_kriteria')
                ->whereIn('kode_kriteria', ['C3', 'C4', 'C5', 'C6', 'C7'])
                ->pluck('id_kriteria');

            if ($affectedIds->isNotEmpty()) {
                DB::table('tb_penilaian')->whereIn('id_kriteria', $affectedIds)->delete();
                DB::table('tb_subkriteria')->whereIn('id_kriteria', $affectedIds)->delete();
            }

            DB::table('tb_kriteria')->where('kode_kriteria', 'C7')->delete();

            $this->upsertKriteria('C3', 'Sikap', 'Benefit', 3, $now);
            $this->upsertKriteria('C4', 'Ekstrakulikuler', 'Benefit', 3, $now);
            $this->upsertKriteria('C5', 'Jumlah poin pelanggaran', 'Cost', 2, $now);
            $this->upsertKriteria('C6', 'Absensi', 'Cost', 3, $now);

            $this->insertSubKriteria('C3', $this->sikapRanges(), $now);
            $this->insertSubKriteria('C4', $this->benefitRanges(), $now);
            $this->insertSubKriteria('C5', $this->costRanges(), $now);
            $this->insertSubKriteria('C6', $this->costRanges(), $now);
        });
    }

    private function upsertKriteria(string $kode, string $nama, string $jenis, int $bobot, $now): void
    {
        DB::table('tb_kriteria')->updateOrInsert(
            ['kode_kriteria' => $kode],
            [
                'nama_kriteria' => $nama,
                'jenis_kriteria' => $jenis,
                'bobot' => $bobot,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
    }

    private function insertSubKriteria(string $kode, array $ranges, $now): void
    {
        $kriteria = DB::table('tb_kriteria')->where('kode_kriteria', $kode)->first();

        if (!$kriteria) {
            return;
        }

        DB::table('tb_subkriteria')->insert(array_map(fn ($range) => [
            'id_kriteria' => $kriteria->id_kriteria,
            'nama_subkriteria' => $range[0],
            'nilai_awal' => $range[1],
            'nilai_akhir' => $range[2],
            'bobot_subkriteria' => $range[3],
            'created_at' => $now,
            'updated_at' => $now,
        ], $ranges));
    }

    private function sikapRanges(): array
    {
        return [
            ['Sangat Baik', 88, 100, 4],
            ['Baik', 74, 87, 3],
            ['Cukup', 61, 73, 2],
            ['Kurang', 0, 60, 1],
        ];
    }

    private function benefitRanges(): array
    {
        return [
            ['Sangat Baik', 90, 100, 4],
            ['Baik', 80, 89, 3],
            ['Cukup', 70, 79, 2],
            ['Kurang', 0, 69, 1],
        ];
    }

    private function costRanges(): array
    {
        return [
            ['Sangat Baik', 0, 0, 4],
            ['Baik', 1, 5, 3],
            ['Cukup', 6, 10, 2],
            ['Kurang', 11, 999, 1],
        ];
    }
};
