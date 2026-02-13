<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubKriteria;
use App\Models\Kriteria;
use Illuminate\Http\Request;

class SubKriteriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $filterKriteria = $request->get('kriteria');
        
        $subkriteria = SubKriteria::with('kriteria')
            ->when($search, function ($query, $search) {
                return $query->where('nama_subkriteria', 'like', "%{$search}%")
                    ->orWhereHas('kriteria', function($q) use ($search) {
                        $q->where('nama_kriteria', 'like', "%{$search}%")
                          ->orWhere('kode_kriteria', 'like', "%{$search}%");
                    });
            })
            ->when($filterKriteria, function ($query, $filterKriteria) {
                return $query->where('id_kriteria', $filterKriteria);
            })
            ->orderBy('id_kriteria')
            ->orderBy('bobot_subkriteria', 'desc')
            ->paginate(10)
            ->appends(['search' => $search, 'kriteria' => $filterKriteria]);
        
        $kriteriaList = Kriteria::orderBy('kode_kriteria')->get();
        
        return view('admin.subkriteria.index', compact('subkriteria', 'search', 'filterKriteria', 'kriteriaList'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kriteria = Kriteria::orderBy('kode_kriteria')->get();
        return view('admin.subkriteria.create', compact('kriteria'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_kriteria' => 'required|exists:tb_kriteria,id_kriteria',
            'nama_subkriteria' => 'required|string|max:255',
            'nilai_awal' => 'required|numeric|min:0',
            'nilai_akhir' => 'required|numeric|min:0',
            'bobot_subkriteria' => 'required|numeric|min:0'
        ], [
            'id_kriteria.required' => 'Kriteria wajib dipilih.',
            'id_kriteria.exists' => 'Kriteria tidak valid.',
            'nama_subkriteria.required' => 'Nama sub kriteria wajib diisi.',
            'nilai_awal.required' => 'Nilai awal wajib diisi.',
            'nilai_awal.numeric' => 'Nilai awal harus berupa angka.',
            'nilai_akhir.required' => 'Nilai akhir wajib diisi.',
            'nilai_akhir.numeric' => 'Nilai akhir harus berupa angka.',
            'bobot_subkriteria.required' => 'Bobot sub kriteria wajib diisi.',
            'bobot_subkriteria.numeric' => 'Bobot sub kriteria harus berupa angka.'
        ]);

        SubKriteria::create($validated);

        return redirect()->route('admin.subkriteria.index')
            ->with('success', 'Sub kriteria berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SubKriteria $subkriterium)
    {
        $kriteria = Kriteria::orderBy('kode_kriteria')->get();
        return view('admin.subkriteria.edit', compact('subkriterium', 'kriteria'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SubKriteria $subkriterium)
    {
        $validated = $request->validate([
            'id_kriteria' => 'required|exists:tb_kriteria,id_kriteria',
            'nama_subkriteria' => 'required|string|max:255',
            'nilai_awal' => 'required|numeric|min:0',
            'nilai_akhir' => 'required|numeric|min:0',
            'bobot_subkriteria' => 'required|numeric|min:0'
        ], [
            'id_kriteria.required' => 'Kriteria wajib dipilih.',
            'id_kriteria.exists' => 'Kriteria tidak valid.',
            'nama_subkriteria.required' => 'Nama sub kriteria wajib diisi.',
            'nilai_awal.required' => 'Nilai awal wajib diisi.',
            'nilai_awal.numeric' => 'Nilai awal harus berupa angka.',
            'nilai_akhir.required' => 'Nilai akhir wajib diisi.',
            'nilai_akhir.numeric' => 'Nilai akhir harus berupa angka.',
            'bobot_subkriteria.required' => 'Bobot sub kriteria wajib diisi.',
            'bobot_subkriteria.numeric' => 'Bobot sub kriteria harus berupa angka.'
        ]);

        $subkriterium->update($validated);

        return redirect()->route('admin.subkriteria.index')
            ->with('success', 'Sub kriteria berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubKriteria $subkriterium)
    {
        $subkriterium->delete();

        return redirect()->route('admin.subkriteria.index')
            ->with('success', 'Sub kriteria berhasil dihapus.');
    }
}
