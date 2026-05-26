<?php

namespace App\Services;

use App\Models\HasilFinalis;
use App\Models\Kelas;
use App\Models\Kriteria;
use App\Models\Penilaian;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

class FinalisCalculatorService
{
    public function __construct(
        protected SmartCalculator $smartCalculator,
        protected MooraCalculator $mooraCalculator
    ) {
    }

    public function calculate(int $idTa, string $method, ?int $userId = null): array
    {
        $method = strtolower($method);
        $this->validateMethod($method);

        $candidates = $this->getClassCandidatesByTingkat($idTa, $method);

        $totalCandidates = collect($candidates['candidates_by_tingkat'])->flatten(1)->count();
        if ($totalCandidates < 2) {
            throw new RuntimeException('Minimal 2 kandidat dari hasil 3 besar per kelas diperlukan untuk menghitung 10 besar.');
        }

        $savedResults = [];
        DB::transaction(function () use ($idTa, $method, $userId, $candidates, &$savedResults) {
            HasilFinalis::where('id_ta', $idTa)
                ->where('user_id', $userId)
                ->where('metode', $method)
                ->delete();

            foreach ($candidates['candidates_by_tingkat'] as $tingkat => $tingkatCandidates) {
                if (count($tingkatCandidates) < 2) {
                    $savedResults[$tingkat] = [];
                    continue;
                }

                $candidateIds = collect($tingkatCandidates)->pluck('id_siswa')->unique()->values()->all();
                $sourceRanks = collect($tingkatCandidates)->pluck('source_rank', 'id_siswa');
                $finalResults = $this->runCalculator($idTa, $method, $candidateIds);
                $topResults = array_slice($finalResults, 0, 10);
                $savedResults[$tingkat] = $topResults;

                foreach ($topResults as $result) {
                    HasilFinalis::create([
                        'id_siswa' => $result['id_siswa'],
                        'id_ta' => $idTa,
                        'user_id' => $userId,
                        'metode' => $method,
                        'tingkat' => $tingkat,
                        'skor' => $result["skor_{$method}"],
                        'rank' => $result["rank_{$method}"],
                        'source_rank' => $sourceRanks[$result['id_siswa']] ?? 1,
                    ]);
                }
            }
        });

        return [
            'results' => $savedResults,
            'candidate_count' => $totalCandidates,
            'skipped_classes' => $candidates['skipped_classes'],
            'unknown_classes' => $candidates['unknown_classes'],
        ];
    }

    public function getReadiness(int $idTa): array
    {
        $kriteriaCount = Kriteria::count();
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $eligible = 0;
        $eligibleByTingkat = ['X' => 0, 'XI' => 0, 'XII' => 0];
        $skipped = [];
        $unknown = [];

        foreach ($kelasList as $kelas) {
            $tingkat = $this->resolveTingkatKelas($kelas->nama_kelas);
            if (!$tingkat) {
                $unknown[] = [
                    'id_kelas' => $kelas->id_kelas,
                    'nama_kelas' => $kelas->nama_kelas,
                ];
                continue;
            }

            $completeStudentCount = $this->getCompleteStudentIds($idTa, $kelas->id_kelas, $kriteriaCount)->count();

            if ($completeStudentCount >= 1) {
                $eligible++;
                $eligibleByTingkat[$tingkat]++;
                continue;
            }

            $skipped[] = [
                'id_kelas' => $kelas->id_kelas,
                'nama_kelas' => $kelas->nama_kelas,
                'complete_students' => $completeStudentCount,
            ];
        }

        return [
            'total_classes' => $kelasList->count(),
            'eligible_classes' => $eligible,
            'eligible_by_tingkat' => $eligibleByTingkat,
            'skipped_classes' => $skipped,
            'unknown_classes' => $unknown,
        ];
    }

    protected function getClassCandidatesByTingkat(int $idTa, string $method): array
    {
        $kriteriaCount = Kriteria::count();
        $candidatesByTingkat = ['X' => [], 'XI' => [], 'XII' => []];
        $skipped = [];
        $unknown = [];

        foreach (Kelas::orderBy('nama_kelas')->get() as $kelas) {
            $tingkat = $this->resolveTingkatKelas($kelas->nama_kelas);
            if (!$tingkat) {
                $unknown[] = [
                    'id_kelas' => $kelas->id_kelas,
                    'nama_kelas' => $kelas->nama_kelas,
                ];
                continue;
            }

            $siswaIds = $this->getCompleteStudentIds($idTa, $kelas->id_kelas, $kriteriaCount)->all();

            if (count($siswaIds) < 1) {
                $skipped[] = [
                    'id_kelas' => $kelas->id_kelas,
                    'nama_kelas' => $kelas->nama_kelas,
                    'complete_students' => count($siswaIds),
                ];
                continue;
            }

            $classResults = $this->runCalculator($idTa, $method, $siswaIds);
            if (empty($classResults)) {
                continue;
            }

            foreach (array_slice($classResults, 0, 3) as $result) {
                $candidatesByTingkat[$tingkat][] = [
                    'id_siswa' => $result['id_siswa'],
                    'id_kelas' => $kelas->id_kelas,
                    'nama_kelas' => $kelas->nama_kelas,
                    'source_rank' => $result["rank_{$method}"],
                ];
            }
        }

        return [
            'candidates_by_tingkat' => $candidatesByTingkat,
            'skipped_classes' => $skipped,
            'unknown_classes' => $unknown,
        ];
    }

    protected function getCompleteStudentIds(int $idTa, $kelasId, int $kriteriaCount)
    {
        return Penilaian::select('id_siswa')
            ->where('id_ta', $idTa)
            ->whereHas('siswa', fn($query) => $query->where('id_kelas', $kelasId))
            ->groupBy('id_siswa')
            ->havingRaw('COUNT(DISTINCT id_kriteria) = ?', [$kriteriaCount])
            ->pluck('id_siswa');
    }

    protected function runCalculator(int $idTa, string $method, array $siswaIds): array
    {
        return $method === 'smart'
            ? $this->smartCalculator->calculate($idTa, $siswaIds)
            : $this->mooraCalculator->calculate($idTa, $siswaIds);
    }

    protected function validateMethod(string $method): void
    {
        if (!in_array($method, ['smart', 'moora'], true)) {
            throw new InvalidArgumentException('Metode finalis tidak valid.');
        }
    }

    protected function resolveTingkatKelas(string $namaKelas): ?string
    {
        $normalized = strtoupper($namaKelas);

        if (preg_match('/(^|[^A-Z0-9])XII([^A-Z0-9]|$)/', $normalized)) {
            return 'XII';
        }

        if (preg_match('/(^|[^A-Z0-9])XI([^A-Z0-9]|$)/', $normalized)) {
            return 'XI';
        }

        if (preg_match('/(^|[^A-Z0-9])X([^A-Z0-9]|$)/', $normalized)) {
            return 'X';
        }

        return null;
    }
}
