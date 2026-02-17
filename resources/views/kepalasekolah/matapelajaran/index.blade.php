@extends('layouts.kepalasekolah')
@section('title', 'Data Mata Pelajaran')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('kepalasekolah.dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Data Mata Pelajaran</li>
                </ol>
            </nav>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('kepalasekolah.matapelajaran.index') }}" method="GET">
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
                            <a href="{{ route('kepalasekolah.matapelajaran.index') }}"
                                class="btn btn-sm btn-label-secondary">
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
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($mataPelajaran as $item)
                            <tr>
                                <td>{{ ($mataPelajaran->currentPage() - 1) * $mataPelajaran->perPage() + $loop->iteration }}
                                </td>
                                <td><strong>{{ $item->kode_mapel }}</strong></td>
                                <td>{{ $item->nama_mapel }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">Tidak ada data mata pelajaran</td>
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
@endsection
