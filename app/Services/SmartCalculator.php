<?php

namespace App\Services;

use App\Models\Penilaian;
use App\Models\Kriteria;
use Illuminate\Support\Collection;

class SmartCalculator
{
    protected $detailedSteps = [];
    
    /**
     * Calculate SMART scores for all students in a given TA and Semester
     * @param int $id_ta
     * @param array|null $siswaIds Optional array of siswa IDs to filter
     * @param int|null $id_semester Optional semester ID to filter
     */
    public function calculate($id_ta, ?array $siswaIds = null, $id_semester = null): array
    {
        $this->detailedSteps = [];
        
        // Get all penilaian for this TA and semester, grouped by student
        $penilaianData = Penilaian::where('id_ta', $id_ta)
            ->when($id_semester, function ($query, $id_semester) {
                return $query->where('id_semester', $id_semester);
            })
            ->when($siswaIds, function ($query, $siswaIds) {
                return $query->whereIn('id_siswa', $siswaIds);
            })
            ->with(['kriteria', 'siswa'])
            ->get()
            ->groupBy('id_siswa');
        
        // Get all kriteria
        $kriteriaList = Kriteria::orderBy('kode_kriteria')->get();
        
        // Calculate total bobot
        $totalBobot = $kriteriaList->sum('bobot');
        
        // Step 1: Get min and max for each kriteria
        $kriteriaStats = $this->getKriteriaStats($penilaianData, $kriteriaList);
        
        $results = [];
        
        // Step 2: Calculate for each student
        foreach ($penilaianData as $siswaId => $penilaianList) {
            // Check if student has all kriteria
            if ($penilaianList->count() < $kriteriaList->count()) {
                continue; // Skip incomplete data
            }
            
            $utilitySum = 0;
            $studentSteps = [
                'siswa_id' => $siswaId,
                'nama' => $penilaianList->first()->siswa->nama_siswa,
                'kriteria_details' => []
            ];
            
            foreach ($kriteriaList as $kriteria) {
                $penilaian = $penilaianList->firstWhere('id_kriteria', $kriteria->id_kriteria);
                
                if (!$penilaian) continue;
                
                $nilai = $penilaian->nilai_konversi;
                $stats = $kriteriaStats[$kriteria->id_kriteria];
                
                // Normalize based on type
                if ($stats['max'] - $stats['min'] == 0) {
                    $normalized = 1; // All values are the same
                } else {
                    if ($kriteria->jenis_kriteria == 'Benefit') {
                        $normalized = ($nilai - $stats['min']) / ($stats['max'] - $stats['min']);
                    } else { // Cost
                        $normalized = ($stats['max'] - $nilai) / ($stats['max'] - $stats['min']);
                    }
                }
                
                // Calculate weighted utility
                $weight = $kriteria->bobot / $totalBobot;
                $weightedUtility = $normalized * $weight;
                $utilitySum += $weightedUtility;
                
                // Store for detailed steps
                $studentSteps['kriteria_details'][] = [
                    'kode' => $kriteria->kode_kriteria,
                    'nama' => $kriteria->nama_kriteria,
                    'jenis' => $kriteria->jenis_kriteria,
                    'nilai_asli' => $penilaian->nilai_asli,
                    'nilai_konversi' => $nilai,
                    'min' => $stats['min'],
                    'max' => $stats['max'],
                    'normalized' => round($normalized, 4),
                    'bobot' => $kriteria->bobot,
                    'weight' => round($weight, 4),
                    'weighted_utility' => round($weightedUtility, 4)
                ];
            }
            
            // Calculate final SMART score (just the utility sum)
            $skorSmart = $utilitySum;
            
            $studentSteps['utility_sum'] = round($utilitySum, 4);
            $studentSteps['skor_smart'] = round($skorSmart, 4);
            
            $results[] = [
                'id_siswa' => $siswaId,
                'skor_smart' => round($skorSmart, 4),
                'steps' => $studentSteps
            ];
        }
        
        // Step 3: Rank by score (descending)
        usort($results, function($a, $b) {
            return $b['skor_smart'] <=> $a['skor_smart'];
        });
        
        // Assign ranks
        foreach ($results as $index => &$result) {
            $result['rank_smart'] = $index + 1;
        }
        
        $this->detailedSteps = $results;
        
        return $results;
    }
    
    /**
     * Get min and max values for each kriteria
     */
    protected function getKriteriaStats(Collection $penilaianData, Collection $kriteriaList): array
    {
        $stats = [];
        
        foreach ($kriteriaList as $kriteria) {
            $values = [];
            
            foreach ($penilaianData as $penilaianList) {
                $penilaian = $penilaianList->firstWhere('id_kriteria', $kriteria->id_kriteria);
                if ($penilaian) {
                    $values[] = $penilaian->nilai_konversi;
                }
            }
            
            $stats[$kriteria->id_kriteria] = [
                'min' => !empty($values) ? min($values) : 0,
                'max' => !empty($values) ? max($values) : 0,
            ];
        }
        
        return $stats;
    }
    
    /**
     * Get detailed calculation steps for display
     */
    public function getDetailedSteps(): array
    {
        return $this->detailedSteps;
    }
}
