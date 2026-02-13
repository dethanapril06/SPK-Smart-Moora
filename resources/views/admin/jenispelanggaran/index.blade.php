@extends('layouts.admin')
@section('title', 'Data Jenis Pelanggaran')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Data Jenis Pelanggaran</li>
                </ol>
            </nav>
            <a href="{{ route('admin.jenispelanggaran.create') }}" class="btn btn-sm btn-primary">
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
                <form action="{{ route('admin.jenispelanggaran.index') }}" method="GET">
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <label class="form-label" for="kategori">Filter Kategori</label>
                            <select class="form-select" id="kategori" name="kategori">
                                <option value="">Semua Kategori</option>
                                @foreach ($kategoriList as $kat)
                                    <option value="{{ $kat }}" {{ $filterKategori == $kat ? 'selected' : '' }}>
                                        {{ str_replace('_', ' ', $kat) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="search">Cari Pelanggaran</label>
                            <input type="text" class="form-control" id="search" name="search"
                                placeholder="Cari berdasarkan nama atau kategori pelanggaran..."
                                value="{{ $search }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-search"></i>
                            </button>
                        </div>
                    </div>
                    @if ($search || $filterKategori)
                        <div class="mt-2">
                            <a href="{{ route('admin.jenispelanggaran.index') }}" class="btn btn-sm btn-label-secondary">
                                <i class="bx bx-x"></i> Reset Filter
                            </a>
                            @if ($search)
                                <span class="text-muted ms-2">Pencarian: "{{ $search }}"</span>
                            @endif
                            @if ($filterKategori)
                                <span class="text-muted ms-2">Kategori: {{ str_replace('_', ' ', $filterKategori) }}</span>
                            @endif
                        </div>
                    @endif
                </form>
            </div>
        </div>

        <!-- Data Jenis Pelanggaran Table -->
        <div class="card">
            <h5 class="card-header">Data Jenis Pelanggaran</h5>
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kategori</th>
                            <th>Nama Pelanggaran</th>
                            <th>Bobot Poin</th>
                            <th style="width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($jenispelanggaran as $item)
                            <tr>
                                <td>{{ ($jenispelanggaran->currentPage() - 1) * $jenispelanggaran->perPage() + $loop->iteration }}
                                </td>
                                <td>
                                    <span
                                        class="badge bg-label-primary">{{ str_replace('_', ' ', $item->kategori_pelanggaran) }}</span>
                                </td>
                                <td>{{ $item->nama_pelanggaran }}</td>
                                <td>
                                    <span
                                        class="badge bg-label-{{ $item->bobot_poin >= 20 ? 'danger' : ($item->bobot_poin >= 10 ? 'warning' : 'info') }}">
                                        {{ $item->bobot_poin }} poin
                                    </span>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item"
                                                href="{{ route('admin.jenispelanggaran.edit', $item->id_jenis_pelanggaran) }}">
                                                <i class="bx bx-edit-alt me-1"></i> Edit
                                            </a>
                                            <a class="dropdown-item btn-delete" href="javascript:void(0)"
                                                data-id="{{ $item->id_jenis_pelanggaran }}"
                                                data-name="{{ $item->nama_pelanggaran }}">
                                                <i class="bx bx-trash me-1"></i> Hapus
                                            </a>
                                            <form id="delete-form-{{ $item->id_jenis_pelanggaran }}"
                                                action="{{ route('admin.jenispelanggaran.destroy', $item->id_jenis_pelanggaran) }}"
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
                                <td colspan="5" class="text-center">Tidak ada data jenis pelanggaran</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($jenispelanggaran->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Menampilkan {{ $jenispelanggaran->firstItem() }} - {{ $jenispelanggaran->lastItem() }} dari
                            {{ $jenispelanggaran->total() }} data
                        </div>
                        <nav aria-label="Page navigation">
                            <ul class="pagination pagination-sm mb-0">
                                {{-- First Page Link --}}
                                @if ($jenispelanggaran->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link"><i class="tf-icon bx bx-chevrons-left"></i></span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $jenispelanggaran->url(1) }}"><i
                                                class="tf-icon bx bx-chevrons-left"></i></a>
                                    </li>
                                @endif

                                {{-- Previous Page Link --}}
                                @if ($jenispelanggaran->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link"><i class="tf-icon bx bx-chevron-left"></i></span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $jenispelanggaran->previousPageUrl() }}"><i
                                                class="tf-icon bx bx-chevron-left"></i></a>
                                    </li>
                                @endif

                                {{-- Pagination Elements --}}
                                @foreach ($jenispelanggaran->getUrlRange(max(1, $jenispelanggaran->currentPage() - 2), min($jenispelanggaran->lastPage(), $jenispelanggaran->currentPage() + 2)) as $page => $url)
                                    @if ($page == $jenispelanggaran->currentPage())
                                        <li class="page-item active"><span class="page-link">{{ $page }}</span>
                                        </li>
                                    @else
                                        <li class="page-item"><a class="page-link"
                                                href="{{ $url }}">{{ $page }}</a></li>
                                    @endif
                                @endforeach

                                {{-- Next Page Link --}}
                                @if ($jenispelanggaran->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $jenispelanggaran->nextPageUrl() }}"><i
                                                class="tf-icon bx bx-chevron-right"></i></a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link"><i class="tf-icon bx bx-chevron-right"></i></span>
                                    </li>
                                @endif

                                {{-- Last Page Link --}}
                                @if ($jenispelanggaran->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $jenispelanggaran->url($jenispelanggaran->lastPage()) }}"><i
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
                        title: 'Hapus Jenis Pelanggaran?',
                        html: `Yakin ingin menghapus <strong>${itemName}</strong>?<br><small class="text-muted">Data yang dihapus tidak dapat dikembalikan!</small>`,
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
