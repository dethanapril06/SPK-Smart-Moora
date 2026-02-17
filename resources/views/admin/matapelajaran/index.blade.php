@extends('layouts.admin')
@section('title', 'Data Mata Pelajaran')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Data Mata Pelajaran</li>
                </ol>
            </nav>
            <a href="{{ route('admin.matapelajaran.create') }}" class="btn btn-sm btn-primary">
                <i class="bx bx-plus"></i>
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('admin.matapelajaran.index') }}" method="GET">
                    <div class="row align-items-end">
                        <div class="col-md-10">
                            <label class="form-label" for="search">Cari Mata Pelajaran</label>
                            <input type="text" class="form-control" id="search" name="search"
                                placeholder="Cari berdasarkan kode atau nama..." value="{{ $search }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary"><i class="bx bx-search"></i></button>
                        </div>
                    </div>
                    @if ($search)
                        <div class="mt-2">
                            <a href="{{ route('admin.matapelajaran.index') }}" class="btn btn-sm btn-label-secondary">
                                <i class="bx bx-x"></i> Reset
                            </a>
                            <span class="text-muted ms-2">Hasil: "{{ $search }}"</span>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        <div class="card">
            <h5 class="card-header">Data Mata Pelajaran</h5>
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Nama Mata Pelajaran</th>
                            <th style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($mataPelajaran as $item)
                            <tr>
                                <td>{{ ($mataPelajaran->currentPage() - 1) * $mataPelajaran->perPage() + $loop->iteration }}
                                </td>
                                <td><strong>{{ $item->kode_mapel }}</strong></td>
                                <td>{{ $item->nama_mapel }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item"
                                                href="{{ route('admin.matapelajaran.edit', $item->id_mapel) }}">
                                                <i class="bx bx-edit-alt me-1"></i> Edit
                                            </a>
                                            <a class="dropdown-item btn-delete" href="javascript:void(0)"
                                                data-id="{{ $item->id_mapel }}" data-name="{{ $item->nama_mapel }}">
                                                <i class="bx bx-trash me-1"></i> Hapus
                                            </a>
                                            <form id="delete-form-{{ $item->id_mapel }}"
                                                action="{{ route('admin.matapelajaran.destroy', $item->id_mapel) }}"
                                                method="POST" class="d-none">
                                                @csrf @method('DELETE')
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Tidak ada data mata pelajaran</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($mataPelajaran->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Menampilkan {{ $mataPelajaran->firstItem() }} - {{ $mataPelajaran->lastItem() }} dari
                            {{ $mataPelajaran->total() }} data
                        </div>
                        {{ $mataPelajaran->links() }}
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
                        title: 'Hapus Mata Pelajaran?',
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
