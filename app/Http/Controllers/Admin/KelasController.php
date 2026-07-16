<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\User;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $kelas = Kelas::with('waliKelas', 'mataPelajaran')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('nama_kelas', 'like', "%{$search}%")
                        ->orWhere('id_kelas', 'like', "%{$search}%")
                        ->orWhereHas('waliKelas', function($wq) use ($search) {
                            $wq->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy('nama_kelas')
            ->paginate(10)
            ->appends(['search' => $search]);
        
        return view('admin.kelas.index', compact('kelas', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $waliKelas = User::where('level', 'Wali Kelas')->get();
        $mataPelajaran = MataPelajaran::orderBy('nama_mapel')->get();
        return view('admin.kelas.create', compact('waliKelas', 'mataPelajaran'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_kelas' => 'required|string|max:50|unique:tb_kelas,id_kelas',
            'nama_kelas' => 'required|string|max:255',
            'id_wali_kelas' => 'nullable|exists:users,id',
            'kapasitas' => 'nullable|integer|min:0',
        ], [
            'id_kelas.required' => 'ID Kelas wajib diisi.',
            'id_kelas.unique' => 'ID Kelas sudah digunakan.',
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
            'id_wali_kelas.exists' => 'Wali kelas tidak valid.',
            'kapasitas.integer' => 'Kapasitas harus berupa angka.',
            'kapasitas.min' => 'Kapasitas minimal 0.',
        ]);

        $kelas = Kelas::create([
            'id_kelas' => $validated['id_kelas'],
            'nama_kelas' => $validated['nama_kelas'],
            'id_wali_kelas' => $validated['id_wali_kelas'] ?? null,
            'kapasitas' => $validated['kapasitas'] ?? 0,
        ]);

        $this->syncAllMataPelajaran($kelas);

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Kelas berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Kelas $kela)
    {
        $kela->load('waliKelas', 'siswa', 'mataPelajaran');
        return view('admin.kelas.show', compact('kela'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kelas $kela)
    {
        $waliKelas = User::where('level', 'Wali Kelas')->get();
        $mataPelajaran = MataPelajaran::orderBy('nama_mapel')->get();
        return view('admin.kelas.edit', compact('kela', 'waliKelas', 'mataPelajaran'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kelas $kela)
    {
        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'id_wali_kelas' => 'nullable|exists:users,id',
            'kapasitas' => 'nullable|integer|min:0',
        ], [
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
            'id_wali_kelas.exists' => 'Wali kelas tidak valid.',
            'kapasitas.integer' => 'Kapasitas harus berupa angka.',
            'kapasitas.min' => 'Kapasitas minimal 0.',
        ]);

        $kela->update([
            'nama_kelas' => $validated['nama_kelas'],
            'id_wali_kelas' => $validated['id_wali_kelas'] ?? null,
            'kapasitas' => $validated['kapasitas'] ?? 0,
        ]);

        $this->syncAllMataPelajaran($kela);

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Kelas berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kelas $kela)
    {
        $kela->delete();

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Kelas berhasil dihapus.');
    }

    private function syncAllMataPelajaran(Kelas $kelas): void
    {
        $kelas->mataPelajaran()->sync(MataPelajaran::pluck('id_mapel')->all());
    }
}
