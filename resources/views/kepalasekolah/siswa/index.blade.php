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
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" placeholder="Cari NISN / Nama..." value="{{ $search ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="ta" id="filter_ta">
                            <option value="">Semua Tahun Ajaran</option>
                            @foreach ($taList as $t)
                                <option value="{{ $t->id_ta }}" {{ ($filterTA ?? '') == $t->id_ta ? 'selected' : '' }}>
                                    {{ $t->tahun_ajaran }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="semester" id="filter_semester">
                            <option value="">Semua Semester</option>
                            @foreach ($semesterList as $s)
                                <option value="{{ $s->id_semester }}" data-ta="{{ $s->id_ta }}" {{ ($filterSemester ?? '') == $s->id_semester ? 'selected' : '' }}>
                                    {{ $s->nama_semester }} ({{ $s->periode_bulan }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="kelas">
                            <option value="">Semua Kelas</option>
                            @foreach ($kelasList as $k)
                                <option value="{{ $k->id_kelas }}" {{ ($filterKelas ?? '') == $k->id_kelas ? 'selected' : '' }}>
                                    {{ $k->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1"><i class="bx bx-search"></i> Filter</button>
                        <a href="{{ route('kepalasekolah.siswa.index') }}" class="btn btn-outline-secondary"><i class="bx bx-reset"></i></a>
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
                            <th>L/P</th>
                            <th>Tahun Ajaran</th>
                            <th>Semester Masuk</th>
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
                                <td>
                                    <span class="badge bg-label-primary">{{ $siswa->tahunAjaran->tahun_ajaran ?? '-' }}</span>
                                </td>
                                <td>
                                    @if($siswa->semester)
                                        <span class="badge bg-label-{{ $siswa->semester->nama_semester == 'Ganjil' ? 'info' : 'warning' }}">
                                            {{ $siswa->semester->nama_semester }}
                                        </span>
                                        @if($siswa->isSiswaBaruGenap())
                                            <span class="badge bg-label-success ms-1" title="Masuk di Semester Genap"><i class="bx bx-user-plus"></i> Baru</span>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data siswa.</td>
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const taSelect = document.getElementById('filter_ta');
        const semesterSelect = document.getElementById('filter_semester');
        if (taSelect && semesterSelect) {
            const allSemesterOptions = Array.from(semesterSelect.querySelectorAll('option')).slice(1);
            function updateSemesterOptions() {
                const selectedTa = taSelect.value;
                const currentVal = semesterSelect.value;
                semesterSelect.innerHTML = '<option value="">Semua Semester</option>';
                allSemesterOptions.forEach(opt => {
                    if (!selectedTa || opt.getAttribute('data-ta') === selectedTa) {
                        const newOpt = opt.cloneNode(true);
                        if (newOpt.value === currentVal) newOpt.selected = true;
                        semesterSelect.appendChild(newOpt);
                    }
                });
            }
            taSelect.addEventListener('change', updateSemesterOptions);
        }
    });
</script>
@endpush
