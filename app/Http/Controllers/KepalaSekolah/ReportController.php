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
use App\Models\Semester;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    // ─── SMART ──────────────────────────────────────────────────────────────────

    public function exportPdfSmart(Request $request)
    {
        [$filterTA, $filterSemester, $source, $filterKelas, $tahunAjaran, $semester, $userId, $sourceName] = $this->resolveParams($request);

        $hasilList = HasilAkhir::with(['siswa.kelas'])
            ->where('id_ta', $filterTA)
            ->when($filterSemester, fn($q) => $q->where('id_semester', $filterSemester))
            ->whereNotNull('rank_smart')
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->when($filterKelas, fn($q) => $q->whereHas('siswa', fn($sq) =>
                $sq->where('id_kelas', $filterKelas)
            ))
            ->get()
            ->sortBy('rank_smart')
            ->values();

        $pdf = Pdf::loadView('kepalasekolah.report.smart_pdf', compact(
            'hasilList', 'tahunAjaran', 'semester', 'sourceName', 'filterKelas'
        ))->setPaper('a4', 'portrait');

        return $pdf->download($this->filename($tahunAjaran, $semester, 'SMART', 'pdf'));
    }

    public function exportExcelSmart(Request $request)
    {
        [$filterTA, $filterSemester, $source, $filterKelas, $tahunAjaran, $semester, $userId, $sourceName] = $this->resolveParams($request);

        return Excel::download(
            new HasilPerangkinganSmartExport($filterTA, $filterSemester, $userId, $filterKelas, $tahunAjaran, $semester, $sourceName),
            $this->filename($tahunAjaran, $semester, 'SMART', 'xlsx')
        );
    }

    // ─── MOORA ──────────────────────────────────────────────────────────────────

    public function exportPdfMoora(Request $request)
    {
        [$filterTA, $filterSemester, $source, $filterKelas, $tahunAjaran, $semester, $userId, $sourceName] = $this->resolveParams($request);

        $hasilList = HasilAkhir::with(['siswa.kelas'])
            ->where('id_ta', $filterTA)
            ->when($filterSemester, fn($q) => $q->where('id_semester', $filterSemester))
            ->whereNotNull('rank_moora')
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->when($filterKelas, fn($q) => $q->whereHas('siswa', fn($sq) =>
                $sq->where('id_kelas', $filterKelas)
            ))
            ->get()
            ->sortBy('rank_moora')
            ->values();

        $pdf = Pdf::loadView('kepalasekolah.report.moora_pdf', compact(
            'hasilList', 'tahunAjaran', 'semester', 'sourceName', 'filterKelas'
        ))->setPaper('a4', 'portrait');

        return $pdf->download($this->filename($tahunAjaran, $semester, 'MOORA', 'pdf'));
    }

    public function exportExcelMoora(Request $request)
    {
        [$filterTA, $filterSemester, $source, $filterKelas, $tahunAjaran, $semester, $userId, $sourceName] = $this->resolveParams($request);

        return Excel::download(
            new HasilPerangkinganMooraExport($filterTA, $filterSemester, $userId, $filterKelas, $tahunAjaran, $semester, $sourceName),
            $this->filename($tahunAjaran, $semester, 'MOORA', 'xlsx')
        );
    }

    public function exportPdfFinalis(Request $request, string $method)
    {
        [$tahunAjaran, $semester, $adminUser] = $this->resolveFinalisParams($request, $method);
        $hasilList = HasilFinalis::with('siswa.kelas')
            ->where('id_ta', $tahunAjaran->id_ta)
            ->when($semester, fn($q) => $q->where('id_semester', $semester->id_semester))
            ->where('user_id', $adminUser->id)
            ->where('metode', $method)
            ->orderByRaw("FIELD(tingkat, 'X', 'XI', 'XII')")
            ->orderBy('rank')
            ->get();

        $pdf = Pdf::loadView('kepalasekolah.report.finalis_pdf', compact(
            'hasilList', 'tahunAjaran', 'semester', 'method'
        ))->setPaper('a4', 'portrait');

        return $pdf->download($this->filename($tahunAjaran, $semester, 'FINALIS_' . strtoupper($method), 'pdf'));
    }

    public function exportExcelFinalis(Request $request, string $method)
    {
        [$tahunAjaran, $semester, $adminUser] = $this->resolveFinalisParams($request, $method);

        return Excel::download(
            new HasilFinalisExport($tahunAjaran->id_ta, $semester?->id_semester, $adminUser->id, $method),
            $this->filename($tahunAjaran, $semester, 'FINALIS_' . strtoupper($method), 'xlsx')
        );
    }

    // ─── Helpers ────────────────────────────────────────────────────────────────

    private function resolveParams(Request $request): array
    {
        $filterTA       = $request->get('tahun_ajaran');
        $filterSemester = $request->get('semester');
        $source         = $request->get('source', 'admin');
        $filterKelas    = $request->get('kelas');
        $tahunAjaran    = TahunAjaran::findOrFail($filterTA);
        $semester       = $filterSemester ? Semester::find($filterSemester) : null;
        $userId         = $this->resolveUserId($source);
        $sourceName     = $this->resolveSourceName($source);

        return [$filterTA, $filterSemester, $source, $filterKelas, $tahunAjaran, $semester, $userId, $sourceName];
    }

    private function resolveFinalisParams(Request $request, string $method): array
    {
        abort_unless(in_array($method, ['smart', 'moora'], true), 404);

        $tahunAjaran = TahunAjaran::findOrFail($request->get('tahun_ajaran'));
        $semesterId  = $request->get('semester');
        $semester    = $semesterId ? Semester::find($semesterId) : null;
        $adminUser   = User::where('level', 'Admin')->firstOrFail();
        return [$tahunAjaran, $semester, $adminUser];
    }

    private function filename($tahunAjaran, $semester, string $method, string $ext): string
    {
        $ta = str_replace(['/', '\\', ' '], '_', $tahunAjaran->tahun_ajaran);
        $sem = $semester ? str_replace(['/', '\\', ' '], '_', $semester->nama_semester) : $tahunAjaran->semester;
        return "Laporan_{$method}_{$ta}_{$sem}.{$ext}";
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
