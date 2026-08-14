<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HasilAkhir;
use App\Models\HasilFinalis;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Models\Kelas;
use App\Models\Kriteria;
use App\Models\Penilaian;
use App\Models\Siswa;
use App\Services\FinalisCalculatorService;
use App\Services\SmartCalculator;
use App\Services\MooraCalculator;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PerhitunganController extends Controller
{
    protected $smartCalculator;
    protected $mooraCalculator;
    protected $finalisCalculator;
    
    public function __construct(
        SmartCalculator $smartCalculator,
        MooraCalculator $mooraCalculator,
        FinalisCalculatorService $finalisCalculator
    )
    {
        $this->smartCalculator = $smartCalculator;
        $this->mooraCalculator = $mooraCalculator;
        $this->finalisCalculator = $finalisCalculator;
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

    // =========================================================================
    // SMART Standalone
    // =========================================================================

    public function indexSmart(Request $request)
    {
        $filterTA         = $request->get('tahun_ajaran');
        $filterSemester   = $request->get('semester');
        $filterKelas      = $this->resolveKelasSelection($request->input('kelas', []));
        $tahunAjaranAktif = TahunAjaran::where('is_active', 1)->first();
        $allKelasSelected = !empty($filterKelas) && count($filterKelas) === Kelas::count();

        if (!$filterTA && $tahunAjaranAktif) {
            $filterTA = $tahunAjaranAktif->id_ta;
        }

        $hasilQuery = HasilAkhir::with(['siswa.kelas', 'tahunAjaran'])
            ->where('user_id', auth()->id())
            ->whereNotNull('skor_smart')
            ->when($filterTA, fn($q) => $q->where('id_ta', $filterTA))
            ->when($filterSemester, fn($q, $s) => $q->where('id_semester', $s))
            ->when($filterKelas, fn($q) => $q->whereHas('siswa', fn($q2) => $q2->whereIn('id_kelas', $filterKelas)));

        $allHasil = $hasilQuery->get()->sortBy([
            fn($a, $b) => strcmp($a->siswa->kelas->nama_kelas ?? '', $b->siswa->kelas->nama_kelas ?? ''),
            fn($a, $b) => ($a->rank_smart ?? 9999) <=> ($b->rank_smart ?? 9999)
        ])->values();

        $hasilByTingkat = [];
        foreach (['X', 'XI', 'XII'] as $tingkat) {
            $tingkatHasil = $allHasil->filter(fn($item) => ($item->siswa->kelas->tingkat ?? null) === $tingkat)->values();
            $byKelas = $tingkatHasil->groupBy(fn($item) => $item->siswa->kelas->nama_kelas ?? 'Tanpa Kelas');
            
            $hasilByTingkat[$tingkat] = [
                'tingkat'     => $tingkat,
                'total_siswa' => $tingkatHasil->count(),
                'total_kelas' => $byKelas->count(),
                'by_kelas'    => $byKelas,
                'all'         => $tingkatHasil,
            ];
        }

        $tahunAjaranList = TahunAjaran::representatives()->orderBy('tahun_ajaran', 'desc')->get();
        $semesterList    = Semester::orderBy('id_semester')->get();
        $kelasList       = Kelas::orderBy('nama_kelas')->get();
        $hasCalculation  = $filterTA && $allHasil->isNotEmpty();

        $studentsWithCompletePenilaian = 0;
        if ($filterTA) {
            $kriteriaCount = Kriteria::count();
            $studentsWithCompletePenilaian = Penilaian::select('id_siswa')
                ->where('id_ta', $filterTA)
                ->when($filterSemester, fn($q, $s) => $q->where('id_semester', $s))
                ->when($filterKelas, fn($q) => $q->whereHas('siswa', fn($q2) => $q2->whereIn('id_kelas', $filterKelas)))
                ->groupBy('id_siswa')
                ->havingRaw('COUNT(DISTINCT id_kriteria) = ?', [$kriteriaCount])
                ->count();
        }

        return view('admin.perhitungan.smart.index', compact(
            'allHasil', 'hasilByTingkat', 'tahunAjaranList', 'semesterList', 'kelasList',
            'filterTA', 'filterSemester', 'filterKelas', 'allKelasSelected',
            'hasCalculation', 'studentsWithCompletePenilaian'
        ));
    }

    public function calculateSmart(Request $request)
    {
        $kelasIds = $this->resolveKelasSelection($request->input('kelas', []));

        $request->validate([
            'id_ta' => 'required|exists:tb_tahun_ajaran,id_ta',
            'id_semester' => 'required|exists:tb_semester,id_semester',
            'kelas' => 'required|array|min:1',
        ]);

        if (empty($kelasIds)) {
            return redirect()->back()->with('error', 'Pilih minimal 1 kelas.');
        }

        $validKelasIds = Kelas::pluck('id_kelas')->all();
        if (!empty(array_diff($kelasIds, $validKelasIds))) {
            return redirect()->back()->with('error', 'Terdapat kelas yang tidak valid.');
        }

        $id_ta       = $request->input('id_ta');
        $id_semester = $request->input('id_semester');
        $kelasIds    = array_values(array_unique($kelasIds));
        $kriteriaCount = Kriteria::count();
        $allSelectedSiswaIds = Siswa::whereIn('id_kelas', $kelasIds)->pluck('id_siswa')->all();
        $userId = auth()->id();

        DB::beginTransaction();
        try {
            // Reset previous SMART score & rank for selected classes
            HasilAkhir::where('id_ta', $id_ta)
                ->where('id_semester', $id_semester)
                ->where('user_id', $userId)
                ->whereIn('id_siswa', $allSelectedSiswaIds)
                ->update([
                    'skor_smart' => null,
                    'rank_smart' => null,
                ]);

            $totalCalculatedSiswa = 0;
            $totalCalculatedKelas = 0;

            // Execute calculation class by class
            foreach ($kelasIds as $kelasId) {
                $siswaClassIds = Penilaian::select('id_siswa')
                    ->where('id_ta', $id_ta)
                    ->where('id_semester', $id_semester)
                    ->whereHas('siswa', fn($q) => $q->where('id_kelas', $kelasId))
                    ->groupBy('id_siswa')
                    ->havingRaw('COUNT(DISTINCT id_kriteria) = ?', [$kriteriaCount])
                    ->pluck('id_siswa')
                    ->all();

                if (count($siswaClassIds) < 1) {
                    continue;
                }

                $smartResults = $this->smartCalculator->calculate($id_ta, $siswaClassIds, $id_semester);

                foreach ($smartResults as $smart) {
                    HasilAkhir::updateOrCreate(
                        [
                            'id_siswa'    => $smart['id_siswa'],
                            'id_ta'       => $id_ta,
                            'id_semester' => $id_semester,
                            'user_id'     => $userId,
                        ],
                        [
                            'skor_smart' => $smart['skor_smart'],
                            'rank_smart' => $smart['rank_smart'],
                        ]
                    );
                }

                $totalCalculatedSiswa += count($smartResults);
                $totalCalculatedKelas++;
            }

            if ($totalCalculatedSiswa < 1) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Tidak ada siswa dengan penilaian lengkap di kelas terpilih.');
            }

            DB::commit();
            return redirect()->route('admin.perhitungan.smart.index', [
                'tahun_ajaran' => $id_ta, 'semester' => $id_semester, 'kelas' => $kelasIds,
            ])->with('success', "Perhitungan SMART berhasil! {$totalCalculatedSiswa} siswa dari {$totalCalculatedKelas} kelas telah di-ranking berdasarkan masing-masing kelas.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function showStepsSmart(Request $request, $id_ta)
    {
        $selectedKelasIds = $this->resolveKelasSelection($request->input('kelas', []));
        $id_semester      = $request->get('semester');
        $tahunAjaran      = TahunAjaran::findOrFail($id_ta);
        $kriteriaList     = Kriteria::orderBy('kode_kriteria')->get();
        $kriteriaCount    = $kriteriaList->count();

        // Get classes that have penilaian
        $eligibleKelasList = Kelas::whereHas('siswa.penilaian', function ($q) use ($id_ta, $id_semester) {
            $q->where('id_ta', $id_ta)
              ->when($id_semester, fn($sq) => $sq->where('id_semester', $id_semester));
        })->orderBy('nama_kelas')->get();

        $activeKelasId = $request->get('id_kelas');
        if (!$activeKelasId || !$eligibleKelasList->contains('id_kelas', $activeKelasId)) {
            $activeKelasId = !empty($selectedKelasIds) ? $selectedKelasIds[0] : ($eligibleKelasList->first()?->id_kelas);
        }

        $activeKelas = Kelas::find($activeKelasId);
        $detailedSteps = [];

        if ($activeKelasId) {
            $siswaIds = Penilaian::select('id_siswa')
                ->where('id_ta', $id_ta)
                ->when($id_semester, fn($q, $s) => $q->where('id_semester', $s))
                ->whereHas('siswa', fn($q) => $q->where('id_kelas', $activeKelasId))
                ->groupBy('id_siswa')
                ->havingRaw('COUNT(DISTINCT id_kriteria) = ?', [$kriteriaCount])
                ->pluck('id_siswa')->all();

            if (!empty($siswaIds)) {
                $this->smartCalculator->calculate($id_ta, $siswaIds, $id_semester);
                $detailedSteps = $this->smartCalculator->getDetailedSteps();
            }
        }

        $perPage         = 10;
        $stepsCollection = collect($detailedSteps)->values();

        $buildPaginator = function (string $pageName) use ($stepsCollection, $perPage) {
            $currentPage = LengthAwarePaginator::resolveCurrentPage($pageName);
            return new LengthAwarePaginator(
                $stepsCollection->forPage($currentPage, $perPage)->values(),
                $stepsCollection->count(), $perPage, $currentPage,
                ['path' => request()->url(), 'pageName' => $pageName, 'query' => request()->query()]
            );
        };

        $step1Steps = $buildPaginator('step1_page');
        $step2Steps = $buildPaginator('step2_page');
        $step3Steps = $buildPaginator('step3_page');
        $step4Steps = $buildPaginator('step4_page');

        return view('admin.perhitungan.smart.steps', compact(
            'tahunAjaran', 'kriteriaList',
            'step1Steps', 'step2Steps', 'step3Steps', 'step4Steps',
            'selectedKelasIds', 'id_semester', 'eligibleKelasList', 'activeKelasId', 'activeKelas'
        ));
    }

    // =========================================================================
    // MOORA Standalone
    // =========================================================================

    public function indexMoora(Request $request)
    {
        $filterTA         = $request->get('tahun_ajaran');
        $filterSemester   = $request->get('semester');
        $filterKelas      = $this->resolveKelasSelection($request->input('kelas', []));
        $tahunAjaranAktif = TahunAjaran::where('is_active', 1)->first();
        $allKelasSelected = !empty($filterKelas) && count($filterKelas) === Kelas::count();

        if (!$filterTA && $tahunAjaranAktif) {
            $filterTA = $tahunAjaranAktif->id_ta;
        }

        $hasilQuery = HasilAkhir::with(['siswa.kelas', 'tahunAjaran'])
            ->where('user_id', auth()->id())
            ->whereNotNull('skor_moora')
            ->when($filterTA, fn($q) => $q->where('id_ta', $filterTA))
            ->when($filterSemester, fn($q, $s) => $q->where('id_semester', $s))
            ->when($filterKelas, fn($q) => $q->whereHas('siswa', fn($q2) => $q2->whereIn('id_kelas', $filterKelas)));

        $allHasil = $hasilQuery->get()->sortBy([
            fn($a, $b) => strcmp($a->siswa->kelas->nama_kelas ?? '', $b->siswa->kelas->nama_kelas ?? ''),
            fn($a, $b) => ($a->rank_moora ?? 9999) <=> ($b->rank_moora ?? 9999)
        ])->values();

        $hasilByTingkat = [];
        foreach (['X', 'XI', 'XII'] as $tingkat) {
            $tingkatHasil = $allHasil->filter(fn($item) => ($item->siswa->kelas->tingkat ?? null) === $tingkat)->values();
            $byKelas = $tingkatHasil->groupBy(fn($item) => $item->siswa->kelas->nama_kelas ?? 'Tanpa Kelas');
            
            $hasilByTingkat[$tingkat] = [
                'tingkat'     => $tingkat,
                'total_siswa' => $tingkatHasil->count(),
                'total_kelas' => $byKelas->count(),
                'by_kelas'    => $byKelas,
                'all'         => $tingkatHasil,
            ];
        }

        $tahunAjaranList = TahunAjaran::representatives()->orderBy('tahun_ajaran', 'desc')->get();
        $semesterList    = Semester::orderBy('id_semester')->get();
        $kelasList       = Kelas::orderBy('nama_kelas')->get();
        $hasCalculation  = $filterTA && $allHasil->isNotEmpty();

        $studentsWithCompletePenilaian = 0;
        if ($filterTA) {
            $kriteriaCount = Kriteria::count();
            $studentsWithCompletePenilaian = Penilaian::select('id_siswa')
                ->where('id_ta', $filterTA)
                ->when($filterSemester, fn($q, $s) => $q->where('id_semester', $s))
                ->when($filterKelas, fn($q) => $q->whereHas('siswa', fn($q2) => $q2->whereIn('id_kelas', $filterKelas)))
                ->groupBy('id_siswa')
                ->havingRaw('COUNT(DISTINCT id_kriteria) = ?', [$kriteriaCount])
                ->count();
        }

        return view('admin.perhitungan.moora.index', compact(
            'allHasil', 'hasilByTingkat', 'tahunAjaranList', 'semesterList', 'kelasList',
            'filterTA', 'filterSemester', 'filterKelas', 'allKelasSelected',
            'hasCalculation', 'studentsWithCompletePenilaian'
        ));
    }

    public function calculateMoora(Request $request)
    {
        $kelasIds = $this->resolveKelasSelection($request->input('kelas', []));

        $request->validate([
            'id_ta' => 'required|exists:tb_tahun_ajaran,id_ta',
            'id_semester' => 'required|exists:tb_semester,id_semester',
            'kelas' => 'required|array|min:1',
        ]);

        if (empty($kelasIds)) {
            return redirect()->back()->with('error', 'Pilih minimal 1 kelas.');
        }

        $validKelasIds = Kelas::pluck('id_kelas')->all();
        if (!empty(array_diff($kelasIds, $validKelasIds))) {
            return redirect()->back()->with('error', 'Terdapat kelas yang tidak valid.');
        }

        $id_ta       = $request->input('id_ta');
        $id_semester = $request->input('id_semester');
        $kelasIds    = array_values(array_unique($kelasIds));
        $kriteriaCount = Kriteria::count();
        $allSelectedSiswaIds = Siswa::whereIn('id_kelas', $kelasIds)->pluck('id_siswa')->all();
        $userId = auth()->id();

        DB::beginTransaction();
        try {
            // Reset previous MOORA score & rank for selected classes
            HasilAkhir::where('id_ta', $id_ta)
                ->where('id_semester', $id_semester)
                ->where('user_id', $userId)
                ->whereIn('id_siswa', $allSelectedSiswaIds)
                ->update([
                    'skor_moora' => null,
                    'rank_moora' => null,
                ]);

            $totalCalculatedSiswa = 0;
            $totalCalculatedKelas = 0;

            // Execute calculation class by class
            foreach ($kelasIds as $kelasId) {
                $siswaClassIds = Penilaian::select('id_siswa')
                    ->where('id_ta', $id_ta)
                    ->where('id_semester', $id_semester)
                    ->whereHas('siswa', fn($q) => $q->where('id_kelas', $kelasId))
                    ->groupBy('id_siswa')
                    ->havingRaw('COUNT(DISTINCT id_kriteria) = ?', [$kriteriaCount])
                    ->pluck('id_siswa')
                    ->all();

                if (count($siswaClassIds) < 1) {
                    continue;
                }

                $mooraResults = $this->mooraCalculator->calculate($id_ta, $siswaClassIds, $id_semester);

                foreach ($mooraResults as $moora) {
                    HasilAkhir::updateOrCreate(
                        [
                            'id_siswa'    => $moora['id_siswa'],
                            'id_ta'       => $id_ta,
                            'id_semester' => $id_semester,
                            'user_id'     => $userId,
                        ],
                        [
                            'skor_moora' => $moora['skor_moora'],
                            'rank_moora' => $moora['rank_moora'],
                        ]
                    );
                }

                $totalCalculatedSiswa += count($mooraResults);
                $totalCalculatedKelas++;
            }

            if ($totalCalculatedSiswa < 1) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Tidak ada siswa dengan penilaian lengkap di kelas terpilih.');
            }

            DB::commit();
            return redirect()->route('admin.perhitungan.moora.index', [
                'tahun_ajaran' => $id_ta, 'semester' => $id_semester, 'kelas' => $kelasIds,
            ])->with('success', "Perhitungan MOORA berhasil! {$totalCalculatedSiswa} siswa dari {$totalCalculatedKelas} kelas telah di-ranking berdasarkan masing-masing kelas.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function showStepsMoora(Request $request, $id_ta)
    {
        $selectedKelasIds = $this->resolveKelasSelection($request->input('kelas', []));
        $id_semester      = $request->get('semester');
        $tahunAjaran      = TahunAjaran::findOrFail($id_ta);
        $kriteriaList     = Kriteria::orderBy('kode_kriteria')->get();
        $kriteriaCount    = $kriteriaList->count();

        // Get classes that have penilaian
        $eligibleKelasList = Kelas::whereHas('siswa.penilaian', function ($q) use ($id_ta, $id_semester) {
            $q->where('id_ta', $id_ta)
              ->when($id_semester, fn($sq) => $sq->where('id_semester', $id_semester));
        })->orderBy('nama_kelas')->get();

        $activeKelasId = $request->get('id_kelas');
        if (!$activeKelasId || !$eligibleKelasList->contains('id_kelas', $activeKelasId)) {
            $activeKelasId = !empty($selectedKelasIds) ? $selectedKelasIds[0] : ($eligibleKelasList->first()?->id_kelas);
        }

        $activeKelas = Kelas::find($activeKelasId);
        $detailedSteps = [];

        if ($activeKelasId) {
            $siswaIds = Penilaian::select('id_siswa')
                ->where('id_ta', $id_ta)
                ->when($id_semester, fn($q, $s) => $q->where('id_semester', $s))
                ->whereHas('siswa', fn($q) => $q->where('id_kelas', $activeKelasId))
                ->groupBy('id_siswa')
                ->havingRaw('COUNT(DISTINCT id_kriteria) = ?', [$kriteriaCount])
                ->pluck('id_siswa')->all();

            if (!empty($siswaIds)) {
                $this->mooraCalculator->calculate($id_ta, $siswaIds, $id_semester);
                $detailedSteps = $this->mooraCalculator->getDetailedSteps();
            }
        }

        $perPage         = 10;
        $stepsCollection = collect($detailedSteps)->values();

        $buildPaginator = function (string $pageName) use ($stepsCollection, $perPage) {
            $currentPage = LengthAwarePaginator::resolveCurrentPage($pageName);
            return new LengthAwarePaginator(
                $stepsCollection->forPage($currentPage, $perPage)->values(),
                $stepsCollection->count(), $perPage, $currentPage,
                ['path' => request()->url(), 'pageName' => $pageName, 'query' => request()->query()]
            );
        };

        $step1Steps = $buildPaginator('step1_page');
        $step2Steps = $buildPaginator('step2_page');
        $step3Steps = $buildPaginator('step3_page');
        $step4Steps = $buildPaginator('step4_page');

        return view('admin.perhitungan.moora.steps', compact(
            'tahunAjaran', 'kriteriaList',
            'step1Steps', 'step2Steps', 'step3Steps', 'step4Steps',
            'selectedKelasIds', 'id_semester', 'eligibleKelasList', 'activeKelasId', 'activeKelas'
        ));
    }

    // =========================================================================
    // Finalis 10 Besar
    // =========================================================================

    public function indexFinalisSmart(Request $request)
    {
        return $this->indexFinalis($request, 'smart');
    }

    public function indexFinalisMoora(Request $request)
    {
        return $this->indexFinalis($request, 'moora');
    }

    public function calculateFinalisSmart(Request $request)
    {
        return $this->calculateFinalis($request, 'smart');
    }

    public function calculateFinalisMoora(Request $request)
    {
        return $this->calculateFinalis($request, 'moora');
    }

    protected function indexFinalis(Request $request, string $method)
    {
        $filterTA = $request->get('tahun_ajaran');
        $filterSemester = $request->get('semester');
        $tahunAjaranAktif = TahunAjaran::where('is_active', 1)->first();

        if (!$filterTA && $tahunAjaranAktif) {
            $filterTA = $tahunAjaranAktif->id_ta;
        }

        $hasilByTingkat = HasilFinalis::with(['siswa.kelas', 'tahunAjaran'])
            ->where('user_id', auth()->id())
            ->where('metode', $method)
            ->when($filterTA, fn($query) => $query->where('id_ta', $filterTA))
            ->when($filterSemester, fn($query, $s) => $query->where('id_semester', $s))
            ->orderByRaw("FIELD(tingkat, 'X', 'XI', 'XII')")
            ->orderBy('rank')
            ->get()
            ->groupBy('tingkat');

        $tahunAjaranList = TahunAjaran::representatives()->orderBy('tahun_ajaran', 'desc')->get();
        $semesterList    = Semester::orderBy('id_semester')->get();
        $hasCalculation  = $filterTA && $hasilByTingkat->flatten(1)->count() > 0;
        $readiness = $filterTA
            ? $this->finalisCalculator->getReadiness((int) $filterTA, $filterSemester ? (int)$filterSemester : null)
            : [
                'total_classes' => 0,
                'eligible_classes' => 0,
                'eligible_by_tingkat' => ['X' => 0, 'XI' => 0, 'XII' => 0],
                'skipped_classes' => [],
                'unknown_classes' => [],
            ];

        return view('admin.perhitungan.finalis.index', compact(
            'hasilByTingkat',
            'tahunAjaranList',
            'semesterList',
            'filterTA',
            'filterSemester',
            'hasCalculation',
            'readiness',
            'method'
        ));
    }

    protected function calculateFinalis(Request $request, string $method)
    {
        $validated = $request->validate([
            'id_ta' => 'required|exists:tb_tahun_ajaran,id_ta',
            'id_semester' => 'required|exists:tb_semester,id_semester',
        ]);

        try {
            $summary = $this->finalisCalculator->calculate(
                (int) $validated['id_ta'],
                $method,
                auth()->id(),
                (int) $validated['id_semester']
            );

            return redirect()
                ->route("admin.perhitungan.finalis.{$method}.index", ['tahun_ajaran' => $validated['id_ta'], 'semester' => $validated['id_semester']])
                ->with('success', 'Perhitungan 10 Besar ' . strtoupper($method) . " berhasil! {$summary['candidate_count']} kandidat dari 3 besar tiap kelas telah dihitung ulang per angkatan.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghitung 10 Besar ' . strtoupper($method) . ': ' . $e->getMessage());
        }
    }
}
