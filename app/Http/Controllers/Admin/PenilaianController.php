<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penilaian;
use App\Models\Siswa;
use App\Models\Kriteria;
use App\Models\SubKriteria;
use App\Models\TahunAjaran;
use App\Models\RiwayatPelanggaran;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenilaianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filterTA = $request->get('tahun_ajaran');
        $filterKelas = $request->get('kelas');
        $search = $request->get('search');
        
        // Get all kriteria for displaying columns
        $kriteriaList = Kriteria::orderBy('kode_kriteria')->get();
        
        // Query siswa with their penilaian
        $siswaQuery = Siswa::with(['kelas', 'tahunAjaran', 'penilaian.kriteria'])
            ->when($filterTA, function ($query, $filterTA) {
                return $query->where('id_ta', $filterTA);
            })
            ->when($filterKelas, function ($query, $filterKelas) {
                return $query->where('id_kelas', $filterKelas);
            })
            ->when($search, function ($query, $search) {
                return $query->where('nama_siswa', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%");
            });
        
        $siswaList = $siswaQuery->paginate(10)->appends([
            'tahun_ajaran' => $filterTA,
            'kelas' => $filterKelas,
            'search' => $search
        ]);
        
        // Data for filters
        $tahunAjaranList = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        
        return view('admin.penilaian.index', compact(
            'siswaList',
            'kriteriaList',
            'tahunAjaranList',
            'kelasList',
            'filterTA',
            'filterKelas',
            'search'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $siswaList = Siswa::with('kelas', 'tahunAjaran')->orderBy('nama_siswa')->get();
        $tahunAjaranList = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();
        $tahunAjaranAktif = TahunAjaran::where('is_active', 1)->first();
        
        // Get all kriteria
        $kriteriaList = Kriteria::with('subKriteria')->orderBy('kode_kriteria')->get();
        
        // Pre-selected siswa & TA from query params
        $selectedSiswa = $request->get('siswa');
        $selectedTA = $request->get('ta');
        
        // Calculate C5 if siswa and TA are selected
        $totalPoinPelanggaran = 0;
        if ($selectedSiswa && $selectedTA) {
            $totalPoinPelanggaran = $this->calculateC5($selectedSiswa, $selectedTA);
        }
        
        return view('admin.penilaian.create', compact(
            'siswaList',
            'tahunAjaranList',
            'tahunAjaranAktif',
            'kriteriaList',
            'selectedSiswa',
            'selectedTA',
            'totalPoinPelanggaran'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_siswa' => 'required|exists:tb_siswa,id_siswa',
            'id_ta' => 'required|exists:tb_tahun_ajaran,id_ta',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Get all kriteria
            $kriteriaList = Kriteria::all();
            
            foreach ($kriteriaList as $kriteria) {
                $kodeKriteria = strtolower($kriteria->kode_kriteria);
                
                // Check if penilaian already exists
                $existingPenilaian = Penilaian::where('id_siswa', $validated['id_siswa'])
                    ->where('id_kriteria', $kriteria->id_kriteria)
                    ->where('id_ta', $validated['id_ta'])
                    ->first();
                
                if ($existingPenilaian) {
                    continue; // Skip if already exists
                }
                
                $nilaiAsli = null;
                $nilaiKonversi = null;
                
                // C5 is auto-calculated
                if ($kriteria->kode_kriteria == 'C5') {
                    $nilaiAsli = $this->calculateC5($validated['id_siswa'], $validated['id_ta']);
                    $nilaiKonversi = $this->convertNilaiToSubKriteria($kriteria->id_kriteria, $nilaiAsli);
                } else {
                    // Manual input for other kriteria
                    if ($request->has("nilai_asli_{$kodeKriteria}")) {
                        $nilaiAsli = $request->input("nilai_asli_{$kodeKriteria}");
                        $nilaiKonversi = $this->convertNilaiToSubKriteria($kriteria->id_kriteria, $nilaiAsli);
                    }
                }
                
                // Only save if nilai_asli is not null
                if ($nilaiAsli !== null) {
                    Penilaian::create([
                        'id_siswa' => $validated['id_siswa'],
                        'id_kriteria' => $kriteria->id_kriteria,
                        'id_ta' => $validated['id_ta'],
                        'nilai_asli' => $nilaiAsli,
                        'nilai_konversi' => $nilaiKonversi,
                    ]);
                }
            }
            
            DB::commit();
            
            return redirect()->route('admin.penilaian.index')
                ->with('success', 'Penilaian siswa berhasil ditambahkan.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan penilaian: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id_siswa, Request $request)
    {
        $siswa = Siswa::with(['kelas', 'tahunAjaran'])->findOrFail($id_siswa);
        
        $filterTA = $request->get('ta', $siswa->id_ta);
        
        // Get penilaian for this siswa and TA
        $penilaianList = Penilaian::with('kriteria')
            ->where('id_siswa', $id_siswa)
            ->where('id_ta', $filterTA)
            ->get()
            ->keyBy('id_kriteria');
        
        $kriteriaList = Kriteria::orderBy('kode_kriteria')->get();
        
        return view('admin.penilaian.show', compact('siswa', 'penilaianList', 'kriteriaList', 'filterTA'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id_siswa, Request $request)
    {
        $siswa = Siswa::with('kelas', 'tahunAjaran')->findOrFail($id_siswa);
        
        $tahunAjaranList = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();
        $filterTA = $request->get('ta', $siswa->id_ta);
        
        // Get all kriteria
        $kriteriaList = Kriteria::with('subKriteria')->orderBy('kode_kriteria')->get();
        
        // Get existing penilaian
        $penilaianList = Penilaian::where('id_siswa', $id_siswa)
            ->where('id_ta', $filterTA)
            ->get()
            ->keyBy('id_kriteria');
        
        // Calculate C5
        $totalPoinPelanggaran = $this->calculateC5($id_siswa, $filterTA);
        
        return view('admin.penilaian.edit', compact(
            'siswa',
            'tahunAjaranList',
            'kriteriaList',
            'penilaianList',
            'filterTA',
            'totalPoinPelanggaran'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id_siswa)
    {
        $validated = $request->validate([
            'id_ta' => 'required|exists:tb_tahun_ajaran,id_ta',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Get all kriteria
            $kriteriaList = Kriteria::all();
            
            foreach ($kriteriaList as $kriteria) {
                $kodeKriteria = strtolower($kriteria->kode_kriteria);
                
                $nilaiAsli = null;
                $nilaiKonversi = null;
                
                // C5 is auto-calculated
                if ($kriteria->kode_kriteria == 'C5') {
                    $nilaiAsli = $this->calculateC5($id_siswa, $validated['id_ta']);
                    $nilaiKonversi = $this->convertNilaiToSubKriteria($kriteria->id_kriteria, $nilaiAsli);
                } else {
                    // Manual input for other kriteria
                    if ($request->has("nilai_asli_{$kodeKriteria}")) {
                        $nilaiAsli = $request->input("nilai_asli_{$kodeKriteria}");
                        $nilaiKonversi = $this->convertNilaiToSubKriteria($kriteria->id_kriteria, $nilaiAsli);
                    }
                }
                
                // Update or create penilaian
                if ($nilaiAsli !== null) {
                    Penilaian::updateOrCreate(
                        [
                            'id_siswa' => $id_siswa,
                            'id_kriteria' => $kriteria->id_kriteria,
                            'id_ta' => $validated['id_ta'],
                        ],
                        [
                            'nilai_asli' => $nilaiAsli,
                            'nilai_konversi' => $nilaiKonversi,
                        ]
                    );
                }
            }
            
            DB::commit();
            
            return redirect()->route('admin.penilaian.index')
                ->with('success', 'Penilaian siswa berhasil diperbarui.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui penilaian: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id_siswa, Request $request)
    {
        $filterTA = $request->get('ta');
        
        Penilaian::where('id_siswa', $id_siswa)
            ->where('id_ta', $filterTA)
            ->delete();
        
        return redirect()->route('admin.penilaian.index')
            ->with('success', 'Penilaian siswa berhasil dihapus.');
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

    /**
     * AJAX: Get C5 value for a student
     */
    public function getC5(Request $request)
    {
        $id_siswa = $request->get('id_siswa');
        $id_ta = $request->get('id_ta');
        
        if (!$id_siswa || !$id_ta) {
            return response()->json(['error' => 'Missing parameters'], 400);
        }
        
        $totalPoin = $this->calculateC5($id_siswa, $id_ta);
        
        // Get C5 kriteria
        $kriteriaC5 = Kriteria::where('kode_kriteria', 'C5')->first();
        $nilaiKonversi = null;
        
        if ($kriteriaC5) {
            $nilaiKonversi = $this->convertNilaiToSubKriteria($kriteriaC5->id_kriteria, $totalPoin);
        }
        
        return response()->json([
            'total_poin' => $totalPoin,
            'nilai_konversi' => $nilaiKonversi
        ]);
    }
}
