<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kriteria = [
            [
                'kode_kriteria' => 'C1',
                'nama_kriteria' => 'Nilai Pengetahuan',
                'jenis_kriteria' => 'Benefit',
                'bobot' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_kriteria' => 'C2',
                'nama_kriteria' => 'Nilai Keterampilan',
                'jenis_kriteria' => 'Benefit',
                'bobot' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_kriteria' => 'C3',
                'nama_kriteria' => 'Sikap Spiritual',
                'jenis_kriteria' => 'Benefit',
                'bobot' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_kriteria' => 'C4',
                'nama_kriteria' => 'Sikap Sosial',
                'jenis_kriteria' => 'Benefit',
                'bobot' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_kriteria' => 'C5',
                'nama_kriteria' => 'Ekstrakulikuler',
                'jenis_kriteria' => 'Benefit',
                'bobot' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_kriteria' => 'C6',
                'nama_kriteria' => 'Jumlah poin pelanggaran',
                'jenis_kriteria' => 'Cost',
                'bobot' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_kriteria' => 'C7',
                'nama_kriteria' => 'Absensi',
                'jenis_kriteria' => 'Cost',
                'bobot' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        \DB::table('tb_kriteria')->insert($kriteria);
    }
}
