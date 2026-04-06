<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MataPelajaran;

class MataPelajaranSeeder extends Seeder
{
    public function run(): void
    {
        $mataPelajaran = [
            ['kode_mapel' => 'AGM', 'nama_mapel' => 'Agama'],
            ['kode_mapel' => 'PKN', 'nama_mapel' => 'Pendidikan Pancasila dan Kewarganegaraan'],
            ['kode_mapel' => 'BIN', 'nama_mapel' => 'Bahasa Indonesia'],
            ['kode_mapel' => 'MTK', 'nama_mapel' => 'Matematika'],
            ['kode_mapel' => 'BING', 'nama_mapel' => 'Bahasa Inggris'],
            ['kode_mapel' => 'SJI', 'nama_mapel' => 'Sejarah Indonesia'],
            ['kode_mapel' => 'SNB', 'nama_mapel' => 'Seni Budaya'],
            ['kode_mapel' => 'PJOK', 'nama_mapel' => 'Pendidikan Jasmani, Olahraga dan Kesehatan'],
            ['kode_mapel' => 'PRK', 'nama_mapel' => 'Prakarya dan Kewirausahaan'],
            ['kode_mapel' => 'TIK', 'nama_mapel' => 'Teknologi Informasi dan Komunikasi'],
            ['kode_mapel' => 'BIO', 'nama_mapel' => 'Biologi'],
            ['kode_mapel' => 'FIS', 'nama_mapel' => 'Fisika'],
            ['kode_mapel' => 'KIM', 'nama_mapel' => 'Kimia'],
            ['kode_mapel' => 'EKO', 'nama_mapel' => 'Ekonomi'],
            ['kode_mapel' => 'GEOG', 'nama_mapel' => 'Geografi'],
            ['kode_mapel' => 'SOS', 'nama_mapel' => 'Sosiologi'],
            ['kode_mapel' => 'MTKP', 'nama_mapel' => 'Matematika Peminatan'],
            ['kode_mapel' => 'SJM', 'nama_mapel' => 'Sejarah Minat'],
            ['kode_mapel' => 'SJM', 'nama_mapel' => 'Sejarah Minat'],
            ['kode_mapel' => 'KIMP', 'nama_mapel' => 'Kimia Peminatan'],
            ['kode_mapel' => 'SOSM', 'nama_mapel' => 'Sosiologi Lintas Minat'],
            ['kode_mapel' => 'SASI', 'nama_mapel' => 'Sastra Indonesia'],
            ['kode_mapel' => 'BHSJ', 'nama_mapel' => 'Bahasa Jerman'],
            ['kode_mapel' => 'SASING', 'nama_mapel' => 'Sastra Inggris'],
            ['kode_mapel' => 'ANTR', 'nama_mapel' => 'Antropologi'],


        ];

        foreach ($mataPelajaran as $mapel) {
            MataPelajaran::firstOrCreate(
                ['kode_mapel' => $mapel['kode_mapel']],
                $mapel
            );
        }
    }
}
