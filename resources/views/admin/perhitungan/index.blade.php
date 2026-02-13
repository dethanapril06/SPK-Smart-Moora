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
                            <label class="form-label" for="kelas">Kelas</label>
                            <select class="form-select" id="kelas" name="kelas">
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

                @if ($filterTA)
                    <hr class="my-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">
                                <i class="bx bx-info-circle"></i>
                                <strong>{{ $studentsWithCompletePenilaian }}</strong> siswa dengan penilaian lengkap
                            </small>
                        </div>
                        <div class="btn-group" role="group">
                            @if ($hasCalculation)
                                <button type="button" class="btn btn-warning btn-sm"
                                    onclick="document.getElementById('recalculate-form').submit();">
                                    <i class="bx bx-refresh"></i> Hitung Ulang
                                </button>
                                <a href="{{ route('admin.perhitungan.compare', $filterTA) }}" class="btn btn-info btn-sm">
                                    <i class="bx bx-git-compare"></i> Bandingkan
                                </a>
                            @else
                                <button type="button" class="btn btn-success btn-sm"
                                    onclick="document.getElementById('calculate-form').submit();"
                                    {{ $studentsWithCompletePenilaian < 2 ? 'disabled' : '' }}>
                                    <i class="bx bx-calculator"></i> Hitung Sekarang
                                </button>
                            @endif
                        </div>
                    </div>

                    <form id="calculate-form" action="{{ route('admin.perhitungan.calculate') }}" method="POST"
                        class="d-none">
                        @csrf
                        <input type="hidden" name="id_ta" value="{{ $filterTA }}">
                    </form>
                    <form id="recalculate-form" action="{{ route('admin.perhitungan.calculate') }}" method="POST"
                        class="d-none">
                        @csrf
                        <input type="hidden" name="id_ta" value="{{ $filterTA }}">
                    </form>
                @endif
            </div>
        </div>

        <!-- Results Table -->
        @if ($hasCalculation)
            <div class="card">
                <h5 class="card-header">Hasil Ranking SMART & MOORA</h5>
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
                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <small class="text-muted">Total: {{ $hasilList->count() }} siswa</small>
                            <div>
                                <a href="{{ route('admin.perhitungan.steps', ['id_ta' => $filterTA, 'metode' => 'smart']) }}"
                                    class="btn btn-sm btn-label-primary">
                                    <i class="bx bx-detail"></i> Langkah SMART
                                </a>
                                <a href="{{ route('admin.perhitungan.steps', ['id_ta' => $filterTA, 'metode' => 'moora']) }}"
                                    class="btn btn-sm btn-label-success">
                                    <i class="bx bx-detail"></i> Langkah MOORA
                                </a>
                            </div>
                        </div>
                    </div>
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
