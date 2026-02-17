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
        $predikatList = ['Sangat Baik', 'Baik', 'Cukup', 'Kurang'];

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
            'siswaList', 'tahunAjaranList', 'predikatList', 'filterTA', 'kelas'
        ));
    }

    public function store(Request $request)
    {
        $kelas = $this->getKelas();

        $validated = $request->validate([
            'id_ta' => 'required|exists:tb_tahun_ajaran,id_ta',
            'id_siswa' => 'required|exists:tb_siswa,id_siswa',
            'nama_ekskul' => 'required|string|max:100',
            'predikat' => 'required|in:Sangat Baik,Baik,Cukup,Kurang',
        ]);

        $siswa = Siswa::findOrFail($validated['id_siswa']);
        abort_if($siswa->id_kelas !== $kelas->id_kelas, 403);

        NilaiEkstrakurikuler::create($validated);

        return redirect()->back()->with('success', 'Data ekstrakurikuler berhasil ditambahkan.');
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
