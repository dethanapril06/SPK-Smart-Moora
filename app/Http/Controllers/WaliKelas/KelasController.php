<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::with(['waliKelas', 'siswa'])
            ->where('id_wali_kelas', auth()->id())
            ->first();

        if (!$kelas) {
            return view('walikelas.kelas.index', ['kelas' => null]);
        }

        return view('walikelas.kelas.index', compact('kelas'));
    }

    public function show(Kelas $kela)
    {
        abort_if($kela->id_wali_kelas != auth()->id(), 403, 'Bukan kelas Anda.');

        $kela->load('waliKelas', 'siswa');
        return view('walikelas.kelas.show', compact('kela'));
    }

    // ─── Naik Kelas ─────────────────────────────────────────────────────────────

    public function naikKelas(Request $request)
    {
        $myKelas = $this->getMyKelas();

        // Kelas wali harus X atau XI agar bisa naik kelas
        if (!$this->isPromotableKelas($myKelas->id_kelas)) {
            return redirect()->route('walikelas.kelas.index')
                ->with('error', 'Fitur Naik Kelas hanya tersedia untuk wali kelas X dan XI. Untuk kelas XII, gunakan fitur Kelulusan.');
        }

        $sourceTahunAjaran = $request->get('tahun_ajaran');

        $tahunAjaranList = TahunAjaran::query()
            ->select('tahun_ajaran')
            ->distinct()
            ->orderByDesc('tahun_ajaran')
            ->pluck('tahun_ajaran');

        $activeTahunAjaran = TahunAjaran::where('is_active', true)->value('tahun_ajaran');

        // Target kelas list sesuai level kelas wali
        if (str_starts_with($myKelas->id_kelas, 'X.')) {
            $targetKelasList = Kelas::where('id_kelas', 'like', 'XI.%')->orderBy('nama_kelas')->get();
            $defaultTargetKelasId = null;
        } else {
            // XI → XII, auto-pilih kelas XII yang sesuai
            $targetKelasList = Kelas::where('id_kelas', 'like', 'XII.%')->orderBy('nama_kelas')->get();
            $possibleDefault = str_replace('XI.', 'XII.', $myKelas->id_kelas);
            $defaultTargetKelasId = $targetKelasList->contains('id_kelas', $possibleDefault)
                ? $possibleDefault : null;
        }

        $sourceTahunAjaranRecord = $sourceTahunAjaran
            ? $this->resolveTahunAjaranByYear($sourceTahunAjaran, $myKelas->id_kelas)
            : null;

        $targetTahunAjaranLabel = $sourceTahunAjaranRecord
            ? $this->getNextTahunAjaranLabel($sourceTahunAjaranRecord->tahun_ajaran)
            : null;

        $siswaList = collect();
        if ($sourceTahunAjaranRecord) {
            $siswaList = Siswa::where('id_ta', $sourceTahunAjaranRecord->id_ta)
                ->where('id_kelas', $myKelas->id_kelas)
                ->orderBy('nama_siswa')
                ->get();
        }

        return view('walikelas.kelas.naikkelas', compact(
            'myKelas',
            'tahunAjaranList',
            'activeTahunAjaran',
            'targetKelasList',
            'defaultTargetKelasId',
            'sourceTahunAjaranRecord',
            'targetTahunAjaranLabel',
            'siswaList',
            'sourceTahunAjaran'
        ));
    }

    public function prosesNaikKelas(Request $request)
    {
        $myKelas = $this->getMyKelas();

        if (!$this->isPromotableKelas($myKelas->id_kelas)) {
            return redirect()->route('walikelas.kelas.index')
                ->with('error', 'Fitur Naik Kelas hanya tersedia untuk wali kelas X dan XI.');
        }

        $validated = $request->validate([
            'tahun_ajaran' => 'required|string|max:50',
            'tujuan'       => 'required|array',
            'tujuan.*'     => 'required|exists:tb_kelas,id_kelas',
        ]);

        $sourceTahunAjaran = $this->resolveTahunAjaranByYear($validated['tahun_ajaran'], $myKelas->id_kelas);
        if (!$sourceTahunAjaran) {
            return back()->with('error', 'Tahun ajaran sumber tidak ditemukan.');
        }

        $targetTahunAjaran = $this->resolveNextTahunAjaran($sourceTahunAjaran);

        $siswaList = Siswa::where('id_ta', $sourceTahunAjaran->id_ta)
            ->where('id_kelas', $myKelas->id_kelas)
            ->get();

        if ($siswaList->isEmpty()) {
            return back()->with('error', 'Tidak ada siswa pada kelas Anda untuk tahun ajaran tersebut.');
        }

        $selectedTujuan = collect($validated['tujuan']);
        $targetKelasIds = $selectedTujuan->values()->unique()->all();
        $targetKelasMap = Kelas::whereIn('id_kelas', $targetKelasIds)->get()->keyBy('id_kelas');
        $assignedCounts = $selectedTujuan->countBy();

        foreach ($assignedCounts as $targetKelasId => $count) {
            $targetKelas = $targetKelasMap->get($targetKelasId);
            if (!$targetKelas) {
                return back()->with('error', "Kelas tujuan {$targetKelasId} tidak ditemukan.");
            }

            $unpromotedCount = Siswa::where('id_ta', $sourceTahunAjaran->id_ta)
                ->where('id_kelas', $targetKelasId)
                ->count();

            $alreadyPromotedCount = Siswa::where('id_ta', $targetTahunAjaran->id_ta)
                ->where('id_kelas', $targetKelasId)
                ->count();

            $totalOccupants = $unpromotedCount + $alreadyPromotedCount;
            $kapasitas = (int) $targetKelas->kapasitas;

            if ($kapasitas > 0 && ($totalOccupants + $count > $kapasitas)) {
                $detailMsg = $unpromotedCount > 0
                    ? "Siswa penghuni {$targetKelas->nama_kelas} di tahun ajaran ini sebanyak {$unpromotedCount} siswa BELUM dinaikkan ke kelas tingkat atas (XII). Silakan naikkan terlebih dahulu siswa {$targetKelas->nama_kelas} ke kelas XII!"
                    : "Kelas {$targetKelas->nama_kelas} untuk tahun ajaran baru sudah terisi {$alreadyPromotedCount} siswa, dan Anda mencoba menambahkan {$count} siswa lagi.";

                return back()->with('error', "Kapasitas {$targetKelas->nama_kelas} tidak mencukupi! Kapasitas maksimal adalah {$kapasitas} siswa. {$detailMsg}");
            }
        }

        DB::transaction(function () use ($siswaList, $validated, $targetTahunAjaran) {
            foreach ($siswaList as $siswa) {
                $targetKelasId = $validated['tujuan'][$siswa->id_siswa] ?? null;
                if (!$targetKelasId) continue;
                $siswa->update([
                    'id_kelas' => $targetKelasId,
                    'id_ta'    => $targetTahunAjaran->id_ta,
                ]);
            }
        });

        return redirect()->route('walikelas.kelas.naik-kelas.index', [
            'tahun_ajaran' => $validated['tahun_ajaran'],
        ])->with('success', 'Naik kelas berhasil diproses untuk siswa kelas ' . $myKelas->nama_kelas . '.');
    }

    // ─── Kelulusan ───────────────────────────────────────────────────────────────

    public function kelulusan(Request $request)
    {
        $myKelas = $this->getMyKelas();

        if (!str_starts_with($myKelas->id_kelas, 'XII.')) {
            return redirect()->route('walikelas.kelas.index')
                ->with('error', 'Fitur Kelulusan hanya tersedia untuk wali kelas XII.');
        }

        $sourceTahunAjaran = $request->get('tahun_ajaran');

        $tahunAjaranList = TahunAjaran::query()
            ->select('tahun_ajaran')
            ->distinct()
            ->orderByDesc('tahun_ajaran')
            ->pluck('tahun_ajaran');

        $activeTahunAjaran = TahunAjaran::where('is_active', true)->value('tahun_ajaran');

        $sourceTahunAjaranRecord = $sourceTahunAjaran
            ? $this->resolveTahunAjaranByYear($sourceTahunAjaran, $myKelas->id_kelas)
            : null;

        $siswaList = collect();
        if ($sourceTahunAjaranRecord) {
            $siswaList = Siswa::aktif()
                ->where('id_ta', $sourceTahunAjaranRecord->id_ta)
                ->where('id_kelas', $myKelas->id_kelas)
                ->orderBy('nama_siswa')
                ->get();
        }

        return view('walikelas.kelas.kelulusan', compact(
            'myKelas',
            'tahunAjaranList',
            'activeTahunAjaran',
            'sourceTahunAjaranRecord',
            'siswaList',
            'sourceTahunAjaran'
        ));
    }

    public function prosesKelulusan(Request $request)
    {
        $myKelas = $this->getMyKelas();

        if (!str_starts_with($myKelas->id_kelas, 'XII.')) {
            return redirect()->route('walikelas.kelas.index')
                ->with('error', 'Fitur Kelulusan hanya tersedia untuk wali kelas XII.');
        }

        $validated = $request->validate([
            'tahun_ajaran' => 'required|string|max:50',
            'siswa_ids'    => 'required|array|min:1',
            'siswa_ids.*'  => 'required|exists:tb_siswa,id_siswa',
        ], [
            'siswa_ids.required' => 'Pilih minimal 1 siswa untuk diluluskan.',
            'siswa_ids.min'      => 'Pilih minimal 1 siswa untuk diluluskan.',
        ]);

        $tahunAjaranRecord = $this->resolveTahunAjaranByYear($validated['tahun_ajaran'], $myKelas->id_kelas);
        if (!$tahunAjaranRecord) {
            return back()->with('error', 'Tahun ajaran tidak ditemukan.');
        }

        [$startYear] = $this->parseTahunAjaran($validated['tahun_ajaran']);
        $tahunLulus = $startYear + 1;

        $count = DB::transaction(function () use ($validated, $tahunAjaranRecord, $myKelas, $tahunLulus) {
            return Siswa::aktif()
                ->where('id_ta', $tahunAjaranRecord->id_ta)
                ->where('id_kelas', $myKelas->id_kelas)
                ->whereIn('id_siswa', $validated['siswa_ids'])
                ->update([
                    'status'      => 'lulus',
                    'id_kelas'    => null,
                    'tahun_lulus' => $tahunLulus,
                ]);
        });

        return redirect()->route('walikelas.kelas.kelulusan.index', [
            'tahun_ajaran' => $validated['tahun_ajaran'],
        ])->with('success', "{$count} siswa berhasil diluluskan dari {$myKelas->nama_kelas} tahun {$tahunLulus}.");
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────────

    protected function getMyKelas(): Kelas
    {
        $kelas = Kelas::where('id_wali_kelas', auth()->id())->first();
        abort_if(!$kelas, 403, 'Anda belum ditugaskan sebagai wali kelas.');
        return $kelas;
    }

    private function isPromotableKelas(string $idKelas): bool
    {
        return preg_match('/^(X|XI)\./', $idKelas) === 1;
    }

    private function resolveTahunAjaranByYear(string $tahunAjaran, ?string $idKelas = null): ?TahunAjaran
    {
        $records = TahunAjaran::where('tahun_ajaran', $tahunAjaran)
            ->orderByRaw("CASE WHEN semester = 'Genap' THEN 1 ELSE 2 END")
            ->orderByDesc('id_ta')
            ->get();

        if ($records->isEmpty()) return null;

        if ($idKelas) {
            foreach ($records as $record) {
                if (Siswa::where('id_ta', $record->id_ta)->where('id_kelas', $idKelas)->exists()) {
                    return $record;
                }
            }
        }

        return $records->first();
    }

    private function resolveNextTahunAjaran(TahunAjaran $sourceTahunAjaran): TahunAjaran
    {
        $nextYearLabel = $this->getNextTahunAjaranLabel($sourceTahunAjaran->tahun_ajaran);
        $tahunAjaran = TahunAjaran::firstOrCreate(
            ['tahun_ajaran' => $nextYearLabel, 'semester' => 'Ganjil'],
            ['is_active' => false]
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
}
