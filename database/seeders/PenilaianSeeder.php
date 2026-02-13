<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Penilaian;
use App\Models\Siswa;
use App\Models\Kriteria;
use App\Models\SubKriteria;
use App\Models\TahunAjaran;
use App\Models\RiwayatPelanggaran;

class PenilaianSeeder extends Seeder
{
    /**
     * Run the database seeders.
     */
    public function run(): void
    {
        // Get all required data
        $siswaList = Siswa::all();
        $kriteriaList = Kriteria::all();
        $tahunAjaranAktif = TahunAjaran::where('is_active', 1)->first();
        
        if (!$tahunAjaranAktif) {
            $this->command->error('Tidak ada tahun ajaran aktif!');
            return;
        }
        
        $this->command->info('Generating penilaian data...');
        
        foreach ($siswaList as $siswa) {
            $this->command->info("Processing: {$siswa->nama_siswa}");
            
            foreach ($kriteriaList as $kriteria) {
                $nilaiAsli = null;
                $nilaiKonversi = null;
                
                // C5 - Jumlah Poin Pelanggaran (Auto-calculated)
                if ($kriteria->kode_kriteria == 'C5') {
                    $nilaiAsli = $this->calculateC5($siswa->id_siswa, $tahunAjaranAktif->id_ta);
                    $nilaiKonversi = $this->convertNilaiToSubKriteria($kriteria->id_kriteria, $nilaiAsli);
                } 
                // C1 - Nilai Pengetahuan (70-95)
                elseif ($kriteria->kode_kriteria == 'C1') {
                    $nilaiAsli = rand(70, 95);
                    $nilaiKonversi = $this->convertNilaiToSubKriteria($kriteria->id_kriteria, $nilaiAsli);
                }
                // C2 - Nilai Keterampilan (65-90)
                elseif ($kriteria->kode_kriteria == 'C2') {
                    $nilaiAsli = rand(65, 90);
                    $nilaiKonversi = $this->convertNilaiToSubKriteria($kriteria->id_kriteria, $nilaiAsli);
                }
                // C3 - Sikap (60-95)
                elseif ($kriteria->kode_kriteria == 'C3') {
                    $nilaiAsli = rand(60, 95);
                    $nilaiKonversi = $this->convertNilaiToSubKriteria($kriteria->id_kriteria, $nilaiAsli);
                }
                // C4 - Ekstrakulikuler (50-95)
                elseif ($kriteria->kode_kriteria == 'C4') {
                    $nilaiAsli = rand(50, 95);
                    $nilaiKonversi = $this->convertNilaiToSubKriteria($kriteria->id_kriteria, $nilaiAsli);
                }
                // C6 - Absensi (jumlah ketidakhadiran, 0-20)
                elseif ($kriteria->kode_kriteria == 'C6') {
                    $nilaiAsli = rand(0, 20);
                    $nilaiKonversi = $this->convertNilaiToSubKriteria($kriteria->id_kriteria, $nilaiAsli);
                }
                
                // Create penilaian
                if ($nilaiAsli !== null) {
                    Penilaian::create([
                        'id_siswa' => $siswa->id_siswa,
                        'id_kriteria' => $kriteria->id_kriteria,
                        'id_ta' => $tahunAjaranAktif->id_ta,
                        'nilai_asli' => $nilaiAsli,
                        'nilai_konversi' => $nilaiKonversi,
                    ]);
                    
                    $this->command->line("  - {$kriteria->kode_kriteria}: {$nilaiAsli} → {$nilaiKonversi}");
                }
            }
        }
        
        $this->command->info('Penilaian data seeded successfully!');
    }
    
    /**
     * Calculate C5 (Total Poin Pelanggaran) for a student in a specific TA
     */
    private function calculateC5($id_siswa, $id_ta)
    {
        $totalPoin = RiwayatPelanggaran::where('id_siswa', $id_siswa)
            ->where('id_ta', $id_ta)
            ->with('jenisPelanggaran')
            ->get()
            ->sum(function($riwayat) {
                return $riwayat->jenisPelanggaran ? $riwayat->jenisPelanggaran->bobot_poin : 0;
            });
        
        return $totalPoin;
    }
    
    /**
     * Convert nilai_asli to nilai_konversi based on SubKriteria ranges
     */
    private function convertNilaiToSubKriteria($id_kriteria, $nilai_asli)
    {
        $subKriteria = SubKriteria::where('id_kriteria', $id_kriteria)
            ->where('nilai_awal', '<=', $nilai_asli)
            ->where('nilai_akhir', '>=', $nilai_asli)
            ->first();
        
        return $subKriteria ? $subKriteria->bobot_subkriteria : null;
    }
}
