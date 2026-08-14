<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NilaiAbsensi;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NilaiAbsensiController extends Controller
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
            $siswaQuery = Siswa::with(['kelas', 'nilaiAbsensi' => function ($q) use ($filterTA, $filterSemester) {
                $q->where('id_ta', $filterTA)->where('id_semester', $filterSemester);
            }])
                ->where('id_ta', $filterTA);

            if ($filterKelas) {
                $siswaQuery->where('id_kelas', $filterKelas);
            }

            $siswaList = $siswaQuery->orderBy('id_kelas')->orderBy('nama_siswa')->get();
        }

        return view('admin.nilaiabsensi.index', compact(
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
            'jumlah_sakit' => 'required|array',
            'jumlah_sakit.*' => 'nullable|integer|min:0|max:30',
            'jumlah_izin' => 'required|array',
            'jumlah_izin.*' => 'nullable|integer|min:0|max:30',
            'jumlah_alpa' => 'required|array',
            'jumlah_alpa.*' => 'nullable|integer|min:0|max:30',
        ], [
            'jumlah_sakit.*.min' => 'Jumlah sakit minimal 0.',
            'jumlah_sakit.*.max' => 'Jumlah sakit maksimal 30 hari.',
            'jumlah_izin.*.min' => 'Jumlah izin minimal 0.',
            'jumlah_izin.*.max' => 'Jumlah izin maksimal 30 hari.',
            'jumlah_alpa.*.min' => 'Jumlah alpa minimal 0.',
            'jumlah_alpa.*.max' => 'Jumlah alpa maksimal 30 hari.',
        ]);

        // Validate that total absence for each student does not exceed 30
        foreach ($request->jumlah_sakit as $id_siswa => $sakit) {
            $sakitVal = (int) ($sakit ?? 0);
            $izinVal  = (int) ($request->jumlah_izin[$id_siswa] ?? 0);
            $alpaVal  = (int) ($request->jumlah_alpa[$id_siswa] ?? 0);

            if (($sakitVal + $izinVal + $alpaVal) > 30) {
                $siswa = Siswa::find($id_siswa);
                $nama = $siswa ? $siswa->nama_siswa : "ID {$id_siswa}";
                return redirect()->back()->with('error', "Total absensi untuk siswa {$nama} ({$sakitVal} Sakit + {$izinVal} Izin + {$alpaVal} Alpa = " . ($sakitVal + $izinVal + $alpaVal) . " hari) melebihi batas maksimal 30 hari.");
            }
        }

        DB::beginTransaction();
        try {
            foreach ($request->jumlah_sakit as $id_siswa => $sakit) {
                $sakitVal = min(30, max(0, (int) ($sakit ?? 0)));
                $izinVal  = min(30, max(0, (int) ($request->jumlah_izin[$id_siswa] ?? 0)));
                $alpaVal  = min(30, max(0, (int) ($request->jumlah_alpa[$id_siswa] ?? 0)));

                NilaiAbsensi::updateOrCreate(
                    [
                        'id_siswa' => $id_siswa,
                        'id_ta' => $validated['id_ta'],
                        'id_semester' => $validated['id_semester'],
                    ],
                    [
                        'jumlah_sakit' => $sakitVal,
                        'jumlah_izin'  => $izinVal,
                        'jumlah_alpa'  => $alpaVal,
                    ]
                );
            }
            DB::commit();
            return redirect()->back()->with('success', 'Data absensi berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }
}
