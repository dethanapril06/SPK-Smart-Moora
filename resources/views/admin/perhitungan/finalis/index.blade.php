@extends('layouts.admin')

@php
    $methodName = strtoupper($method);
    $isSmart = $method === 'smart';
    $themeClass = $isSmart ? 'primary' : 'success';
    $iconClass = $isSmart ? 'bx-trophy' : 'bx-medal';
    $tingkatList = ['X', 'XI', 'XII'];
@endphp

@section('title', '10 Besar ' . $methodName)

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">10 Besar {{ $methodName }}</li>
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

        <div class="card mb-4">
            <h5 class="card-header">Filter &amp; Perhitungan 10 Besar {{ $methodName }}</h5>
            <div class="card-body">
                <form action="{{ route("admin.perhitungan.finalis.{$method}.index") }}" method="GET">
                    <div class="row g-3 align-items-end">
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
                        <div class="col-md-4">
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
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <small class="text-muted d-block">
                                <i class="bx bx-info-circle"></i>
                                <strong>{{ $readiness['eligible_classes'] }}</strong> dari
                                <strong>{{ $readiness['total_classes'] }}</strong> kelas siap menjadi sumber kandidat.
                            </small>
                            <small class="text-muted">
                                Sistem mengambil 3 besar dari setiap kelas, menghitung ulang per tingkat X/XI/XII, lalu mengambil 10 besar tiap tingkat.
                            </small>
                        </div>
                        <button type="button" class="btn btn-{{ $hasCalculation ? 'warning' : $themeClass }} btn-sm"
                            onclick="document.getElementById('calculate-finalis-form').submit();"
                            {{ $readiness['eligible_classes'] < 1 ? 'disabled' : '' }}>
                            <i class="bx {{ $hasCalculation ? 'bx-refresh' : 'bx-calculator' }}"></i>
                            {{ $hasCalculation ? 'Hitung Ulang' : 'Hitung Sekarang' }}
                        </button>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-3">
                        @foreach ($tingkatList as $tingkat)
                            <span class="badge bg-label-{{ $themeClass }}">
                                Kelas {{ $tingkat }}: {{ $readiness['eligible_by_tingkat'][$tingkat] ?? 0 }} kelas siap
                            </span>
                        @endforeach
                    </div>

                    @if ($readiness['eligible_classes'] < 1)
                        <div class="alert alert-info mt-3 mb-0" role="alert">
                            <i class="bx bx-bulb me-1"></i>
                            Minimal 1 kelas dengan penilaian lengkap diperlukan untuk mengambil kandidat finalis.
                        </div>
                    @endif

                    @if (!empty($readiness['skipped_classes']))
                        <div class="alert alert-warning mt-3 mb-0" role="alert">
                            <div class="fw-semibold mb-1">
                                <i class="bx bx-error-circle me-1"></i>
                                Kelas yang belum memiliki siswa lengkap
                            </div>
                            <div>
                                @foreach ($readiness['skipped_classes'] as $class)
                                    <span class="badge bg-label-warning me-1 mb-1">
                                        {{ $class['nama_kelas'] }}: {{ $class['complete_students'] }} siswa lengkap
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if (!empty($readiness['unknown_classes']))
                        <div class="alert alert-danger mt-3 mb-0" role="alert">
                            <div class="fw-semibold mb-1">
                                <i class="bx bx-error-circle me-1"></i>
                                Nama kelas belum terbaca sebagai X, XI, atau XII
                            </div>
                            <div>
                                @foreach ($readiness['unknown_classes'] as $class)
                                    <span class="badge bg-label-danger me-1 mb-1">{{ $class['nama_kelas'] }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <form id="calculate-finalis-form"
                        action="{{ route("admin.perhitungan.finalis.{$method}.calculate") }}"
                        method="POST"
                        class="d-none">
                        @csrf
                        <input type="hidden" name="id_ta" value="{{ $filterTA }}">
                    </form>
                @endif
            </div>
        </div>

        @if ($hasCalculation)
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Hasil 10 Besar {{ $methodName }}</h5>
                    <span class="badge bg-label-{{ $themeClass }}">
                        <i class="bx {{ $iconClass }} me-1"></i> Finalis
                    </span>
                </div>

                <div class="nav-align-top">
                    <ul class="nav nav-tabs" role="tablist">
                        @foreach ($tingkatList as $index => $tingkat)
                            <li class="nav-item" role="presentation">
                                <button type="button"
                                    class="nav-link {{ $index === 0 ? 'active' : '' }}"
                                    role="tab"
                                    data-bs-toggle="tab"
                                    data-bs-target="#tab-finalis-{{ strtolower($tingkat) }}"
                                    aria-controls="tab-finalis-{{ strtolower($tingkat) }}"
                                    aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                                    Kelas {{ $tingkat }}
                                </button>
                            </li>
                        @endforeach
                    </ul>

                    <div class="tab-content p-0">
                        @foreach ($tingkatList as $index => $tingkat)
                            @php
                                $hasilList = $hasilByTingkat->get($tingkat, collect());
                            @endphp
                            <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}"
                                id="tab-finalis-{{ strtolower($tingkat) }}"
                                role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-center">Rank</th>
                                                <th>NISN / Nama Siswa</th>
                                                <th class="text-center">Kelas</th>
                                                <th class="text-center">Asal Rank Kelas</th>
                                                <th class="text-center bg-label-{{ $themeClass }}">Skor {{ $methodName }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($hasilList as $item)
                                                <tr>
                                                    <td class="text-center">
                                                        <span class="badge {{ $item->rank == 1 ? 'bg-warning' : 'bg-label-dark' }}">
                                                            {{ $item->rank }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <strong>{{ $item->siswa->nisn }}</strong><br>
                                                        <small>{{ $item->siswa->nama_siswa }}</small>
                                                    </td>
                                                    <td class="text-center">
                                                        @if ($item->siswa->kelas)
                                                            <span class="badge bg-label-info">{{ $item->siswa->kelas->nama_kelas }}</span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-label-secondary">{{ $item->source_rank }}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <strong class="text-{{ $themeClass }}">{{ number_format($item->skor, 4) }}</strong>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center">Belum ada hasil 10 besar {{ $methodName }} untuk Kelas {{ $tingkat }}</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bx {{ $iconClass }} bx-lg text-muted mb-3 d-block"></i>
                    <h5>Belum Ada Hasil 10 Besar {{ $methodName }}</h5>
                    <p class="text-muted">
                        @if ($filterTA)
                            Klik tombol "Hitung Sekarang" untuk mengambil 3 besar tiap kelas dan menghitung finalis per tingkat.
                        @else
                            Pilih tahun ajaran untuk melihat hasil 10 besar.
                        @endif
                    </p>
                </div>
            </div>
        @endif
    </div>
@endsection
