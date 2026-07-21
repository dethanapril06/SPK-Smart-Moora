@extends('layouts.walikelas')
@section('title', 'Kelulusan Siswa')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('walikelas.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('walikelas.kelas.index') }}">Kelas Saya</a></li>
                    <li class="breadcrumb-item active">Kelulusan Siswa</li>
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

        {{-- Info Alert --}}
        <div class="alert alert-warning mb-4" role="alert">
            <div class="d-flex align-items-start">
                <i class="bx bx-graduation fs-4 me-2 mt-1"></i>
                <div>
                    <strong>Petunjuk Kelulusan Siswa — {{ $myKelas->nama_kelas }}:</strong>
                    <ul class="mb-0 ps-3 mt-1 text-muted">
                        <li>Anda hanya dapat meluluskan siswa dari kelas Anda sendiri: <strong>{{ $myKelas->nama_kelas }}</strong>.</li>
                        <li>Siswa yang diluluskan akan ditandai <span class="badge bg-success">Lulus</span>, tidak lagi terdaftar di kelas manapun, dan data nilai tetap tersimpan sebagai arsip.</li>
                        <li>Proses ini <strong>tidak dapat dibatalkan</strong>. Pastikan data siswa sudah benar sebelum menyimpan.</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Filter Form --}}
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('walikelas.kelas.kelulusan.index') }}" method="GET">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label" for="tahun_ajaran">Tahun Ajaran</label>
                            <select class="form-select" id="tahun_ajaran" name="tahun_ajaran" required>
                                <option value="">-- Pilih Tahun Ajaran --</option>
                                @foreach ($tahunAjaranList as $ta)
                                    <option value="{{ $ta }}" {{ $sourceTahunAjaran === $ta ? 'selected' : '' }}>
                                        {{ $ta }}{{ $activeTahunAjaran === $ta ? ' (Aktif)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-auto">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-search me-1"></i> Tampilkan Siswa
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Student Table --}}
        @if ($sourceTahunAjaran)
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="mb-0">
                            Daftar Siswa &mdash;
                            <span class="text-primary">{{ $myKelas->nama_kelas }}</span>
                            <span class="text-muted fw-normal fs-6">Tahun Ajaran {{ $sourceTahunAjaran }}</span>
                        </h5>
                        <small class="text-muted">Total: {{ $siswaList->count() }} siswa aktif</small>
                    </div>
                </div>

                @if ($siswaList->isEmpty())
                    <div class="card-body text-center py-5">
                        <i class="bx bx-user-x fs-1 text-muted d-block mb-2"></i>
                        <p class="text-muted mb-0">Tidak ada siswa aktif di kelas ini, atau semua siswa sudah diluluskan.</p>
                    </div>
                @else
                    <form action="{{ route('walikelas.kelas.kelulusan.store') }}" method="POST" id="form-kelulusan">
                        @csrf
                        <input type="hidden" name="tahun_ajaran" value="{{ $sourceTahunAjaran }}">

                        <div class="card-body border-bottom py-3">
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="select_all">
                                    <label class="form-check-label fw-semibold" for="select_all">Pilih Semua</label>
                                </div>
                                <span class="text-muted" id="selected_count_label">0 siswa dipilih</span>
                                <button type="button" id="btn_lulus" class="btn btn-success ms-auto" disabled
                                    data-bs-toggle="modal" data-bs-target="#modal-konfirmasi">
                                    <i class="bx bx-check-circle me-1"></i> Luluskan Siswa Terpilih
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50" class="text-center">#</th>
                                        <th width="50" class="text-center">Pilih</th>
                                        <th>NISN</th>
                                        <th>Nama Siswa</th>
                                        <th width="80" class="text-center">JK</th>
                                        <th>Alamat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($siswaList as $i => $siswa)
                                        <tr>
                                            <td class="text-center text-muted">{{ $i + 1 }}</td>
                                            <td class="text-center">
                                                <input class="form-check-input siswa-checkbox" type="checkbox"
                                                    name="siswa_ids[]" value="{{ $siswa->id_siswa }}"
                                                    id="siswa_{{ $siswa->id_siswa }}">
                                            </td>
                                            <td>
                                                <label for="siswa_{{ $siswa->id_siswa }}" class="mb-0">
                                                    <code>{{ $siswa->nisn }}</code>
                                                </label>
                                            </td>
                                            <td>
                                                <label for="siswa_{{ $siswa->id_siswa }}" class="mb-0 fw-medium">
                                                    {{ $siswa->nama_siswa }}
                                                </label>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge {{ $siswa->jenis_kelamin === 'L' ? 'bg-label-primary' : 'bg-label-danger' }}">
                                                    {{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                                </span>
                                            </td>
                                            <td class="text-muted">{{ $siswa->alamat ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </form>

                    {{-- Confirmation Modal --}}
                    <div class="modal fade" id="modal-konfirmasi" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="modal-title">
                                        <i class="bx bx-error-circle text-warning me-2"></i>Konfirmasi Kelulusan
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p class="mb-2">
                                        Anda akan meluluskan <strong id="confirm_count">0</strong> siswa dari
                                        <strong>{{ $myKelas->nama_kelas }}</strong>
                                        Tahun Ajaran <strong>{{ $sourceTahunAjaran }}</strong>.
                                    </p>
                                    <div class="alert alert-danger py-2 mb-0">
                                        <i class="bx bx-info-circle me-1"></i>
                                        <strong>Proses ini tidak dapat dibatalkan.</strong>
                                        Status siswa akan berubah menjadi <strong>Lulus</strong>.
                                    </div>
                                </div>
                                <div class="modal-footer border-0 pt-0">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="button" class="btn btn-success" id="btn_confirm_lulus">
                                        <i class="bx bx-check-circle me-1"></i> Ya, Luluskan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkboxes = document.querySelectorAll('.siswa-checkbox');
    const selectAll = document.getElementById('select_all');
    const btnLulus = document.getElementById('btn_lulus');
    const selectedCountLabel = document.getElementById('selected_count_label');
    const confirmCount = document.getElementById('confirm_count');
    const btnConfirmLulus = document.getElementById('btn_confirm_lulus');
    const formKelulusan = document.getElementById('form-kelulusan');

    function updateCount() {
        const selected = document.querySelectorAll('.siswa-checkbox:checked').length;
        if (selectedCountLabel) selectedCountLabel.textContent = selected + ' siswa dipilih';
        if (btnLulus) btnLulus.disabled = selected === 0;
        if (confirmCount) confirmCount.textContent = selected;
        if (checkboxes.length > 0 && selectAll) {
            selectAll.indeterminate = selected > 0 && selected < checkboxes.length;
            selectAll.checked = selected === checkboxes.length;
        }
    }

    checkboxes.forEach(cb => cb.addEventListener('change', updateCount));

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            checkboxes.forEach(cb => { cb.checked = this.checked; });
            updateCount();
        });
    }

    if (btnConfirmLulus) {
        btnConfirmLulus.addEventListener('click', function () {
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Memproses...';
            formKelulusan.submit();
        });
    }

    updateCount();
});
</script>
@endpush
