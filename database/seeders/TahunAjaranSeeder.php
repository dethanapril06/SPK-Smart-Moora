<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TahunAjaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tahunAjaran = [
            [
                'tahun_ajaran' => '2024/2025',
                'semester' => 'Ganjil',
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tahun_ajaran' => '2024/2025',
                'semester' => 'Genap',
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tahun_ajaran' => '2025/2026',
                'semester' => 'Ganjil',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tahun_ajaran' => '2025/2026',
                'semester' => 'Genap',
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        \DB::table('tb_tahun_ajaran')->insert($tahunAjaran);
    }
}
