<?php

namespace App\Http\Controllers\WaliKelas;

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

        $siswaList = collect();

        if ($filterTA && $filterSemester) {
            $siswaList = Siswa::with(['nilaiAbsensi' => function ($q) use ($filterTA, $filterSemester) {
                $q->where('id_ta', $filterTA)->where('id_semester', $filterSemester);
            }])
                ->where('id_kelas', $kelas->id_kelas)
                ->where('id_ta', $filterTA)
                ->orderBy('nama_siswa')
                ->get();
        }

        return view('walikelas.nilaiabsensi.index', compact(
            'siswaList', 'tahunAjaranList', 'semesterList', 'filterTA', 'filterSemester', 'kelas'
        ));
    }

    public function store(Request $request)
    {
        $kelas = $this->getKelas();

        $validated = $request->validate([
            'id_ta' => 'required|exists:tb_tahun_ajaran,id_ta',
            'id_semester' => 'required|exists:tb_semester,id_semester',
            'absensi' => 'required|array',
            'absensi.*.sakit' => 'nullable|integer|min:0|max:30',
            'absensi.*.izin' => 'nullable|integer|min:0|max:30',
            'absensi.*.alpa' => 'nullable|integer|min:0|max:30',
        ], [
            'absensi.*.sakit.min' => 'Jumlah sakit minimal 0.',
            'absensi.*.sakit.max' => 'Jumlah sakit maksimal 30 hari.',
            'absensi.*.izin.min' => 'Jumlah izin minimal 0.',
            'absensi.*.izin.max' => 'Jumlah izin maksimal 30 hari.',
            'absensi.*.alpa.min' => 'Jumlah alpa minimal 0.',
            'absensi.*.alpa.max' => 'Jumlah alpa maksimal 30 hari.',
        ]);

        // Validate that total absence for each student does not exceed 30
        foreach ($request->absensi as $id_siswa => $values) {
            $sakitVal = (int) ($values['sakit'] ?? 0);
            $izinVal  = (int) ($values['izin'] ?? 0);
            $alpaVal  = (int) ($values['alpa'] ?? 0);

            if (($sakitVal + $izinVal + $alpaVal) > 30) {
                $siswa = Siswa::find($id_siswa);
                $nama = $siswa ? $siswa->nama_siswa : "ID {$id_siswa}";
                return redirect()->back()->with('error', "Total absensi untuk siswa {$nama} ({$sakitVal} Sakit + {$izinVal} Izin + {$alpaVal} Alpa = " . ($sakitVal + $izinVal + $alpaVal) . " hari) melebihi batas maksimal 30 hari.");
            }
        }

        DB::beginTransaction();
        try {
            foreach ($request->absensi as $id_siswa => $values) {
                $siswa = Siswa::findOrFail($id_siswa);
                abort_if($siswa->id_kelas !== $kelas->id_kelas, 403);

                $sakitVal = min(30, max(0, (int) ($values['sakit'] ?? 0)));
                $izinVal  = min(30, max(0, (int) ($values['izin'] ?? 0)));
                $alpaVal  = min(30, max(0, (int) ($values['alpa'] ?? 0)));

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
