<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\NilaiEkstrakurikuler;
use App\Models\Siswa;
use App\Models\TahunAjaran;
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

        $tahunAjaranList = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();

        $siswaList = collect();

        if ($filterTA) {
            $siswaList = Siswa::with(['nilaiEkstrakurikuler' => function ($q) use ($filterTA) {
                $q->where('id_ta', $filterTA);
            }])
                ->where('id_kelas', $kelas->id_kelas)
                ->where('id_ta', $filterTA)
                ->orderBy('nama_siswa')
                ->get();
        }

        return view('walikelas.nilaiekstrakurikuler.index', compact(
            'siswaList', 'tahunAjaranList', 'filterTA', 'kelas'
        ));
    }

    public function create(Request $request)
    {
        $kelas = $this->getKelas();
        $tahunAjaranList = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();
        $siswaList = collect();

        $selectedTA = $request->get('tahun_ajaran');

        if ($selectedTA) {
            $siswaList = Siswa::where('id_kelas', $kelas->id_kelas)
                ->where('id_ta', $selectedTA)
                ->orderBy('nama_siswa')
                ->get();
        }

        return view('walikelas.nilaiekstrakurikuler.create', compact(
            'tahunAjaranList', 'siswaList', 'selectedTA', 'kelas'
        ));
    }

    public function getEkskul(Request $request)
    {
        $kelas = $this->getKelas();
        $idSiswa = $request->get('id_siswa');
        $idTa = $request->get('id_ta');

        // Verify siswa belongs to wali kelas's class
        $siswa = Siswa::findOrFail($idSiswa);
        abort_if($siswa->id_kelas !== $kelas->id_kelas, 403);

        $ekskul = NilaiEkstrakurikuler::where('id_siswa', $idSiswa)
            ->where('id_ta', $idTa)
            ->get(['id_nilai_ekskul', 'nama_ekskul', 'predikat']);

        return response()->json($ekskul);
    }

    public function store(Request $request)
    {
        $kelas = $this->getKelas();

        $validated = $request->validate([
            'id_ta' => 'required|exists:tb_tahun_ajaran,id_ta',
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
                    'id_siswa' => $validated['id_siswa'],
                    'nama_ekskul' => $item['nama_ekskul'],
                    'predikat' => $item['predikat'],
                ]);
            }
            DB::commit();

            return redirect()->route('walikelas.nilaiekstrakurikuler.index', [
                'tahun_ajaran' => $validated['id_ta'],
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
}
