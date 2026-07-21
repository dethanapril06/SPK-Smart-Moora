@extends('layouts.walikelas')
@section('title', 'Naik Kelas')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('walikelas.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('walikelas.kelas.index') }}">Kelas Saya</a></li>
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

        {{-- Info Alert --}}
        <div class="alert alert-primary mb-4" role="alert">
            <div class="d-flex align-items-start">
                <i class="bx bx-info-circle fs-4 me-2 mt-1"></i>
                <div>
                    <strong>Petunjuk Kenaikan Kelas — {{ $myKelas->nama_kelas }}:</strong>
                    <ul class="mb-0 ps-3 mt-1 text-muted">
                        <li>Anda hanya dapat menaikkan siswa dari kelas Anda sendiri: <strong>{{ $myKelas->nama_kelas }}</strong>.</li>
                        <li>Data siswa dipisahkan otomatis berdasarkan <strong>Tahun Ajaran</strong>, sehingga tidak akan bercampur dengan data tahun pelajaran sebelumnya.</li>
                        @if(str_starts_with($myKelas->id_kelas, 'XI.'))
                            <li>Karena kelas Anda adalah <strong>XI</strong>, kelas tujuan XII sudah dipilih otomatis sesuai jurusan.</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        {{-- Filter Form --}}
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('walikelas.kelas.naik-kelas.index') }}" method="GET">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label" for="tahun_ajaran">Tahun Ajaran Sumber</label>
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
                            Siswa <span class="text-primary">{{ $myKelas->nama_kelas }}</span>
                            <span class="text-muted fw-normal fs-6">TA {{ $sourceTahunAjaran }}</span>
                            @if($targetTahunAjaranLabel)
                                <i class="bx bx-right-arrow-alt mx-1 text-muted"></i>
                                <span class="text-success">TA {{ $targetTahunAjaranLabel }}</span>
                            @endif
                        </h5>
                        <small class="text-muted">Total: {{ $siswaList->count() }} siswa</small>
                    </div>
                </div>

                @if ($siswaList->isEmpty())
                    <div class="card-body text-center py-5">
                        <i class="bx bx-user-x fs-1 text-muted d-block mb-2"></i>
                        <p class="text-muted mb-0">Tidak ada siswa di kelas Anda untuk tahun ajaran ini.</p>
                    </div>
                @else
                    <form action="{{ route('walikelas.kelas.naik-kelas.store') }}" method="POST" id="form-naik-kelas">
                        @csrf
                        <input type="hidden" name="tahun_ajaran" value="{{ $sourceTahunAjaran }}">

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50" class="text-center">#</th>
                                        <th>NISN</th>
                                        <th>Nama Siswa</th>
                                        <th width="80" class="text-center">JK</th>
                                        <th>Kelas Tujuan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($siswaList as $i => $siswa)
                                        <tr>
                                            <td class="text-center text-muted">{{ $i + 1 }}</td>
                                            <td><code>{{ $siswa->nisn }}</code></td>
                                            <td class="fw-medium">{{ $siswa->nama_siswa }}</td>
                                            <td class="text-center">
                                                <span class="badge {{ $siswa->jenis_kelamin === 'L' ? 'bg-label-primary' : 'bg-label-danger' }}">
                                                    {{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                                </span>
                                            </td>
                                            <td>
                                                <select class="form-select form-select-sm kelas-tujuan"
                                                    name="tujuan[{{ $siswa->id_siswa }}]"
                                                    id="tujuan_{{ $siswa->id_siswa }}"
                                                    style="min-width: 160px;"
                                                    {{ str_starts_with($myKelas->id_kelas, 'XI.') ? 'readonly' : '' }}>
                                                    <option value="">-- Pilih Kelas --</option>
                                                    @foreach ($targetKelasList as $kelas)
                                                        <option value="{{ $kelas->id_kelas }}"
                                                            {{ $defaultTargetKelasId === $kelas->id_kelas ? 'selected' : '' }}>
                                                            {{ $kelas->nama_kelas }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer d-flex justify-content-between align-items-center">
                            <small class="text-muted">Pastikan kelas tujuan sudah diisi sebelum menyimpan.</small>
                            <button type="submit" class="btn btn-warning" id="btn_naik_kelas">
                                <i class="bx bx-transfer-alt me-1"></i> Proses Naik Kelas
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // For XI classes, all selects are auto-filled — make them visually read-only (disabled backup via hidden input)
    const isXI = {{ str_starts_with($myKelas->id_kelas, 'XI.') ? 'true' : 'false' }};
    if (isXI) {
        document.querySelectorAll('.kelas-tujuan').forEach(function (sel) {
            sel.setAttribute('disabled', true);
            // Add a hidden input so the disabled value still submits
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = sel.name;
            hidden.value = sel.value;
            sel.parentNode.appendChild(hidden);
        });
    }
});
</script>
@endpush
