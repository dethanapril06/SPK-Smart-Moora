<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;

class MataPelajaranController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $mataPelajaran = MataPelajaran::query()
            ->when($search, function ($query, $search) {
                return $query->where('kode_mapel', 'like', "%{$search}%")
                    ->orWhere('nama_mapel', 'like', "%{$search}%");
            })
            ->orderBy('kode_mapel')
            ->paginate(10)
            ->appends(['search' => $search]);

        return view('admin.matapelajaran.index', compact('mataPelajaran', 'search'));
    }

    public function create()
    {
        return view('admin.matapelajaran.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_mapel' => 'required|string|max:10|unique:tb_mata_pelajaran,kode_mapel',
            'nama_mapel' => 'required|string|max:100',
        ], [
            'kode_mapel.required' => 'Kode mata pelajaran wajib diisi.',
            'kode_mapel.unique' => 'Kode mata pelajaran sudah digunakan.',
            'nama_mapel.required' => 'Nama mata pelajaran wajib diisi.',
        ]);

        MataPelajaran::create($validated);

        return redirect()->route('admin.matapelajaran.index')
            ->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function edit(MataPelajaran $matapelajaran)
    {
        return view('admin.matapelajaran.edit', compact('matapelajaran'));
    }

    public function update(Request $request, MataPelajaran $matapelajaran)
    {
        $validated = $request->validate([
            'kode_mapel' => 'required|string|max:10|unique:tb_mata_pelajaran,kode_mapel,' . $matapelajaran->id_mapel . ',id_mapel',
            'nama_mapel' => 'required|string|max:100',
        ], [
            'kode_mapel.required' => 'Kode mata pelajaran wajib diisi.',
            'kode_mapel.unique' => 'Kode mata pelajaran sudah digunakan.',
            'nama_mapel.required' => 'Nama mata pelajaran wajib diisi.',
        ]);

        $matapelajaran->update($validated);

        return redirect()->route('admin.matapelajaran.index')
            ->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroy(MataPelajaran $matapelajaran)
    {
        $matapelajaran->delete();

        return redirect()->route('admin.matapelajaran.index')
            ->with('success', 'Mata pelajaran berhasil dihapus.');
    }
}
