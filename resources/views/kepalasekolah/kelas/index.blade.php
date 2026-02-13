@extends('layouts.kepalasekolah')
@section('title', 'Data Kelas')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('kepalasekolah.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Data Kelas</li>
            </ol>
        </nav>

        <div class="card">
            <h5 class="card-header">Data Kelas</h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" width="50">No</th>
                            <th>Kode Kelas</th>
                            <th>Nama Kelas</th>
                            <th>Wali Kelas</th>
                            <th class="text-center">Jumlah Siswa</th>
                            <th class="text-center">Kapasitas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kelasList as $kelas)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td><strong>{{ $kelas->id_kelas }}</strong></td>
                                <td>{{ $kelas->nama_kelas }}</td>
                                <td>{{ $kelas->waliKelas->name ?? '-' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-label-primary">{{ $kelas->siswa_count }}</span>
                                </td>
                                <td class="text-center">{{ $kelas->kapasitas }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data kelas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
