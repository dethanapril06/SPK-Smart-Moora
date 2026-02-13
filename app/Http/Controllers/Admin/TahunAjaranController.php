<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class TahunAjaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $tahunAjaran = TahunAjaran::query()
            ->when($search, function ($query, $search) {
                return $query->where('tahun_ajaran', 'like', "%{$search}%")
                    ->orWhere('semester', 'like', "%{$search}%");
            })
            ->orderBy('is_active', 'desc')
            ->orderBy('tahun_ajaran', 'desc')
            ->paginate(10)
            ->appends(['search' => $search]);
        
        return view('admin.tahunajaran.index', compact('tahunAjaran', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.tahunajaran.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tahun_ajaran' => 'required|string|max:20|unique:tb_tahun_ajaran,tahun_ajaran',
            'semester' => 'required|in:Ganjil,Genap',
            'is_active' => 'nullable|boolean'
        ], [
            'tahun_ajaran.required' => 'Tahun ajaran wajib diisi.',
            'tahun_ajaran.unique' => 'Tahun ajaran sudah ada.',
            'semester.required' => 'Semester wajib dipilih.',
            'semester.in' => 'Semester harus Ganjil atau Genap.'
        ]);

        // If is_active is set to true, deactivate other tahun ajaran
        if ($request->boolean('is_active')) {
            TahunAjaran::query()->update(['is_active' => false]);
        }

        $validated['is_active'] = $request->boolean('is_active');

        TahunAjaran::create($validated);

        return redirect()->route('admin.tahunajaran.index')
            ->with('success', 'Tahun ajaran berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TahunAjaran $tahunajaran)
    {
        $tahunajaran->load('siswa', 'penilaian');
        return view('admin.tahunajaran.show', compact('tahunajaran'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TahunAjaran $tahunajaran)
    {
        return view('admin.tahunajaran.edit', compact('tahunajaran'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TahunAjaran $tahunajaran)
    {
        $validated = $request->validate([
            'tahun_ajaran' => 'required|string|max:20|unique:tb_tahun_ajaran,tahun_ajaran,' . $tahunajaran->id_ta . ',id_ta',
            'semester' => 'required|in:Ganjil,Genap',
            'is_active' => 'nullable|boolean'
        ], [
            'tahun_ajaran.required' => 'Tahun ajaran wajib diisi.',
            'tahun_ajaran.unique' => 'Tahun ajaran sudah ada.',
            'semester.required' => 'Semester wajib dipilih.',
            'semester.in' => 'Semester harus Ganjil atau Genap.'
        ]);

        // If is_active is set to true, deactivate other tahun ajaran
        if ($request->boolean('is_active')) {
            TahunAjaran::where('id_ta', '!=', $tahunajaran->id_ta)->update(['is_active' => false]);
        }

        $validated['is_active'] = $request->boolean('is_active');

        $tahunajaran->update($validated);

        return redirect()->route('admin.tahunajaran.index')
            ->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TahunAjaran $tahunajaran)
    {
        $tahunajaran->delete();

        return redirect()->route('admin.tahunajaran.index')
            ->with('success', 'Tahun ajaran berhasil dihapus.');
    }

    /**
     * Set tahun ajaran as active
     */
    public function setActive(TahunAjaran $tahunajaran)
    {
        // Deactivate all tahun ajaran
        TahunAjaran::query()->update(['is_active' => false]);
        
        // Activate this tahun ajaran
        $tahunajaran->update(['is_active' => true]);

        return redirect()->route('admin.tahunajaran.index')
            ->with('success', 'Tahun ajaran berhasil diaktifkan.');
    }
}
