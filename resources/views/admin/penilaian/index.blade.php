@extends('layouts.admin')
@section('title', 'Penilaian Siswa')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Penilaian Siswa</li>
                </ol>
            </nav>
            <button type="button" class="btn btn-sm btn-success" id="btn-aggregate">
                <i class="bx bx-refresh me-1"></i> Agregasi dari Rapor
            </button>
        </div>

        <div class="alert alert-info d-flex" role="alert">
            <i class="bx bx-info-circle me-2 mt-1"></i>
            <div>
                Jika hasil penilaian belum sesuai, silakan lakukan <strong>Agregasi dari Rapor</strong> ulang agar nilai
                C1-C6 diperbarui berdasarkan data terbaru.
            </div>
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

        <!-- Filter Form -->
        <div class="card mb-4">
            <h5 class="card-header">Filter</h5>
            <div class="card-body">
                <form action="{{ route('admin.penilaian.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label" for="search">Cari Siswa</label>
                            <input type="text" class="form-control" id="search" name="search"
                                placeholder="NISN atau nama siswa..." value="{{ $search }}">
                        </div>
                        <div class="col-md-4 mt-3">
                            <label class="form-label" for="tahun_ajaran">Tahun Ajaran</label>
                            <select class="form-select" id="tahun_ajaran" name="tahun_ajaran">
                                <option value="">Semua Tahun Ajaran</option>
                                @foreach ($tahunAjaranList as $ta)
                                    <option value="{{ $ta->id_ta }}" {{ $filterTA == $ta->id_ta ? 'selected' : '' }}>
                                        {{ $ta->tahun_ajaran }} - {{ $ta->semester }} {{ $ta->is_active ? '(Aktif)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mt-3">
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
                    </div>
                    <div class="mt-3 text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-search"></i> Cari
                        </button>
                        <a href="{{ route('admin.penilaian.index') }}" class="btn btn-label-secondary">
                            <i class="bx bx-x"></i> Reset
                        </a>
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
                            <th class="text-center" style="width: 120px;">Aksi</th>
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
                                            @php
                                                $nilai = $penilaianBySiswa[$kriteria->id_kriteria]->nilai_konversi;
                                            @endphp
                                            <span class="badge bg-label-success"
                                                title="Nilai Asli: {{ $penilaianBySiswa[$kriteria->id_kriteria]->nilai_asli }}">
                                                {{ $nilai ?? 'N/A' }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        @if ($penilaianBySiswa->count() > 0)
                                            <a href="{{ route('admin.penilaian.show', ['penilaian' => $siswa->id_siswa, 'ta' => $filterTA ?: $siswa->id_ta]) }}"
                                                class="btn btn-sm btn-icon btn-label-info" title="Detail">
                                                <i class="bx bx-show"></i>
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </div>
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
                                {{-- First Page Link --}}
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

                                {{-- Previous Page Link --}}
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

                                {{-- Pagination Elements --}}
                                @foreach ($siswaList->getUrlRange(max(1, $siswaList->currentPage() - 2), min($siswaList->lastPage(), $siswaList->currentPage() + 2)) as $page => $url)
                                    @if ($page == $siswaList->currentPage())
                                        <li class="page-item active"><span class="page-link">{{ $page }}</span>
                                        </li>
                                    @else
                                        <li class="page-item"><a class="page-link"
                                                href="{{ $url }}">{{ $page }}</a></li>
                                    @endif
                                @endforeach

                                {{-- Next Page Link --}}
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

                                {{-- Last Page Link --}}
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

    <!-- Hidden form for aggregate -->
    <form id="aggregate-form" action="{{ route('admin.penilaian.aggregate') }}" method="POST" class="d-none">
        @csrf
        <input type="hidden" name="id_ta" id="aggregate_id_ta">
    </form>

    @push('scripts')
        <script>
            // SweetAlert untuk Agregasi
            document.getElementById('btn-aggregate').addEventListener('click', function() {
                let taOptions = '';
                @foreach ($tahunAjaranList as $ta)
                    taOptions +=
                        '<option value="{{ $ta->id_ta }}">{{ $ta->tahun_ajaran }} - Semester {{ $ta->semester }} {{ $ta->is_active ? '(Aktif)' : '' }}</option>';
                @endforeach

                Swal.fire({
                    title: 'Agregasi Data Rapor',
                    html: `
                        <p class="text-muted">Hitung otomatis nilai C1-C6 dari data rapor yang telah diinput (Pengetahuan, Keterampilan, Sikap, Ekskul, Pelanggaran, Absensi).</p>
                        <select id="swal-ta" class="form-select">
                            <option value="">-- Pilih Tahun Ajaran --</option>
                            ${taOptions}
                        </select>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: '<i class="bx bx-refresh"></i> Proses Agregasi',
                    cancelButtonText: 'Batal',
                    preConfirm: () => {
                        const selected = document.getElementById('swal-ta').value;
                        if (!selected) {
                            Swal.showValidationMessage('Pilih tahun ajaran terlebih dahulu');
                        }
                        return selected;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('aggregate_id_ta').value = result.value;
                        document.getElementById('aggregate-form').submit();
                    }
                });
            });
        </script>
    @endpush
@endsection
