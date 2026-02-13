@extends('layouts.walikelas')
@section('title', 'Jenis Pelanggaran')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('walikelas.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Jenis Pelanggaran</li>
            </ol>
        </nav>

        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('walikelas.jenispelanggaran.index') }}" method="GET">
                    <div class="row align-items-end">
                        <div class="col-md-5">
                            <label class="form-label" for="search">Cari Pelanggaran</label>
                            <input type="text" class="form-control" id="search" name="search" placeholder="Cari..."
                                value="{{ $search }}">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label" for="kategori">Filter Kategori</label>
                            <select class="form-select" id="kategori" name="kategori">
                                <option value="">Semua Kategori</option>
                                @foreach ($kategoriList as $k)
                                    <option value="{{ $k }}" {{ $filterKategori == $k ? 'selected' : '' }}>
                                        {{ str_replace('_', ' ', $k) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary"><i class="bx bx-search"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

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
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jenispelanggaran as $item)
                            <tr>
                                <td>{{ ($jenispelanggaran->currentPage() - 1) * $jenispelanggaran->perPage() + $loop->iteration }}
                                </td>
                                <td><span
                                        class="badge bg-label-warning">{{ str_replace('_', ' ', $item->kategori_pelanggaran) }}</span>
                                </td>
                                <td>{{ $item->nama_pelanggaran }}</td>
                                <td><span class="badge bg-label-danger">{{ $item->bobot_poin }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Tidak ada data</td>
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
@endsection
