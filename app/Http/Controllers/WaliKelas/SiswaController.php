<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\Semester;
use Illuminate\Http\Request;

class SiswaController extends Controller
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
        $search = $request->get('search');

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

        $siswa = Siswa::with(['kelas', 'tahunAjaran', 'semester'])
            ->where('id_kelas', $kelas->id_kelas)
            ->when($filterTA, function ($query, $filterTA) {
                return $query->where('tb_siswa.id_ta', $filterTA);
            })
            ->when($filterSemester, function ($query, $filterSemester) {
                return $query->forSemester($filterSemester);
            })
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('nisn', 'like', "%{$search}%")
                        ->orWhere('nama_siswa', 'like', "%{$search}%")
                        ->orWhere('alamat', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends([
                'search' => $search,
                'tahun_ajaran' => $filterTA,
                'semester' => $filterSemester,
            ]);

        $tahunAjaranList = TahunAjaran::representatives()->orderBy('tahun_ajaran', 'desc')->get();
        $semesterList = Semester::orderBy('id_semester')->get();

        return view('walikelas.siswa.index', compact(
            'siswa', 'search', 'kelas', 'tahunAjaranList', 'semesterList', 
            'filterTA', 'filterSemester', 'activeTA', 'activeSemester'
        ));
    }

    public function create()
    {
        $kelas = $this->getKelas();
        $tahunAjaran = TahunAjaran::representatives()->with('semesters')->orderBy('tahun_ajaran', 'desc')->get();
        $activeTA = TahunAjaran::where('is_active', true)->first();
        $activeSemester = $activeTA ? ($activeTA->activeSemester ?: $activeTA->semesters()->first()) : null;
        $semesters = Semester::all();

        return view('walikelas.siswa.create', compact('kelas', 'tahunAjaran', 'semesters', 'activeTA', 'activeSemester'));
    }

    public function store(Request $request)
    {
        $kelas = $this->getKelas();

        $validated = $request->validate([
            'nisn' => 'required|string|max:20|unique:tb_siswa,nisn',
            'nama_siswa' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'nullable|string',
            'id_ta' => 'required|exists:tb_tahun_ajaran,id_ta',
            'id_semester' => 'nullable|exists:tb_semester,id_semester',
        ], [
            'nisn.required' => 'NISN wajib diisi.',
            'nisn.unique' => 'NISN sudah terdaftar.',
            'nama_siswa.required' => 'Nama siswa wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => 'Jenis kelamin tidak valid.',
            'id_ta.required' => 'Tahun ajaran wajib dipilih.',
            'id_ta.exists' => 'Tahun ajaran tidak valid.',
            'id_semester.exists' => 'Semester tidak valid.',
        ]);

        if (empty($validated['id_semester'])) {
            $defaultSemester = Semester::where('id_ta', $validated['id_ta'])->where('is_active', true)->first()
                ?? Semester::where('id_ta', $validated['id_ta'])->where('nama_semester', 'Ganjil')->first()
                ?? Semester::where('id_ta', $validated['id_ta'])->first();
            $validated['id_semester'] = $defaultSemester?->id_semester;
        }

        $validated['id_kelas'] = $kelas->id_kelas;

        Siswa::create($validated);

        return redirect()->route('walikelas.siswa.index')
            ->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function show(Siswa $siswa)
    {
        $kelas = $this->getKelas();
        abort_if($siswa->id_kelas !== $kelas->id_kelas, 403, 'Siswa bukan anggota kelas Anda.');

        $siswa->load('kelas', 'tahunAjaran', 'semester', 'penilaian', 'riwayatPelanggaran');
        return view('walikelas.siswa.show', compact('siswa'));
    }

    public function edit(Siswa $siswa)
    {
        $kelas = $this->getKelas();
        abort_if($siswa->id_kelas !== $kelas->id_kelas, 403, 'Siswa bukan anggota kelas Anda.');

        $tahunAjaran = TahunAjaran::representatives()->with('semesters')->orderBy('tahun_ajaran', 'desc')->get();
        $semesters = Semester::all();

        return view('walikelas.siswa.edit', compact('siswa', 'kelas', 'tahunAjaran', 'semesters'));
    }

    public function update(Request $request, Siswa $siswa)
    {
        $kelas = $this->getKelas();
        abort_if($siswa->id_kelas !== $kelas->id_kelas, 403, 'Siswa bukan anggota kelas Anda.');

        $validated = $request->validate([
            'nisn' => 'required|string|max:20|unique:tb_siswa,nisn,' . $siswa->id_siswa . ',id_siswa',
            'nama_siswa' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'nullable|string',
            'id_ta' => 'required|exists:tb_tahun_ajaran,id_ta',
            'id_semester' => 'nullable|exists:tb_semester,id_semester',
        ], [
            'nisn.required' => 'NISN wajib diisi.',
            'nisn.unique' => 'NISN sudah terdaftar.',
            'nama_siswa.required' => 'Nama siswa wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => 'Jenis kelamin tidak valid.',
            'id_ta.required' => 'Tahun ajaran wajib dipilih.',
            'id_ta.exists' => 'Tahun ajaran tidak valid.',
            'id_semester.exists' => 'Semester tidak valid.',
        ]);

        if (empty($validated['id_semester'])) {
            $defaultSemester = Semester::where('id_ta', $validated['id_ta'])->where('is_active', true)->first()
                ?? Semester::where('id_ta', $validated['id_ta'])->where('nama_semester', 'Ganjil')->first()
                ?? Semester::where('id_ta', $validated['id_ta'])->first();
            $validated['id_semester'] = $defaultSemester?->id_semester;
        }

        $validated['id_kelas'] = $kelas->id_kelas;
        $siswa->update($validated);

        return redirect()->route('walikelas.siswa.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Siswa $siswa)
    {
        $kelas = $this->getKelas();
        abort_if($siswa->id_kelas !== $kelas->id_kelas, 403, 'Siswa bukan anggota kelas Anda.');

        $siswa->delete();

        return redirect()->route('walikelas.siswa.index')
            ->with('success', 'Data siswa berhasil dihapus.');
    }

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

        return view('walikelas.siswa.lulus', compact(
            'siswa',
            'search',
            'tahunLulus',
            'jenisKelamin',
            'tahunLulusList'
        ));
    }

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

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.siswa.lulus_pdf', compact(
            'siswaList',
            'search',
            'tahunLulus',
            'jenisKelamin'
        ))->setPaper('a4', 'portrait');

        $filename = 'Daftar_Siswa_Lulus' . ($tahunLulus ? '_' . $tahunLulus : '') . '.pdf';

        return $pdf->download($filename);
    }
}
