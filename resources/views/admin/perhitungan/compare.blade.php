@extends('layouts.admin')
@section('title', 'Perbandingan SMART vs MOORA')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.perhitungan.index') }}">Perhitungan</a>
                </li>
                <li class="breadcrumb-item active">Perbandingan</li>
            </ol>
        </nav>

        <div class="card mb-4">
            <h5 class="card-header">
                Perbandingan Metode SMART & MOORA
                <small class="text-muted">- {{ $tahunAjaran->tahun_ajaran }} {{ $tahunAjaran->semester }}</small>
            </h5>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card bg-label-success">
                            <div class="card-body text-center">
                                <h2 class="mb-1">{{ $agreementPercentage }}%</h2>
                                <p class="mb-0">Tingkat Kesepakatan</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <p><strong>Analisis:</strong></p>
                        <ul class="mb-0">
                            @if ($agreementPercentage >= 70)
                                <li>Metode SMART dan MOORA memiliki <strong>kesepakatan tinggi</strong>
                                    ({{ $agreementPercentage }}%)</li>
                                <li>Hasil ranking cukup konsisten antar kedua metode</li>
                            @elseif($agreementPercentage >= 40)
                                <li>Metode SMART dan MOORA memiliki <strong>kesepakatan sedang</strong>
                                    ({{ $agreementPercentage }}%)</li>
                                <li>Terdapat perbedaan hasil ranking yang perlu dipertimbangkan</li>
                            @else
                                <li>Metode SMART dan MOORA memiliki <strong>kesepakatan rendah</strong>
                                    ({{ $agreementPercentage }}%)</li>
                                <li>Hasil ranking sangat berbeda, analisis lebih lanjut diperlukan</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <h5 class="card-header">Detail Perbandingan Ranking</h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>NISN / Nama</th>
                            <th class="text-center">SMART<br><small>(Skor / Rank)</small></th>
                            <th class="text-center">MOORA<br><small>(Skor / Rank)</small></th>
                            <th class="text-center">Perbedaan<br>Ranking</th>
                            {{-- <th class="text-center">Status</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($hasilList as $item)
                            @php
                                $rankDiff = abs($item->rank_smart - $item->rank_moora);
                            @endphp
                            <tr>
                                <td>{{ ($hasilList->currentPage() - 1) * $hasilList->perPage() + $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $item->siswa->nisn }}</strong><br>
                                    <small>{{ $item->siswa->nama_siswa }}</small>
                                </td>
                                <td class="text-center">
                                    <strong class="text-primary">{{ number_format($item->skor_smart, 4) }}</strong><br>
                                    @if ($item->rank_smart <= 3)
                                        <span class="badge bg-warning">🏆 #{{ $item->rank_smart }}</span>
                                    @else
                                        <span class="badge bg-label-primary">#{{ $item->rank_smart }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <strong class="text-success">{{ number_format($item->skor_moora, 4) }}</strong><br>
                                    @if ($item->rank_moora <= 3)
                                        <span class="badge bg-warning">🏆 #{{ $item->rank_moora }}</span>
                                    @else
                                        <span class="badge bg-label-success">#{{ $item->rank_moora }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($rankDiff == 0)
                                        <span class="badge bg-label-success">0</span>
                                    @elseif($rankDiff <= 2)
                                        <span class="badge bg-label-warning">{{ $rankDiff }}</span>
                                    @else
                                        <span class="badge bg-label-danger">{{ $rankDiff }}</span>
                                    @endif
                                </td>
                                {{-- <td class="text-center">
                                    @if ($item->rank_smart == $item->rank_moora)
                                        <i class="bx bx-check-circle text-success bx-sm" title="Ranking sama"></i>
                                    @elseif($rankDiff <= 2)
                                        <i class="bx bx-error-circle text-warning bx-sm" title="Perbedaan kecil"></i>
                                    @else
                                        <i class="bx bx-x-circle text-danger bx-sm" title="Perbedaan besar"></i>
                                    @endif
                                </td> --}}
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-footer">
                @if ($hasilList->hasPages())
                    <div class="d-flex justify-content-between align-items-center mb-3">
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
                                        <li class="page-item active"><span class="page-link">{{ $page }}</span>
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
                                        <a class="page-link" href="{{ $hasilList->url($hasilList->lastPage()) }}"><i
                                                class="tf-icon bx bx-chevrons-right"></i></a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link"><i class="tf-icon bx bx-chevrons-right"></i></span>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                @endif

                <div class="text-end">
                    <a href="{{ route('admin.perhitungan.index', ['tahun_ajaran' => $tahunAjaran->id_ta]) }}"
                        class="btn btn-secondary">
                        <i class="bx bx-arrow-back"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
