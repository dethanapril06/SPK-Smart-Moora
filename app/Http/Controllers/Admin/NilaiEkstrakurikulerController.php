<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NilaiEkstrakurikuler;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NilaiEkstrakurikulerController extends Controller
{
    public function index(Request $request)
    {
        $filterTA = $request->get('tahun_ajaran');
        $filterKelas = $request->get('kelas');

        $tahunAjaranList = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();
        $kelasList = Kelas::orderBy('nama_kelas')->get();

        $siswaList = collect();

        if ($filterTA && $filterKelas) {
            $siswaList = Siswa::with(['nilaiEkstrakurikuler' => function ($q) use ($filterTA) {
                $q->where('id_ta', $filterTA);
            }])
                ->where('id_kelas', $filterKelas)
                ->where('id_ta', $filterTA)
                ->orderBy('nama_siswa')
                ->get();
        }

        return view('admin.nilaiekstrakurikuler.index', compact(
            'siswaList', 'tahunAjaranList', 'kelasList',
            'filterTA', 'filterKelas'
        ));
    }

    public function create(Request $request)
    {
        $tahunAjaranList = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $siswaList = collect();

        $selectedTA = $request->get('tahun_ajaran');
        $selectedKelas = $request->get('kelas');

        if ($selectedTA && $selectedKelas) {
            $siswaList = Siswa::where('id_kelas', $selectedKelas)
                ->where('id_ta', $selectedTA)
                ->orderBy('nama_siswa')
                ->get();
        }

        return view('admin.nilaiekstrakurikuler.create', compact(
            'tahunAjaranList', 'kelasList', 'siswaList',
            'selectedTA', 'selectedKelas'
        ));
    }

    public function getEkskul(Request $request)
    {
        $idSiswa = $request->get('id_siswa');
        $idTa = $request->get('id_ta');

        $ekskul = NilaiEkstrakurikuler::where('id_siswa', $idSiswa)
            ->where('id_ta', $idTa)
            ->get(['id_nilai_ekskul', 'nama_ekskul', 'predikat']);

        return response()->json($ekskul);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_ta' => 'required|exists:tb_tahun_ajaran,id_ta',
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
                    'id_siswa' => $validated['id_siswa'],
                    'nama_ekskul' => $item['nama_ekskul'],
                    'predikat' => $item['predikat'],
                ]);
            }
            DB::commit();

            return redirect()->route('admin.nilaiekstrakurikuler.index', [
                'tahun_ajaran' => $validated['id_ta'],
                'kelas' => $request->get('id_kelas'),
            ])->with('success', 'Nilai ekstrakurikuler berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $ekskul = NilaiEkstrakurikuler::findOrFail($id);
        $ekskul->delete();

        return redirect()->back()->with('success', 'Nilai ekstrakurikuler berhasil dihapus.');
    }
}
