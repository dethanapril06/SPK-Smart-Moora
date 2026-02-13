<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
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
        
        $kelas = Kelas::with('waliKelas')
            ->when($search, function ($query, $search) {
                return $query->where('nama_kelas', 'like', "%{$search}%")
                    ->orWhere('id_kelas', 'like', "%{$search}%")
                    ->orWhereHas('waliKelas', function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->orderBy('created_at', 'desc')
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
        return view('admin.kelas.create', compact('waliKelas'));
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
            'kapasitas' => 'nullable|integer|min:0'
        ], [
            'id_kelas.required' => 'ID Kelas wajib diisi.',
            'id_kelas.unique' => 'ID Kelas sudah digunakan.',
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
            'id_wali_kelas.exists' => 'Wali kelas tidak valid.',
            'kapasitas.integer' => 'Kapasitas harus berupa angka.',
            'kapasitas.min' => 'Kapasitas minimal 0.'
        ]);

        Kelas::create($validated);

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Kelas berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Kelas $kela)
    {
        $kela->load('waliKelas', 'siswa');
        return view('admin.kelas.show', compact('kela'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kelas $kela)
    {
        $waliKelas = User::where('level', 'Wali Kelas')->get();
        return view('admin.kelas.edit', compact('kela', 'waliKelas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kelas $kela)
    {
        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'id_wali_kelas' => 'nullable|exists:users,id',
            'kapasitas' => 'nullable|integer|min:0'
        ], [
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
            'id_wali_kelas.exists' => 'Wali kelas tidak valid.',
            'kapasitas.integer' => 'Kapasitas harus berupa angka.',
            'kapasitas.min' => 'Kapasitas minimal 0.'
        ]);

        $kela->update($validated);

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
}
