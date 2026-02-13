@extends('layouts.admin')
@section('title', 'Data Tahun Ajaran')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Data Tahun Ajaran</li>
                </ol>
            </nav>
            <a href="{{ route('admin.tahunajaran.create') }}" class="btn btn-sm btn-primary">
                <i class="bx bx-plus"></i>
            </a>
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
                <form action="{{ route('admin.tahunajaran.index') }}" method="GET">
                    <div class="row align-items-end">
                        <div class="col-md-10">
                            <label class="form-label" for="search">Cari Tahun Ajaran</label>
                            <input type="text" class="form-control" id="search" name="search"
                                placeholder="Cari berdasarkan tahun ajaran atau semester..." value="{{ $search }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-search"></i>
                            </button>
                        </div>
                    </div>
                    @if ($search)
                        <div class="mt-2">
                            <a href="{{ route('admin.tahunajaran.index') }}" class="btn btn-sm btn-label-secondary">
                                <i class="bx bx-x"></i> Reset Pencarian
                            </a>
                            <span class="text-muted ms-2">Menampilkan hasil untuk "{{ $search }}"</span>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        <!-- Data Tahun Ajaran Table -->
        <div class="card">
            <h5 class="card-header">Data Tahun Ajaran</h5>
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tahun Ajaran</th>
                            <th>Semester</th>
                            <th>Status</th>
                            <th>Jumlah Siswa</th>
                            <th style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($tahunAjaran as $item)
                            <tr>
                                <td>{{ ($tahunAjaran->currentPage() - 1) * $tahunAjaran->perPage() + $loop->iteration }}
                                </td>
                                <td><strong>{{ $item->tahun_ajaran }}</strong></td>
                                <td>
                                    <span class="badge bg-label-{{ $item->semester == 'Ganjil' ? 'primary' : 'info' }}">
                                        {{ $item->semester }}
                                    </span>
                                </td>
                                <td>
                                    @if ($item->is_active)
                                        <span class="badge bg-label-success">Aktif</span>
                                    @else
                                        <span class="badge bg-label-secondary">Tidak Aktif</span>
                                    @endif
                                </td>
                                <td>{{ $item->siswa->count() }} siswa</td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item"
                                                href="{{ route('admin.tahunajaran.show', $item->id_ta) }}">
                                                <i class="bx bx-show me-1"></i> Detail
                                            </a>
                                            @if (!$item->is_active)
                                                <a class="dropdown-item btn-activate" href="javascript:void(0)"
                                                    data-id="{{ $item->id_ta }}"
                                                    data-name="{{ $item->tahun_ajaran }} - {{ $item->semester }}">
                                                    <i class="bx bx-check-circle me-1"></i> Aktifkan
                                                </a>
                                                <form id="activate-form-{{ $item->id_ta }}"
                                                    action="{{ route('admin.tahunajaran.set-active', $item->id_ta) }}"
                                                    method="POST" class="d-none">
                                                    @csrf
                                                    @method('PUT')
                                                </form>
                                            @endif
                                            <a class="dropdown-item"
                                                href="{{ route('admin.tahunajaran.edit', $item->id_ta) }}">
                                                <i class="bx bx-edit-alt me-1"></i> Edit
                                            </a>
                                            <a class="dropdown-item btn-delete" href="javascript:void(0)"
                                                data-id="{{ $item->id_ta }}"
                                                data-name="{{ $item->tahun_ajaran }} - {{ $item->semester }}">
                                                <i class="bx bx-trash me-1"></i> Hapus
                                            </a>
                                            <form id="delete-form-{{ $item->id_ta }}"
                                                action="{{ route('admin.tahunajaran.destroy', $item->id_ta) }}"
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
                                <td colspan="6" class="text-center">Tidak ada data tahun ajaran</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($tahunAjaran->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Menampilkan {{ $tahunAjaran->firstItem() }} - {{ $tahunAjaran->lastItem() }} dari
                            {{ $tahunAjaran->total() }} data
                        </div>
                        <nav aria-label="Page navigation">
                            <ul class="pagination pagination-sm mb-0">
                                @if ($tahunAjaran->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link"><i class="tf-icon bx bx-chevrons-left"></i></span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $tahunAjaran->url(1) }}"><i
                                                class="tf-icon bx bx-chevrons-left"></i></a>
                                    </li>
                                @endif

                                @if ($tahunAjaran->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link"><i class="tf-icon bx bx-chevron-left"></i></span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $tahunAjaran->previousPageUrl() }}"><i
                                                class="tf-icon bx bx-chevron-left"></i></a>
                                    </li>
                                @endif

                                @foreach ($tahunAjaran->getUrlRange(max(1, $tahunAjaran->currentPage() - 2), min($tahunAjaran->lastPage(), $tahunAjaran->currentPage() + 2)) as $page => $url)
                                    @if ($page == $tahunAjaran->currentPage())
                                        <li class="page-item active"><span class="page-link">{{ $page }}</span>
                                        </li>
                                    @else
                                        <li class="page-item"><a class="page-link"
                                                href="{{ $url }}">{{ $page }}</a></li>
                                    @endif
                                @endforeach

                                @if ($tahunAjaran->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $tahunAjaran->nextPageUrl() }}"><i
                                                class="tf-icon bx bx-chevron-right"></i></a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link"><i class="tf-icon bx bx-chevron-right"></i></span>
                                    </li>
                                @endif

                                @if ($tahunAjaran->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $tahunAjaran->url($tahunAjaran->lastPage()) }}"><i
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
            // SweetAlert untuk Aktifkan
            document.querySelectorAll('.btn-activate').forEach(button => {
                button.addEventListener('click', function() {
                    const itemId = this.dataset.id;
                    const itemName = this.dataset.name;

                    Swal.fire({
                        title: 'Aktifkan Tahun Ajaran?',
                        html: `Yakin ingin mengaktifkan <strong>${itemName}</strong>?<br><small class="text-muted">Tahun ajaran lain akan dinonaktifkan</small>`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#696cff',
                        cancelButtonColor: '#8592a3',
                        confirmButtonText: 'Ya, Aktifkan!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById(`activate-form-${itemId}`).submit();
                        }
                    });
                });
            });

            // SweetAlert untuk Delete
            document.querySelectorAll('.btn-delete').forEach(button => {
                button.addEventListener('click', function() {
                    const itemId = this.dataset.id;
                    const itemName = this.dataset.name;

                    Swal.fire({
                        title: 'Hapus Tahun Ajaran?',
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
