@extends('layouts.main')

@section('container')
<div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
        <div class="col-12 col-md-8 col-lg-6 col-xl-5">
            <div class="card bg-white text-dark shadow-lg" style="border-radius: 1rem;">
                <div class="card-body p-5 text-center">
                    <div class="mb-md-4 mt-md-4">
                        <div class="text-center mb-4">
                            <img src="{{ asset('img/iconWeb.png') }}" alt="Logo" width="80">
                            <h2 class="fw-bold mb-0 text-primary">Arsip Surat</h2>
                            <p class="text-muted mb-4">Silahkan login untuk melanjutkan</p>
                        </div>

                        @if (session()->has('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session()->has('loginError'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('loginError') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="/login" method="post">
                            @csrf
                            <div class="form-floating mb-3">
                                <input type="text" name="login" class="form-control @error('login') is-invalid @enderror" id="login" placeholder="Username atau NIM" autofocus required value="{{ old('login') }}">
                                <label for="login"><i class="bi bi-person-fill me-2"></i>Username atau NIM</label>
                                @error('login')
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

                            <button class="btn btn-primary btn-lg w-100 mb-4" type="submit">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Login
                            </button>
                        </form>
                        
                        <div class="mt-3">
                            <p class="mb-0">Belum memiliki akun? 
                                <a href="/register" class="text-primary fw-bold">Daftar Sekarang!</a>
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