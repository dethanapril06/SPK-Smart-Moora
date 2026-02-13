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
     * Display listing of calculation results
     */
    public function index(Request $request)
    {
        $filterTA = $request->get('tahun_ajaran');
        $filterKelas = $request->get('kelas');
        $tahunAjaranAktif = TahunAjaran::where('is_active', 1)->first();
        
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
                    $q->where('id_kelas', $filterKelas);
                });
            });
        
        $hasilList = $hasilQuery->get()->sortBy('rank_smart');
        
        // Data for filters
        $tahunAjaranList = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        
        // Check if calculation exists for selected TA
        $hasCalculation = $filterTA && $hasilList->count() > 0;
        
        // Get students with complete penilaian for selected TA
        $studentsWithCompletePenilaian = 0;
        if ($filterTA) {
            $kriteriaCount = Kriteria::count();
            $studentsWithCompletePenilaian = Penilaian::select('id_siswa')
                ->where('id_ta', $filterTA)
                ->groupBy('id_siswa')
                ->havingRaw('COUNT(DISTINCT id_kriteria) = ?', [$kriteriaCount])
                ->count();
        }
        
        return view('admin.perhitungan.index', compact(
            'hasilList',
            'tahunAjaranList',
            'kelasList',
            'filterTA',
            'filterKelas',
            'hasCalculation',
            'studentsWithCompletePenilaian'
        ));
    }
    
    /**
     * Calculate SMART and MOORA scores
     */
    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'id_ta' => 'required|exists:tb_tahun_ajaran,id_ta'
        ]);
        
        $id_ta = $validated['id_ta'];
        
        // Check if there are students with complete penilaian
        $kriteriaCount = Kriteria::count();
        $studentsWithCompletePenilaian = Penilaian::select('id_siswa')
            ->where('id_ta', $id_ta)
            ->groupBy('id_siswa')
            ->havingRaw('COUNT(DISTINCT id_kriteria) = ?', [$kriteriaCount])
            ->count();
        
        if ($studentsWithCompletePenilaian < 2) {
            return redirect()->back()->with('error', 'Minimal 2 siswa dengan penilaian lengkap diperlukan untuk perhitungan ranking.');
        }
        
        DB::beginTransaction();
        
        try {
            // Calculate SMART scores
            $smartResults = $this->smartCalculator->calculate($id_ta);
            
            // Calculate MOORA scores
            $mooraResults = $this->mooraCalculator->calculate($id_ta);
            
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
            
            return redirect()->route('admin.perhitungan.index', ['tahun_ajaran' => $id_ta])
                ->with('success', "Perhitungan berhasil! {$studentsWithCompletePenilaian} siswa telah di-ranking menggunakan metode SMART dan MOORA.");
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal melakukan perhitungan: ' . $e->getMessage());
        }
    }
    
    /**
     * Show detailed calculation steps
     */
    public function showSteps($id_ta, $metode)
    {
        if (!in_array($metode, ['smart', 'moora'])) {
            abort(404);
        }
        
        $tahunAjaran = TahunAjaran::findOrFail($id_ta);
        $kriteriaList = Kriteria::orderBy('kode_kriteria')->get();
        
        // Calculate to get detailed steps
        if ($metode == 'smart') {
            $results = $this->smartCalculator->calculate($id_ta);
            $detailedSteps = $this->smartCalculator->getDetailedSteps();
        } else {
            $results = $this->mooraCalculator->calculate($id_ta);
            $detailedSteps = $this->mooraCalculator->getDetailedSteps();
        }
        
        return view('admin.perhitungan.steps', compact(
            'tahunAjaran',
            'metode',
            'kriteriaList',
            'detailedSteps'
        ));
    }
    
    /**
     * Compare SMART and MOORA results
     */
    public function compare($id_ta)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id_ta);
        
        $hasilList = HasilAkhir::with('siswa')
            ->where('id_ta', $id_ta)
            ->where('user_id', auth()->id())
            ->get();
        
        // Calculate agreement
        $agreement = 0;
        foreach ($hasilList as $hasil) {
            if ($hasil->rank_smart == $hasil->rank_moora) {
                $agreement++;
            }
        }
        
        $agreementPercentage = $hasilList->count() > 0 
            ? round(($agreement / $hasilList->count()) * 100, 2) 
            : 0;
        
        return view('admin.perhitungan.compare', compact(
            'tahunAjaran',
            'hasilList',
            'agreementPercentage'
        ));
    }
}
