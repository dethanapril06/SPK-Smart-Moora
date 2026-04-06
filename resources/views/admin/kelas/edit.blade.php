@extends('layouts.admin')
@section('title', 'Edit Kelas')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.kelas.index') }}">Data Kelas</a>
                </li>
                <li class="breadcrumb-item active">Edit Kelas</li>
            </ol>
        </nav>

        <div class="card">
            <h5 class="card-header">Form Edit Kelas</h5>
            <div class="card-body">
                <form action="{{ route('admin.kelas.update', $kela->id_kelas) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label" for="id_kelas">ID Kelas</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-id-card"></i></span>
                            <input type="text" class="form-control" id="id_kelas" value="{{ $kela->id_kelas }}"
                                disabled />
                        </div>
                        <div class="form-text">ID kelas tidak dapat diubah</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="nama_kelas">Nama Kelas <span class="text-danger">*</span></label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-book"></i></span>
                            <input type="text" id="nama_kelas" name="nama_kelas"
                                class="form-control @error('nama_kelas') is-invalid @enderror"
                                placeholder="Contoh: Kelas X IPA 1" value="{{ old('nama_kelas', $kela->nama_kelas) }}"
                                autofocus />
                        </div>
                        @error('nama_kelas')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="id_wali_kelas">Wali Kelas</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-user"></i></span>
                            <select id="id_wali_kelas" name="id_wali_kelas"
                                class="form-select @error('id_wali_kelas') is-invalid @enderror">
                                <option value="">Pilih Wali Kelas</option>
                                @foreach ($waliKelas as $wali)
                                    <option value="{{ $wali->id }}"
                                        {{ old('id_wali_kelas', $kela->id_wali_kelas) == $wali->id ? 'selected' : '' }}>
                                        {{ $wali->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('id_wali_kelas')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="kapasitas">Kapasitas Kelas</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-group"></i></span>
                            <input type="number" id="kapasitas" name="kapasitas"
                                class="form-control @error('kapasitas') is-invalid @enderror" placeholder="0"
                                value="{{ old('kapasitas', $kela->kapasitas) }}" min="0" />
                        </div>
                        @error('kapasitas')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Maksimal jumlah siswa yang dapat diterima di kelas ini</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mata Pelajaran</label>
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="mb-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllMapel">
                                        <i class="bx bx-check-double"></i> Pilih Semua
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllMapel">
                                        <i class="bx bx-x"></i> Hapus Semua
                                    </button>
                                    <span class="ms-2 text-muted">
                                        Terpilih: <span class="badge bg-primary"
                                            id="mapelCount">{{ count($assignedMapelIds) }}</span> mapel
                                    </span>
                                </div>
                                @if ($mataPelajaran->count() > 0)
                                    <div class="row" style="max-height: 250px; overflow-y: auto;">
                                        @foreach ($mataPelajaran as $mapel)
                                            <div class="col-md-4 col-sm-6 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input mapel-checkbox" type="checkbox"
                                                        name="mapel[]" value="{{ $mapel->id_mapel }}"
                                                        id="mapel_{{ $mapel->id_mapel }}"
                                                        {{ in_array($mapel->id_mapel, old('mapel', $assignedMapelIds)) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="mapel_{{ $mapel->id_mapel }}">
                                                        {{ $mapel->nama_mapel }}
                                                        <small class="text-muted d-block">{{ $mapel->kode_mapel }}</small>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-muted">
                                        <i class="bx bx-info-circle me-1"></i>
                                        Belum ada mata pelajaran.
                                        <a href="{{ route('admin.matapelajaran.create') }}">Tambah mata pelajaran</a>
                                        terlebih dahulu.
                                    </div>
                                @endif
                            </div>
                        </div>
                        @error('mapel')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Pilih mata pelajaran yang diajarkan di kelas ini</div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.kelas.index') }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back me-1"></i> Kembali
                        </a>
                        <button class="btn btn-primary">
                            <i class="bx bx-save me-1"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const checkboxes = document.querySelectorAll('.mapel-checkbox');
                const mapelCount = document.getElementById('mapelCount');
                const selectAllBtn = document.getElementById('selectAllMapel');
                const deselectAllBtn = document.getElementById('deselectAllMapel');

                function updateCount() {
                    const checked = document.querySelectorAll('.mapel-checkbox:checked').length;
                    mapelCount.textContent = checked;
                }

                checkboxes.forEach(cb => {
                    cb.addEventListener('change', updateCount);
                });

                selectAllBtn.addEventListener('click', function() {
                    checkboxes.forEach(cb => cb.checked = true);
                    updateCount();
                });

                deselectAllBtn.addEventListener('click', function() {
                    checkboxes.forEach(cb => cb.checked = false);
                    updateCount();
                });
            });
        </script>
    @endpush
@endsection
