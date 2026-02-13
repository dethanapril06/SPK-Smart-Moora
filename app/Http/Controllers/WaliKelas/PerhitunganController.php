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

    public function index(Request $request)
    {
        $kelas = $this->getKelas();
        $kelasId = $kelas->id_kelas;

        $filterTA = $request->get('tahun_ajaran');
        $tahunAjaranAktif = TahunAjaran::where('is_active', 1)->first();

        if (!$filterTA && $tahunAjaranAktif) {
            $filterTA = $tahunAjaranAktif->id_ta;
        }

        $userId = auth()->id();

        // Get hasil akhir hanya untuk siswa di kelas ini & milik wali kelas ini
        $hasilQuery = HasilAkhir::with(['siswa.kelas', 'tahunAjaran'])
            ->where('user_id', $userId)
            ->when($filterTA, function ($query, $filterTA) {
                return $query->where('id_ta', $filterTA);
            })
            ->whereHas('siswa', function ($q) use ($kelasId) {
                $q->where('id_kelas', $kelasId);
            });

        $hasilList = $hasilQuery->get()->sortBy('rank_smart');

        $tahunAjaranList = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();

        $hasCalculation = $filterTA && $hasilList->count() > 0;

        // Siswa dengan penilaian lengkap di kelas ini
        $studentsWithCompletePenilaian = 0;
        if ($filterTA) {
            $kriteriaCount = Kriteria::count();
            $siswaIds = Siswa::where('id_kelas', $kelasId)->pluck('id_siswa');
            $studentsWithCompletePenilaian = Penilaian::select('id_siswa')
                ->where('id_ta', $filterTA)
                ->whereIn('id_siswa', $siswaIds)
                ->groupBy('id_siswa')
                ->havingRaw('COUNT(DISTINCT id_kriteria) = ?', [$kriteriaCount])
                ->count();
        }

        return view('walikelas.perhitungan.index', compact(
            'hasilList', 'tahunAjaranList', 'filterTA',
            'hasCalculation', 'studentsWithCompletePenilaian', 'kelas'
        ));
    }

    public function calculate(Request $request)
    {
        $kelas = $this->getKelas();
        $kelasId = $kelas->id_kelas;

        $validated = $request->validate([
            'id_ta' => 'required|exists:tb_tahun_ajaran,id_ta'
        ]);

        $id_ta = $validated['id_ta'];
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
            // Calculate SMART & MOORA hanya untuk siswa di kelas ini
            $smartResults = $this->smartCalculator->calculate($id_ta, $siswaIds);
            $mooraResults = $this->mooraCalculator->calculate($id_ta, $siswaIds);

            $userId = auth()->id();

            $mergedResults = [];
            foreach ($smartResults as $smart) {
                $moora = collect($mooraResults)->firstWhere('id_siswa', $smart['id_siswa']);

                $mergedResults[] = [
                    'id_siswa' => $smart['id_siswa'],
                    'id_ta' => $id_ta,
                    'user_id' => $userId,
                    'skor_smart' => $smart['skor_smart'],
                    'rank_smart' => $smart['rank_smart'],
                    'skor_moora' => $moora ? $moora['skor_moora'] : null,
                    'rank_moora' => $moora ? $moora['rank_moora'] : null,
                ];
            }

            // Hapus hasil sebelumnya hanya milik wali kelas ini untuk siswa di kelas ini
            HasilAkhir::where('id_ta', $id_ta)
                ->where('user_id', $userId)
                ->whereIn('id_siswa', $siswaIds)
                ->delete();

            foreach ($mergedResults as $result) {
                HasilAkhir::create($result);
            }

            DB::commit();

            return redirect()->route('walikelas.perhitungan.index', ['tahun_ajaran' => $id_ta])
                ->with('success', "Perhitungan berhasil! {$studentsWithCompletePenilaian} siswa telah di-ranking menggunakan metode SMART dan MOORA.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal melakukan perhitungan: ' . $e->getMessage());
        }
    }

    public function showSteps($id_ta, $metode)
    {
        $kelas = $this->getKelas();
        $kelasId = $kelas->id_kelas;

        if (!in_array($metode, ['smart', 'moora'])) {
            abort(404);
        }

        $tahunAjaran = TahunAjaran::findOrFail($id_ta);
        $kriteriaList = Kriteria::orderBy('kode_kriteria')->get();
        $siswaIds = Siswa::where('id_kelas', $kelasId)->pluck('id_siswa')->toArray();

        if ($metode == 'smart') {
            $results = $this->smartCalculator->calculate($id_ta, $siswaIds);
            $detailedSteps = $this->smartCalculator->getDetailedSteps();
        } else {
            $results = $this->mooraCalculator->calculate($id_ta, $siswaIds);
            $detailedSteps = $this->mooraCalculator->getDetailedSteps();
        }

        return view('walikelas.perhitungan.steps', compact(
            'tahunAjaran', 'metode', 'kriteriaList', 'detailedSteps'
        ));
    }

    public function compare($id_ta)
    {
        $kelas = $this->getKelas();
        $kelasId = $kelas->id_kelas;

        $tahunAjaran = TahunAjaran::findOrFail($id_ta);

        $userId = auth()->id();

        $hasilList = HasilAkhir::with('siswa')
            ->where('id_ta', $id_ta)
            ->where('user_id', $userId)
            ->whereHas('siswa', function ($q) use ($kelasId) {
                $q->where('id_kelas', $kelasId);
            })
            ->get();

        $agreement = 0;
        foreach ($hasilList as $hasil) {
            if ($hasil->rank_smart == $hasil->rank_moora) {
                $agreement++;
            }
        }

        $agreementPercentage = $hasilList->count() > 0
            ? round(($agreement / $hasilList->count()) * 100, 2)
            : 0;

        return view('walikelas.perhitungan.compare', compact(
            'tahunAjaran', 'hasilList', 'agreementPercentage'
        ));
    }
}
