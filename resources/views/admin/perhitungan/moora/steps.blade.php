@extends('layouts.admin')
@section('title', 'Langkah Perhitungan MOORA')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.perhitungan.moora.index') }}">Perhitungan MOORA</a>
                </li>
                <li class="breadcrumb-item active">Langkah MOORA</li>
            </ol>
        </nav>

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    Langkah Perhitungan Metode MOORA
                    <small class="text-muted">- {{ $tahunAjaran->tahun_ajaran }} {{ $tahunAjaran->semester }}</small>
                </h5>
                <a href="{{ route('admin.perhitungan.moora.index', ['tahun_ajaran' => $tahunAjaran->id_ta, 'kelas' => $selectedKelasIds ?? []]) }}"
                    class="btn btn-sm btn-secondary">
                    <i class="bx bx-arrow-back"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <strong>Metode MOORA (Multi-Objective Optimization on the basis of Ratio Analysis)</strong>
                    <ol class="mb-0 mt-2">
                        <li>Normalisasi vektor: xij / √(Σ xij²)</li>
                        <li>Kalikan nilai ternormalisasi dengan bobot kriteria</li>
                        <li>Hitung Yi = Σ vij(benefit) - Σ vij(cost)</li>
                        <li>Ranking berdasarkan Yi tertinggi</li>
                    </ol>
                </div>
            </div>
        </div>

        {{-- Langkah 1: Matrix Nilai Konversi --}}
        <div class="card mb-4">
            <h5 class="card-header bg-label-primary">Langkah 1: Matrix Nilai Konversi</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">No</th>
                            <th>Alternatif (Siswa)</th>
                            @foreach ($kriteriaList as $kriteria)
                                <th class="text-center">
                                    {{ $kriteria->kode_kriteria }}<br>
                                    <small class="badge bg-label-{{ $kriteria->jenis_kriteria == 'Benefit' ? 'success' : 'warning' }}">
                                        {{ $kriteria->jenis_kriteria }}
                                    </small>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($step1Steps as $index => $step)
                            <tr>
                                <td class="text-center">{{ $step1Steps->firstItem() + $index }}</td>
                                <td><strong>{{ $step['steps']['nama'] }}</strong></td>
                                @foreach ($step['steps']['kriteria_details'] as $detail)
                                    <td class="text-center">{{ $detail['nilai_konversi'] }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($step1Steps->hasPages())
                <div class="card-footer">
                    @include('admin.perhitungan._pagination', ['paginator' => $step1Steps])
                </div>
            @endif
        </div>

        {{-- Langkah 2: Matrix Normalisasi --}}
        <div class="card mb-4">
            <h5 class="card-header bg-label-info">
                Langkah 2: Matrix Normalisasi
                <small>(nij = xij / √(Σ xij²))</small>
            </h5>
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">No</th>
                            <th>Alternatif (Siswa)</th>
                            @foreach ($kriteriaList as $kriteria)
                                <th class="text-center">{{ $kriteria->kode_kriteria }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($step2Steps as $index => $step)
                            <tr>
                                <td class="text-center">{{ $step2Steps->firstItem() + $index }}</td>
                                <td><strong>{{ $step['steps']['nama'] }}</strong></td>
                                @foreach ($step['steps']['kriteria_details'] as $detail)
                                    <td class="text-center">{{ $detail['normalized'] }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($step2Steps->hasPages())
                <div class="card-footer">
                    @include('admin.perhitungan._pagination', ['paginator' => $step2Steps])
                </div>
            @endif
        </div>

        {{-- Langkah 3: Matrix Pembobotan --}}
        <div class="card mb-4">
            <h5 class="card-header bg-label-warning">
                Langkah 3: Matrix Pembobotan
                <small>(Normalized × Weight)</small>
            </h5>
            <div class="card-body pb-2">
                <p class="mb-2"><strong>Bobot Kriteria:</strong></p>
                <div class="d-flex gap-2 flex-wrap mb-3">
                    @foreach ($kriteriaList as $kriteria)
                        <span class="badge bg-label-secondary">
                            {{ $kriteria->kode_kriteria }}: {{ $kriteria->bobot }}
                        </span>
                    @endforeach
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">No</th>
                            <th>Alternatif (Siswa)</th>
                            @foreach ($kriteriaList as $kriteria)
                                <th class="text-center">{{ $kriteria->kode_kriteria }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($step3Steps as $index => $step)
                            <tr>
                                <td class="text-center">{{ $step3Steps->firstItem() + $index }}</td>
                                <td><strong>{{ $step['steps']['nama'] }}</strong></td>
                                @foreach ($step['steps']['kriteria_details'] as $detail)
                                    <td class="text-center">
                                        <span class="text-{{ $detail['type_sum'] == 'benefit' ? 'success' : 'danger' }}">
                                            {{ $detail['weighted'] }}
                                        </span>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($step3Steps->hasPages())
                <div class="card-footer">
                    @include('admin.perhitungan._pagination', ['paginator' => $step3Steps])
                </div>
            @endif
        </div>

        {{-- Langkah 4: Perhitungan Skor Akhir & Ranking --}}
        <div class="card mb-4">
            <h5 class="card-header bg-label-success">Langkah 4: Perhitungan Skor Akhir &amp; Ranking</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">No</th>
                            <th>Alternatif (Siswa)</th>
                            <th class="text-center">Σ Benefit</th>
                            <th class="text-center">Σ Cost</th>
                            <th class="text-center">Yi (Benefit - Cost)</th>
                            <th class="text-center">Ranking</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($step4Steps as $index => $step)
                            <tr>
                                <td class="text-center">{{ $step4Steps->firstItem() + $index }}</td>
                                <td><strong>{{ $step['steps']['nama'] }}</strong></td>
                                <td class="text-center text-success">{{ $step['steps']['benefit_sum'] }}</td>
                                <td class="text-center text-danger">{{ $step['steps']['cost_sum'] }}</td>
                                <td class="text-center">
                                    <strong class="text-success">{{ $step['steps']['skor_moora'] }}</strong>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success">{{ $step['rank_moora'] }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($step4Steps->hasPages())
                <div class="card-footer">
                    @include('admin.perhitungan._pagination', ['paginator' => $step4Steps])
                </div>
            @endif
        </div>

        <div class="card">
            <div class="card-body text-center">
                <a href="{{ route('admin.perhitungan.moora.index', ['tahun_ajaran' => $tahunAjaran->id_ta, 'kelas' => $selectedKelasIds ?? []]) }}"
                    class="btn btn-secondary">
                    <i class="bx bx-arrow-back"></i> Kembali ke Hasil MOORA
                </a>
            </div>
        </div>
    </div>
@endsection
