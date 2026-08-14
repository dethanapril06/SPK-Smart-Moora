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
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0">
                    Langkah Perhitungan Metode MOORA (Per Kelas)
                    <small class="text-muted">- {{ $tahunAjaran->tahun_ajaran }}</small>
                </h5>
                <a href="{{ route('admin.perhitungan.moora.index', ['tahun_ajaran' => $tahunAjaran->id_ta, 'semester' => $id_semester, 'kelas' => $selectedKelasIds ?? []]) }}"
                    class="btn btn-sm btn-secondary">
                    <i class="bx bx-arrow-back"></i> Kembali ke Hasil
                </a>
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-3">
                    <strong>Metode MOORA (Multi-Objective Optimization on the basis of Ratio Analysis)</strong>
                    <ol class="mb-0 mt-2">
                        <li>Normalisasi vektor dalam kelas: xij / √(Σ xij²)</li>
                        <li>Kalikan nilai ternormalisasi dengan bobot kriteria terstandarisasi</li>
                        <li>Hitung nilai optimasi Yi = Σ vij(benefit) - Σ vij(cost)</li>
                        <li>Ranking berdasarkan nilai Yi tertinggi dalam kelas</li>
                    </ol>
                </div>

                {{-- Class Selector Form --}}
                <form action="{{ route('admin.perhitungan.moora.steps', $tahunAjaran->id_ta) }}" method="GET" class="row g-3 align-items-center">
                    <input type="hidden" name="semester" value="{{ $id_semester }}">
                    @foreach ($selectedKelasIds ?? [] as $kId)
                        <input type="hidden" name="kelas[]" value="{{ $kId }}">
                    @endforeach
                    <div class="col-auto">
                        <label class="col-form-label fw-semibold" for="id_kelas">
                            <i class="bx bx-chalkboard me-1"></i> Pilih Kelas yang Dianalisis:
                        </label>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" id="id_kelas" name="id_kelas" onchange="this.form.submit()">
                            @foreach ($eligibleKelasList as $kelas)
                                <option value="{{ $kelas->id_kelas }}" {{ $activeKelasId == $kelas->id_kelas ? 'selected' : '' }}>
                                    {{ $kelas->nama_kelas }} ({{ $kelas->tingkat ? 'Tingkat ' . $kelas->tingkat : '' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @if ($activeKelas)
                        <div class="col-auto">
                            <span class="badge bg-label-success fs-6">
                                <i class="bx bx-check-circle me-1"></i> Menampilkan: {{ $activeKelas->nama_kelas }}
                            </span>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        @if ($step1Steps->total() > 0)
            {{-- Langkah 1: Matrix Nilai Konversi --}}
            <div class="card mb-4">
                <h5 class="card-header bg-label-primary">Langkah 1: Matrix Nilai Konversi (Kelas {{ $activeKelas->nama_kelas ?? '' }})</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 50px;">No</th>
                                <th>Alternatif (Siswa)</th>
                                @foreach ($kriteriaList as $kriteria)
                                    <th class="text-center">
                                        {{ $kriteria->kode_kriteria }}<br>
                                        <small
                                            class="badge bg-label-{{ $kriteria->jenis_kriteria == 'Benefit' ? 'success' : 'warning' }}">
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
                    Langkah 2: Matrix Normalisasi (Kelas {{ $activeKelas->nama_kelas ?? '' }})
                    <small class="d-block mt-1">(nij = xij / √(Σ xij²))</small>
                </h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 50px;">No</th>
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
                    Langkah 3: Matrix Pembobotan (Normalized × Weight)
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
                                <th class="text-center" style="width: 50px;">No</th>
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
                                        <td class="text-center">{{ $detail['weighted'] }}</td>
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

            {{-- Langkah 4: Perhitungan Nilai Yi & Ranking --}}
            <div class="card mb-4">
                <h5 class="card-header bg-label-success">Langkah 4: Perhitungan Nilai Yi (Benefit - Cost) &amp; Ranking Kelas {{ $activeKelas->nama_kelas ?? '' }}</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 50px;">No</th>
                                <th>Alternatif (Siswa)</th>
                                <th class="text-center">Σ Benefit</th>
                                <th class="text-center">Σ Cost</th>
                                <th class="text-center">Nilai Yi (MOORA)</th>
                                <th class="text-center">Rank di Kelas</th>
                                <th class="text-center">Status Finalis</th>
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
                                        <strong class="text-primary">{{ $step['steps']['skor_moora'] }}</strong>
                                    </td>
                                    <td class="text-center">
                                        @if ($step['rank_moora'] == 1)
                                            <span class="badge bg-warning text-dark"><i class="bx bx-trophy"></i> 1</span>
                                        @elseif ($step['rank_moora'] == 2)
                                            <span class="badge bg-secondary text-white"><i class="bx bx-medal"></i> 2</span>
                                        @elseif ($step['rank_moora'] == 3)
                                            <span class="badge bg-info text-white"><i class="bx bx-medal"></i> 3</span>
                                        @else
                                            <span class="badge bg-label-dark">{{ $step['rank_moora'] }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($step['rank_moora'] <= 3)
                                            <span class="badge bg-label-warning"><i class="bx bx-star"></i> 3 Besar Kelas</span>
                                        @else
                                            <span class="badge bg-label-secondary">Reguler</span>
                                        @endif
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
        @else
            <div class="card mb-4">
                <div class="card-body text-center py-5">
                    <i class="bx bx-error-circle bx-lg text-muted mb-3 d-block"></i>
                    <h5>Tidak Ada Data Penilaian untuk Kelas Terpilih</h5>
                    <p class="text-muted">Silakan pilih kelas lain atau lengkapi data penilaian siswa terlebih dahulu.</p>
                </div>
            </div>
        @endif

        <div class="card">
            <div class="card-body text-center d-flex justify-content-center gap-2">
                <a href="{{ route('admin.perhitungan.moora.index', ['tahun_ajaran' => $tahunAjaran->id_ta, 'semester' => $id_semester, 'kelas' => $selectedKelasIds ?? []]) }}"
                    class="btn btn-secondary">
                    <i class="bx bx-arrow-back"></i> Kembali ke Hasil MOORA
                </a>
                <a href="{{ route('admin.perhitungan.finalis.moora.index', ['tahun_ajaran' => $tahunAjaran->id_ta, 'semester' => $id_semester]) }}" class="btn btn-success">
                    <i class="tf-icons bx bx-medal"></i> 10 Besar MOORA
                </a>
            </div>
        </div>
    </div>
@endsection
