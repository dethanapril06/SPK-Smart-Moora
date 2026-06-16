<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Exports\HasilPerangkinganSmartExport;
use App\Exports\HasilPerangkinganMooraExport;
use App\Exports\HasilFinalisExport;
use App\Models\HasilAkhir;
use App\Models\HasilFinalis;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    // ─── SMART ──────────────────────────────────────────────────────────────────

    public function exportPdfSmart(Request $request)
    {
        [$filterTA, $source, $filterKelas, $tahunAjaran, $userId, $sourceName] = $this->resolveParams($request);

        $hasilList = HasilAkhir::with(['siswa.kelas'])
            ->where('id_ta', $filterTA)
            ->whereNotNull('rank_smart')
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->when($filterKelas, fn($q) => $q->whereHas('siswa', fn($sq) =>
                $sq->where('id_kelas', $filterKelas)
            ))
            ->get()
            ->sortBy('rank_smart')
            ->values();

        $pdf = Pdf::loadView('kepalasekolah.report.smart_pdf', compact(
            'hasilList', 'tahunAjaran', 'sourceName', 'filterKelas'
        ))->setPaper('a4', 'portrait');

        return $pdf->download($this->filename($tahunAjaran, 'SMART', 'pdf'));
    }

    public function exportExcelSmart(Request $request)
    {
        [$filterTA, $source, $filterKelas, $tahunAjaran, $userId, $sourceName] = $this->resolveParams($request);

        return Excel::download(
            new HasilPerangkinganSmartExport($filterTA, $userId, $filterKelas, $tahunAjaran, $sourceName),
            $this->filename($tahunAjaran, 'SMART', 'xlsx')
        );
    }

    // ─── MOORA ──────────────────────────────────────────────────────────────────

    public function exportPdfMoora(Request $request)
    {
        [$filterTA, $source, $filterKelas, $tahunAjaran, $userId, $sourceName] = $this->resolveParams($request);

        $hasilList = HasilAkhir::with(['siswa.kelas'])
            ->where('id_ta', $filterTA)
            ->whereNotNull('rank_moora')
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->when($filterKelas, fn($q) => $q->whereHas('siswa', fn($sq) =>
                $sq->where('id_kelas', $filterKelas)
            ))
            ->get()
            ->sortBy('rank_moora')
            ->values();

        $pdf = Pdf::loadView('kepalasekolah.report.moora_pdf', compact(
            'hasilList', 'tahunAjaran', 'sourceName', 'filterKelas'
        ))->setPaper('a4', 'portrait');

        return $pdf->download($this->filename($tahunAjaran, 'MOORA', 'pdf'));
    }

    public function exportExcelMoora(Request $request)
    {
        [$filterTA, $source, $filterKelas, $tahunAjaran, $userId, $sourceName] = $this->resolveParams($request);

        return Excel::download(
            new HasilPerangkinganMooraExport($filterTA, $userId, $filterKelas, $tahunAjaran, $sourceName),
            $this->filename($tahunAjaran, 'MOORA', 'xlsx')
        );
    }

    public function exportPdfFinalis(Request $request, string $method)
    {
        [$tahunAjaran, $adminUser] = $this->resolveFinalisParams($request, $method);
        $hasilList = HasilFinalis::with('siswa.kelas')
            ->where('id_ta', $tahunAjaran->id_ta)
            ->where('user_id', $adminUser->id)
            ->where('metode', $method)
            ->orderByRaw("FIELD(tingkat, 'X', 'XI', 'XII')")
            ->orderBy('rank')
            ->get();

        $pdf = Pdf::loadView('kepalasekolah.report.finalis_pdf', compact(
            'hasilList', 'tahunAjaran', 'method'
        ))->setPaper('a4', 'portrait');

        return $pdf->download($this->filename($tahunAjaran, 'FINALIS_' . strtoupper($method), 'pdf'));
    }

    public function exportExcelFinalis(Request $request, string $method)
    {
        [$tahunAjaran, $adminUser] = $this->resolveFinalisParams($request, $method);

        return Excel::download(
            new HasilFinalisExport($tahunAjaran->id_ta, $adminUser->id, $method),
            $this->filename($tahunAjaran, 'FINALIS_' . strtoupper($method), 'xlsx')
        );
    }

    // ─── Helpers ────────────────────────────────────────────────────────────────

    private function resolveParams(Request $request): array
    {
        $filterTA    = $request->get('tahun_ajaran');
        $source      = $request->get('source', 'admin');
        $filterKelas = $request->get('kelas');
        $tahunAjaran = TahunAjaran::findOrFail($filterTA);
        $userId      = $this->resolveUserId($source);
        $sourceName  = $this->resolveSourceName($source);

        return [$filterTA, $source, $filterKelas, $tahunAjaran, $userId, $sourceName];
    }

    private function resolveFinalisParams(Request $request, string $method): array
    {
        abort_unless(in_array($method, ['smart', 'moora'], true), 404);

        $tahunAjaran = TahunAjaran::findOrFail($request->get('tahun_ajaran'));
        $adminUser = User::where('level', 'Admin')->firstOrFail();
        return [$tahunAjaran, $adminUser];
    }

    private function filename($tahunAjaran, string $method, string $ext): string
    {
        $ta = str_replace(['/', '\\', ' '], '_', $tahunAjaran->tahun_ajaran);
        return "Laporan_{$method}_{$ta}_{$tahunAjaran->semester}.{$ext}";
    }

    protected function resolveUserId($source): ?int
    {
        if ($source === 'admin') {
            return User::where('level', 'Admin')->first()?->id;
        }
        return (int) $source;
    }

    protected function resolveSourceName($source): string
    {
        if ($source === 'admin') return 'Admin (Semua Siswa)';

        $user = User::find($source);
        if ($user) {
            $kelas = Kelas::where('id_wali_kelas', $user->id)->first();
            return 'Wali Kelas: ' . $user->name . ($kelas ? ' (' . $kelas->nama_kelas . ')' : '');
        }
        return 'Tidak Diketahui';
    }
}
