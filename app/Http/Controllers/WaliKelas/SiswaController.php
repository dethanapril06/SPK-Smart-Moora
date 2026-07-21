<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
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

        $siswa = Siswa::with(['kelas', 'tahunAjaran'])
            ->where('id_kelas', $kelas->id_kelas)
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('nisn', 'like', "%{$search}%")
                        ->orWhere('nama_siswa', 'like', "%{$search}%")
                        ->orWhere('alamat', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends(['search' => $search]);

        return view('walikelas.siswa.index', compact('siswa', 'search', 'kelas'));
    }

    public function create()
    {
        $kelas = $this->getKelas();
        $tahunAjaran = TahunAjaran::representatives()->orderBy('tahun_ajaran', 'desc')->get();

        return view('walikelas.siswa.create', compact('kelas', 'tahunAjaran'));
    }

    public function store(Request $request)
    {
        $kelas = $this->getKelas();

        $validated = $request->validate([
            'nisn' => 'required|string|max:20|unique:tb_siswa,nisn',
            'nama_siswa' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'nullable|string',
            'id_ta' => 'required|exists:tb_tahun_ajaran,id_ta'
        ], [
            'nisn.required' => 'NISN wajib diisi.',
            'nisn.unique' => 'NISN sudah terdaftar.',
            'nama_siswa.required' => 'Nama siswa wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => 'Jenis kelamin tidak valid.',
            'id_ta.required' => 'Tahun ajaran wajib dipilih.',
            'id_ta.exists' => 'Tahun ajaran tidak valid.'
        ]);

        $validated['id_kelas'] = $kelas->id_kelas;

        Siswa::create($validated);

        return redirect()->route('walikelas.siswa.index')
            ->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function show(Siswa $siswa)
    {
        $kelas = $this->getKelas();
        abort_if($siswa->id_kelas !== $kelas->id_kelas, 403, 'Siswa bukan anggota kelas Anda.');

        $siswa->load('kelas', 'tahunAjaran', 'penilaian', 'riwayatPelanggaran');
        return view('walikelas.siswa.show', compact('siswa'));
    }

    public function edit(Siswa $siswa)
    {
        $kelas = $this->getKelas();
        abort_if($siswa->id_kelas !== $kelas->id_kelas, 403, 'Siswa bukan anggota kelas Anda.');

        $tahunAjaran = TahunAjaran::representatives()->orderBy('tahun_ajaran', 'desc')->get();

        return view('walikelas.siswa.edit', compact('siswa', 'kelas', 'tahunAjaran'));
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
            'id_ta' => 'required|exists:tb_tahun_ajaran,id_ta'
        ], [
            'nisn.required' => 'NISN wajib diisi.',
            'nisn.unique' => 'NISN sudah terdaftar.',
            'nama_siswa.required' => 'Nama siswa wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => 'Jenis kelamin tidak valid.',
            'id_ta.required' => 'Tahun ajaran wajib dipilih.',
            'id_ta.exists' => 'Tahun ajaran tidak valid.'
        ]);

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
}
