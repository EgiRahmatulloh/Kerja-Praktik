@extends('layouts.main')

@section('container')
<div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
        <div class="col-12 col-md-8 col-lg-6 col-xl-5">
            <div class="card bg-white text-dark shadow-lg" style="border-radius: 1rem;">
                <div class="card-body p-5">
                    <div class="mb-md-4 mt-md-4">
                        <div class="text-center mb-4">
                            <img src="{{ asset('img/iconWeb.png') }}" alt="Logo" width="80">
                            <h2 class="fw-bold mb-0 text-primary">Arsip Surat</h2>
                            <p class="text-muted mb-4">Form Pendaftaran Akun</p>
                        </div>

                        <form action="/register" method="post">
                            @csrf
                            <div class="form-floating mb-3">
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Nama Lengkap" required value="{{ old('name') }}">
                                <label for="name"><i class="bi bi-person-badge-fill me-2"></i>Nama Lengkap</label>
                                @error('name')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            
                            <div class="form-floating mb-3">
                                <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" id="username" placeholder="Username" required value="{{ old('username') }}">
                                <label for="username"><i class="bi bi-person-fill me-2"></i>Username</label>
                                @error('username')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            
                            <div class="form-floating mb-3">
                                <input type="text" name="nim" class="form-control @error('nim') is-invalid @enderror" id="nim" placeholder="Nomor Induk Mahasiswa" required value="{{ old('nim') }}">
                                <label for="nim"><i class="bi bi-card-heading me-2"></i>NIM</label>
                                @error('nim')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            
                            <div class="form-floating mb-3">
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" id="email" placeholder="name@example.com" required value="{{ old('email') }}">
                                <label for="email"><i class="bi bi-envelope-fill me-2"></i>Email</label>
                                @error('email')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            
                            <div class="form-floating mb-4">
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="password" placeholder="Password" required>
                                <label for="password"><i class="bi bi-lock-fill me-2"></i>Password</label>
                                @error('password')
                                <div class="invalid-feedback text-start">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <button class="btn btn-primary btn-lg w-100 mb-3" type="submit">
                                <i class="bi bi-person-plus-fill me-2"></i>Daftar
                            </button>
                        </form>
                        
                        <div class="text-center mt-3">
                            <p class="mb-0">Sudah memiliki akun? 
                                <a href="/login" class="text-primary fw-bold">Login</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        transition: all 0.3s;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.2) !important;
    }
    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.25rem rgba(22, 213, 255, 0.25);
    }
    .btn-primary {
        background-color: var(--primary);
        border-color: var(--primary);
        transition: all 0.3s;
    }
    .btn-primary:hover {
        background-color: #14c0e7;
        border-color: #14c0e7;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
</style>
@endsection