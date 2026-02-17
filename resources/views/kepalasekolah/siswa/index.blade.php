@extends('layouts.kepalasekolah')
@section('title', 'Data Siswa')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('kepalasekolah.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Data Siswa</li>
            </ol>
        </nav>

        <div class="card">
            <h5 class="card-header">Data Siswa</h5>
            <div class="card-body">
                <form action="{{ route('kepalasekolah.siswa.index') }}" method="GET" class="row g-3 mb-3">
                    <div class="col-md-4">
                        <select class="form-select" name="kelas">
                            <option value="">Semua Kelas</option>
                            @foreach ($kelasList as $k)
                                <option value="{{ $k->id_kelas }}" {{ $filterKelas == $k->id_kelas ? 'selected' : '' }}>
                                    {{ $k->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary"><i class="bx bx-search"></i> Filter</button>
                    </div>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" width="50">No</th>
                            <th>NISN</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Jenis Kelamin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswaList as $siswa)
                            <tr>
                                <td class="text-center">
                                    {{ $loop->iteration + ($siswaList->currentPage() - 1) * $siswaList->perPage() }}</td>
                                <td><strong>{{ $siswa->nisn }}</strong></td>
                                <td>{{ $siswa->nama_siswa }}</td>
                                <td>
                                    @if ($siswa->kelas)
                                        <span class="badge bg-label-info">{{ $siswa->kelas->nama_kelas }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $siswa->jenis_kelamin }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data siswa.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            @if ($siswaList->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Menampilkan {{ $siswaList->firstItem() }} - {{ $siswaList->lastItem() }} dari {{ $siswaList->total() }}
                            data
                        </div>
                        <nav aria-label="Page navigation">
                            <ul class="pagination pagination-sm mb-0">
                                @if ($siswaList->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link"><i class="tf-icon bx bx-chevrons-left"></i></span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $siswaList->url(1) }}"><i
                                                class="tf-icon bx bx-chevrons-left"></i></a>
                                    </li>
                                @endif

                                @if ($siswaList->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link"><i class="tf-icon bx bx-chevron-left"></i></span>
                                    </li>   
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $siswaList->previousPageUrl() }}"><i
                                                class="tf-icon bx bx-chevron-left"></i></a>
                                    </li>
                                @endif

                                @foreach ($siswaList->getUrlRange(max(1, $siswaList->currentPage() - 2), min($siswaList->lastPage(), $siswaList->currentPage() + 2)) as $page => $url)
                                    @if ($page == $siswaList->currentPage())
                                        <li class="page-item active"><span class="page-link">{{ $page }}</span>
                                        </li>
                                    @else
                                        <li class="page-item"><a class="page-link"
                                                href="{{ $url }}">{{ $page }}</a></li>
                                    @endif
                                @endforeach

                                @if ($siswaList->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $siswaList->nextPageUrl() }}"><i
                                                class="tf-icon bx bx-chevron-right"></i></a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link"><i class="tf-icon bx bx-chevron-right"></i></span>
                                    </li>
                                @endif

                                @if ($siswaList->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $siswaList->url($siswaList->lastPage()) }}"><i
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
