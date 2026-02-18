@extends('layouts.walikelas')
@section('title', 'Penilaian Siswa')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('walikelas.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Penilaian Siswa - {{ $kelas->nama_kelas }}</li>
                </ol>
            </nav>
            <button type="button" class="btn btn-sm btn-success" id="btn-aggregate">
                <i class="bx bx-refresh"></i> Agregasi dari Rapor
            </button>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">{{ session('success') }}<button type="button"
                    class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible" role="alert">{{ session('error') }}<button type="button"
                    class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        <!-- Hidden aggregate form -->
        <form id="aggregate-form" action="{{ route('walikelas.penilaian.aggregate') }}" method="POST" class="d-none">
            @csrf
            <input type="hidden" name="id_ta" id="aggregate_id_ta">
        </form>

        <div class="card mb-4">
            <h5 class="card-header">Filter</h5>
            <div class="card-body">
                <form action="{{ route('walikelas.penilaian.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-6">
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
                                        {{ $ta->tahun_ajaran }} - {{ $ta->semester }}
                                        {{ $ta->is_active ? '(Aktif)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mt-3 text-end">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary"><i class="bx bx-search"></i> Cari</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <h5 class="card-header">Data Penilaian Siswa</h5>
            <div class="table-responsive text-nowrap">
                <table class="table table-sm">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>NISN / Nama</th>
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
                                <td><strong>{{ $siswa->nisn }}</strong><br><small>{{ $siswa->nama_siswa }}</small></td>
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
                                        <a href="{{ route('walikelas.penilaian.show', ['penilaian' => $siswa->id_siswa, 'ta' => $filterTA ?: $siswa->id_ta]) }}"
                                            class="btn btn-sm btn-icon btn-label-info" title="Detail"><i
                                                class="bx bx-show"></i></a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 2 + $kriteriaList->count() + 1 }}" class="text-center">Tidak ada data siswa
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
