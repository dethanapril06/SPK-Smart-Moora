@extends('layouts.walikelas')
@section('title', 'Perhitungan MOORA')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('walikelas.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Perhitungan MOORA</li>
                </ol>
            </nav>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Filter & Action --}}
        <div class="card mb-4">
            <h5 class="card-header">Filter &amp; Perhitungan MOORA</h5>
            <div class="card-body">
                <form action="{{ route('walikelas.perhitungan.moora.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label" for="tahun_ajaran">Tahun Ajaran <span class="text-danger">*</span></label>
                            <select class="form-select" id="tahun_ajaran" name="tahun_ajaran" required>
                                @foreach ($tahunAjaranList as $ta)
                                    <option value="{{ $ta->id_ta }}" {{ $filterTA == $ta->id_ta ? 'selected' : '' }}>
                                        {{ $ta->tahun_ajaran }} - {{ $ta->semester }} {{ $ta->is_active ? '(Aktif)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mt-3 text-end">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary"><i class="bx bx-search"></i> Filter</button>
                            </div>
                        </div>
                    </div>
                </form>

                @if ($filterTA)
                    <hr class="my-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">
                                <i class="bx bx-info-circle"></i>
                                <strong>{{ $studentsWithCompletePenilaian }}</strong> siswa dengan penilaian lengkap
                                (Kelas: <strong>{{ $kelas->nama_kelas }}</strong>)
                            </small>
                        </div>
                        <div class="btn-group" role="group">
                            @if ($hasCalculation)
                                <button type="button" class="btn btn-warning btn-sm"
                                    onclick="document.getElementById('recalculate-form').submit();">
                                    <i class="bx bx-refresh"></i> Hitung Ulang MOORA
                                </button>
                            @else
                                <button type="button" class="btn btn-success btn-sm"
                                    onclick="document.getElementById('calculate-form').submit();"
                                    {{ $studentsWithCompletePenilaian < 2 ? 'disabled' : '' }}>
                                    <i class="bx bx-calculator"></i> Hitung Sekarang
                                </button>
                            @endif
                        </div>
                    </div>

                    @if (!$hasCalculation && $studentsWithCompletePenilaian < 2)
                        <div class="alert alert-info mt-3 mb-0" role="alert">
                            <i class="bx bx-bulb me-1"></i>
                            Tombol <strong>"Hitung Sekarang"</strong> belum aktif karena belum ada cukup data penilaian.
                            Silakan lakukan <strong>Agregasi Nilai Rapor</strong> terlebih dahulu di menu
                            <a href="{{ route('walikelas.penilaian.index') }}" class="alert-link">Penilaian Siswa</a>.
                        </div>
                    @endif

                    <form id="calculate-form" action="{{ route('walikelas.perhitungan.moora.calculate') }}" method="POST" class="d-none">
                        @csrf
                        <input type="hidden" name="id_ta" value="{{ $filterTA }}">
                    </form>
                    <form id="recalculate-form" action="{{ route('walikelas.perhitungan.moora.calculate') }}" method="POST" class="d-none">
                        @csrf
                        <input type="hidden" name="id_ta" value="{{ $filterTA }}">
                    </form>
                @endif
            </div>
        </div>

        {{-- Results Table --}}
        @if ($hasCalculation)
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Hasil Ranking MOORA - {{ $kelas->nama_kelas }}</h5>
                    <div>
                        <a href="{{ route('walikelas.perhitungan.moora.steps', $filterTA) }}"
                            class="btn btn-sm btn-success">
                            <i class="bx bx-detail"></i> Langkah MOORA
                        </a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">No</th>
                                <th>NISN / Nama Siswa</th>
                                <th class="text-center bg-label-success">Skor MOORA</th>
                                <th class="text-center bg-label-success">Rank</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($hasilList as $item)
                                <tr>
                                    <td class="text-center">
                                        {{ ($hasilList->currentPage() - 1) * $hasilList->perPage() + $loop->iteration }}
                                    </td>
                                    <td>
                                        <strong>{{ $item->siswa->nisn }}</strong><br>
                                        <small>{{ $item->siswa->nama_siswa }}</small>
                                    </td>
                                    <td class="text-center">
                                        <strong class="text-success">{{ number_format($item->skor_moora, 4) }}</strong>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-label-dark">{{ $item->rank_moora }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada data perhitungan MOORA</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($hasilList->count() > 0 && $hasilList->hasPages())
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted">
                                Menampilkan {{ $hasilList->firstItem() }} - {{ $hasilList->lastItem() }} dari
                                {{ $hasilList->total() }} data
                            </div>
                            <nav aria-label="Page navigation">
                                <ul class="pagination pagination-sm mb-0">
                                    @if ($hasilList->onFirstPage())
                                        <li class="page-item disabled"><span class="page-link"><i class="tf-icon bx bx-chevrons-left"></i></span></li>
                                        <li class="page-item disabled"><span class="page-link"><i class="tf-icon bx bx-chevron-left"></i></span></li>
                                    @else
                                        <li class="page-item"><a class="page-link" href="{{ $hasilList->url(1) }}"><i class="tf-icon bx bx-chevrons-left"></i></a></li>
                                        <li class="page-item"><a class="page-link" href="{{ $hasilList->previousPageUrl() }}"><i class="tf-icon bx bx-chevron-left"></i></a></li>
                                    @endif

                                    @foreach ($hasilList->getUrlRange(max(1, $hasilList->currentPage() - 2), min($hasilList->lastPage(), $hasilList->currentPage() + 2)) as $page => $url)
                                        @if ($page == $hasilList->currentPage())
                                            <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                                        @else
                                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                                        @endif
                                    @endforeach

                                    @if ($hasilList->hasMorePages())
                                        <li class="page-item"><a class="page-link" href="{{ $hasilList->nextPageUrl() }}"><i class="tf-icon bx bx-chevron-right"></i></a></li>
                                        <li class="page-item"><a class="page-link" href="{{ $hasilList->url($hasilList->lastPage()) }}"><i class="tf-icon bx bx-chevrons-right"></i></a></li>
                                    @else
                                        <li class="page-item disabled"><span class="page-link"><i class="tf-icon bx bx-chevron-right"></i></span></li>
                                        <li class="page-item disabled"><span class="page-link"><i class="tf-icon bx bx-chevrons-right"></i></span></li>
                                    @endif
                                </ul>
                            </nav>
                        </div>
                    </div>
                @endif
            </div>
        @else
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bx bx-bar-chart-alt-2 bx-lg text-muted mb-3 d-block"></i>
                    <h5>Belum Ada Perhitungan MOORA</h5>
                    <p class="text-muted">
                        @if ($filterTA)
                            @if ($studentsWithCompletePenilaian >= 2)
                                Klik tombol "Hitung Sekarang" untuk memulai perhitungan ranking menggunakan metode MOORA
                                untuk kelas {{ $kelas->nama_kelas }}.
                            @else
                                Minimal 2 siswa dengan penilaian lengkap diperlukan untuk melakukan perhitungan.
                            @endif
                        @else
                            Pilih tahun ajaran untuk melihat hasil perhitungan.
                        @endif
                    </p>
                </div>
            </div>
        @endif
    </div>
@endsection
