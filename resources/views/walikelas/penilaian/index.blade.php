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
            <a href="{{ route('walikelas.penilaian.create') }}" class="btn btn-sm btn-primary"><i class="bx bx-plus"></i></a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">{{ session('success') }}<button type="button"
                    class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible" role="alert">{{ session('error') }}<button type="button"
                    class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

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
                        <div class="col-md-4">
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
                                    <div class="btn-group btn-group-sm" role="group">
                                        @if ($penilaianBySiswa->count() > 0)
                                            <a href="{{ route('walikelas.penilaian.show', ['penilaian' => $siswa->id_siswa, 'ta' => $filterTA ?: $siswa->id_ta]) }}"
                                                class="btn btn-sm btn-icon btn-label-info" title="Detail"><i
                                                    class="bx bx-show"></i></a>
                                            <a href="{{ route('walikelas.penilaian.edit', ['penilaian' => $siswa->id_siswa, 'ta' => $filterTA ?: $siswa->id_ta]) }}"
                                                class="btn btn-sm btn-icon btn-label-primary" title="Edit"><i
                                                    class="bx bx-edit"></i></a>
                                            <button type="button" class="btn btn-sm btn-icon btn-label-danger btn-delete"
                                                data-id="{{ $siswa->id_siswa }}"
                                                data-ta="{{ $filterTA ?: $siswa->id_ta }}"
                                                data-name="{{ $siswa->nama_siswa }}" title="Hapus"><i
                                                    class="bx bx-trash"></i></button>
                                            <form id="delete-form-{{ $siswa->id_siswa }}"
                                                action="{{ route('walikelas.penilaian.destroy', ['penilaian' => $siswa->id_siswa, 'ta' => $filterTA ?: $siswa->id_ta]) }}"
                                                method="POST" class="d-none">@csrf @method('DELETE')</form>
                                        @else
                                            <a href="{{ route('walikelas.penilaian.create', ['siswa' => $siswa->id_siswa, 'ta' => $filterTA ?: $siswa->id_ta]) }}"
                                                class="btn btn-sm btn-label-success"><i class="bx bx-plus"></i> Nilai</a>
                                        @endif
                                    </div>
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

    @push('scripts')
        <script>
            document.querySelectorAll('.btn-delete').forEach(button => {
                button.addEventListener('click', function() {
                    const itemId = this.dataset.id;
                    const itemName = this.dataset.name;
                    Swal.fire({
                        title: 'Hapus Penilaian?',
                        html: `Yakin ingin menghapus semua penilaian siswa <strong>${itemName}</strong>?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) document.getElementById(`delete-form-${itemId}`)
                        .submit();
                    });
                });
            });
        </script>
    @endpush
@endsection
