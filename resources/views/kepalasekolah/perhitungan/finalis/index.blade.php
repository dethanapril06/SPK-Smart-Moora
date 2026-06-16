@extends('layouts.kepalasekolah')

@php
    $methodName = strtoupper($method);
    $isSmart = $method === 'smart';
    $themeClass = $isSmart ? 'primary' : 'success';
    $tingkatList = ['X', 'XI', 'XII'];
@endphp

@section('title', 'Finalis ' . $methodName)

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('kepalasekolah.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Finalis {{ $methodName }}</li>
                </ol>
            </nav>
            <div class="btn-group">
                <a href="{{ route('kepalasekolah.perhitungan.finalis.smart.index', ['tahun_ajaran' => $filterTA]) }}"
                    class="btn btn-sm btn-{{ $isSmart ? 'primary' : 'outline-primary' }}">SMART</a>
                <a href="{{ route('kepalasekolah.perhitungan.finalis.moora.index', ['tahun_ajaran' => $filterTA]) }}"
                    class="btn btn-sm btn-{{ $isSmart ? 'outline-success' : 'success' }}">MOORA</a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Filter Finalis {{ $methodName }}</h5>
                <a href="{{ route('kepalasekolah.perhitungan.smart.index') }}" class="btn btn-sm btn-secondary">
                    <i class="bx bx-arrow-back"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route("kepalasekolah.perhitungan.finalis.{$method}.index") }}" method="GET">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-8">
                            <label class="form-label" for="tahun_ajaran">Tahun Ajaran</label>
                            <select class="form-select" id="tahun_ajaran" name="tahun_ajaran" required>
                                @foreach ($tahunAjaranList as $ta)
                                    <option value="{{ $ta->id_ta }}" {{ $filterTA == $ta->id_ta ? 'selected' : '' }}>
                                        {{ $ta->tahun_ajaran }} - {{ $ta->semester }} {{ $ta->is_active ? '(Aktif)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 d-grid">
                            <button type="submit" class="btn btn-{{ $themeClass }}">
                                <i class="bx bx-search"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if ($hasCalculation)
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="card-title m-0"><i class="bx bx-medal text-warning"></i> Finalis {{ $methodName }}</h5>
                    <div class="btn-group">
                        <a href="{{ route('kepalasekolah.report.finalis.pdf', ['method' => $method, 'tahun_ajaran' => $filterTA]) }}"
                            class="btn btn-sm btn-danger" target="_blank">
                            <i class="bx bxs-file-pdf"></i> Export PDF
                        </a>
                        <a href="{{ route('kepalasekolah.report.finalis.excel', ['method' => $method, 'tahun_ajaran' => $filterTA]) }}"
                            class="btn btn-sm btn-success">
                            <i class="bx bxs-file-export"></i> Export Excel
                        </a>
                    </div>
                </div>

                <div class="nav-align-top">
                    <ul class="nav nav-tabs" role="tablist">
                        @foreach ($tingkatList as $index => $tingkat)
                            <li class="nav-item" role="presentation">
                                <button type="button" class="nav-link {{ $index === 0 ? 'active' : '' }}" role="tab"
                                    data-bs-toggle="tab" data-bs-target="#finalis-{{ strtolower($tingkat) }}">
                                    Kelas {{ $tingkat }}
                                </button>
                            </li>
                        @endforeach
                    </ul>
                    <div class="tab-content p-0">
                        @foreach ($tingkatList as $index => $tingkat)
                            @php($hasilList = $hasilByTingkat->get($tingkat, collect()))
                            <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}"
                                id="finalis-{{ strtolower($tingkat) }}" role="tabpanel">
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
                                                    <td class="text-center"><span class="badge {{ $item->rank == 1 ? 'bg-warning' : 'bg-label-dark' }}">{{ $item->rank }}</span></td>
                                                    <td><strong>{{ $item->siswa->nisn }}</strong><br><small>{{ $item->siswa->nama_siswa }}</small></td>
                                                    <td class="text-center"><span class="badge bg-label-info">{{ $item->siswa->kelas->nama_kelas ?? '-' }}</span></td>
                                                    <td class="text-center"><span class="badge bg-label-secondary">{{ $item->source_rank }}</span></td>
                                                    <td class="text-center"><strong class="text-{{ $themeClass }}">{{ number_format($item->skor, 4) }}</strong></td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="5" class="text-center">Belum ada finalis Kelas {{ $tingkat }}.</td></tr>
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
                    <i class="bx bx-medal bx-lg text-muted mb-3 d-block"></i>
                    <h5>Belum Ada Finalis {{ $methodName }}</h5>
                    <p class="text-muted">Admin belum menghitung finalis untuk tahun ajaran yang dipilih.</p>
                </div>
            </div>
        @endif
    </div>
@endsection
