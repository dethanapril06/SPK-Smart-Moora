<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TahunAjaran;

class TahunAjaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tahunAjaranList = [
            [
                'tahun_ajaran' => '2024/2025',
                'is_active' => false,
            ],
            [
                'tahun_ajaran' => '2025/2026',
                'is_active' => true,
            ],
        ];

        foreach ($tahunAjaranList as $data) {
            $tahunAjaran = TahunAjaran::updateOrCreate(
                ['tahun_ajaran' => $data['tahun_ajaran']],
                [
                    'semester' => 'Ganjil',
                    'is_active' => $data['is_active'],
                ]
            );

            $tahunAjaran->ensureDefaultSemesters($tahunAjaran->is_active);
        }
    }
}
