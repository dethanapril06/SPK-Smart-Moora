<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class SiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $siswa = Siswa::aktif()->with(['kelas', 'tahunAjaran'])
            ->when($search, function ($query, $search) {
                return $query->where('nisn', 'like', "%{$search}%")
                    ->orWhere('nama_siswa', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%")
                    ->orWhereHas('kelas', function($q) use ($search) {
                        $q->where('nama_kelas', 'like', "%{$search}%");
                    });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends(['search' => $search]);
        
        return view('admin.siswa.index', compact('siswa', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kelas = Kelas::orderBy('nama_kelas')->get();
        $tahunAjaran = TahunAjaran::representatives()->orderBy('tahun_ajaran', 'desc')->get();
        
        return view('admin.siswa.create', compact('kelas', 'tahunAjaran'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nisn' => 'required|string|max:20|unique:tb_siswa,nisn',
            'nama_siswa' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'nullable|string',
            'id_kelas' => 'required|exists:tb_kelas,id_kelas',
            'id_ta' => 'required|exists:tb_tahun_ajaran,id_ta'
        ], [
            'nisn.required' => 'NISN wajib diisi.',
            'nisn.unique' => 'NISN sudah terdaftar.',
            'nama_siswa.required' => 'Nama siswa wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => 'Jenis kelamin tidak valid.',
            'id_kelas.required' => 'Kelas wajib dipilih.',
            'id_kelas.exists' => 'Kelas tidak valid.',
            'id_ta.required' => 'Tahun ajaran wajib dipilih.',
            'id_ta.exists' => 'Tahun ajaran tidak valid.'
        ]);

        Siswa::create($validated);

        return redirect()->route('admin.siswa.index')
            ->with('success', 'Data siswa berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Siswa $siswa)
    {
        $siswa->load('kelas', 'tahunAjaran', 'penilaian', 'riwayatPelanggaran');
        return view('admin.siswa.show', compact('siswa'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Siswa $siswa)
    {
        $kelas = Kelas::orderBy('nama_kelas')->get();
        $tahunAjaran = TahunAjaran::representatives()->orderBy('tahun_ajaran', 'desc')->get();
        
        return view('admin.siswa.edit', compact('siswa', 'kelas', 'tahunAjaran'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Siswa $siswa)
    {
        $validated = $request->validate([
            'nisn' => 'required|string|max:20|unique:tb_siswa,nisn,' . $siswa->id_siswa . ',id_siswa',
            'nama_siswa' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'nullable|string',
            'id_kelas' => 'required|exists:tb_kelas,id_kelas',
            'id_ta' => 'required|exists:tb_tahun_ajaran,id_ta'
        ], [
            'nisn.required' => 'NISN wajib diisi.',
            'nisn.unique' => 'NISN sudah terdaftar.',
            'nama_siswa.required' => 'Nama siswa wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => 'Jenis kelamin tidak valid.',
            'id_kelas.required' => 'Kelas wajib dipilih.',
            'id_kelas.exists' => 'Kelas tidak valid.',
            'id_ta.required' => 'Tahun ajaran wajib dipilih.',
            'id_ta.exists' => 'Tahun ajaran tidak valid.'
        ]);

        $siswa->update($validated);

        return redirect()->route('admin.siswa.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Siswa $siswa)
    {
        $siswa->delete();

        return redirect()->route('admin.siswa.index')
            ->with('success', 'Data siswa berhasil dihapus.');
    }

    /**
     * Display a listing of graduated students.
     */
    public function lulus(Request $request)
    {
        $search = $request->get('search');
        $tahunLulus = $request->get('tahun_lulus');
        $jenisKelamin = $request->get('jenis_kelamin');

        $siswa = Siswa::lulus()
            ->when($search, function ($q, $search) {
                return $q->where(function ($sub) use ($search) {
                    $sub->where('nisn', 'like', "%{$search}%")
                        ->orWhere('nama_siswa', 'like', "%{$search}%")
                        ->orWhere('alamat', 'like', "%{$search}%");
                });
            })
            ->when($tahunLulus, function ($q, $tahunLulus) {
                return $q->where('tahun_lulus', $tahunLulus);
            })
            ->when($jenisKelamin, function ($q, $jenisKelamin) {
                return $q->where('jenis_kelamin', $jenisKelamin);
            })
            ->orderBy('tahun_lulus', 'desc')
            ->orderBy('nama_siswa', 'asc')
            ->paginate(15)
            ->appends($request->all());

        $tahunLulusList = Siswa::lulus()
            ->whereNotNull('tahun_lulus')
            ->select('tahun_lulus')
            ->distinct()
            ->orderBy('tahun_lulus', 'desc')
            ->pluck('tahun_lulus');

        return view('admin.siswa.lulus', compact(
            'siswa',
            'search',
            'tahunLulus',
            'jenisKelamin',
            'tahunLulusList'
        ));
    }

    /**
     * Export graduated students to PDF.
     */
    public function exportPdfLulus(Request $request)
    {
        $search = $request->get('search');
        $tahunLulus = $request->get('tahun_lulus');
        $jenisKelamin = $request->get('jenis_kelamin');

        $siswaList = Siswa::lulus()
            ->when($search, function ($q, $search) {
                return $q->where(function ($sub) use ($search) {
                    $sub->where('nisn', 'like', "%{$search}%")
                        ->orWhere('nama_siswa', 'like', "%{$search}%")
                        ->orWhere('alamat', 'like', "%{$search}%");
                });
            })
            ->when($tahunLulus, function ($q, $tahunLulus) {
                return $q->where('tahun_lulus', $tahunLulus);
            })
            ->when($jenisKelamin, function ($q, $jenisKelamin) {
                return $q->where('jenis_kelamin', $jenisKelamin);
            })
            ->orderBy('tahun_lulus', 'desc')
            ->orderBy('nama_siswa', 'asc')
            ->get();

        $pdf = Pdf::loadView('admin.siswa.lulus_pdf', compact(
            'siswaList',
            'search',
            'tahunLulus',
            'jenisKelamin'
        ))->setPaper('a4', 'portrait');

        $filename = 'Daftar_Siswa_Lulus' . ($tahunLulus ? '_' . $tahunLulus : '') . '.pdf';

        return $pdf->download($filename);
    }
}
