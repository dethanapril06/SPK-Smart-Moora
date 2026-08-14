<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\NilaiKeterampilan;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NilaiKeterampilanController extends Controller
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

        $activeTA = TahunAjaran::where('is_active', true)->first();
        $activeSemester = null;
        if ($activeTA) {
            $activeSemester = Semester::where('id_ta', $activeTA->id_ta)->where('is_active', true)->first()
                ?? Semester::where('id_ta', $activeTA->id_ta)->first();
        }
        if (!$activeSemester) {
            $activeSemester = Semester::where('is_active', true)->first() ?? Semester::first();
        }

        $filterTA = $request->has('tahun_ajaran') ? $request->get('tahun_ajaran') : ($activeTA?->id_ta ?? null);
        $filterSemester = $request->has('semester') ? $request->get('semester') : ($activeSemester?->id_semester ?? null);

        if ($filterTA && $filterSemester) {
            $semExists = Semester::where('id_semester', $filterSemester)->where('id_ta', $filterTA)->exists();
            if (!$semExists) {
                $validSem = Semester::where('id_ta', $filterTA)->where('is_active', true)->first()
                    ?? Semester::where('id_ta', $filterTA)->first();
                $filterSemester = $validSem?->id_semester;
            }
        }

        $tahunAjaranList = TahunAjaran::representatives()->orderBy('tahun_ajaran', 'desc')->get();
        $semesterList = Semester::orderBy('id_semester')->get();
        
        $mapelList = MataPelajaran::orderBy('kode_mapel')->get();

        $siswaList = collect();

        if ($filterTA && $filterSemester) {
            $siswaList = Siswa::with(['nilaiKeterampilan' => function ($q) use ($filterTA, $filterSemester) {
                $q->where('id_ta', $filterTA)->where('id_semester', $filterSemester);
            }])
                ->where('id_kelas', $kelas->id_kelas)
                ->where('id_ta', $filterTA)
                ->orderBy('nama_siswa')
                ->get();
        }

        return view('walikelas.nilaiketerampilan.index', compact(
            'siswaList', 'tahunAjaranList', 'semesterList', 'mapelList', 'filterTA', 'filterSemester', 'kelas'
        ));
    }

    public function store(Request $request)
    {
        $kelas = $this->getKelas();

        $validated = $request->validate([
            'id_ta' => 'required|exists:tb_tahun_ajaran,id_ta',
            'id_semester' => 'required|exists:tb_semester,id_semester',
            'nilai' => 'required|array',
            'nilai.*.*' => 'nullable|numeric|min:0|max:100',
        ], [
            'nilai.*.*.numeric' => 'Nilai harus berupa angka.',
            'nilai.*.*.min' => 'Nilai keterampilan minimal 0.',
            'nilai.*.*.max' => 'Nilai keterampilan maksimal 100.',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->nilai as $id_siswa => $mapelValues) {
                $siswa = Siswa::findOrFail($id_siswa);
                abort_if($siswa->id_kelas !== $kelas->id_kelas, 403);

                foreach ($mapelValues as $id_mapel => $nilai) {
                    if ($nilai !== null && $nilai !== '') {
                        NilaiKeterampilan::updateOrCreate(
                            [
                                'id_siswa' => $id_siswa,
                                'id_mapel' => $id_mapel,
                                'id_ta' => $validated['id_ta'],
                                'id_semester' => $validated['id_semester'],
                            ],
                            ['nilai' => $nilai]
                        );
                    }
                }
            }
            DB::commit();
            return redirect()->back()->with('success', 'Nilai keterampilan berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan nilai: ' . $e->getMessage());
        }
    }
}
