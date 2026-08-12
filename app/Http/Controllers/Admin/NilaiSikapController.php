<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NilaiSikap;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NilaiSikapController extends Controller
{
    public function index(Request $request)
    {
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
        $filterKelas = $request->get('kelas');

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
        $kelasList = Kelas::orderBy('nama_kelas')->get();

        $siswaList = collect();

        if ($filterTA && $filterSemester) {
            $siswaQuery = Siswa::with(['kelas', 'nilaiSikap' => function ($q) use ($filterTA, $filterSemester) {
                $q->where('id_ta', $filterTA)->where('id_semester', $filterSemester);
            }])
                ->where('id_ta', $filterTA);

            if ($filterKelas) {
                $siswaQuery->where('id_kelas', $filterKelas);
            }

            $siswaList = $siswaQuery->orderBy('id_kelas')->orderBy('nama_siswa')->get();
        }

        return view('admin.nilaisikap.index', compact(
            'siswaList', 'tahunAjaranList', 'semesterList', 'kelasList',
            'filterTA', 'filterSemester', 'filterKelas'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_ta' => 'required|exists:tb_tahun_ajaran,id_ta',
            'id_semester' => 'required|exists:tb_semester,id_semester',
            'id_kelas' => 'nullable',
            'sikap_spiritual' => 'required|array',
            'sikap_spiritual.*' => 'required|in:Sangat Baik,Baik,Cukup,Kurang',
            'sikap_sosial' => 'required|array',
            'sikap_sosial.*' => 'required|in:Sangat Baik,Baik,Cukup,Kurang',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->sikap_spiritual as $id_siswa => $spiritual) {
                NilaiSikap::updateOrCreate(
                    [
                        'id_siswa' => $id_siswa,
                        'id_ta' => $validated['id_ta'],
                        'id_semester' => $validated['id_semester'],
                    ],
                    [
                        'sikap_spiritual' => $spiritual,
                        'sikap_sosial' => $request->sikap_sosial[$id_siswa],
                    ]
                );
            }
            DB::commit();
            return redirect()->back()->with('success', 'Nilai sikap berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan nilai: ' . $e->getMessage());
        }
    }
}
