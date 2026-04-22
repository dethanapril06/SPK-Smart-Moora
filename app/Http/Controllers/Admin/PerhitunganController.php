<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HasilAkhir;
use App\Models\TahunAjaran;
use App\Models\Kelas;
use App\Models\Kriteria;
use App\Models\Penilaian;
use App\Services\SmartCalculator;
use App\Services\MooraCalculator;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PerhitunganController extends Controller
{
    protected $smartCalculator;
    protected $mooraCalculator;
    
    public function __construct(SmartCalculator $smartCalculator, MooraCalculator $mooraCalculator)
    {
        $this->smartCalculator = $smartCalculator;
        $this->mooraCalculator = $mooraCalculator;
    }

    /**
     * Normalize kelas selection, expanding "all" to all kelas ids.
     */
    private function resolveKelasSelection($kelasInput): array
    {
        if (!is_array($kelasInput)) {
            $kelasInput = [$kelasInput];
        }

        $kelasInput = array_values(array_filter($kelasInput, function ($value) {
            return $value !== null && $value !== '';
        }));

        if (in_array('all', $kelasInput, true)) {
            return Kelas::orderBy('nama_kelas')->pluck('id_kelas')->all();
        }

        return $kelasInput;
    }
    
    /**
     * Display listing of calculation results
     */
    public function index(Request $request)
    {
        $filterTA = $request->get('tahun_ajaran');
        $filterKelas = $this->resolveKelasSelection($request->input('kelas', []));
        $tahunAjaranAktif = TahunAjaran::where('is_active', 1)->first();
        $allKelasSelected = !empty($filterKelas) && count($filterKelas) === Kelas::count();
        
        if (!$filterTA && $tahunAjaranAktif) {
            $filterTA = $tahunAjaranAktif->id_ta;
        }
        
        // Get hasil akhir with filters (admin: scoped to logged-in admin)
        $hasilQuery = HasilAkhir::with(['siswa.kelas', 'tahunAjaran'])
            ->where('user_id', auth()->id())
            ->when($filterTA, function ($query, $filterTA) {
                return $query->where('id_ta', $filterTA);
            })
            ->when($filterKelas, function ($query, $filterKelas) {
                return $query->whereHas('siswa', function($q) use ($filterKelas) {
                    $q->whereIn('id_kelas', $filterKelas);
                });
            });

        $hasilList = $hasilQuery
            ->orderBy('rank_smart')
            ->paginate(10)
            ->withQueryString();
        
        // Data for filters
        $tahunAjaranList = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        
        // Check if calculation exists for selected TA
        $hasCalculation = $filterTA && $hasilList->total() > 0;
        
        // Get students with complete penilaian for selected TA
        $studentsWithCompletePenilaian = 0;
        if ($filterTA) {
            $kriteriaCount = Kriteria::count();
            $studentsWithCompletePenilaianQuery = Penilaian::select('id_siswa')
                ->where('id_ta', $filterTA)
                ->when($filterKelas, function ($query, $filterKelas) {
                    return $query->whereHas('siswa', function ($q) use ($filterKelas) {
                        $q->whereIn('id_kelas', $filterKelas);
                    });
                })
                ->groupBy('id_siswa')
                ->havingRaw('COUNT(DISTINCT id_kriteria) = ?', [$kriteriaCount]);

            $studentsWithCompletePenilaian = $studentsWithCompletePenilaianQuery->count();
        }
        
        return view('admin.perhitungan.index', compact(
            'hasilList',
            'tahunAjaranList',
            'kelasList',
            'filterTA',
            'filterKelas',
            'allKelasSelected',
            'hasCalculation',
            'studentsWithCompletePenilaian'
        ));
    }
    
    /**
     * Calculate SMART and MOORA scores
     */
    public function calculate(Request $request)
    {
        $kelasIds = $this->resolveKelasSelection($request->input('kelas', []));

        $validated = $request->validate([
            'id_ta' => 'required|exists:tb_tahun_ajaran,id_ta',
            'kelas' => 'required|array|min:1',
        ]);

        if (empty($kelasIds)) {
            return redirect()->back()->with('error', 'Pilih minimal 1 kelas atau gunakan opsi Semua Kelas.');
        }

        $validKelasIds = Kelas::pluck('id_kelas')->all();
        $invalidKelasIds = array_values(array_diff($kelasIds, $validKelasIds));
        if (!empty($invalidKelasIds)) {
            return redirect()->back()->with('error', 'Terdapat kelas yang tidak valid pada pilihan Anda.');
        }
        
        $id_ta = $validated['id_ta'];
        $kelasIds = array_values(array_unique($kelasIds));
        
        // Check if there are students with complete penilaian
        $kriteriaCount = Kriteria::count();
        $studentsWithCompletePenilaianQuery = Penilaian::select('id_siswa')
            ->where('id_ta', $id_ta)
            ->whereHas('siswa', function ($query) use ($kelasIds) {
                $query->whereIn('id_kelas', $kelasIds);
            })
            ->groupBy('id_siswa')
            ->havingRaw('COUNT(DISTINCT id_kriteria) = ?', [$kriteriaCount]);

        $studentsWithCompletePenilaian = $studentsWithCompletePenilaianQuery->count();
        $siswaIds = $studentsWithCompletePenilaianQuery->pluck('id_siswa')->all();
        
        if ($studentsWithCompletePenilaian < 2) {
            return redirect()->back()->with('error', 'Minimal 2 siswa dengan penilaian lengkap diperlukan untuk perhitungan ranking.');
        }
        
        DB::beginTransaction();
        
        try {
            // Calculate SMART scores
            $smartResults = $this->smartCalculator->calculate($id_ta, $siswaIds);
            
            // Calculate MOORA scores
            $mooraResults = $this->mooraCalculator->calculate($id_ta, $siswaIds);
            
            // Merge results by id_siswa
            $mergedResults = [];
            foreach ($smartResults as $smart) {
                $moora = collect($mooraResults)->firstWhere('id_siswa', $smart['id_siswa']);
                
                $mergedResults[] = [
                    'id_siswa' => $smart['id_siswa'],
                    'id_ta' => $id_ta,
                    'skor_smart' => $smart['skor_smart'],
                    'rank_smart' => $smart['rank_smart'],
                    'skor_moora' => $moora ? $moora['skor_moora'] : null,
                    'rank_moora' => $moora ? $moora['rank_moora'] : null,
                ];
            }
            
            // Delete previous results for this TA (scoped to this admin)
            HasilAkhir::where('id_ta', $id_ta)->where('user_id', auth()->id())->delete();
            
            // Insert new results
            foreach ($mergedResults as $result) {
                HasilAkhir::create(array_merge($result, ['user_id' => auth()->id()]));
            }
            
            DB::commit();
            
            return redirect()->route('admin.perhitungan.index', [
                'tahun_ajaran' => $id_ta,
                'kelas' => $kelasIds,
            ])->with('success', "Perhitungan berhasil! {$studentsWithCompletePenilaian} siswa dari " . count($kelasIds) . " kelas telah di-ranking menggunakan metode SMART dan MOORA.");
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal melakukan perhitungan: ' . $e->getMessage());
        }
    }
    
    /**
     * Show detailed calculation steps
     */
    public function showSteps(Request $request, $id_ta, $metode)
    {
        if (!in_array($metode, ['smart', 'moora'])) {
            abort(404);
        }

        $selectedKelasIds = $this->resolveKelasSelection($request->input('kelas', []));
        
        $tahunAjaran = TahunAjaran::findOrFail($id_ta);
        $kriteriaList = Kriteria::orderBy('kode_kriteria')->get();

        $siswaIds = null;
        if (!empty($selectedKelasIds)) {
            $kriteriaCount = Kriteria::count();
            $siswaIds = Penilaian::select('id_siswa')
                ->where('id_ta', $id_ta)
                ->whereHas('siswa', function ($query) use ($selectedKelasIds) {
                    $query->whereIn('id_kelas', $selectedKelasIds);
                })
                ->groupBy('id_siswa')
                ->havingRaw('COUNT(DISTINCT id_kriteria) = ?', [$kriteriaCount])
                ->pluck('id_siswa')
                ->all();
        }
        
        // Calculate to get detailed steps
        if ($metode == 'smart') {
            $results = $this->smartCalculator->calculate($id_ta, $siswaIds);
            $detailedSteps = $this->smartCalculator->getDetailedSteps();
        } else {
            $results = $this->mooraCalculator->calculate($id_ta, $siswaIds);
            $detailedSteps = $this->mooraCalculator->getDetailedSteps();
        }

        $perPage = 10;
        $stepsCollection = collect($detailedSteps)->values();

        $buildPaginator = function (string $pageName) use ($stepsCollection, $perPage) {
            $currentPage = LengthAwarePaginator::resolveCurrentPage($pageName);

            return new LengthAwarePaginator(
                $stepsCollection->forPage($currentPage, $perPage)->values(),
                $stepsCollection->count(),
                $perPage,
                $currentPage,
                [
                    'path' => request()->url(),
                    'pageName' => $pageName,
                    'query' => request()->query(),
                ]
            );
        };

        $step1Steps = $buildPaginator('step1_page');
        $step2Steps = $buildPaginator('step2_page');
        $step3Steps = $buildPaginator('step3_page');
        $step4Steps = $buildPaginator('step4_page');
        
        return view('admin.perhitungan.steps', compact(
            'tahunAjaran',
            'metode',
            'kriteriaList',
            'step1Steps',
            'step2Steps',
            'step3Steps',
            'step4Steps',
            'selectedKelasIds'
        ));
    }
    
    /**
     * Compare SMART and MOORA results
     */
    public function compare(Request $request, $id_ta)
    {
        $selectedKelasIds = $this->resolveKelasSelection($request->input('kelas', []));

        $tahunAjaran = TahunAjaran::findOrFail($id_ta);

        $baseQuery = HasilAkhir::query()
            ->where('id_ta', $id_ta)
            ->where('user_id', auth()->id())
            ->when($selectedKelasIds, function ($query, $selectedKelasIds) {
                return $query->whereHas('siswa', function ($q) use ($selectedKelasIds) {
                    $q->whereIn('id_kelas', $selectedKelasIds);
                });
            });

        $totalData = (clone $baseQuery)->count();
        $agreement = (clone $baseQuery)
            ->whereColumn('rank_smart', 'rank_moora')
            ->count();

        $hasilList = (clone $baseQuery)
            ->with('siswa')
            ->orderBy('rank_smart')
            ->paginate(10)
            ->withQueryString();

        // Calculate agreement
        $agreementPercentage = $totalData > 0
            ? round(($agreement / $totalData) * 100, 2)
            : 0;
        
        return view('admin.perhitungan.compare', compact(
            'tahunAjaran',
            'hasilList',
            'agreementPercentage',
            'selectedKelasIds'
        ));
    }
}
