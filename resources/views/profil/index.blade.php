@extends($layout)
@section('title', 'Profil Saya')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item">Akun</li>
                <li class="breadcrumb-item active">Profil Saya</li>
            </ol>
        </nav>

        <div class="row">
            {{-- Profil Info --}}
            <div class="col-md-12">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </div>

            {{-- Update Profile --}}
            <div class="col-md-6">
                <div class="card mb-4">
                    <h5 class="card-header">Informasi Profil</h5>
                    <div class="card-body">
                        <div class="d-flex align-items-start align-items-sm-center gap-4 mb-4">
                            <img src="https://ui-avatars.com/api/?background=0D8ABC&color=fff&name={{ urlencode(auth()->user()->name) }}" alt class="w-px-100 h-auto rounded-circle" />
                            <div class="button-wrapper">
                                <h5 class="mb-1">{{ $user->name }}</h5>
                                <span class="badge bg-label-primary">{{ $user->level }}</span>
                            </div>
                        </div>

                        <form action="{{ route('profil.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="name" class="form-label">Nama</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $user->name) }}"
                                    placeholder="Masukkan nama" />
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email', $user->email) }}"
                                    placeholder="Masukkan email" />
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Level</label>
                                <input type="text" class="form-control" value="{{ $user->level }}" disabled />
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Bergabung Sejak</label>
                                <input type="text" class="form-control" value="{{ $user->created_at->format('d F Y') }}"
                                    disabled />
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Simpan Perubahan
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Update Password --}}
            <div class="col-md-6">
                <div class="card mb-4">
                    <h5 class="card-header">Ubah Password</h5>
                    <div class="card-body">
                        <form action="{{ route('profil.password') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3 form-password-toggle">
                                <label for="current_password" class="form-label">Password Lama</label>
                                <div class="input-group input-group-merge">
                                    <input type="password"
                                        class="form-control @error('current_password') is-invalid @enderror"
                                        id="current_password" name="current_password"
                                        placeholder="Masukkan password lama" />
                                    <span class="input-group-text cursor-pointer toggle-password"><i
                                            class="bx bx-hide"></i></span>
                                </div>
                            </div>

                            <div class="mb-3 form-password-toggle">
                                <label for="password" class="form-label">Password Baru</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" name="password" placeholder="Masukkan password baru" />
                                    <span class="input-group-text cursor-pointer toggle-password"><i
                                            class="bx bx-hide"></i></span>
                                </div>
                            </div>

                            <div class="mb-3 form-password-toggle">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation" placeholder="Konfirmasi password baru" />
                                    <span class="input-group-text cursor-pointer toggle-password"><i
                                            class="bx bx-hide"></i></span>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-warning">
                                <i class="bx bx-lock-alt me-1"></i> Ubah Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.toggle-password').forEach(function(el) {
            el.addEventListener('click', function() {
                const input = this.previousElementSibling;
                const icon = this.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('bx-hide');
                    icon.classList.add('bx-show');
                } else {
                    input.type = 'password';
                    icon.classList.remove('bx-show');
                    icon.classList.add('bx-hide');
                }
            });
        });
    </script>
@endpush
