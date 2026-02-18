@extends('layouts.admin')
@section('title', 'Data Sub Kriteria')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Data Sub Kriteria</li>
                </ol>
            </nav>
            <a href="{{ route('admin.subkriteria.create') }}" class="btn btn-sm btn-primary">
                <i class="bx bx-plus"></i>
            </a>
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
                <form action="{{ route('admin.subkriteria.index') }}" method="GET">
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <label class="form-label" for="kriteria">Filter Kriteria</label>
                            <select class="form-select" id="kriteria" name="kriteria">
                                <option value="">Semua Kriteria</option>
                                @foreach ($kriteriaList as $k)
                                    <option value="{{ $k->id_kriteria }}"
                                        {{ $filterKriteria == $k->id_kriteria ? 'selected' : '' }}>
                                        {{ $k->kode_kriteria }} - {{ $k->nama_kriteria }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="search">Cari Sub Kriteria</label>
                            <input type="text" class="form-control" id="search" name="search"
                                placeholder="Cari berdasarkan nama sub kriteria atau nama kriteria..."
                                value="{{ $search }}">
                        </div>
                        <div class="col-md-2 mt-3 text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-search"></i>
                            </button>
                        </div>
                    </div>
                    @if ($search || $filterKriteria)
                        <div class="mt-2">
                            <a href="{{ route('admin.subkriteria.index') }}" class="btn btn-sm btn-label-secondary">
                                <i class="bx bx-x"></i> Reset Filter
                            </a>
                            @if ($search)
                                <span class="text-muted ms-2">Pencarian: "{{ $search }}"</span>
                            @endif
                            @if ($filterKriteria)
                                <span class="text-muted ms-2">
                                    Filter:
                                    {{ $kriteriaList->firstWhere('id_kriteria', $filterKriteria)->kode_kriteria ?? '' }}
                                </span>
                            @endif
                        </div>
                    @endif
                </form>
            </div>
        </div>

        <!-- Data Sub Kriteria Table -->
        <div class="card">
            <h5 class="card-header">Data Sub Kriteria</h5>
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kriteria</th>
                            <th>Nama Sub Kriteria</th>
                            <th>Rentang Nilai</th>
                            <th>Bobot</th>
                            <th style="width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($subkriteria as $item)
                            <tr>
                                <td>{{ ($subkriteria->currentPage() - 1) * $subkriteria->perPage() + $loop->iteration }}
                                </td>
                                <td>
                                    @if ($item->kriteria)
                                        <span class="badge bg-label-primary">{{ $item->kriteria->kode_kriteria }}</span>
                                        <small class="text-muted d-block">{{ $item->kriteria->nama_kriteria }}</small>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td><strong>{{ $item->nama_subkriteria }}</strong></td>
                                <td>
                                    <span class="badge bg-label-info">{{ $item->nilai_awal }} -
                                        {{ $item->nilai_akhir }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-label-success">{{ $item->bobot_subkriteria }}</span>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item"
                                                href="{{ route('admin.subkriteria.edit', $item->id_subkriteria) }}">
                                                <i class="bx bx-edit-alt me-1"></i> Edit
                                            </a>
                                            <a class="dropdown-item btn-delete" href="javascript:void(0)"
                                                data-id="{{ $item->id_subkriteria }}"
                                                data-name="{{ $item->nama_subkriteria }}">
                                                <i class="bx bx-trash me-1"></i> Hapus
                                            </a>
                                            <form id="delete-form-{{ $item->id_subkriteria }}"
                                                action="{{ route('admin.subkriteria.destroy', $item->id_subkriteria) }}"
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
                                <td colspan="6" class="text-center">Tidak ada data sub kriteria</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($subkriteria->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Menampilkan {{ $subkriteria->firstItem() }} - {{ $subkriteria->lastItem() }} dari
                            {{ $subkriteria->total() }} data
                        </div>
                        <nav aria-label="Page navigation">
                            <ul class="pagination pagination-sm mb-0">
                                @if ($subkriteria->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link"><i class="tf-icon bx bx-chevrons-left"></i></span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $subkriteria->url(1) }}"><i
                                                class="tf-icon bx bx-chevrons-left"></i></a>
                                    </li>
                                @endif

                                @if ($subkriteria->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link"><i class="tf-icon bx bx-chevron-left"></i></span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $subkriteria->previousPageUrl() }}"><i
                                                class="tf-icon bx bx-chevron-left"></i></a>
                                    </li>
                                @endif

                                @foreach ($subkriteria->getUrlRange(max(1, $subkriteria->currentPage() - 2), min($subkriteria->lastPage(), $subkriteria->currentPage() + 2)) as $page => $url)
                                    @if ($page == $subkriteria->currentPage())
                                        <li class="page-item active"><span class="page-link">{{ $page }}</span>
                                        </li>
                                    @else
                                        <li class="page-item"><a class="page-link"
                                                href="{{ $url }}">{{ $page }}</a></li>
                                    @endif
                                @endforeach

                                @if ($subkriteria->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $subkriteria->nextPageUrl() }}"><i
                                                class="tf-icon bx bx-chevron-right"></i></a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link"><i class="tf-icon bx bx-chevron-right"></i></span>
                                    </li>
                                @endif

                                @if ($subkriteria->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $subkriteria->url($subkriteria->lastPage()) }}"><i
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
                        title: 'Hapus Sub Kriteria?',
                        html: `Yakin ingin menghapus sub kriteria <strong>${itemName}</strong>?<br><small class="text-muted">Data yang dihapus tidak dapat dikembalikan!</small>`,
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
