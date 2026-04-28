@extends('layouts.admin')
@section('title', 'Perhitungan SMART-MOORA')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Perhitungan SMART-MOORA</li>
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

        <!-- Filter & Action -->
        <div class="card mb-4">
            <h5 class="card-header">Filter & Perhitungan</h5>
            <div class="card-body">
                <form action="{{ route('admin.perhitungan.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label" for="tahun_ajaran">Tahun Ajaran <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="tahun_ajaran" name="tahun_ajaran" required>
                                @foreach ($tahunAjaranList as $ta)
                                    <option value="{{ $ta->id_ta }}" {{ $filterTA == $ta->id_ta ? 'selected' : '' }}>
                                        {{ $ta->tahun_ajaran }} - {{ $ta->semester }} {{ $ta->is_active ? '(Aktif)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="kelas">Kelas (Pilih untuk Perhitungan)</label>
                            <select class="form-select js-kelas-select2" id="kelas" name="kelas[]" multiple
                                data-placeholder="Pilih satu atau lebih kelas...">
                                <option value="all" {{ $allKelasSelected ? 'selected' : '' }}>
                                    Semua Kelas
                                </option>
                                @foreach ($kelasList as $k)
                                    <option value="{{ $k->id_kelas }}"
                                        {{ !$allKelasSelected && in_array($k->id_kelas, $filterKelas ?? []) ? 'selected' : '' }}>
                                        {{ $k->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-1">Pilih "Semua Kelas" untuk menghitung seluruh kelas,
                                atau pilih beberapa kelas secara manual.</small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-search"></i> Filter
                                </button>
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
                                @if (!empty($filterKelas))
                                    pada <strong>{{ count($filterKelas) }}</strong> kelas terpilih
                                @endif
                            </small>
                        </div>
                        <div class="btn-group" role="group">
                            @if ($hasCalculation)
                                <button type="button" class="btn btn-warning btn-sm"
                                    onclick="document.getElementById('recalculate-form').submit();"
                                    {{ empty($filterKelas) ? 'disabled' : '' }}>
                                    <i class="bx bx-refresh"></i> Hitung Ulang
                                </button>
                                <a href="{{ route('admin.perhitungan.compare', ['id_ta' => $filterTA, 'kelas' => $filterKelas]) }}"
                                    class="btn btn-info btn-sm">
                                    <i class="bx bx-git-compare"></i> Bandingkan
                                </a>
                            @else
                                <button type="button" class="btn btn-success btn-sm"
                                    onclick="document.getElementById('calculate-form').submit();"
                                    {{ $studentsWithCompletePenilaian < 2 || empty($filterKelas) ? 'disabled' : '' }}>
                                    <i class="bx bx-calculator"></i> Hitung Sekarang
                                </button>
                            @endif
                        </div>
                    </div>

                    @if (empty($filterKelas))
                        <div class="alert alert-warning mt-3 mb-0" role="alert">
                            <i class="bx bx-error-circle me-1"></i>
                            Pilih minimal <strong>1 kelas</strong> terlebih dahulu untuk memulai perhitungan.
                        </div>
                    @endif

                    @if (!$hasCalculation && $studentsWithCompletePenilaian < 2)
                        <div class="alert alert-info mt-3 mb-0" role="alert">
                            <i class="bx bx-bulb me-1"></i>
                            Tombol <strong>"Hitung Sekarang"</strong> belum aktif karena belum ada cukup data penilaian.
                            Silakan lakukan <strong>Agregasi Nilai Rapor</strong> terlebih dahulu di menu
                            <a href="{{ route('admin.penilaian.index') }}" class="alert-link">Penilaian Siswa</a>.
                        </div>
                    @endif

                    <form id="calculate-form" action="{{ route('admin.perhitungan.calculate') }}" method="POST"
                        class="d-none">
                        @csrf
                        <input type="hidden" name="id_ta" value="{{ $filterTA }}">
                        @foreach ($filterKelas as $kelasId)
                            <input type="hidden" name="kelas[]" value="{{ $kelasId }}">
                        @endforeach
                    </form>
                    <form id="recalculate-form" action="{{ route('admin.perhitungan.calculate') }}" method="POST"
                        class="d-none">
                        @csrf
                        <input type="hidden" name="id_ta" value="{{ $filterTA }}">
                        @foreach ($filterKelas as $kelasId)
                            <input type="hidden" name="kelas[]" value="{{ $kelasId }}">
                        @endforeach
                    </form>
                @endif
            </div>
        </div>

        <!-- Results Table -->
        @if ($hasCalculation)
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Hasil Ranking SMART & MOORA</h5>
                    <div>
                        <a href="{{ route('admin.perhitungan.steps', ['id_ta' => $filterTA, 'metode' => 'smart', 'kelas' => $filterKelas]) }}"
                            class="btn btn-sm btn-primary">
                            <i class="bx bx-detail"></i> Langkah SMART
                        </a>
                        <a href="{{ route('admin.perhitungan.steps', ['id_ta' => $filterTA, 'metode' => 'moora', 'kelas' => $filterKelas]) }}"
                            class="btn btn-sm btn-success">
                            <i class="bx bx-detail"></i> Langkah MOORA
                        </a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th rowspan="2" class="align-middle text-center">No</th>
                                <th rowspan="2" class="align-middle">NISN / Nama Siswa</th>
                                <th rowspan="2" class="align-middle text-center">Kelas</th>
                                <th colspan="2" class="text-center bg-label-primary">SMART</th>
                                <th colspan="2" class="text-center bg-label-success">MOORA</th>
                            </tr>
                            <tr>
                                <th class="text-center bg-label-primary">Skor</th>
                                <th class="text-center bg-label-primary">Rank</th>
                                <th class="text-center bg-label-success">Skor</th>
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
                                        @if ($item->siswa->kelas)
                                            <span class="badge bg-label-info">{{ $item->siswa->kelas->nama_kelas }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <strong class="text-primary">{{ number_format($item->skor_smart, 4) }}</strong>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-label-dark">{{ $item->rank_smart }}</span>
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
                                    <td colspan="8" class="text-center">Belum ada data perhitungan</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($hasilList->count() > 0)
                    @if ($hasilList->hasPages())
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Menampilkan {{ $hasilList->firstItem() }} - {{ $hasilList->lastItem() }} dari
                                    {{ $hasilList->total() }} data
                                </div>
                                <nav aria-label="Page navigation">
                                    <ul class="pagination pagination-sm mb-0">
                                        @if ($hasilList->onFirstPage())
                                            <li class="page-item disabled">
                                                <span class="page-link"><i class="tf-icon bx bx-chevrons-left"></i></span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $hasilList->url(1) }}"><i
                                                        class="tf-icon bx bx-chevrons-left"></i></a>
                                            </li>
                                        @endif

                                        @if ($hasilList->onFirstPage())
                                            <li class="page-item disabled">
                                                <span class="page-link"><i class="tf-icon bx bx-chevron-left"></i></span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $hasilList->previousPageUrl() }}"><i
                                                        class="tf-icon bx bx-chevron-left"></i></a>
                                            </li>
                                        @endif

                                        @foreach ($hasilList->getUrlRange(max(1, $hasilList->currentPage() - 2), min($hasilList->lastPage(), $hasilList->currentPage() + 2)) as $page => $url)
                                            @if ($page == $hasilList->currentPage())
                                                <li class="page-item active"><span
                                                        class="page-link">{{ $page }}</span>
                                                </li>
                                            @else
                                                <li class="page-item"><a class="page-link"
                                                        href="{{ $url }}">{{ $page }}</a></li>
                                            @endif
                                        @endforeach

                                        @if ($hasilList->hasMorePages())
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $hasilList->nextPageUrl() }}"><i
                                                        class="tf-icon bx bx-chevron-right"></i></a>
                                            </li>
                                        @else
                                            <li class="page-item disabled">
                                                <span class="page-link"><i class="tf-icon bx bx-chevron-right"></i></span>
                                            </li>
                                        @endif

                                        @if ($hasilList->hasMorePages())
                                            <li class="page-item">
                                                <a class="page-link"
                                                    href="{{ $hasilList->url($hasilList->lastPage()) }}"><i
                                                        class="tf-icon bx bx-chevrons-right"></i></a>
                                            </li>
                                        @else
                                            <li class="page-item disabled">
                                                <span class="page-link"><i
                                                        class="tf-icon bx bx-chevrons-right"></i></span>
                                            </li>
                                        @endif
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        @else
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bx bx-calculator bx-lg text-muted mb-3 d-block"></i>
                    <h5>Belum Ada Perhitungan</h5>
                    <p class="text-muted">
                        @if ($filterTA)
                            @if ($studentsWithCompletePenilaian >= 2)
                                Klik tombol "Hitung Sekarang" untuk memulai perhitungan ranking menggunakan metode SMART dan
                                MOORA.
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

@push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(function() {
            const $kelasSelect = $('.js-kelas-select2');

            if ($kelasSelect.length) {
                $kelasSelect.select2({
                    width: '100%',
                    placeholder: $kelasSelect.data('placeholder') || 'Pilih kelas',
                    closeOnSelect: false,
                    allowClear: true
                });
            }
        });
    </script>
    <style>
        .select2-container--default .select2-selection--multiple {
            min-height: calc(2.25rem + 2px);
            border: 1px solid #d9dee3;
            border-radius: 0.375rem;
            padding: 0.2rem 0.4rem;
        }

        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border-color: #696cff;
            box-shadow: 0 0 0 .2rem rgba(105, 108, 255, .25);
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #e7e7ff;
            border: 1px solid #c7c9ff;
            color: #566a7f;
            border-radius: 999px;
            padding: 0 0.5rem;
            margin-top: 0.25rem;
        }

        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            background-color: #696cff;
        }
    </style>
@endpush
