@extends('layouts.kepalasekolah')
@section('title', 'Penilaian Siswa')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('kepalasekolah.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Penilaian Siswa</li>
                </ol>
            </nav>
        </div>

        <!-- Filter Form -->
        <div class="card mb-4">
            <h5 class="card-header">Filter</h5>
            <div class="card-body">
                <form action="{{ route('kepalasekolah.penilaian.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label" for="search">Cari Siswa</label>
                            <input type="text" class="form-control" id="search" name="search"
                                placeholder="NISN atau nama siswa..." value="{{ $search }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="tahun_ajaran">Tahun Ajaran</label>
                            <select class="form-select" id="tahun_ajaran" name="tahun_ajaran">
                                <option value="">Semua Tahun Ajaran</option>
                                @foreach ($tahunAjaranList as $ta)
                                    <option value="{{ $ta->id_ta }}" {{ $filterTA == $ta->id_ta ? 'selected' : '' }}>
                                        {{ $ta->tahun_ajaran }} - {{ $ta->semester }}
                                        {{ $ta->is_active ? '(Aktif)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
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
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary"><i class="bx bx-search"></i> Cari</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Data Table -->
        <div class="card">
            <h5 class="card-header">Data Penilaian Siswa</h5>
            <div class="table-responsive text-nowrap">
                <table class="table table-sm">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>NISN / Nama</th>
                            <th>Kelas</th>
                            @foreach ($kriteriaList as $kriteria)
                                <th class="text-center" style="min-width: 80px;">
                                    {{ $kriteria->kode_kriteria }}
                                    @if ($kriteria->kode_kriteria == 'C5')
                                        <i class="bx bx-bolt text-warning" title="Auto"></i>
                                    @endif
                                </th>
                            @endforeach
                            <th class="text-center" style="width: 80px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswaList as $siswa)
                            @php
                                $penilaianBySiswa = $siswa->penilaian
                                    ->where('id_ta', $filterTA ?: $siswa->id_ta)
                                    ->keyBy('id_kriteria');
                            @endphp
                            <tr>
                                <td>{{ ($siswaList->currentPage() - 1) * $siswaList->perPage() + $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $siswa->nisn }}</strong><br>
                                    <small>{{ $siswa->nama_siswa }}</small>
                                </td>
                                <td>
                                    @if ($siswa->kelas)
                                        <span class="badge bg-label-info">{{ $siswa->kelas->nama_kelas }}</span>
                                    @endif
                                </td>
                                @foreach ($kriteriaList as $kriteria)
                                    <td class="text-center">
                                        @if (isset($penilaianBySiswa[$kriteria->id_kriteria]))
                                            <span class="badge bg-label-success"
                                                title="Nilai Asli: {{ $penilaianBySiswa[$kriteria->id_kriteria]->nilai_asli }}">
                                                {{ $penilaianBySiswa[$kriteria->id_kriteria]->nilai_konversi ?? 'N/A' }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="text-center">
                                    @if ($penilaianBySiswa->count() > 0)
                                        <a href="{{ route('kepalasekolah.penilaian.show', ['penilaian' => $siswa->id_siswa, 'ta' => $filterTA ?: $siswa->id_ta]) }}"
                                            class="btn btn-sm btn-icon btn-label-info" title="Detail">
                                            <i class="bx bx-show"></i>
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 3 + $kriteriaList->count() + 1 }}" class="text-center">
                                    Tidak ada data siswa
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($siswaList->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Menampilkan {{ $siswaList->firstItem() }} - {{ $siswaList->lastItem() }} dari
                            {{ $siswaList->total() }} data
                        </div>
                        <nav aria-label="Page navigation">
                            <ul class="pagination pagination-sm mb-0">
                                @if ($siswaList->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link"><i class="tf-icon bx bx-chevrons-left"></i></span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $siswaList->url(1) }}"><i
                                                class="tf-icon bx bx-chevrons-left"></i></a>
                                    </li>
                                @endif

                                @if ($siswaList->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link"><i class="tf-icon bx bx-chevron-left"></i></span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $siswaList->previousPageUrl() }}"><i
                                                class="tf-icon bx bx-chevron-left"></i></a>
                                    </li>
                                @endif

                                @foreach ($siswaList->getUrlRange(max(1, $siswaList->currentPage() - 2), min($siswaList->lastPage(), $siswaList->currentPage() + 2)) as $page => $url)
                                    @if ($page == $siswaList->currentPage())
                                        <li class="page-item active"><span class="page-link">{{ $page }}</span>
                                        </li>
                                    @else
                                        <li class="page-item"><a class="page-link"
                                                href="{{ $url }}">{{ $page }}</a></li>
                                    @endif
                                @endforeach

                                @if ($siswaList->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $siswaList->nextPageUrl() }}"><i
                                                class="tf-icon bx bx-chevron-right"></i></a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link"><i class="tf-icon bx bx-chevron-right"></i></span>
                                    </li>
                                @endif

                                @if ($siswaList->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $siswaList->url($siswaList->lastPage()) }}"><i
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
                </div>
            @endif
        </div>
    </div>
@endsection
