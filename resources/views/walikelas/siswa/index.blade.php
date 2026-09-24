@extends('layouts.walikelas')
@section('title', 'Data Siswa')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('walikelas.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Data Siswa - {{ $kelas->nama_kelas }}</li>
                </ol>
            </nav>
            <a href="{{ route('walikelas.siswa.create') }}" class="btn btn-sm btn-primary"><i class="bx bx-plus"></i></a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Search & Filter Form -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('walikelas.siswa.index') }}" method="GET">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label" for="tahun_ajaran">Tahun Ajaran</label>
                            <select name="tahun_ajaran" id="tahun_ajaran" class="form-select" onchange="updateSemesterFilter(this.value)">
                                <option value="">Semua Tahun Ajaran</option>
                                @foreach ($tahunAjaranList as $ta)
                                    <option value="{{ $ta->id_ta }}" {{ $filterTA == $ta->id_ta ? 'selected' : '' }}>
                                        {{ $ta->tahun_ajaran }} {{ $ta->is_active ? '(Aktif)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="semester">Semester</label>
                            <select name="semester" id="semester" class="form-select">
                                <option value="">Semua Semester</option>
                                @foreach ($semesterList as $sem)
                                    <option value="{{ $sem->id_semester }}" data-ta="{{ $sem->id_ta }}"
                                        {{ $filterSemester == $sem->id_semester ? 'selected' : '' }}>
                                        {{ $sem->nama_semester }} ({{ $sem->periode_bulan }}) {{ $sem->is_active ? '(Aktif)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="search">Cari Siswa</label>
                            <input type="text" class="form-control" id="search" name="search"
                                placeholder="Cari berdasarkan NISN, nama siswa, atau alamat..." value="{{ $search }}">
                        </div>
                        <div class="col-md-2 text-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bx bx-filter-alt me-1"></i> Filter
                            </button>
                        </div>
                    </div>
                    @if ($search || $filterTA || $filterSemester)
                        <div class="mt-3 d-flex align-items-center gap-2 flex-wrap">
                            <a href="{{ route('walikelas.siswa.index') }}" class="btn btn-sm btn-label-secondary">
                                <i class="bx bx-x"></i> Reset Filter
                            </a>
                            @if ($filterTA)
                                <span class="badge bg-label-primary">
                                    TA: {{ $tahunAjaranList->firstWhere('id_ta', $filterTA)?->tahun_ajaran }}
                                </span>
                            @endif
                            @if ($filterSemester)
                                @php
                                    $currentSemObj = $semesterList->firstWhere('id_semester', $filterSemester);
                                @endphp
                                <span class="badge bg-label-info">
                                    Semester: {{ $currentSemObj?->nama_semester }} ({{ $currentSemObj?->periode_bulan }})
                                </span>
                            @endif
                            @if ($search)
                                <span class="text-muted ms-2">Pencarian: "{{ $search }}"</span>
                            @endif
                        </div>
                    @endif
                </form>
            </div>
        </div>

        <div class="card">
            <h5 class="card-header">Data Siswa - {{ $kelas->nama_kelas }}</h5>
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NISN</th>
                            <th>Nama Siswa</th>
                            <th>JK</th>
                            <th>Tahun Ajaran & Masuk</th>
                            <th style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($siswa as $item)
                            <tr>
                                <td>{{ ($siswa->currentPage() - 1) * $siswa->perPage() + $loop->iteration }}</td>
                                <td><strong>{{ $item->nisn }}</strong></td>
                                <td>
                                    {{ $item->nama_siswa }}
                                    @if ($item->isSiswaBaruGenap())
                                        <span class="badge bg-label-warning ms-1" style="font-size: 0.7rem;" title="Siswa baru masuk di semester Genap">
                                            <i class="bx bx-user-plus"></i> Siswa Baru Genap
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-label-{{ $item->jenis_kelamin == 'L' ? 'primary' : 'danger' }}">
                                        {{ $item->jenis_kelamin }}
                                    </span>
                                </td>
                                <td>
                                    @if ($item->tahunAjaran)
                                        <div><strong>{{ $item->tahunAjaran->tahun_ajaran }}</strong></div>
                                        <small class="text-muted">
                                            Masuk: {{ $item->semester?->nama_semester ?? $item->tahunAjaran->semester }}
                                            ({{ $item->semester?->periode_bulan ?? '-' }})
                                        </small>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item"
                                                href="{{ route('walikelas.siswa.show', $item->id_siswa) }}">
                                                <i class="bx bx-show me-1"></i> Detail
                                            </a>
                                            <a class="dropdown-item"
                                                href="{{ route('walikelas.siswa.edit', $item->id_siswa) }}">
                                                <i class="bx bx-edit-alt me-1"></i> Edit
                                            </a>
                                            <a class="dropdown-item btn-delete" href="javascript:void(0)"
                                                data-id="{{ $item->id_siswa }}" data-name="{{ $item->nama_siswa }}">
                                                <i class="bx bx-trash me-1"></i> Hapus
                                            </a>
                                            <form id="delete-form-{{ $item->id_siswa }}"
                                                action="{{ route('walikelas.siswa.destroy', $item->id_siswa) }}"
                                                method="POST" class="d-none">
                                                @csrf @method('DELETE')
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data siswa</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            @if ($siswa->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Menampilkan {{ $siswa->firstItem() }} - {{ $siswa->lastItem() }} dari
                            {{ $siswa->total() }} data
                        </div>
                        <nav aria-label="Page navigation">
                            <ul class="pagination pagination-sm mb-0">
                                {{-- First Page Link --}}
                                @if ($siswa->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link"><i class="tf-icon bx bx-chevrons-left"></i></span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $siswa->url(1) }}"><i
                                                class="tf-icon bx bx-chevrons-left"></i></a>
                                    </li>
                                @endif

                                {{-- Previous Page Link --}}
                                @if ($siswa->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link"><i class="tf-icon bx bx-chevron-left"></i></span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $siswa->previousPageUrl() }}"><i
                                                class="tf-icon bx bx-chevron-left"></i></a>
                                    </li>
                                @endif

                                {{-- Pagination Elements --}}
                                @foreach ($siswa->getUrlRange(max(1, $siswa->currentPage() - 2), min($siswa->lastPage(), $siswa->currentPage() + 2)) as $page => $url)
                                    @if ($page == $siswa->currentPage())
                                        <li class="page-item active"><span class="page-link">{{ $page }}</span>
                                        </li>
                                    @else
                                        <li class="page-item"><a class="page-link"
                                                href="{{ $url }}">{{ $page }}</a></li>
                                    @endif
                                @endforeach

                                {{-- Next Page Link --}}
                                @if ($siswa->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $siswa->nextPageUrl() }}"><i
                                                class="tf-icon bx bx-chevron-right"></i></a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link"><i class="tf-icon bx bx-chevron-right"></i></span>
                                    </li>
                                @endif

                                {{-- Last Page Link --}}
                                @if ($siswa->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $siswa->url($siswa->lastPage()) }}"><i
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

@push('scripts')
    <script>
        document.querySelectorAll('.btn-delete').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const name = this.dataset.name;
                Swal.fire({
                    title: 'Hapus Siswa?',
                    text: 'Data siswa "' + name + '" akan dihapus permanen.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('delete-form-' + id).submit();
                    }
                });
            });
        });

        // Dynamic Semester filter based on selected Tahun Ajaran
        function updateSemesterFilter(selectedTA) {
            const semesterSelect = document.getElementById('semester');
            if (!semesterSelect) return;

            const options = semesterSelect.querySelectorAll('option');
            options.forEach(opt => {
                if (!opt.value) {
                    opt.style.display = 'block';
                    return;
                }
                const taId = opt.getAttribute('data-ta');
                if (!selectedTA || taId === selectedTA) {
                    opt.style.display = 'block';
                } else {
                    opt.style.display = 'none';
                    if (opt.selected) {
                        semesterSelect.value = '';
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const taSelect = document.getElementById('tahun_ajaran');
            if (taSelect && taSelect.value) {
                updateSemesterFilter(taSelect.value);
            }
        });
    </script>
@endpush
