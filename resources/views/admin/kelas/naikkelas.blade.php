@extends('layouts.admin')
@section('title', 'Naik Kelas')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.kelas.index') }}">Data Kelas</a></li>
                    <li class="breadcrumb-item active">Naik Kelas</li>
                </ol>
            </nav>
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

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('admin.kelas.naik-kelas.index') }}" method="GET">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label" for="tahun_ajaran">Tahun Ajaran Sumber</label>
                            <select class="form-select" id="tahun_ajaran" name="tahun_ajaran" required>
                                <option value="">-- Pilih Tahun Ajaran --</option>
                                @foreach ($tahunAjaranList as $tahunAjaran)
                                    <option value="{{ $tahunAjaran }}"
                                        {{ $sourceTahunAjaran === $tahunAjaran ? 'selected' : '' }}>
                                        {{ $tahunAjaran }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="id_kelas">Kelas X Sumber</label>
                            <select class="form-select" id="id_kelas" name="id_kelas" required>
                                <option value="">-- Pilih Kelas X --</option>
                                @foreach ($kelasList as $kelas)
                                    <option value="{{ $kelas->id_kelas }}"
                                        {{ $sourceKelasId == $kelas->id_kelas ? 'selected' : '' }}>
                                        {{ $kelas->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-search me-1"></i> Tampilkan Siswa
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if ($sourceTahunAjaranRecord && $sourceKelasId && $selectedKelas)
            @if (!$siswaList->count())
                <div class="alert alert-info">Tidak ada siswa ditemukan pada kelas sumber yang dipilih.</div>
            @else
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                            <div>
                                <h5 class="mb-1">Naik Kelas dari {{ $selectedKelas->nama_kelas }}</h5>
                                <small class="text-muted">Pilih kelas tujuan XI per siswa untuk tahun ajaran baru.</small>
                            </div>
                            <div class="text-muted">
                                Total siswa: <strong>{{ $siswaList->count() }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.kelas.naik-kelas.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="tahun_ajaran" value="{{ $sourceTahunAjaran }}">
                            <input type="hidden" name="id_kelas" value="{{ $sourceKelasId }}">

                            <div class="alert alert-info mb-3">
                                Tahun ajaran tujuan otomatis menjadi <strong>{{ $targetTahunAjaranLabel }}</strong>
                                semester <strong>Ganjil</strong>.
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead>
                                        <tr>
                                            <th style="width: 70px;">No</th>
                                            <th>Nama Siswa</th>
                                            <th>NISN</th>
                                            <th class="text-center">Kelas Tujuan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($siswaList as $siswa)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    <strong>{{ $siswa->nama_siswa }}</strong>
                                                </td>
                                                <td>{{ $siswa->nisn }}</td>
                                                <td>
                                                    <select class="form-select" name="tujuan[{{ $siswa->id_siswa }}]"
                                                        required>
                                                        <option value="">-- Pilih Kelas XI Tujuan --</option>
                                                        @foreach ($targetKelasList as $targetKelas)
                                                            <option value="{{ $targetKelas->id_kelas }}">
                                                                {{ $targetKelas->nama_kelas }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="alert alert-warning mt-3 mb-0">
                                Setiap siswa kelas X dipilih satu per satu ke kelas XI tujuan. Pastikan kapasitas kelas
                                tujuan cukup.
                            </div>

                            <div class="mt-3 text-end">
                                <button type="submit" class="btn btn-success">
                                    <i class="bx bx-transfer-alt me-1"></i> Proses Naik Kelas
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        @elseif($sourceTahunAjaran || $sourceKelasId)
            <div class="alert alert-info">Pilih tahun ajaran dan kelas X terlebih dahulu.</div>
        @else
            <div class="alert alert-info">Pilih tahun ajaran sumber dan kelas X untuk menampilkan daftar siswa.</div>
        @endif
    </div>
@endsection
