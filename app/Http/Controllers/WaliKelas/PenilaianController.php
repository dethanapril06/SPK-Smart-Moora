<?php

namespace App\Http\Controllers\WaliKelas;

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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenilaianController extends Controller
{
    protected function getKelas()
    {
        $kelas = Kelas::where('id_wali_kelas', auth()->id())->first();
        abort_if(!$kelas, 403, 'Anda belum ditugaskan sebagai wali kelas.');
        return $kelas;
    }

    public function index(Request $request)
    {
        $kelas = $this->getKelas();
        $filterTA = $request->get('tahun_ajaran');
        $search = $request->get('search');

        $kriteriaList = Kriteria::orderBy('kode_kriteria')->get();

        $siswaQuery = Siswa::with(['kelas', 'tahunAjaran', 'penilaian.kriteria'])
            ->where('id_kelas', $kelas->id_kelas)
            ->when($filterTA, function ($query, $filterTA) {
                return $query->where('id_ta', $filterTA);
            })
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('nama_siswa', 'like', "%{$search}%")
                        ->orWhere('nisn', 'like', "%{$search}%");
                });
            });

        $siswaList = $siswaQuery->paginate(10)->appends([
            'tahun_ajaran' => $filterTA,
            'search' => $search
        ]);

        $tahunAjaranList = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();

        return view('walikelas.penilaian.index', compact(
            'siswaList', 'kriteriaList', 'tahunAjaranList',
            'filterTA', 'search', 'kelas'
        ));
    }

    public function show($id_siswa, Request $request)
    {
        $kelas = $this->getKelas();
        $siswa = Siswa::with(['kelas', 'tahunAjaran'])->findOrFail($id_siswa);
        abort_if($siswa->id_kelas !== $kelas->id_kelas, 403, 'Siswa bukan anggota kelas Anda.');

        $filterTA = $request->get('ta', $siswa->id_ta);

        $penilaianList = Penilaian::with('kriteria')
            ->where('id_siswa', $id_siswa)
            ->where('id_ta', $filterTA)
            ->get()
            ->keyBy('id_kriteria');

        $kriteriaList = Kriteria::orderBy('kode_kriteria')->get();

        return view('walikelas.penilaian.show', compact('siswa', 'penilaianList', 'kriteriaList', 'filterTA'));
    }

    /**
     * Auto-aggregate raw data from new tables into tb_penilaian (scoped to kelas)
     */
    public function aggregate(Request $request)
    {
        $kelas = $this->getKelas();

        $validated = $request->validate([
            'id_ta' => 'required|exists:tb_tahun_ajaran,id_ta',
        ]);

        $id_ta = $validated['id_ta'];
        $siswaList = Siswa::where('id_ta', $id_ta)
            ->where('id_kelas', $kelas->id_kelas)
            ->get();
        $kriteriaList = Kriteria::all()->keyBy('kode_kriteria');

        DB::beginTransaction();
        try {
            $count = 0;
            foreach ($siswaList as $siswa) {
                // C1: Rata-rata Nilai Pengetahuan
                $avgPengetahuan = NilaiPengetahuan::where('id_siswa', $siswa->id_siswa)
                    ->where('id_ta', $id_ta)
                    ->avg('nilai');

                // C2: Rata-rata Nilai Keterampilan
                $avgKeterampilan = NilaiKeterampilan::where('id_siswa', $siswa->id_siswa)
                    ->where('id_ta', $id_ta)
                    ->avg('nilai');

                // C3: Sikap (average of converted spiritual + sosial)
                $sikap = NilaiSikap::where('id_siswa', $siswa->id_siswa)
                    ->where('id_ta', $id_ta)
                    ->first();
                $nilaiSikap = $sikap ? $sikap->nilai_rata_rata : null;

                // C4: Ekstrakurikuler (average of converted predikats)
                $ekskulList = NilaiEkstrakurikuler::where('id_siswa', $siswa->id_siswa)
                    ->where('id_ta', $id_ta)
                    ->get();
                $nilaiEkskul = $ekskulList->count() > 0
                    ? $ekskulList->avg(fn($e) => NilaiEkstrakurikuler::konversiPredikat($e->predikat))
                    : null;

                // C5: Total Poin Pelanggaran
                $totalPoinPelanggaran = $this->calculateC5($siswa->id_siswa, $id_ta);

                // C6: Total Absensi (sakit + izin + alpa)
                $absensi = NilaiAbsensi::where('id_siswa', $siswa->id_siswa)
                    ->where('id_ta', $id_ta)
                    ->first();
                $totalAbsensi = $absensi ? $absensi->total_tidak_hadir : null;

                // Save to tb_penilaian
                $mapping = [
                    'C1' => $avgPengetahuan !== null ? round($avgPengetahuan) : null,
                    'C2' => $avgKeterampilan !== null ? round($avgKeterampilan) : null,
                    'C3' => $nilaiSikap !== null ? round($nilaiSikap) : null,
                    'C4' => $nilaiEkskul !== null ? round($nilaiEkskul) : null,
                    'C5' => $totalPoinPelanggaran,
                    'C6' => $totalAbsensi,
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
                            'id_ta' => $id_ta,
                        ],
                        [
                            'nilai_asli' => $nilaiAsli,
                            'nilai_konversi' => $nilaiKonversi,
                        ]
                    );
                    $count++;
                }
            }

            DB::commit();
            return redirect()->route('walikelas.penilaian.index')
                ->with('success', "Berhasil mengagregasi data. {$count} penilaian diperbarui untuk " . $siswaList->count() . " siswa.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal mengagregasi data: ' . $e->getMessage());
        }
    }

    private function calculateC5($id_siswa, $id_ta)
    {
        $totalPoin = RiwayatPelanggaran::where('id_siswa', $id_siswa)
            ->where('id_ta', $id_ta)
            ->with('jenisPelanggaran')
            ->get()
            ->sum(function ($riwayat) {
                return $riwayat->jenisPelanggaran ? $riwayat->jenisPelanggaran->bobot_poin : 0;
            });

        return $totalPoin;
    }

    private function convertNilaiToSubKriteria($id_kriteria, $nilai_asli)
    {
        $subKriteria = SubKriteria::where('id_kriteria', $id_kriteria)
            ->where('nilai_awal', '<=', $nilai_asli)
            ->where('nilai_akhir', '>=', $nilai_asli)
            ->first();

        return $subKriteria ? $subKriteria->bobot_subkriteria : null;
    }

    public function getC5(Request $request)
    {
        $id_siswa = $request->get('id_siswa');
        $id_ta = $request->get('id_ta');

        if (!$id_siswa || !$id_ta) {
            return response()->json(['error' => 'Missing parameters'], 400);
        }

        $totalPoin = $this->calculateC5($id_siswa, $id_ta);

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
