<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MataPelajaran;

class MataPelajaranSeeder extends Seeder
{
    public function run(): void
    {
        $mataPelajaran = [
            ['kode_mapel' => 'PAI', 'nama_mapel' => 'Pendidikan Agama Islam'],
            ['kode_mapel' => 'PKN', 'nama_mapel' => 'Pendidikan Kewarganegaraan'],
            ['kode_mapel' => 'BIN', 'nama_mapel' => 'Bahasa Indonesia'],
            ['kode_mapel' => 'MTK', 'nama_mapel' => 'Matematika'],
            ['kode_mapel' => 'IPA', 'nama_mapel' => 'Ilmu Pengetahuan Alam'],
            ['kode_mapel' => 'IPS', 'nama_mapel' => 'Ilmu Pengetahuan Sosial'],
            ['kode_mapel' => 'SBK', 'nama_mapel' => 'Seni Budaya dan Keterampilan'],
            ['kode_mapel' => 'PJOK', 'nama_mapel' => 'Pendidikan Jasmani, Olahraga dan Kesehatan'],
            ['kode_mapel' => 'BING', 'nama_mapel' => 'Bahasa Inggris'],
            ['kode_mapel' => 'TIK', 'nama_mapel' => 'Teknologi Informasi dan Komunikasi'],
        ];

        foreach ($mataPelajaran as $mapel) {
            MataPelajaran::firstOrCreate(
                ['kode_mapel' => $mapel['kode_mapel']],
                $mapel
            );
        }
    }
}
