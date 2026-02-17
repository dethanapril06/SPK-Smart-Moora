@extends('layouts.admin')
@section('title', 'Input Nilai Ekstrakurikuler')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Input Nilai Ekstrakurikuler</li>
                </ol>
            </nav>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('admin.nilaiekstrakurikuler.index') }}" method="GET">
                    <div class="row align-items-end">
                        <div class="col-md-5">
                            <label class="form-label">Tahun Ajaran</label>
                            <select name="tahun_ajaran" class="form-select">
                                <option value="">-- Pilih Tahun Ajaran --</option>
                                @foreach ($tahunAjaranList as $ta)
                                    <option value="{{ $ta->id_ta }}" {{ $filterTA == $ta->id_ta ? 'selected' : '' }}>
                                        {{ $ta->tahun_ajaran }} - Semester {{ $ta->semester }}
                                        {{ $ta->is_active ? '(Aktif)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Kelas</label>
                            <select name="kelas" class="form-select">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach ($kelasList as $kelas)
                                    <option value="{{ $kelas->id_kelas }}"
                                        {{ $filterKelas == $kelas->id_kelas ? 'selected' : '' }}>
                                        {{ $kelas->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary"><i class="bx bx-filter-alt"></i> Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if ($filterTA && $filterKelas && $siswaList->count() > 0)
            @foreach ($siswaList as $siswa)
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">{{ $siswa->nama_siswa }} <small
                                class="text-muted">({{ $siswa->nisn }})</small></h6>
                        <button type="button" class="btn btn-sm btn-primary btn-add-ekskul" data-bs-toggle="modal"
                            data-bs-target="#addEkskulModal" data-siswa="{{ $siswa->id_siswa }}"
                            data-nama="{{ $siswa->nama_siswa }}">
                            <i class="bx bx-plus"></i> Tambah
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Ekskul</th>
                                    <th>Predikat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($siswa->nilaiEkstrakurikuler as $ekskul)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $ekskul->nama_ekskul }}</td>
                                        <td>
                                            <span
                                                class="badge bg-label-{{ $ekskul->predikat == 'Sangat Baik' ? 'success' : ($ekskul->predikat == 'Baik' ? 'primary' : ($ekskul->predikat == 'Cukup' ? 'warning' : 'danger')) }}">
                                                {{ $ekskul->predikat }}
                                            </span>
                                        </td>
                                        <td>
                                            <form
                                                action="{{ route('admin.nilaiekstrakurikuler.destroy', $ekskul->id_nilai_ekskul) }}"
                                                method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-icon btn-label-danger"
                                                    onclick="return confirm('Yakin hapus?')">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Belum ada data ekskul</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        @elseif($filterTA && $filterKelas)
            <div class="alert alert-info">Tidak ada siswa ditemukan untuk filter yang dipilih.</div>
        @else
            <div class="alert alert-info">Pilih Tahun Ajaran dan Kelas untuk mulai input nilai.</div>
        @endif
    </div>

    <!-- Add Ekskul Modal -->
    <div class="modal fade" id="addEkskulModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.nilaiekstrakurikuler.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id_ta" value="{{ $filterTA }}">
                    <input type="hidden" name="id_siswa" id="modal_id_siswa">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Ekskul - <span id="modal_nama_siswa"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Ekstrakurikuler <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_ekskul" required
                                placeholder="Contoh: Pramuka">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Predikat <span class="text-danger">*</span></label>
                            <select class="form-select" name="predikat" required>
                                <option value="Sangat Baik">Sangat Baik</option>
                                <option value="Baik" selected>Baik</option>
                                <option value="Cukup">Cukup</option>
                                <option value="Kurang">Kurang</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.querySelectorAll('.btn-add-ekskul').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.getElementById('modal_id_siswa').value = this.dataset.siswa;
                    document.getElementById('modal_nama_siswa').textContent = this.dataset.nama;
                });
            });
        </script>
    @endpush
@endsection
