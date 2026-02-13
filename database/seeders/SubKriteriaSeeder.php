<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubKriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil ID kriteria berdasarkan kode
        $kriteriaC1 = \DB::table('tb_kriteria')->where('kode_kriteria', 'C1')->first();
        
        if ($kriteriaC1) {
            $subKriteriaC1 = [
                [
                    'id_kriteria' => $kriteriaC1->id_kriteria,
                    'nama_subkriteria' => 'Sangat Mampu',
                    'nilai_awal' => 90,
                    'nilai_akhir' => 100,
                    'bobot_subkriteria' => 4,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id_kriteria' => $kriteriaC1->id_kriteria,
                    'nama_subkriteria' => 'Mampu',
                    'nilai_awal' => 80,
                    'nilai_akhir' => 89,
                    'bobot_subkriteria' => 3,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id_kriteria' => $kriteriaC1->id_kriteria,
                    'nama_subkriteria' => 'Cukup Mampu',
                    'nilai_awal' => 70,
                    'nilai_akhir' => 79,
                    'bobot_subkriteria' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id_kriteria' => $kriteriaC1->id_kriteria,
                    'nama_subkriteria' => 'Kurang Mampu',
                    'nilai_awal' => 0,
                    'nilai_akhir' => 69,
                    'bobot_subkriteria' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            \DB::table('tb_subkriteria')->insert($subKriteriaC1);
        }

        // Sub Kriteria C2 - Nilai Keterampilan
        $kriteriaC2 = \DB::table('tb_kriteria')->where('kode_kriteria', 'C2')->first();
        
        if ($kriteriaC2) {
            $subKriteriaC2 = [
                [
                    'id_kriteria' => $kriteriaC2->id_kriteria,
                    'nama_subkriteria' => 'Sangat Mampu',
                    'nilai_awal' => 90,
                    'nilai_akhir' => 100,
                    'bobot_subkriteria' => 4,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id_kriteria' => $kriteriaC2->id_kriteria,
                    'nama_subkriteria' => 'Mampu',
                    'nilai_awal' => 80,
                    'nilai_akhir' => 89,
                    'bobot_subkriteria' => 3,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id_kriteria' => $kriteriaC2->id_kriteria,
                    'nama_subkriteria' => 'Cukup Mampu',
                    'nilai_awal' => 70,
                    'nilai_akhir' => 79,
                    'bobot_subkriteria' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id_kriteria' => $kriteriaC2->id_kriteria,
                    'nama_subkriteria' => 'Kurang Mampu',
                    'nilai_awal' => 0,
                    'nilai_akhir' => 69,
                    'bobot_subkriteria' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            \DB::table('tb_subkriteria')->insert($subKriteriaC2);
        }

        // Sub Kriteria C3 - Sikap
        $kriteriaC3 = \DB::table('tb_kriteria')->where('kode_kriteria', 'C3')->first();
        
        if ($kriteriaC3) {
            $subKriteriaC3 = [
                [
                    'id_kriteria' => $kriteriaC3->id_kriteria,
                    'nama_subkriteria' => 'Sangat Baik',
                    'nilai_awal' => 88,
                    'nilai_akhir' => 100,
                    'bobot_subkriteria' => 4,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id_kriteria' => $kriteriaC3->id_kriteria,
                    'nama_subkriteria' => 'Baik',
                    'nilai_awal' => 74,
                    'nilai_akhir' => 87,
                    'bobot_subkriteria' => 3,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id_kriteria' => $kriteriaC3->id_kriteria,
                    'nama_subkriteria' => 'Cukup',
                    'nilai_awal' => 61,
                    'nilai_akhir' => 73,
                    'bobot_subkriteria' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id_kriteria' => $kriteriaC3->id_kriteria,
                    'nama_subkriteria' => 'Kurang',
                    'nilai_awal' => 0,
                    'nilai_akhir' => 60,
                    'bobot_subkriteria' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            \DB::table('tb_subkriteria')->insert($subKriteriaC3);
        }

        // Sub Kriteria C4 - Ekstrakulikuler
        $kriteriaC4 = \DB::table('tb_kriteria')->where('kode_kriteria', 'C4')->first();
        
        if ($kriteriaC4) {
            $subKriteriaC4 = [
                [
                    'id_kriteria' => $kriteriaC4->id_kriteria,
                    'nama_subkriteria' => 'Sangat Baik',
                    'nilai_awal' => 90,
                    'nilai_akhir' => 100,
                    'bobot_subkriteria' => 4,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id_kriteria' => $kriteriaC4->id_kriteria,
                    'nama_subkriteria' => 'Baik',
                    'nilai_awal' => 80,
                    'nilai_akhir' => 89,
                    'bobot_subkriteria' => 3,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id_kriteria' => $kriteriaC4->id_kriteria,
                    'nama_subkriteria' => 'Cukup',
                    'nilai_awal' => 70,
                    'nilai_akhir' => 79,
                    'bobot_subkriteria' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id_kriteria' => $kriteriaC4->id_kriteria,
                    'nama_subkriteria' => 'Kurang',
                    'nilai_awal' => 0,
                    'nilai_akhir' => 69,
                    'bobot_subkriteria' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            \DB::table('tb_subkriteria')->insert($subKriteriaC4);
        }

        // Sub Kriteria C5 - Jumlah Poin Pelanggaran
        $kriteriaC5 = \DB::table('tb_kriteria')->where('kode_kriteria', 'C5')->first();
        
        if ($kriteriaC5) {
            $subKriteriaC5 = [
                [
                    'id_kriteria' => $kriteriaC5->id_kriteria,
                    'nama_subkriteria' => 'Sangat Baik',
                    'nilai_awal' => 0,
                    'nilai_akhir' => 0,
                    'bobot_subkriteria' => 4,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id_kriteria' => $kriteriaC5->id_kriteria,
                    'nama_subkriteria' => 'Baik',
                    'nilai_awal' => 1,
                    'nilai_akhir' => 5,
                    'bobot_subkriteria' => 3,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id_kriteria' => $kriteriaC5->id_kriteria,
                    'nama_subkriteria' => 'Cukup',
                    'nilai_awal' => 6,
                    'nilai_akhir' => 10,
                    'bobot_subkriteria' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id_kriteria' => $kriteriaC5->id_kriteria,
                    'nama_subkriteria' => 'Kurang',
                    'nilai_awal' => 11,
                    'nilai_akhir' => 999,
                    'bobot_subkriteria' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            \DB::table('tb_subkriteria')->insert($subKriteriaC5);
        }

        // Sub Kriteria C6 - Absensi
        $kriteriaC6 = \DB::table('tb_kriteria')->where('kode_kriteria', 'C6')->first();
        
        if ($kriteriaC6) {
            $subKriteriaC6 = [
                [
                    'id_kriteria' => $kriteriaC6->id_kriteria,
                    'nama_subkriteria' => 'Sangat Baik',
                    'nilai_awal' => 0,
                    'nilai_akhir' => 0,
                    'bobot_subkriteria' => 4,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id_kriteria' => $kriteriaC6->id_kriteria,
                    'nama_subkriteria' => 'Baik',
                    'nilai_awal' => 1,
                    'nilai_akhir' => 5,
                    'bobot_subkriteria' => 3,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id_kriteria' => $kriteriaC6->id_kriteria,
                    'nama_subkriteria' => 'Cukup',
                    'nilai_awal' => 6,
                    'nilai_akhir' => 10,
                    'bobot_subkriteria' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id_kriteria' => $kriteriaC6->id_kriteria,
                    'nama_subkriteria' => 'Kurang',
                    'nilai_awal' => 11,
                    'nilai_akhir' => 999,
                    'bobot_subkriteria' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            \DB::table('tb_subkriteria')->insert($subKriteriaC6);
        }
    }
}
