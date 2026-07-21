@extends('layouts.admin')
@section('title', 'Edit Riwayat Pelanggaran')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.riwayatpelanggaran.index') }}">Riwayat Pelanggaran</a>
                </li>
                <li class="breadcrumb-item active">Edit Riwayat</li>
            </ol>
        </nav>

        <div class="card">
            <h5 class="card-header">Form Edit Riwayat Pelanggaran</h5>
            <div class="card-body">
                <form action="{{ route('admin.riwayatpelanggaran.update', $riwayatpelanggaran->id_riwayat) }}"
                    method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Data Siswa Section -->
                    <h6 class="mb-3 text-primary"><i class="bx bx-user me-2"></i>Data Siswa</h6>
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <label class="form-label" for="id_siswa">Siswa <span class="text-danger">*</span></label>
                            <select id="id_siswa" name="id_siswa"
                                class="form-select @error('id_siswa') is-invalid @enderror" required>
                                <option value="">Pilih Siswa</option>
                                @foreach ($siswaList as $siswa)
                                    <option value="{{ $siswa->id_siswa }}"
                                        {{ old('id_siswa', $riwayatpelanggaran->id_siswa) == $siswa->id_siswa ? 'selected' : '' }}
                                        data-kelas="{{ $siswa->kelas ? $siswa->kelas->nama_kelas : '-' }}"
                                        data-ta="{{ $siswa->tahunAjaran ? $siswa->tahunAjaran->tahun_ajaran . ' - ' . $siswa->tahunAjaran->semester : '-' }}">
                                        {{ $siswa->nisn }} - {{ $siswa->nama_siswa }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_siswa')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Data Pelanggaran Section -->
                    <h6 class="mb-3 text-primary"><i class="bx bx-error me-2"></i>Data Pelanggaran</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="id_ta">Tahun Ajaran <span
                                        class="text-danger">*</span></label>
                                <select id="id_ta" name="id_ta"
                                    class="form-select @error('id_ta') is-invalid @enderror" required>
                                    @foreach ($tahunAjaranList as $ta)
                                        <option value="{{ $ta->id_ta }}"
                                            {{ old('id_ta', $riwayatpelanggaran->id_ta) == $ta->id_ta ? 'selected' : '' }}>
                                            {{ $ta->tahun_ajaran }} {{ $ta->is_active ? '(Aktif)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_ta')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="id_semester">Semester <span
                                        class="text-danger">*</span></label>
                                <select id="id_semester" name="id_semester"
                                    class="form-select @error('id_semester') is-invalid @enderror" required>
                                    <option value="">-- Pilih Semester --</option>
                                    @foreach ($semesterList as $s)
                                        <option value="{{ $s->id_semester }}" data-id-ta="{{ $s->id_ta }}"
                                            {{ old('id_semester', $riwayatpelanggaran->id_semester) == $s->id_semester ? 'selected' : '' }}>
                                            {{ $s->nama_semester }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_semester')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="tanggal_kejadian">Tanggal Kejadian <span
                                        class="text-danger">*</span></label>
                                <input type="date" id="tanggal_kejadian" name="tanggal_kejadian"
                                    class="form-control @error('tanggal_kejadian') is-invalid @enderror"
                                    value="{{ old('tanggal_kejadian', $riwayatpelanggaran->tanggal_kejadian->format('Y-m-d')) }}"
                                    required />
                                @error('tanggal_kejadian')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="kategori_filter">Kategori Pelanggaran</label>
                                <select id="kategori_filter" class="form-select">
                                    <option value="">Pilih Kategori untuk Filter</option>
                                    @foreach ($kategoriList as $kat)
                                        <option value="{{ $kat }}"
                                            {{ $riwayatpelanggaran->jenisPelanggaran && $riwayatpelanggaran->jenisPelanggaran->kategori_pelanggaran == $kat ? 'selected' : '' }}>
                                            {{ str_replace('_', ' ', $kat) }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Filter untuk mempermudah pencarian jenis pelanggaran</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="id_jenis_pelanggaran">Jenis Pelanggaran <span
                                        class="text-danger">*</span></label>
                                <select id="id_jenis_pelanggaran" name="id_jenis_pelanggaran"
                                    class="form-select @error('id_jenis_pelanggaran') is-invalid @enderror" required>
                                    <option value="{{ $riwayatpelanggaran->id_jenis_pelanggaran }}" selected
                                        data-bobot="{{ $riwayatpelanggaran->jenisPelanggaran->bobot_poin ?? 0 }}">
                                        {{ $riwayatpelanggaran->jenisPelanggaran->nama_pelanggaran ?? 'Pilih Jenis Pelanggaran' }}
                                    </option>
                                </select>
                                @error('id_jenis_pelanggaran')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <div id="bobot_display" class="mt-2" style="display: block;">
                                    <small class="text-muted">Bobot Poin:
                                        @if ($riwayatpelanggaran->jenisPelanggaran)
                                            @php
                                                $poin = $riwayatpelanggaran->jenisPelanggaran->bobot_poin;
                                                $badgeClass =
                                                    $poin >= 20
                                                        ? 'bg-label-danger'
                                                        : ($poin >= 10
                                                            ? 'bg-label-warning'
                                                            : 'bg-label-info');
                                            @endphp
                                            <span id="bobot_value" class="badge {{ $badgeClass }}">{{ $poin }}
                                                poin</span>
                                        @else
                                            <span id="bobot_value" class="badge"></span>
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="keterangan_tambahan">Keterangan Tambahan</label>
                        <textarea id="keterangan_tambahan" name="keterangan_tambahan"
                            class="form-control @error('keterangan_tambahan') is-invalid @enderror" rows="3"
                            placeholder="Tambahan informasi atau catatan...">{{ old('keterangan_tambahan', $riwayatpelanggaran->keterangan_tambahan) }}</textarea>
                        @error('keterangan_tambahan')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.riwayatpelanggaran.index') }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back me-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
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
                const kategoriFilter = document.getElementById('kategori_filter');
                const jenisPelanggaranSelect = document.getElementById('id_jenis_pelanggaran');
                const bobotDisplay = document.getElementById('bobot_display');
                const bobotValue = document.getElementById('bobot_value');

                // Load jenis pelanggaran by kategori
                kategoriFilter.addEventListener('change', function() {
                    const kategori = this.value;
                    jenisPelanggaranSelect.innerHTML = '<option value="">Loading...</option>';
                    bobotDisplay.style.display = 'none';

                    if (!kategori) {
                        jenisPelanggaranSelect.innerHTML =
                            '<option value="">Pilih Kategori terlebih dahulu</option>';
                        return;
                    }

                    // Fetch jenis pelanggaran
                    fetch(`/admin/riwayatpelanggaran/get-jenis-pelanggaran?kategori=${kategori}`)
                        .then(response => response.json())
                        .then(data => {
                            jenisPelanggaranSelect.innerHTML =
                                '<option value="">Pilih Jenis Pelanggaran</option>';
                            data.forEach(jp => {
                                const option = document.createElement('option');
                                option.value = jp.id_jenis_pelanggaran;
                                option.textContent = jp.nama_pelanggaran;
                                option.dataset.bobot = jp.bobot_poin;
                                jenisPelanggaranSelect.appendChild(option);
                            });
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            jenisPelanggaranSelect.innerHTML =
                                '<option value="">Error loading data</option>';
                        });
                });

                // Show bobot when jenis pelanggaran selected
                jenisPelanggaranSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    if (selectedOption.dataset.bobot) {
                        const poin = parseInt(selectedOption.dataset.bobot);
                        const badgeClass = poin >= 20 ? 'bg-label-danger' : (poin >= 10 ? 'bg-label-warning' :
                            'bg-label-info');
                        bobotValue.className = 'badge ' + badgeClass;
                        bobotValue.textContent = poin + ' poin';
                        bobotDisplay.style.display = 'block';
                    } else {
                        bobotDisplay.style.display = 'none';
                    }
                });
            });
        </script>
    @endpush
@endsection
