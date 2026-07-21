<?php

namespace App\Http\Controllers\WaliKelas;

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
        $filterSemester = $request->get('semester');

        $tahunAjaranList = TahunAjaran::representatives()->orderBy('tahun_ajaran', 'desc')->get();
        $semesterList = Semester::orderBy('id_semester')->get();

        $siswaList = collect();

        if ($filterTA && $filterSemester) {
            $siswaList = Siswa::with(['nilaiEkstrakurikuler' => function ($q) use ($filterTA, $filterSemester) {
                $q->where('id_ta', $filterTA)->where('id_semester', $filterSemester);
            }])
                ->where('id_kelas', $kelas->id_kelas)
                ->where('id_ta', $filterTA)
                ->orderBy('nama_siswa')
                ->get();
        }

        return view('walikelas.nilaiekstrakurikuler.index', compact(
            'siswaList', 'tahunAjaranList', 'semesterList', 'filterTA', 'filterSemester', 'kelas'
        ));
    }

    public function create(Request $request)
    {
        $kelas = $this->getKelas();
        $tahunAjaranList = TahunAjaran::representatives()->orderBy('tahun_ajaran', 'desc')->get();
        $semesterList = Semester::orderBy('id_semester')->get();
        $siswaList = collect();

        $selectedTA = $request->get('tahun_ajaran');
        $selectedSemester = $request->get('semester');

        if ($selectedTA) {
            $siswaList = Siswa::where('id_kelas', $kelas->id_kelas)
                ->where('id_ta', $selectedTA)
                ->orderBy('nama_siswa')
                ->get();
        }

        return view('walikelas.nilaiekstrakurikuler.create', compact(
            'tahunAjaranList', 'semesterList', 'siswaList', 'selectedTA', 'selectedSemester', 'kelas'
        ));
    }

    public function getEkskul(Request $request)
    {
        $kelas = $this->getKelas();
        $idSiswa = $request->get('id_siswa');
        $idTa = $request->get('id_ta');
        $idSemester = $request->get('id_semester');

        // Verify siswa belongs to wali kelas's class
        $siswa = Siswa::findOrFail($idSiswa);
        abort_if($siswa->id_kelas !== $kelas->id_kelas, 403);

        $ekskul = NilaiEkstrakurikuler::where('id_siswa', $idSiswa)
            ->where('id_ta', $idTa)
            ->when($idSemester, fn($q, $s) => $q->where('id_semester', $s))
            ->get(['id_nilai_ekskul', 'nama_ekskul', 'predikat']);

        return response()->json($ekskul);
    }

    public function store(Request $request)
    {
        $kelas = $this->getKelas();

        $validated = $request->validate([
            'id_ta' => 'required|exists:tb_tahun_ajaran,id_ta',
            'id_semester' => 'required|exists:tb_semester,id_semester',
            'id_siswa' => 'required|exists:tb_siswa,id_siswa',
            'ekskul' => 'required|array|min:1',
            'ekskul.*.nama_ekskul' => 'required|string|max:100',
            'ekskul.*.predikat' => 'required|in:Sangat Baik,Baik,Cukup,Kurang',
        ], [
            'ekskul.required' => 'Minimal harus ada 1 ekstrakurikuler.',
            'ekskul.*.nama_ekskul.required' => 'Nama ekstrakurikuler wajib diisi.',
            'ekskul.*.predikat.required' => 'Predikat wajib dipilih.',
        ]);

        $siswa = Siswa::findOrFail($validated['id_siswa']);
        abort_if($siswa->id_kelas !== $kelas->id_kelas, 403);

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
                return redirect()->route('walikelas.nilaiekstrakurikuler.edit', [
                    'id' => $validated['id_siswa'],
                    'tahun_ajaran' => $validated['id_ta'],
                    'semester' => $validated['id_semester'],
                ])->with('success', 'Nilai ekstrakurikuler berhasil ditambahkan.');
            }

            return redirect()->route('walikelas.nilaiekstrakurikuler.index', [
                'tahun_ajaran' => $validated['id_ta'],
                'semester' => $validated['id_semester'],
            ])->with('success', 'Nilai ekstrakurikuler berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $kelas = $this->getKelas();

        $ekskul = NilaiEkstrakurikuler::findOrFail($id);
        $siswa = Siswa::findOrFail($ekskul->id_siswa);
        abort_if($siswa->id_kelas !== $kelas->id_kelas, 403);

        $ekskul->delete();

        return redirect()->back()->with('success', 'Data ekstrakurikuler berhasil dihapus.');
    }

    public function edit(Request $request, $id)
    {
        $kelas = $this->getKelas();
        $siswa = Siswa::with('kelas')->findOrFail($id);
        abort_if($siswa->id_kelas !== $kelas->id_kelas, 403);

        $selectedTA = $request->get('tahun_ajaran', $siswa->id_ta);
        $selectedSemester = $request->get('semester');
        $tahunAjaran = TahunAjaran::findOrFail($selectedTA);
        $semesterList = Semester::orderBy('id_semester')->get();
        $ekskulList = NilaiEkstrakurikuler::where('id_siswa', $siswa->id_siswa)
            ->where('id_ta', $selectedTA)
            ->when($selectedSemester, fn($q, $s) => $q->where('id_semester', $s))
            ->orderBy('nama_ekskul')
            ->get();

        return view('walikelas.nilaiekstrakurikuler.edit', compact(
            'siswa', 'tahunAjaran', 'semesterList', 'ekskulList', 'selectedTA', 'selectedSemester', 'kelas'
        ));
    }

    public function updateAll(Request $request, $siswa_id)
    {
        $kelas = $this->getKelas();
        $siswa = Siswa::findOrFail($siswa_id);
        abort_if($siswa->id_kelas !== $kelas->id_kelas, 403);

        $id_ta = $request->input('id_ta');
        $id_semester = $request->input('id_semester');

        $validated = $request->validate([
            'id_ta' => 'required|exists:tb_tahun_ajaran,id_ta',
            'id_semester' => 'nullable|exists:tb_semester,id_semester',
            'ekskul' => 'required|array',
            'ekskul.*.nama_ekskul' => 'required|string|max:100',
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

            return redirect()->route('walikelas.nilaiekstrakurikuler.edit', [
                'id' => $siswa_id,
                'tahun_ajaran' => $id_ta,
                'semester' => $id_semester,
            ])->with('success', 'Semua nilai ekstrakurikuler berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }
}
