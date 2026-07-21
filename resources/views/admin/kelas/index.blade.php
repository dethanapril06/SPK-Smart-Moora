@extends('layouts.admin')
@section('title', 'Data Kelas')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Data Kelas</li>
                </ol>
            </nav>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.kelas.naik-kelas.index') }}" class="btn btn-sm btn-warning">
                    <i class="bx bx-transfer-alt me-1"></i> Naik Kelas
                </a>
                <a href="{{ route('admin.kelas.create') }}" class="btn btn-sm btn-primary">
                    <i class="bx bx-plus"></i>
                </a>
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

        <!-- Search Form -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('admin.kelas.index') }}" method="GET">
                    <div class="row align-items-end">
                        <div class="col-md-10">
                            <label class="form-label" for="search">Cari Kelas</label>
                            <input type="text" class="form-control" id="search" name="search"
                                placeholder="Cari berdasarkan ID kelas, nama kelas, atau wali kelas..."
                                value="{{ $search }}">
                        </div>
                        <div class="col-md-2 mt-3 text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-search"></i>
                            </button>
                        </div>
                    </div>
                    @if ($search)
                        <div class="mt-2">
                            <a href="{{ route('admin.kelas.index') }}" class="btn btn-sm btn-label-secondary">
                                <i class="bx bx-x"></i> Reset Pencarian
                            </a>
                            <span class="text-muted ms-2">Menampilkan hasil untuk "{{ $search }}"</span>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        <!-- Data Kelas Table -->
        <div class="card">
            <h5 class="card-header">Data Kelas</h5>
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID Kelas</th>
                            <th>Nama Kelas</th>
                            <th>Wali Kelas</th>
                            <th>Jumlah Siswa</th>
                            <th>Kapasitas</th>
                            <th style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($kelas as $item)
                            <tr>
                                <td>{{ ($kelas->currentPage() - 1) * $kelas->perPage() + $loop->iteration }}</td>
                                <td><strong>{{ $item->id_kelas }}</strong></td>
                                <td>{{ $item->nama_kelas }}</td>
                                <td>
                                    @if ($item->waliKelas)
                                        <span class="badge bg-label-info">{{ $item->waliKelas->name }}</span>
                                    @else
                                        <span class="badge bg-label-secondary">Belum Ada</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-label-primary">{{ $item->siswa->count() }} siswa</span>
                                </td>
                                <td>{{ $item->kapasitas ?? '-' }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item"
                                                href="{{ route('admin.kelas.show', $item->id_kelas) }}">
                                                <i class="bx bx-show me-1"></i> Detail
                                            </a>
                                            <a class="dropdown-item"
                                                href="{{ route('admin.kelas.edit', $item->id_kelas) }}">
                                                <i class="bx bx-edit-alt me-1"></i> Edit
                                            </a>
                                            <a class="dropdown-item btn-delete" href="javascript:void(0)"
                                                data-id="{{ $item->id_kelas }}" data-name="{{ $item->nama_kelas }}">
                                                <i class="bx bx-trash me-1"></i> Hapus
                                            </a>

                                            <!-- Hidden form -->
                                            <form id="delete-form-{{ $item->id_kelas }}"
                                                action="{{ route('admin.kelas.destroy', $item->id_kelas) }}" method="POST"
                                                class="d-none">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data kelas</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($kelas->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Menampilkan {{ $kelas->firstItem() }} - {{ $kelas->lastItem() }} dari {{ $kelas->total() }}
                            data
                        </div>
                        <nav aria-label="Page navigation">
                            <ul class="pagination pagination-sm mb-0">
                                {{-- First Page Link --}}
                                @if ($kelas->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link"><i class="tf-icon bx bx-chevrons-left"></i></span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $kelas->url(1) }}"><i
                                                class="tf-icon bx bx-chevrons-left"></i></a>
                                    </li>
                                @endif

                                {{-- Previous Page Link --}}
                                @if ($kelas->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link"><i class="tf-icon bx bx-chevron-left"></i></span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $kelas->previousPageUrl() }}"><i
                                                class="tf-icon bx bx-chevron-left"></i></a>
                                    </li>
                                @endif

                                {{-- Pagination Elements --}}
                                @foreach ($kelas->getUrlRange(max(1, $kelas->currentPage() - 2), min($kelas->lastPage(), $kelas->currentPage() + 2)) as $page => $url)
                                    @if ($page == $kelas->currentPage())
                                        <li class="page-item active"><span class="page-link">{{ $page }}</span>
                                        </li>
                                    @else
                                        <li class="page-item"><a class="page-link"
                                                href="{{ $url }}">{{ $page }}</a></li>
                                    @endif
                                @endforeach

                                {{-- Next Page Link --}}
                                @if ($kelas->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $kelas->nextPageUrl() }}"><i
                                                class="tf-icon bx bx-chevron-right"></i></a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link"><i class="tf-icon bx bx-chevron-right"></i></span>
                                    </li>
                                @endif

                                {{-- Last Page Link --}}
                                @if ($kelas->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $kelas->url($kelas->lastPage()) }}"><i
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
        <!--/ Data Kelas Table -->
    </div>

    @push('scripts')
        <script>
            // SweetAlert untuk Delete
            document.querySelectorAll('.btn-delete').forEach(button => {
                button.addEventListener('click', function() {
                    const itemId = this.dataset.id;
                    const itemName = this.dataset.name;

                    Swal.fire({
                        title: 'Hapus Kelas?',
                        html: `Yakin ingin menghapus kelas <strong>${itemName}</strong>?<br><small class="text-muted">Data yang dihapus tidak dapat dikembalikan!</small>`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#8592a3',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById(`delete-form-${itemId}`).submit();
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
