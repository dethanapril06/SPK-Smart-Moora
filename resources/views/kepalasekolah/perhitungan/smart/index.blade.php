@extends('layouts.kepalasekolah')
@section('title', 'Hasil Perangkingan SMART')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('kepalasekolah.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Hasil Perangkingan SMART</li>
                </ol>
            </nav>

            {{-- Tab switch SMART / MOORA --}}
            <div class="btn-group">
                <a href="{{ route('kepalasekolah.perhitungan.smart.index', request()->query()) }}"
                    class="btn btn-primary btn-sm">
                    <i class="bx bx-bar-chart-alt-2"></i> SMART
                </a>
                <a href="{{ route('kepalasekolah.perhitungan.moora.index', request()->query()) }}"
                    class="btn btn-outline-success btn-sm">
                    <i class="bx bx-line-chart"></i> MOORA
                </a>
            </div>
        </div>

        <!-- Filter -->
        <div class="card mb-4">
            <h5 class="card-header">Filter Hasil Perangkingan SMART</h5>
            <div class="card-body">
                <form action="{{ route('kepalasekolah.perhitungan.smart.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                            <select class="form-select" name="tahun_ajaran" required>
                                @foreach ($tahunAjaranList as $ta)
                                    <option value="{{ $ta->id_ta }}" {{ $filterTA == $ta->id_ta ? 'selected' : '' }}>
                                        {{ $ta->tahun_ajaran }} - {{ $ta->semester }} {{ $ta->is_active ? '(Aktif)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Sumber Perhitungan</label>
                            <select class="form-select" name="source">
                                @if (count($sourceList) > 0)
                                    @foreach ($sourceList as $src)
                                        <option value="{{ $src['value'] }}"
                                            {{ $source == $src['value'] ? 'selected' : '' }}>
                                            {{ $src['label'] }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="admin" selected>Admin (Semua Siswa)</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Kelas</label>
                            <select class="form-select" name="kelas">
                                <option value="">Semua Kelas</option>
                                @foreach ($kelasList as $k)
                                    <option value="{{ $k->id_kelas }}"
                                        {{ $filterKelas == $k->id_kelas ? 'selected' : '' }}>
                                        {{ $k->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
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
            </div>
        </div>

        <!-- Results -->
        @if ($hasCalculation)
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title m-0">
                        <i class="bx bx-trophy text-warning"></i> Hasil Ranking SMART — {{ $sourceName }}
                    </h5>
                    <div class="btn-group">
                        <a href="{{ route('kepalasekolah.report.pdf', ['tahun_ajaran' => $filterTA, 'source' => $source, 'kelas' => $filterKelas]) }}"
                            class="btn btn-sm btn-danger" target="_blank">
                            <i class="bx bxs-file-pdf"></i> Export PDF
                        </a>
                        <a href="{{ route('kepalasekolah.report.excel', ['tahun_ajaran' => $filterTA, 'source' => $source, 'kelas' => $filterKelas]) }}"
                            class="btn btn-sm btn-success">
                            <i class="bx bxs-file-export"></i> Export Excel
                        </a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">No</th>
                                <th>NISN / Nama Siswa</th>
                                <th class="text-center">Kelas</th>
                                <th class="text-center bg-label-primary">Skor SMART</th>
                                <th class="text-center bg-label-primary">Rank SMART</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($hasilList as $item)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
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
                                        @if ($item->rank_smart <= 3)
                                            <span class="badge bg-warning">{{ $item->rank_smart }}</span>
                                        @else
                                            <span class="badge bg-label-dark">{{ $item->rank_smart }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada data perhitungan SMART.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <small class="text-muted">Total: {{ $hasilList->count() }} siswa</small>
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bx bx-calculator bx-lg text-muted mb-3 d-block"></i>
                    <h5>Belum Ada Data Perhitungan SMART</h5>
                    <p class="text-muted">
                        @if ($filterTA)
                            Belum ada hasil perhitungan SMART dari sumber yang dipilih untuk tahun ajaran ini.
                        @else
                            Pilih tahun ajaran untuk melihat hasil perhitungan.
                        @endif
                    </p>
                </div>
            </div>
        @endif
    </div>
@endsection
