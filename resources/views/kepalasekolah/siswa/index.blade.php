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
            @if ($siswaList->hasPages())
                <div class="card-footer">
                    {{ $siswaList->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
