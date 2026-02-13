@extends('layouts.walikelas')
@section('title', 'Data Kriteria')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('walikelas.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Data Kriteria</li>
            </ol>
        </nav>

        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('walikelas.kriteria.index') }}" method="GET">
                    <div class="row align-items-end">
                        <div class="col-md-10">
                            <label class="form-label" for="search">Cari Kriteria</label>
                            <input type="text" class="form-control" id="search" name="search"
                                placeholder="Cari kode, nama, atau jenis kriteria..." value="{{ $search }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary"><i class="bx bx-search"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

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
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kriteria as $item)
                            <tr>
                                <td>{{ ($kriteria->currentPage() - 1) * $kriteria->perPage() + $loop->iteration }}</td>
                                <td><strong>{{ $item->kode_kriteria }}</strong></td>
                                <td>{{ $item->nama_kriteria }}</td>
                                <td><span
                                        class="badge bg-label-{{ $item->jenis_kriteria == 'Benefit' ? 'success' : 'warning' }}">{{ $item->jenis_kriteria }}</span>
                                </td>
                                <td>{{ $item->bobot }}</td>
                                <td><span class="badge bg-label-info">{{ $item->sub_kriteria_count }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data</td>
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
                                {{-- First Page Link --}}
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

                                {{-- Previous Page Link --}}
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

                                {{-- Pagination Elements --}}
                                @foreach ($kriteria->getUrlRange(max(1, $kriteria->currentPage() - 2), min($kriteria->lastPage(), $kriteria->currentPage() + 2)) as $page => $url)
                                    @if ($page == $kriteria->currentPage())
                                        <li class="page-item active"><span class="page-link">{{ $page }}</span>
                                        </li>
                                    @else
                                        <li class="page-item"><a class="page-link"
                                                href="{{ $url }}">{{ $page }}</a></li>
                                    @endif
                                @endforeach

                                {{-- Next Page Link --}}
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

                                {{-- Last Page Link --}}
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
@endsection
