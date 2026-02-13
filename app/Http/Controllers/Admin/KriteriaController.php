<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kriteria;
use Illuminate\Http\Request;

class KriteriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $kriteria = Kriteria::query()
            ->withCount('subKriteria')
            ->when($search, function ($query, $search) {
                return $query->where('kode_kriteria', 'like', "%{$search}%")
                    ->orWhere('nama_kriteria', 'like', "%{$search}%")
                    ->orWhere('jenis_kriteria', 'like', "%{$search}%");
            })
            ->orderBy('kode_kriteria')
            ->paginate(10)
            ->appends(['search' => $search]);
        
        return view('admin.kriteria.index', compact('kriteria', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.kriteria.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_kriteria' => 'required|string|max:10|unique:tb_kriteria,kode_kriteria',
            'nama_kriteria' => 'required|string|max:255',
            'jenis_kriteria' => 'required|in:Benefit,Cost',
            'bobot' => 'required|numeric|min:0|max:100'
        ], [
            'kode_kriteria.required' => 'Kode kriteria wajib diisi.',
            'kode_kriteria.unique' => 'Kode kriteria sudah digunakan.',
            'nama_kriteria.required' => 'Nama kriteria wajib diisi.',
            'jenis_kriteria.required' => 'Jenis kriteria wajib dipilih.',
            'jenis_kriteria.in' => 'Jenis kriteria harus Benefit atau Cost.',
            'bobot.required' => 'Bobot wajib diisi.',
            'bobot.numeric' => 'Bobot harus berupa angka.',
            'bobot.min' => 'Bobot minimal 0.',
            'bobot.max' => 'Bobot maksimal 100.'
        ]);

        Kriteria::create($validated);

        return redirect()->route('admin.kriteria.index')
            ->with('success', 'Kriteria berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Kriteria $kriterium)
    {
        $kriterium->load('subKriteria', 'penilaian');
        return view('admin.kriteria.show', compact('kriterium'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kriteria $kriterium)
    {
        return view('admin.kriteria.edit', compact('kriterium'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kriteria $kriterium)
    {
        $validated = $request->validate([
            'kode_kriteria' => 'required|string|max:10|unique:tb_kriteria,kode_kriteria,' . $kriterium->id_kriteria . ',id_kriteria',
            'nama_kriteria' => 'required|string|max:255',
            'jenis_kriteria' => 'required|in:Benefit,Cost',
            'bobot' => 'required|numeric|min:0|max:100'
        ], [
            'kode_kriteria.required' => 'Kode kriteria wajib diisi.',
            'kode_kriteria.unique' => 'Kode kriteria sudah digunakan.',
            'nama_kriteria.required' => 'Nama kriteria wajib diisi.',
            'jenis_kriteria.required' => 'Jenis kriteria wajib dipilih.',
            'jenis_kriteria.in' => 'Jenis kriteria harus Benefit atau Cost.',
            'bobot.required' => 'Bobot wajib diisi.',
            'bobot.numeric' => 'Bobot harus berupa angka.',
            'bobot.min' => 'Bobot minimal 0.',
            'bobot.max' => 'Bobot maksimal 100.'
        ]);

        $kriterium->update($validated);

        return redirect()->route('admin.kriteria.index')
            ->with('success', 'Kriteria berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kriteria $kriterium)
    {
        $kriterium->delete();

        return redirect()->route('admin.kriteria.index')
            ->with('success', 'Kriteria berhasil dihapus.');
    }
}
