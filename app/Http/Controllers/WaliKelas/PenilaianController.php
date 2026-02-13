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

    public function create(Request $request)
    {
        $kelas = $this->getKelas();

        $siswaList = Siswa::with('kelas', 'tahunAjaran')
            ->where('id_kelas', $kelas->id_kelas)
            ->orderBy('nama_siswa')
            ->get();
        $tahunAjaranList = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();
        $tahunAjaranAktif = TahunAjaran::where('is_active', 1)->first();
        $kriteriaList = Kriteria::with('subKriteria')->orderBy('kode_kriteria')->get();

        $selectedSiswa = $request->get('siswa');
        $selectedTA = $request->get('ta');

        $totalPoinPelanggaran = 0;
        if ($selectedSiswa && $selectedTA) {
            $totalPoinPelanggaran = $this->calculateC5($selectedSiswa, $selectedTA);
        }

        return view('walikelas.penilaian.create', compact(
            'siswaList', 'tahunAjaranList', 'tahunAjaranAktif',
            'kriteriaList', 'selectedSiswa', 'selectedTA', 'totalPoinPelanggaran'
        ));
    }

    public function store(Request $request)
    {
        $kelas = $this->getKelas();

        $validated = $request->validate([
            'id_siswa' => 'required|exists:tb_siswa,id_siswa',
            'id_ta' => 'required|exists:tb_tahun_ajaran,id_ta',
        ]);

        $siswa = Siswa::findOrFail($validated['id_siswa']);
        abort_if($siswa->id_kelas !== $kelas->id_kelas, 403, 'Siswa bukan anggota kelas Anda.');

        DB::beginTransaction();

        try {
            $kriteriaList = Kriteria::all();

            foreach ($kriteriaList as $kriteria) {
                $kodeKriteria = strtolower($kriteria->kode_kriteria);

                $existingPenilaian = Penilaian::where('id_siswa', $validated['id_siswa'])
                    ->where('id_kriteria', $kriteria->id_kriteria)
                    ->where('id_ta', $validated['id_ta'])
                    ->first();

                if ($existingPenilaian) {
                    continue;
                }

                $nilaiAsli = null;
                $nilaiKonversi = null;

                if ($kriteria->kode_kriteria == 'C5') {
                    $nilaiAsli = $this->calculateC5($validated['id_siswa'], $validated['id_ta']);
                    $nilaiKonversi = $this->convertNilaiToSubKriteria($kriteria->id_kriteria, $nilaiAsli);
                } else {
                    if ($request->has("nilai_asli_{$kodeKriteria}")) {
                        $nilaiAsli = $request->input("nilai_asli_{$kodeKriteria}");
                        $nilaiKonversi = $this->convertNilaiToSubKriteria($kriteria->id_kriteria, $nilaiAsli);
                    }
                }

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

            return redirect()->route('walikelas.penilaian.index')
                ->with('success', 'Penilaian siswa berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan penilaian: ' . $e->getMessage());
        }
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

    public function edit($id_siswa, Request $request)
    {
        $kelas = $this->getKelas();
        $siswa = Siswa::with('kelas', 'tahunAjaran')->findOrFail($id_siswa);
        abort_if($siswa->id_kelas !== $kelas->id_kelas, 403, 'Siswa bukan anggota kelas Anda.');

        $tahunAjaranList = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();
        $filterTA = $request->get('ta', $siswa->id_ta);
        $kriteriaList = Kriteria::with('subKriteria')->orderBy('kode_kriteria')->get();

        $penilaianList = Penilaian::where('id_siswa', $id_siswa)
            ->where('id_ta', $filterTA)
            ->get()
            ->keyBy('id_kriteria');

        $totalPoinPelanggaran = $this->calculateC5($id_siswa, $filterTA);

        return view('walikelas.penilaian.edit', compact(
            'siswa', 'tahunAjaranList', 'kriteriaList',
            'penilaianList', 'filterTA', 'totalPoinPelanggaran'
        ));
    }

    public function update(Request $request, $id_siswa)
    {
        $kelas = $this->getKelas();
        $siswa = Siswa::findOrFail($id_siswa);
        abort_if($siswa->id_kelas !== $kelas->id_kelas, 403, 'Siswa bukan anggota kelas Anda.');

        $validated = $request->validate([
            'id_ta' => 'required|exists:tb_tahun_ajaran,id_ta',
        ]);

        DB::beginTransaction();

        try {
            $kriteriaList = Kriteria::all();

            foreach ($kriteriaList as $kriteria) {
                $kodeKriteria = strtolower($kriteria->kode_kriteria);
                $nilaiAsli = null;
                $nilaiKonversi = null;

                if ($kriteria->kode_kriteria == 'C5') {
                    $nilaiAsli = $this->calculateC5($id_siswa, $validated['id_ta']);
                    $nilaiKonversi = $this->convertNilaiToSubKriteria($kriteria->id_kriteria, $nilaiAsli);
                } else {
                    if ($request->has("nilai_asli_{$kodeKriteria}")) {
                        $nilaiAsli = $request->input("nilai_asli_{$kodeKriteria}");
                        $nilaiKonversi = $this->convertNilaiToSubKriteria($kriteria->id_kriteria, $nilaiAsli);
                    }
                }

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

            return redirect()->route('walikelas.penilaian.index')
                ->with('success', 'Penilaian siswa berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui penilaian: ' . $e->getMessage());
        }
    }

    public function destroy($id_siswa, Request $request)
    {
        $kelas = $this->getKelas();
        $siswa = Siswa::findOrFail($id_siswa);
        abort_if($siswa->id_kelas !== $kelas->id_kelas, 403, 'Siswa bukan anggota kelas Anda.');

        $filterTA = $request->get('ta');

        Penilaian::where('id_siswa', $id_siswa)
            ->where('id_ta', $filterTA)
            ->delete();

        return redirect()->route('walikelas.penilaian.index')
            ->with('success', 'Penilaian siswa berhasil dihapus.');
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
