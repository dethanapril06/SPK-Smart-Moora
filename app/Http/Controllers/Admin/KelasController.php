<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class KelasController extends Controller
{
    public function naikKelas(Request $request)
    {
        $sourceTahunAjaran = $request->get('tahun_ajaran');
        $sourceKelasId = $request->get('id_kelas');

        $tahunAjaranList = TahunAjaran::query()
            ->select('tahun_ajaran')
            ->distinct()
            ->orderByDesc('tahun_ajaran')
            ->pluck('tahun_ajaran');

        $kelasList = Kelas::where('id_kelas', 'like', 'X.%')
            ->orderBy('nama_kelas')
            ->get();

        $targetKelasList = Kelas::where('id_kelas', 'like', 'XI.%')
            ->orderBy('nama_kelas')
            ->get();

        $sourceTahunAjaranRecord = $sourceTahunAjaran
            ? $this->resolveTahunAjaranByYear($sourceTahunAjaran)
            : null;
        $targetTahunAjaranLabel = $sourceTahunAjaranRecord
            ? $this->getNextTahunAjaranLabel($sourceTahunAjaranRecord->tahun_ajaran)
            : null;

        $selectedKelas = null;
        $siswaList = collect();

        if ($sourceTahunAjaranRecord && $sourceKelasId) {
            $selectedKelas = Kelas::find($sourceKelasId);

            if ($selectedKelas && $this->isKelasX($selectedKelas->id_kelas)) {
                $siswaList = Siswa::where('id_ta', $sourceTahunAjaranRecord->id_ta)
                    ->where('id_kelas', $sourceKelasId)
                    ->orderBy('nama_siswa')
                    ->get();
            }
        }

        return view('admin.kelas.naikkelas', compact(
            'tahunAjaranList',
            'kelasList',
            'targetKelasList',
            'sourceTahunAjaranRecord',
            'targetTahunAjaranLabel',
            'selectedKelas',
            'siswaList',
            'sourceTahunAjaran',
            'sourceKelasId'
        ));
    }

    public function prosesNaikKelas(Request $request)
    {
        $validated = $request->validate([
            'tahun_ajaran' => 'required|string|max:50',
            'id_kelas' => 'required|exists:tb_kelas,id_kelas',
            'tujuan' => 'required|array',
            'tujuan.*' => 'required|exists:tb_kelas,id_kelas',
        ]);

        $sourceTahunAjaran = $this->resolveTahunAjaranByYear($validated['tahun_ajaran']);
        if (!$sourceTahunAjaran) {
            return back()->with('error', 'Tahun ajaran sumber tidak ditemukan.');
        }

        $targetTahunAjaran = $this->resolveNextTahunAjaran($sourceTahunAjaran);

        $sourceKelas = Kelas::findOrFail($validated['id_kelas']);
        if (!$this->isKelasX($sourceKelas->id_kelas)) {
            return back()->with('error', 'Fitur naik kelas ini hanya untuk kelas X.');
        }

        $siswaList = Siswa::where('id_ta', $sourceTahunAjaran->id_ta)
            ->where('id_kelas', $validated['id_kelas'])
            ->get();

        if ($siswaList->isEmpty()) {
            return back()->with('error', 'Tidak ada siswa pada kelas sumber yang dipilih.');
        }

        $selectedTujuan = collect($validated['tujuan']);
        $targetKelasIds = $selectedTujuan->values()->unique()->all();
        $targetKelasMap = Kelas::whereIn('id_kelas', $targetKelasIds)->get()->keyBy('id_kelas');

        $assignedCounts = $selectedTujuan->countBy();

        foreach ($assignedCounts as $targetKelasId => $count) {
            $targetKelas = $targetKelasMap->get($targetKelasId);
            if (!$targetKelas) {
                throw ValidationException::withMessages([
                    'tujuan' => "Kelas tujuan {$targetKelasId} tidak valid.",
                ]);
            }

            $currentTargetCount = Siswa::where('id_ta', $targetTahunAjaran->id_ta)
                ->where('id_kelas', $targetKelasId)
                ->count();

            if ($currentTargetCount + $count > (int) $targetKelas->kapasitas) {
                throw ValidationException::withMessages([
                    'tujuan' => "Kapasitas {$targetKelas->nama_kelas} tidak cukup untuk menampung {$count} siswa tambahan.",
                ]);
            }
        }

        DB::transaction(function () use ($siswaList, $validated, $targetTahunAjaran) {
            foreach ($siswaList as $siswa) {
                $targetKelasId = $validated['tujuan'][$siswa->id_siswa] ?? null;

                if (!$targetKelasId) {
                    continue;
                }

                $siswa->update([
                    'id_kelas' => $targetKelasId,
                    'id_ta' => $targetTahunAjaran->id_ta,
                ]);
            }
        });

        return redirect()->route('admin.kelas.naik-kelas.index', [
            'tahun_ajaran' => $validated['tahun_ajaran'],
            'id_kelas' => $validated['id_kelas'],
        ])->with('success', 'Naik kelas berhasil diproses untuk siswa kelas X yang dipilih.');
    }

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

    private function resolveTahunAjaranByYear(string $tahunAjaran): ?TahunAjaran
    {
        return TahunAjaran::where('tahun_ajaran', $tahunAjaran)
            ->orderByRaw("CASE WHEN semester = 'Genap' THEN 1 ELSE 2 END")
            ->orderByDesc('id_ta')
            ->first();
    }

    private function resolveNextTahunAjaran(TahunAjaran $sourceTahunAjaran): TahunAjaran
    {
        $nextYearLabel = $this->getNextTahunAjaranLabel($sourceTahunAjaran->tahun_ajaran);

        $tahunAjaran = TahunAjaran::firstOrCreate(
            [
                'tahun_ajaran' => $nextYearLabel,
                'semester' => 'Ganjil',
            ],
            [
                'is_active' => false,
            ]
        );

        $tahunAjaran->ensureDefaultSemesters(false);

        return $tahunAjaran;
    }

    private function getNextTahunAjaranLabel(string $tahunAjaran): string
    {
        [$startYear, $endYear] = $this->parseTahunAjaran($tahunAjaran);

        return ($startYear + 1) . '/' . ($endYear + 1);
    }

    private function parseTahunAjaran(string $tahunAjaran): array
    {
        if (!preg_match('/^(\d{4})\/(\d{4})$/', $tahunAjaran, $matches)) {
            throw ValidationException::withMessages([
                'tahun_ajaran' => 'Format tahun ajaran tidak valid.',
            ]);
        }

        return [(int) $matches[1], (int) $matches[2]];
    }

    private function isKelasX(string $idKelas): bool
    {
        return preg_match('/^X\.\d+$/', $idKelas) === 1;
    }
}
