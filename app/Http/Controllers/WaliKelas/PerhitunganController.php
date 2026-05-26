<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\HasilAkhir;
use App\Models\TahunAjaran;
use App\Models\Kelas;
use App\Models\Kriteria;
use App\Models\Penilaian;
use App\Models\Siswa;
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

    protected function getKelas()
    {
        $kelas = Kelas::where('id_wali_kelas', auth()->id())->first();
        abort_if(!$kelas, 403, 'Anda belum ditugaskan sebagai wali kelas.');
        return $kelas;
    }

    // ─── SMART ──────────────────────────────────────────────────────────────────

    public function indexSmart(Request $request)
    {
        $kelas = $this->getKelas();
        $kelasId = $kelas->id_kelas;

        $filterTA = $request->get('tahun_ajaran');
        $tahunAjaranAktif = TahunAjaran::where('is_active', 1)->first();

        if (!$filterTA && $tahunAjaranAktif) {
            $filterTA = $tahunAjaranAktif->id_ta;
        }

        $userId = auth()->id();

        $hasilQuery = HasilAkhir::with(['siswa.kelas', 'tahunAjaran'])
            ->where('user_id', $userId)
            ->whereNotNull('skor_smart')
            ->when($filterTA, fn($q) => $q->where('id_ta', $filterTA))
            ->whereHas('siswa', fn($q) => $q->where('id_kelas', $kelasId));

        $hasilList = $hasilQuery
            ->orderBy('rank_smart')
            ->paginate(10)
            ->withQueryString();

        $tahunAjaranList = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();
        $hasCalculation  = $filterTA && $hasilList->total() > 0;

        $studentsWithCompletePenilaian = 0;
        if ($filterTA) {
            $kriteriaCount = Kriteria::count();
            $siswaIds      = Siswa::where('id_kelas', $kelasId)->pluck('id_siswa');
            $studentsWithCompletePenilaian = Penilaian::select('id_siswa')
                ->where('id_ta', $filterTA)
                ->whereIn('id_siswa', $siswaIds)
                ->groupBy('id_siswa')
                ->havingRaw('COUNT(DISTINCT id_kriteria) = ?', [$kriteriaCount])
                ->count();
        }

        return view('walikelas.perhitungan.smart.index', compact(
            'hasilList', 'tahunAjaranList', 'filterTA',
            'hasCalculation', 'studentsWithCompletePenilaian', 'kelas'
        ));
    }

    public function calculateSmart(Request $request)
    {
        $kelas   = $this->getKelas();
        $kelasId = $kelas->id_kelas;

        $validated = $request->validate([
            'id_ta' => 'required|exists:tb_tahun_ajaran,id_ta'
        ]);

        $id_ta    = $validated['id_ta'];
        $siswaIds = Siswa::where('id_kelas', $kelasId)->pluck('id_siswa')->toArray();

        $kriteriaCount = Kriteria::count();
        $studentsWithCompletePenilaian = Penilaian::select('id_siswa')
            ->where('id_ta', $id_ta)
            ->whereIn('id_siswa', $siswaIds)
            ->groupBy('id_siswa')
            ->havingRaw('COUNT(DISTINCT id_kriteria) = ?', [$kriteriaCount])
            ->count();

        if ($studentsWithCompletePenilaian < 2) {
            return redirect()->back()->with('error', 'Minimal 2 siswa dengan penilaian lengkap diperlukan untuk perhitungan ranking.');
        }

        DB::beginTransaction();
        try {
            $smartResults = $this->smartCalculator->calculate($id_ta, $siswaIds);
            $userId       = auth()->id();

            HasilAkhir::where('id_ta', $id_ta)
                ->where('user_id', $userId)
                ->whereIn('id_siswa', $siswaIds)
                ->update([
                    'skor_smart' => null,
                    'rank_smart' => null,
                ]);

            foreach ($smartResults as $smart) {
                HasilAkhir::updateOrCreate(
                    [
                        'id_siswa' => $smart['id_siswa'],
                        'id_ta' => $id_ta,
                        'user_id' => $userId,
                    ],
                    [
                        'skor_smart' => $smart['skor_smart'],
                        'rank_smart' => $smart['rank_smart'],
                    ]
                );
            }

            DB::commit();

            return redirect()->route('walikelas.perhitungan.smart.index', ['tahun_ajaran' => $id_ta])
                ->with('success', "Perhitungan SMART berhasil! {$studentsWithCompletePenilaian} siswa telah di-ranking.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal melakukan perhitungan SMART: ' . $e->getMessage());
        }
    }

    public function showStepsSmart($id_ta)
    {
        $kelas   = $this->getKelas();
        $kelasId = $kelas->id_kelas;

        $tahunAjaran = TahunAjaran::findOrFail($id_ta);
        $kriteriaList = Kriteria::orderBy('kode_kriteria')->get();
        $siswaIds    = Siswa::where('id_kelas', $kelasId)->pluck('id_siswa')->toArray();

        $results       = $this->smartCalculator->calculate($id_ta, $siswaIds);
        $detailedSteps = $this->smartCalculator->getDetailedSteps();

        $perPage          = 10;
        $stepsCollection  = collect($detailedSteps)->values();

        $buildPaginator = function (string $pageName) use ($stepsCollection, $perPage) {
            $currentPage = LengthAwarePaginator::resolveCurrentPage($pageName);
            return new LengthAwarePaginator(
                $stepsCollection->forPage($currentPage, $perPage)->values(),
                $stepsCollection->count(),
                $perPage,
                $currentPage,
                ['path' => request()->url(), 'pageName' => $pageName, 'query' => request()->query()]
            );
        };

        $step1Steps = $buildPaginator('step1_page');
        $step2Steps = $buildPaginator('step2_page');
        $step3Steps = $buildPaginator('step3_page');
        $step4Steps = $buildPaginator('step4_page');

        return view('walikelas.perhitungan.smart.steps', compact(
            'tahunAjaran', 'kriteriaList', 'step1Steps', 'step2Steps', 'step3Steps', 'step4Steps'
        ));
    }

    // ─── MOORA ──────────────────────────────────────────────────────────────────

    public function indexMoora(Request $request)
    {
        $kelas   = $this->getKelas();
        $kelasId = $kelas->id_kelas;

        $filterTA        = $request->get('tahun_ajaran');
        $tahunAjaranAktif = TahunAjaran::where('is_active', 1)->first();

        if (!$filterTA && $tahunAjaranAktif) {
            $filterTA = $tahunAjaranAktif->id_ta;
        }

        $userId = auth()->id();

        $hasilQuery = HasilAkhir::with(['siswa.kelas', 'tahunAjaran'])
            ->where('user_id', $userId)
            ->whereNotNull('skor_moora')
            ->when($filterTA, fn($q) => $q->where('id_ta', $filterTA))
            ->whereHas('siswa', fn($q) => $q->where('id_kelas', $kelasId));

        $hasilList = $hasilQuery
            ->orderBy('rank_moora')
            ->paginate(10)
            ->withQueryString();

        $tahunAjaranList = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();
        $hasCalculation  = $filterTA && $hasilList->total() > 0;

        $studentsWithCompletePenilaian = 0;
        if ($filterTA) {
            $kriteriaCount = Kriteria::count();
            $siswaIds      = Siswa::where('id_kelas', $kelasId)->pluck('id_siswa');
            $studentsWithCompletePenilaian = Penilaian::select('id_siswa')
                ->where('id_ta', $filterTA)
                ->whereIn('id_siswa', $siswaIds)
                ->groupBy('id_siswa')
                ->havingRaw('COUNT(DISTINCT id_kriteria) = ?', [$kriteriaCount])
                ->count();
        }

        return view('walikelas.perhitungan.moora.index', compact(
            'hasilList', 'tahunAjaranList', 'filterTA',
            'hasCalculation', 'studentsWithCompletePenilaian', 'kelas'
        ));
    }

    public function calculateMoora(Request $request)
    {
        $kelas   = $this->getKelas();
        $kelasId = $kelas->id_kelas;

        $validated = $request->validate([
            'id_ta' => 'required|exists:tb_tahun_ajaran,id_ta'
        ]);

        $id_ta    = $validated['id_ta'];
        $siswaIds = Siswa::where('id_kelas', $kelasId)->pluck('id_siswa')->toArray();

        $kriteriaCount = Kriteria::count();
        $studentsWithCompletePenilaian = Penilaian::select('id_siswa')
            ->where('id_ta', $id_ta)
            ->whereIn('id_siswa', $siswaIds)
            ->groupBy('id_siswa')
            ->havingRaw('COUNT(DISTINCT id_kriteria) = ?', [$kriteriaCount])
            ->count();

        if ($studentsWithCompletePenilaian < 2) {
            return redirect()->back()->with('error', 'Minimal 2 siswa dengan penilaian lengkap diperlukan untuk perhitungan ranking.');
        }

        DB::beginTransaction();
        try {
            $mooraResults = $this->mooraCalculator->calculate($id_ta, $siswaIds);
            $userId       = auth()->id();

            HasilAkhir::where('id_ta', $id_ta)
                ->where('user_id', $userId)
                ->whereIn('id_siswa', $siswaIds)
                ->update([
                    'skor_moora' => null,
                    'rank_moora' => null,
                ]);

            foreach ($mooraResults as $moora) {
                HasilAkhir::updateOrCreate(
                    [
                        'id_siswa' => $moora['id_siswa'],
                        'id_ta' => $id_ta,
                        'user_id' => $userId,
                    ],
                    [
                        'skor_moora' => $moora['skor_moora'],
                        'rank_moora' => $moora['rank_moora'],
                    ]
                );
            }

            DB::commit();

            return redirect()->route('walikelas.perhitungan.moora.index', ['tahun_ajaran' => $id_ta])
                ->with('success', "Perhitungan MOORA berhasil! {$studentsWithCompletePenilaian} siswa telah di-ranking.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal melakukan perhitungan MOORA: ' . $e->getMessage());
        }
    }

    public function showStepsMoora($id_ta)
    {
        $kelas   = $this->getKelas();
        $kelasId = $kelas->id_kelas;

        $tahunAjaran  = TahunAjaran::findOrFail($id_ta);
        $kriteriaList = Kriteria::orderBy('kode_kriteria')->get();
        $siswaIds     = Siswa::where('id_kelas', $kelasId)->pluck('id_siswa')->toArray();

        $results       = $this->mooraCalculator->calculate($id_ta, $siswaIds);
        $detailedSteps = $this->mooraCalculator->getDetailedSteps();

        $perPage         = 10;
        $stepsCollection = collect($detailedSteps)->values();

        $buildPaginator = function (string $pageName) use ($stepsCollection, $perPage) {
            $currentPage = LengthAwarePaginator::resolveCurrentPage($pageName);
            return new LengthAwarePaginator(
                $stepsCollection->forPage($currentPage, $perPage)->values(),
                $stepsCollection->count(),
                $perPage,
                $currentPage,
                ['path' => request()->url(), 'pageName' => $pageName, 'query' => request()->query()]
            );
        };

        $step1Steps = $buildPaginator('step1_page');
        $step2Steps = $buildPaginator('step2_page');
        $step3Steps = $buildPaginator('step3_page');
        $step4Steps = $buildPaginator('step4_page');

        return view('walikelas.perhitungan.moora.steps', compact(
            'tahunAjaran', 'kriteriaList', 'step1Steps', 'step2Steps', 'step3Steps', 'step4Steps'
        ));
    }

    // ─── Legacy combined methods (kept for compare page) ────────────────────────

    public function index(Request $request)
    {
        return $this->indexSmart($request);
    }

    public function compare($id_ta)
    {
        $kelas   = $this->getKelas();
        $kelasId = $kelas->id_kelas;

        $tahunAjaran = TahunAjaran::findOrFail($id_ta);
        $userId      = auth()->id();

        $baseQuery = HasilAkhir::query()
            ->where('id_ta', $id_ta)
            ->where('user_id', $userId)
            ->whereHas('siswa', fn($q) => $q->where('id_kelas', $kelasId));

        $totalData = (clone $baseQuery)->count();
        $agreement = (clone $baseQuery)->whereColumn('rank_smart', 'rank_moora')->count();

        $hasilList = (clone $baseQuery)
            ->with('siswa')
            ->orderBy('rank_smart')
            ->paginate(10)
            ->withQueryString();

        $agreementPercentage = $totalData > 0
            ? round(($agreement / $totalData) * 100, 2)
            : 0;

        return view('walikelas.perhitungan.compare', compact(
            'tahunAjaran', 'hasilList', 'agreementPercentage'
        ));
    }
}
