@extends('layouts.admin')
@section('title', 'Perhitungan SMART')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Perhitungan SMART</li>
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

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible" role="alert">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Filter & Action --}}
        <div class="card mb-4">
            <h5 class="card-header">Filter &amp; Perhitungan SMART</h5>
            <div class="card-body">
                <form action="{{ route('admin.perhitungan.smart.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label" for="tahun_ajaran">Tahun Ajaran <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="tahun_ajaran" name="tahun_ajaran" required>
                                @foreach ($tahunAjaranList as $ta)
                                    <option value="{{ $ta->id_ta }}" {{ $filterTA == $ta->id_ta ? 'selected' : '' }}>
                                        {{ $ta->tahun_ajaran }} {{ $ta->is_active ? '(Aktif)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="semester">Semester</label>
                            <select class="form-select" id="semester" name="semester">
                                <option value="">Semua Semester</option>
                                @foreach ($semesterList as $s)
                                    <option value="{{ $s->id_semester }}" data-id-ta="{{ $s->id_ta }}" {{ $filterSemester == $s->id_semester ? 'selected' : '' }}>
                                        {{ $s->nama_semester }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="kelas">Kelas (Pilih untuk Perhitungan)</label>
                            <select class="form-select js-kelas-select2" id="kelas" name="kelas[]" multiple
                                data-placeholder="Pilih satu atau lebih kelas...">
                                <option value="all" {{ $allKelasSelected ? 'selected' : '' }}>Semua Kelas</option>
                                @foreach ($kelasList as $k)
                                    <option value="{{ $k->id_kelas }}"
                                        {{ !$allKelasSelected && in_array($k->id_kelas, $filterKelas ?? []) ? 'selected' : '' }}>
                                        {{ $k->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-1">Perhitungan SMART dijalankan per kelas (X.1, X.2, dst.).</small>
                        </div>
                        <div class="col-md-2">
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
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <small class="text-muted d-block">
                                <i class="bx bx-info-circle"></i>
                                <strong>{{ $studentsWithCompletePenilaian }}</strong> siswa dengan penilaian lengkap
                                @if (!empty($filterKelas))
                                    pada <strong>{{ count($filterKelas) }}</strong> kelas terpilih
                                @endif
                            </small>
                            <small class="text-muted">
                                Perhitungan SMART dihitung mandiri per masing-masing kelas (Peringkat 1..N di setiap kelas).
                            </small>
                        </div>
                        <div class="btn-group" role="group">
                            @if ($hasCalculation)
                                <button type="button" class="btn btn-warning btn-sm"
                                    onclick="document.getElementById('recalculate-form').submit();"
                                    {{ empty($filterKelas) ? 'disabled' : '' }}>
                                    <i class="bx bx-refresh"></i> Hitung Ulang SMART
                                </button>
                            @else
                                <button type="button" class="btn btn-success btn-sm"
                                    onclick="document.getElementById('calculate-form').submit();"
                                    {{ $studentsWithCompletePenilaian < 1 || empty($filterKelas) ? 'disabled' : '' }}>
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

                    @if (!$hasCalculation && $studentsWithCompletePenilaian < 1)
                        <div class="alert alert-info mt-3 mb-0" role="alert">
                            <i class="bx bx-bulb me-1"></i>
                            Tombol <strong>"Hitung Sekarang"</strong> belum aktif karena belum ada data penilaian lengkap.
                            Silakan lakukan penilaian di menu
                            <a href="{{ route('admin.penilaian.index') }}" class="alert-link">Penilaian Siswa</a>.
                        </div>
                    @endif

                    <form id="calculate-form" action="{{ route('admin.perhitungan.smart.calculate') }}" method="POST"
                        class="d-none">
                        @csrf
                        <input type="hidden" name="id_ta" value="{{ $filterTA }}">
                        <input type="hidden" name="id_semester" value="{{ $filterSemester }}">
                        @foreach ($filterKelas as $kelasId)
                            <input type="hidden" name="kelas[]" value="{{ $kelasId }}">
                        @endforeach
                    </form>
                    <form id="recalculate-form" action="{{ route('admin.perhitungan.smart.calculate') }}" method="POST"
                        class="d-none">
                        @csrf
                        <input type="hidden" name="id_ta" value="{{ $filterTA }}">
                        <input type="hidden" name="id_semester" value="{{ $filterSemester }}">
                        @foreach ($filterKelas as $kelasId)
                            <input type="hidden" name="kelas[]" value="{{ $kelasId }}">
                        @endforeach
                    </form>
                @endif
            </div>
        </div>

        {{-- Results Organized by Angkatan Tabs --}}
        @if ($hasCalculation)
            @php
                $tingkatTabs = [
                    'X'   => ['label' => 'Kelas X', 'icon' => 'bx-book-reader', 'data' => $hasilByTingkat['X']],
                    'XI'  => ['label' => 'Kelas XI', 'icon' => 'bx-book-reader', 'data' => $hasilByTingkat['XI']],
                    'XII' => ['label' => 'Kelas XII', 'icon' => 'bx-book-reader', 'data' => $hasilByTingkat['XII']],
                ];
            @endphp
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 pb-2">
                    <div>
                        <h5 class="mb-1">Hasil Perangkingan SMART (Per Angkatan &amp; Per Kelas)</h5>
                        <small class="text-muted">Pilih tab angkatan di bawah untuk melihat hasil ranking masing-masing kelas (X.1, X.2, dst.). 3 besar dari setiap kelas berhak maju ke Finalis 10 Besar.</small>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.perhitungan.smart.steps', ['id_ta' => $filterTA, 'semester' => $filterSemester, 'kelas' => $filterKelas]) }}"
                            class="btn btn-sm btn-outline-primary">
                            <i class="bx bx-detail"></i> Langkah Perhitungan
                        </a>
                        <a href="{{ route('admin.perhitungan.finalis.smart.index', ['tahun_ajaran' => $filterTA, 'semester' => $filterSemester]) }}" class="btn btn-sm btn-primary">
                            <i class="tf-icons bx bx-trophy"></i> 10 Besar SMART (Finalis)
                        </a>
                    </div>
                </div>

                <div class="nav-align-top">
                    <ul class="nav nav-tabs" role="tablist">
                        @foreach ($tingkatTabs as $tingkatKey => $tab)
                            <li class="nav-item" role="presentation">
                                <button type="button"
                                    class="nav-link {{ $loop->first ? 'active' : '' }}"
                                    role="tab"
                                    data-bs-toggle="tab"
                                    data-bs-target="#tab-angkatan-{{ $tingkatKey }}"
                                    aria-controls="tab-angkatan-{{ $tingkatKey }}"
                                    aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                    <i class="bx {{ $tab['icon'] }} me-1"></i> {{ $tab['label'] }}
                                    <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-label-primary ms-1">
                                        {{ $tab['data']['total_siswa'] }}
                                    </span>
                                </button>
                            </li>
                        @endforeach
                    </ul>

                    <div class="tab-content p-3">
                        @foreach ($tingkatTabs as $tingkatKey => $tab)
                            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                id="tab-angkatan-{{ $tingkatKey }}"
                                role="tabpanel">

                                @if ($tab['data']['total_siswa'] > 0)
                                    <div class="alert alert-light border mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                        <div>
                                            <i class="bx bx-info-circle text-primary me-1"></i>
                                            Angkatan <strong>Kelas {{ $tingkatKey }}</strong> memiliki <strong>{{ $tab['data']['total_kelas'] }} Kelas</strong> dengan total <strong>{{ $tab['data']['total_siswa'] }} Siswa</strong> yang telah di-ranking.
                                        </div>
                                        <div>
                                            <span class="badge bg-label-warning">
                                                <i class="bx bx-star"></i> Top 3 dari tiap kelas = Kandidat Finalis 10 Besar
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Loop each class in this Angkatan --}}
                                    <div class="row g-4">
                                        @foreach ($tab['data']['by_kelas'] as $namaKelas => $siswaList)
                                            <div class="col-12">
                                                <div class="card border shadow-none">
                                                    <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                                                        <h6 class="mb-0 text-primary">
                                                            <i class="bx bx-chalkboard me-1"></i> {{ $namaKelas }}
                                                        </h6>
                                                        <span class="badge bg-label-primary">
                                                            {{ $siswaList->count() }} Siswa
                                                        </span>
                                                    </div>
                                                    <div class="table-responsive">
                                                        <table class="table table-hover table-sm mb-0">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th class="text-center" style="width: 70px;">Rank Kelas</th>
                                                                    <th>NISN</th>
                                                                    <th>Nama Siswa</th>
                                                                    <th class="text-center bg-label-primary" style="width: 140px;">Skor SMART</th>
                                                                    <th class="text-center" style="width: 220px;">Status</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($siswaList as $item)
                                                                    <tr>
                                                                        <td class="text-center">
                                                                            @if ($item->rank_smart == 1)
                                                                                <span class="badge bg-warning text-dark"><i class="bx bx-trophy"></i> 1</span>
                                                                            @elseif ($item->rank_smart == 2)
                                                                                <span class="badge bg-secondary text-white"><i class="bx bx-medal"></i> 2</span>
                                                                            @elseif ($item->rank_smart == 3)
                                                                                <span class="badge bg-info text-white"><i class="bx bx-medal"></i> 3</span>
                                                                            @else
                                                                                <span class="badge bg-label-dark">{{ $item->rank_smart }}</span>
                                                                            @endif
                                                                        </td>
                                                                        <td><strong>{{ $item->siswa->nisn }}</strong></td>
                                                                        <td>{{ $item->siswa->nama_siswa }}</td>
                                                                        <td class="text-center">
                                                                            <strong class="text-primary">{{ number_format($item->skor_smart, 4) }}</strong>
                                                                        </td>
                                                                        <td class="text-center">
                                                                            @if ($item->rank_smart <= 3)
                                                                                <span class="badge bg-label-warning">
                                                                                    <i class="bx bx-star me-1"></i> Kandidat Finalis (Top 3)
                                                                                </span>
                                                                            @else
                                                                                <span class="badge bg-label-secondary">Reguler</span>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-5 text-muted">
                                        <i class="bx bx-folder-open bx-lg mb-2 d-block"></i>
                                        Belum ada data perhitungan SMART untuk Angkatan <strong>Kelas {{ $tingkatKey }}</strong>
                                    </div>
                                @endif

                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @elseif ($filterTA)
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bx bx-calculator bx-lg text-muted mb-3 d-block"></i>
                    <h5>Belum Ada Data Perhitungan SMART</h5>
                    <p class="text-muted">
                        Silakan pilih kelas dan klik tombol <strong>"Hitung Sekarang"</strong> untuk melakukan perhitungan SMART per masing-masing kelas.
                    </p>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            if (typeof $.fn.select2 !== 'undefined' && $('.js-kelas-select2').length) {
                $('.js-kelas-select2').select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Pilih satu atau lebih kelas...'
                });

                // Auto handle "Semua Kelas" option
                $('.js-kelas-select2').on('change', function() {
                    let values = $(this).val() || [];
                    if (values.includes('all') && values.length > 1) {
                        if (values[values.length - 1] === 'all') {
                            $(this).val(['all']).trigger('change');
                        } else {
                            let filtered = values.filter(v => v !== 'all');
                            $(this).val(filtered).trigger('change');
                        }
                    }
                });
            }
        });
    </script>
@endpush
