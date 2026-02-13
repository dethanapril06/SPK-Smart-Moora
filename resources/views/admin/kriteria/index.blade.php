@extends('layouts.admin')
@section('title', 'Data Kriteria')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Data Kriteria</li>
                </ol>
            </nav>
            <a href="{{ route('admin.kriteria.create') }}" class="btn btn-sm btn-primary">
                <i class="bx bx-plus"></i>
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Search Form -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('admin.kriteria.index') }}" method="GET">
                    <div class="row align-items-end">
                        <div class="col-md-10">
                            <label class="form-label" for="search">Cari Kriteria</label>
                            <input type="text" class="form-control" id="search" name="search"
                                placeholder="Cari berdasarkan kode, nama, atau jenis kriteria..."
                                value="{{ $search }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-search"></i>
                            </button>
                        </div>
                    </div>
                    @if ($search)
                        <div class="mt-2">
                            <a href="{{ route('admin.kriteria.index') }}" class="btn btn-sm btn-label-secondary">
                                <i class="bx bx-x"></i> Reset Pencarian
                            </a>
                            <span class="text-muted ms-2">Menampilkan hasil untuk "{{ $search }}"</span>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        <!-- Data Kriteria Table -->
        <div class="card">
            <h5 class="card-header">Data Kriteria</h5>
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Nama Kriteria</th>
                            <th>Jenis</th>
                            <th>Bobot</th>
                            <th>Sub Kriteria</th>
                            <th style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($kriteria as $item)
                            <tr>
                                <td>{{ ($kriteria->currentPage() - 1) * $kriteria->perPage() + $loop->iteration }}</td>
                                <td><strong>{{ $item->kode_kriteria }}</strong></td>
                                <td>{{ $item->nama_kriteria }}</td>
                                <td>
                                    <span
                                        class="badge bg-label-{{ $item->jenis_kriteria == 'Benefit' ? 'success' : 'warning' }}">
                                        {{ $item->jenis_kriteria }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-label-primary">{{ $item->bobot }}</span>
                                </td>
                                <td>{{ $item->sub_kriteria_count }} sub kriteria</td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item"
                                                href="{{ route('admin.kriteria.show', $item->id_kriteria) }}">
                                                <i class="bx bx-show me-1"></i> Detail
                                            </a>
                                            <a class="dropdown-item"
                                                href="{{ route('admin.kriteria.edit', $item->id_kriteria) }}">
                                                <i class="bx bx-edit-alt me-1"></i> Edit
                                            </a>
                                            <a class="dropdown-item btn-delete" href="javascript:void(0)"
                                                data-id="{{ $item->id_kriteria }}" data-name="{{ $item->nama_kriteria }}">
                                                <i class="bx bx-trash me-1"></i> Hapus
                                            </a>
                                            <form id="delete-form-{{ $item->id_kriteria }}"
                                                action="{{ route('admin.kriteria.destroy', $item->id_kriteria) }}"
                                                method="POST" class="d-none">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data kriteria</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($kriteria->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Menampilkan {{ $kriteria->firstItem() }} - {{ $kriteria->lastItem() }} dari
                            {{ $kriteria->total() }} data
                        </div>
                        <nav aria-label="Page navigation">
                            <ul class="pagination pagination-sm mb-0">
                                @if ($kriteria->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link"><i class="tf-icon bx bx-chevrons-left"></i></span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $kriteria->url(1) }}"><i
                                                class="tf-icon bx bx-chevrons-left"></i></a>
                                    </li>
                                @endif

                                @if ($kriteria->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link"><i class="tf-icon bx bx-chevron-left"></i></span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $kriteria->previousPageUrl() }}"><i
                                                class="tf-icon bx bx-chevron-left"></i></a>
                                    </li>
                                @endif

                                @foreach ($kriteria->getUrlRange(max(1, $kriteria->currentPage() - 2), min($kriteria->lastPage(), $kriteria->currentPage() + 2)) as $page => $url)
                                    @if ($page == $kriteria->currentPage())
                                        <li class="page-item active"><span class="page-link">{{ $page }}</span>
                                        </li>
                                    @else
                                        <li class="page-item"><a class="page-link"
                                                href="{{ $url }}">{{ $page }}</a></li>
                                    @endif
                                @endforeach

                                @if ($kriteria->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $kriteria->nextPageUrl() }}"><i
                                                class="tf-icon bx bx-chevron-right"></i></a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link"><i class="tf-icon bx bx-chevron-right"></i></span>
                                    </li>
                                @endif

                                @if ($kriteria->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $kriteria->url($kriteria->lastPage()) }}"><i
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
            // SweetAlert untuk Delete
            document.querySelectorAll('.btn-delete').forEach(button => {
                button.addEventListener('click', function() {
                    const itemId = this.dataset.id;
                    const itemName = this.dataset.name;

                    Swal.fire({
                        title: 'Hapus Kriteria?',
                        html: `Yakin ingin menghapus kriteria <strong>${itemName}</strong>?<br><small class="text-muted">Data yang dihapus tidak dapat dikembalikan!</small>`,
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
