<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NilaiEkstrakurikuler;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NilaiEkstrakurikulerController extends Controller
{
    public function index(Request $request)
    {
        $filterTA = $request->get('tahun_ajaran');
        $filterSemester = $request->get('semester');
        $filterKelas = $request->get('kelas');

        $tahunAjaranList = TahunAjaran::representatives()->orderBy('tahun_ajaran', 'desc')->get();
        $semesterList = Semester::orderBy('id_semester')->get();
        $kelasList = Kelas::orderBy('nama_kelas')->get();

        $siswaList = collect();

        if ($filterTA && $filterSemester && $filterKelas) {
            $siswaList = Siswa::with(['nilaiEkstrakurikuler' => function ($q) use ($filterTA, $filterSemester) {
                $q->where('id_ta', $filterTA)->where('id_semester', $filterSemester);
            }])
                ->where('id_kelas', $filterKelas)
                ->where('id_ta', $filterTA)
                ->orderBy('nama_siswa')
                ->get();
        }

        return view('admin.nilaiekstrakurikuler.index', compact(
            'siswaList', 'tahunAjaranList', 'semesterList', 'kelasList',
            'filterTA', 'filterSemester', 'filterKelas'
        ));
    }

    public function create(Request $request)
    {
        $tahunAjaranList = TahunAjaran::representatives()->orderBy('tahun_ajaran', 'desc')->get();
        $semesterList = Semester::orderBy('id_semester')->get();
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $siswaList = collect();

        $selectedTA = $request->get('tahun_ajaran');
        $selectedSemester = $request->get('semester');
        $selectedKelas = $request->get('kelas');

        if ($selectedTA && $selectedKelas) {
            $siswaList = Siswa::where('id_kelas', $selectedKelas)
                ->where('id_ta', $selectedTA)
                ->orderBy('nama_siswa')
                ->get();
        }

        return view('admin.nilaiekstrakurikuler.create', compact(
            'tahunAjaranList', 'semesterList', 'kelasList', 'siswaList',
            'selectedTA', 'selectedSemester', 'selectedKelas'
        ));
    }

    public function getEkskul(Request $request)
    {
        $idSiswa = $request->get('id_siswa');
        $idTa = $request->get('id_ta');
        $idSemester = $request->get('id_semester');

        $ekskul = NilaiEkstrakurikuler::where('id_siswa', $idSiswa)
            ->where('id_ta', $idTa)
            ->when($idSemester, fn($q, $s) => $q->where('id_semester', $s))
            ->get(['id_nilai_ekskul', 'nama_ekskul', 'predikat']);

        return response()->json($ekskul);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_ta' => 'required|exists:tb_tahun_ajaran,id_ta',
            'id_semester' => 'required|exists:tb_semester,id_semester',
            'id_siswa' => 'required|exists:tb_siswa,id_siswa',
            'ekskul' => 'required|array|min:1',
            'ekskul.*.nama_ekskul' => 'required|string|max:50',
            'ekskul.*.predikat' => 'required|in:Sangat Baik,Baik,Cukup,Kurang',
        ], [
            'ekskul.required' => 'Minimal harus ada 1 ekstrakurikuler.',
            'ekskul.*.nama_ekskul.required' => 'Nama ekstrakurikuler wajib diisi.',
            'ekskul.*.predikat.required' => 'Predikat wajib dipilih.',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['ekskul'] as $item) {
                NilaiEkstrakurikuler::create([
                    'id_ta' => $validated['id_ta'],
                    'id_semester' => $validated['id_semester'],
                    'id_siswa' => $validated['id_siswa'],
                    'nama_ekskul' => $item['nama_ekskul'],
                    'predikat' => $item['predikat'],
                ]);
            }
            DB::commit();

            if ($request->boolean('redirect_to_edit')) {
                return redirect()->route('admin.nilaiekstrakurikuler.edit', [
                    'id' => $validated['id_siswa'],
                    'tahun_ajaran' => $validated['id_ta'],
                    'semester' => $validated['id_semester'],
                ])->with('success', 'Nilai ekstrakurikuler berhasil ditambahkan.');
            }

            return redirect()->route('admin.nilaiekstrakurikuler.index', [
                'tahun_ajaran' => $validated['id_ta'],
                'semester' => $validated['id_semester'],
                'kelas' => $request->get('id_kelas'),
            ])->with('success', 'Nilai ekstrakurikuler berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function edit(Request $request, $id)
    {
        $siswa = Siswa::with('kelas')->findOrFail($id);
        $selectedTA = $request->get('tahun_ajaran', $siswa->id_ta);
        $selectedSemester = $request->get('semester');
        $tahunAjaran = TahunAjaran::findOrFail($selectedTA);
        $semesterList = Semester::orderBy('id_semester')->get();
        
        $ekskulList = NilaiEkstrakurikuler::where('id_siswa', $siswa->id_siswa)
            ->where('id_ta', $selectedTA)
            ->when($selectedSemester, fn($q, $s) => $q->where('id_semester', $s))
            ->orderBy('nama_ekskul')
            ->get();

        return view('admin.nilaiekstrakurikuler.edit', compact(
            'siswa', 'tahunAjaran', 'semesterList', 'ekskulList', 'selectedTA', 'selectedSemester'
        ));
    }

    public function updateAll(Request $request, $siswa_id)
    {
        $siswa = Siswa::findOrFail($siswa_id);
        $id_ta = $request->input('id_ta');
        $id_semester = $request->input('id_semester');

        $validated = $request->validate([
            'id_ta' => 'required|exists:tb_tahun_ajaran,id_ta',
            'id_semester' => 'nullable|exists:tb_semester,id_semester',
            'ekskul' => 'required|array',
            'ekskul.*.nama_ekskul' => 'required|string|max:50',
            'ekskul.*.predikat' => 'required|in:Sangat Baik,Baik,Cukup,Kurang',
        ], [
            'ekskul.*.nama_ekskul.required' => 'Nama ekstrakurikuler wajib diisi.',
            'ekskul.*.predikat.required' => 'Predikat wajib dipilih.',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['ekskul'] as $id => $item) {
                $ekskul = NilaiEkstrakurikuler::where('id_siswa', $siswa_id)
                    ->where('id_ta', $id_ta)
                    ->when($id_semester, fn($q, $s) => $q->where('id_semester', $s))
                    ->findOrFail($id);

                $ekskul->update([
                    'nama_ekskul' => $item['nama_ekskul'],
                    'predikat' => $item['predikat'],
                ]);
            }
            DB::commit();

            return redirect()->route('admin.nilaiekstrakurikuler.edit', [
                'id' => $siswa_id,
                'tahun_ajaran' => $id_ta,
                'semester' => $id_semester,
            ])->with('success', 'Semua nilai ekstrakurikuler berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $ekskul = NilaiEkstrakurikuler::findOrFail($id);
        $ekskul->delete();

        return redirect()->back()->with('success', 'Nilai ekstrakurikuler berhasil dihapus.');
    }
}
