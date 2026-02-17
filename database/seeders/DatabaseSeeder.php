<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            TahunAjaranSeeder::class,
            KelasSeeder::class,
            SiswaSeeder::class,
            KriteriaSeeder::class,
            SubKriteriaSeeder::class,
            MataPelajaranSeeder::class,
            JenisPelanggaranSeeder::class,
            NilaiPengetahuanSeeder::class,
            NilaiKeterampilanSeeder::class,
            NilaiSikapSeeder::class,
            NilaiEkstrakurikulerSeeder::class,
            NilaiAbsensiSeeder::class,
        ]);
    }
}
