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
use App\Models\NilaiPengetahuan;
use App\Models\NilaiKeterampilan;
use App\Models\NilaiSikap;
use App\Models\NilaiEkstrakurikuler;
use App\Models\NilaiAbsensi;
use App\Models\Semester;
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
        $filterSemester = $request->get('semester');
        $filterKelas = $request->get('kelas');
        $search = $request->get('search');
        
        // Get all kriteria for displaying columns
        $kriteriaList = Kriteria::orderBy('kode_kriteria')->get();
        
        // Query siswa with their penilaian
        $siswaQuery = Siswa::with(['kelas', 'tahunAjaran', 'penilaian' => function ($query) use ($filterSemester) {
            if ($filterSemester) {
                $query->where('id_semester', $filterSemester);
            }
            $query->with('kriteria');
        }])
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
            'semester' => $filterSemester,
            'kelas' => $filterKelas,
            'search' => $search
        ]);
        
        // Data for filters
        $tahunAjaranList = TahunAjaran::representatives()->orderBy('tahun_ajaran', 'desc')->get();
        $semesterList = Semester::orderBy('id_semester')->get();
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        
        return view('admin.penilaian.index', compact(
            'siswaList',
            'kriteriaList',
            'tahunAjaranList',
            'semesterList',
            'kelasList',
            'filterTA',
            'filterSemester',
            'filterKelas',
            'search'
        ));
    }

    /**
     * Display the specified resource.
     */
    public function show($id_siswa, Request $request)
    {
        $siswa = Siswa::with(['kelas', 'tahunAjaran'])->findOrFail($id_siswa);
        
        $filterTA = $request->get('ta', $siswa->id_ta);
        $filterSemester = $request->get('semester');
        
        // Get penilaian for this siswa and TA/Semester
        $penilaianList = Penilaian::with('kriteria')
            ->where('id_siswa', $id_siswa)
            ->where('id_ta', $filterTA)
            ->when($filterSemester, fn($q, $s) => $q->where('id_semester', $s))
            ->get()
            ->keyBy('id_kriteria');
        
        $kriteriaList = Kriteria::orderBy('kode_kriteria')->get();
        $semesterList = Semester::where('id_ta', $filterTA)->get();
        
        return view('admin.penilaian.show', compact('siswa', 'penilaianList', 'kriteriaList', 'semesterList', 'filterTA', 'filterSemester'));
    }

    /**
     * Calculate C6 (Total Poin Pelanggaran) for a student in a specific TA & Semester
     */
    private function calculateC5($id_siswa, $id_ta, $id_semester = null)
    {
        $totalPoin = RiwayatPelanggaran::where('id_siswa', $id_siswa)
            ->where('id_ta', $id_ta)
            ->when($id_semester, fn($q, $s) => $q->where('id_semester', $s))
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
        $nilaiReferensi = round($nilai_asli);

        $subKriteria = SubKriteria::where('id_kriteria', $id_kriteria)
            ->where('nilai_awal', '<=', $nilaiReferensi)
            ->where('nilai_akhir', '>=', $nilaiReferensi)
            ->first();
        
        return $subKriteria ? $subKriteria->bobot_subkriteria : null;
    }

    /**
     * Calculate average nilai for mapel assigned to student's class.
     */
    private function calculateMapelAverage(string $modelClass, $siswa, $id_ta, $id_semester = null)
    {
        $mapelIds = DB::table('tb_kelas_mata_pelajaran')
            ->where('id_kelas', $siswa->id_kelas)
            ->pluck('id_mapel');

        $query = $modelClass::where('id_siswa', $siswa->id_siswa)
            ->where('id_ta', $id_ta)
            ->when($id_semester, fn($q, $s) => $q->where('id_semester', $s));

        if ($mapelIds->isNotEmpty()) {
            $query->whereIn('id_mapel', $mapelIds);
        }

        return $query->avg('nilai');
    }

    /**
     * AJAX: Get C6 value for a student
     */
    public function getC5(Request $request)
    {
        $id_siswa = $request->get('id_siswa');
        $id_ta = $request->get('id_ta');
        $id_semester = $request->get('id_semester');
        
        if (!$id_siswa || !$id_ta) {
            return response()->json(['error' => 'Missing parameters'], 400);
        }
        
        $totalPoin = $this->calculateC5($id_siswa, $id_ta, $id_semester);
        
        // Get C6 kriteria
        $kriteriaC6 = Kriteria::where('kode_kriteria', 'C6')->first();
        $nilaiKonversi = null;
        
        if ($kriteriaC6) {
            $nilaiKonversi = $this->convertNilaiToSubKriteria($kriteriaC6->id_kriteria, $totalPoin);
        }
        
        return response()->json([
            'total_poin' => $totalPoin,
            'nilai_konversi' => $nilaiKonversi
        ]);
    }

    /**
     * Auto-aggregate raw data from new tables into tb_penilaian
     */
    public function aggregate(Request $request)
    {
        $validated = $request->validate([
            'id_ta' => 'required|exists:tb_tahun_ajaran,id_ta',
            'id_semester' => 'required|exists:tb_semester,id_semester',
        ]);

        $id_ta = $validated['id_ta'];
        $id_semester = $validated['id_semester'];
        $siswaList = Siswa::where('id_ta', $id_ta)->get();
        $kriteriaList = Kriteria::all()->keyBy('kode_kriteria');

        DB::beginTransaction();
        try {
            $count = 0;
            foreach ($siswaList as $siswa) {
                // C1: Rata-rata Nilai Pengetahuan
                $avgPengetahuan = $this->calculateMapelAverage(NilaiPengetahuan::class, $siswa, $id_ta, $id_semester);

                // C2: Rata-rata Nilai Keterampilan
                $avgKeterampilan = $this->calculateMapelAverage(NilaiKeterampilan::class, $siswa, $id_ta, $id_semester);

                // C3: Sikap Spiritual, C4: Sikap Sosial
                $sikap = NilaiSikap::where('id_siswa', $siswa->id_siswa)
                    ->where('id_ta', $id_ta)
                    ->where('id_semester', $id_semester)
                    ->first();
                $nilaiSikapSpiritual = $sikap ? $sikap->nilai_spiritual : null;
                $nilaiSikapSosial = $sikap ? $sikap->nilai_sosial : null;

                // C5: Ekstrakurikuler (average of converted predikats)
                $ekskulList = NilaiEkstrakurikuler::where('id_siswa', $siswa->id_siswa)
                    ->where('id_ta', $id_ta)
                    ->where('id_semester', $id_semester)
                    ->get();
                $nilaiEkskul = $ekskulList->count() > 0
                    ? $ekskulList->avg(fn($e) => NilaiEkstrakurikuler::konversiPredikat($e->predikat))
                    : null;

                // C6: Total Poin Pelanggaran
                $totalPoinPelanggaran = $this->calculateC5($siswa->id_siswa, $id_ta, $id_semester);

                // C7: Total Absensi (sakit + izin + alpa)
                $absensi = NilaiAbsensi::where('id_siswa', $siswa->id_siswa)
                    ->where('id_ta', $id_ta)
                    ->where('id_semester', $id_semester)
                    ->first();
                $totalAbsensi = $absensi ? $absensi->total_tidak_hadir : null;

                // Save to tb_penilaian
                $mapping = [
                    'C1' => $avgPengetahuan !== null ? round($avgPengetahuan, 2) : null,
                    'C2' => $avgKeterampilan !== null ? round($avgKeterampilan, 2) : null,
                    'C3' => $nilaiSikapSpiritual !== null ? round($nilaiSikapSpiritual, 2) : null,
                    'C4' => $nilaiSikapSosial !== null ? round($nilaiSikapSosial, 2) : null,
                    'C5' => $nilaiEkskul !== null ? round($nilaiEkskul, 2) : null,
                    'C6' => $totalPoinPelanggaran,
                    'C7' => $totalAbsensi,
                ];

                foreach ($mapping as $kode => $nilaiAsli) {
                    if ($nilaiAsli === null) continue;
                    $kriteria = $kriteriaList[$kode] ?? null;
                    if (!$kriteria) continue;

                    $nilaiKonversi = $this->convertNilaiToSubKriteria($kriteria->id_kriteria, $nilaiAsli);

                    Penilaian::updateOrCreate(
                        [
                            'id_siswa' => $siswa->id_siswa,
                            'id_kriteria' => $kriteria->id_kriteria,
                            'id_semester' => $id_semester,
                        ],
                        [
                            'id_ta' => $id_ta,
                            'nilai_asli' => $nilaiAsli,
                            'nilai_konversi' => $nilaiKonversi,
                        ]
                    );
                    $count++;
                }
            }

            DB::commit();
            return redirect()->route('admin.penilaian.index', ['tahun_ajaran' => $id_ta, 'semester' => $id_semester])
                ->with('success', "Berhasil mengagregasi data. {$count} penilaian diperbarui untuk " . $siswaList->count() . " siswa.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal mengagregasi data: ' . $e->getMessage());
        }
    }
}
