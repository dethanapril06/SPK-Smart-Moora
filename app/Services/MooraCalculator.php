<?php

namespace App\Services;

use App\Models\Penilaian;
use App\Models\Kriteria;
use Illuminate\Support\Collection;

class MooraCalculator
{
    protected $detailedSteps = [];
    
    /**
     * Calculate MOORA scores for all students in a given TA and Semester
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
        
        // Step 1: Calculate sum of squares for each kriteria (vector normalization)
        $sqrtSumSquares = $this->calculateSqrtSumSquares($penilaianData, $kriteriaList);
        
        $results = [];
        
        // Step 2 & 3: Normalize, weight, and calculate Yi for each student
        foreach ($penilaianData as $siswaId => $penilaianList) {
            // Check if student has all kriteria
            if ($penilaianList->count() < $kriteriaList->count()) {
                continue; // Skip incomplete data
            }
            
            $benefitSum = 0;
            $costSum = 0;
            
            $studentSteps = [
                'siswa_id' => $siswaId,
                'nama' => $penilaianList->first()->siswa->nama_siswa,
                'kriteria_details' => []
            ];
            
            foreach ($kriteriaList as $kriteria) {
                $penilaian = $penilaianList->firstWhere('id_kriteria', $kriteria->id_kriteria);
                
                if (!$penilaian) continue;
                
                $nilai = $penilaian->nilai_konversi;
                
                // Vector normalization: xij / sqrt(sum of xij²)
                $normalized = $nilai / $sqrtSumSquares[$kriteria->id_kriteria];
                
                // Weight the normalized value
                $weight = $kriteria->bobot / $totalBobot;
                $weighted = $normalized * $weight;
                
                // Sum based on kriteria type
                if ($kriteria->jenis_kriteria == 'Benefit') {
                    $benefitSum += $weighted;
                } else { // Cost
                    $costSum += $weighted;
                }
                
                // Store for detailed steps
                $studentSteps['kriteria_details'][] = [
                    'kode' => $kriteria->kode_kriteria,
                    'nama' => $kriteria->nama_kriteria,
                    'jenis' => $kriteria->jenis_kriteria,
                    'nilai_asli' => $penilaian->nilai_asli,
                    'nilai_konversi' => $nilai,
                    'sqrt_sum_squares' => round($sqrtSumSquares[$kriteria->id_kriteria], 4),
                    'normalized' => round($normalized, 4),
                    'bobot' => $kriteria->bobot,
                    'weight' => round($weight, 4),
                    'weighted' => round($weighted, 4),
                    'type_sum' => $kriteria->jenis_kriteria == 'Benefit' ? 'benefit' : 'cost'
                ];
            }
            
            // Calculate Yi = Σ benefit - Σ cost
            $skorMoora = $benefitSum - $costSum;
            
            $studentSteps['benefit_sum'] = round($benefitSum, 4);
            $studentSteps['cost_sum'] = round($costSum, 4);
            $studentSteps['skor_moora'] = round($skorMoora, 4);
            
            $results[] = [
                'id_siswa' => $siswaId,
                'skor_moora' => round($skorMoora, 4),
                'steps' => $studentSteps
            ];
        }
        
        // Step 4: Rank by Yi (descending)
        usort($results, function($a, $b) {
            return $b['skor_moora'] <=> $a['skor_moora'];
        });
        
        // Assign ranks
        foreach ($results as $index => &$result) {
            $result['rank_moora'] = $index + 1;
        }
        
        $this->detailedSteps = $results;
        
        return $results;
    }
    
    /**
     * Calculate sqrt of sum of squares for each kriteria (for vector normalization)
     */
    protected function calculateSqrtSumSquares(Collection $penilaianData, Collection $kriteriaList): array
    {
        $sqrtSumSquares = [];
        
        foreach ($kriteriaList as $kriteria) {
            $sumSquares = 0;
            
            foreach ($penilaianData as $penilaianList) {
                $penilaian = $penilaianList->firstWhere('id_kriteria', $kriteria->id_kriteria);
                if ($penilaian) {
                    $sumSquares += pow($penilaian->nilai_konversi, 2);
                }
            }
            
            $sqrtSumSquares[$kriteria->id_kriteria] = sqrt($sumSquares);
        }
        
        return $sqrtSumSquares;
    }
    
    /**
     * Get detailed calculation steps for display
     */
    public function getDetailedSteps(): array
    {
        return $this->detailedSteps;
    }
}
