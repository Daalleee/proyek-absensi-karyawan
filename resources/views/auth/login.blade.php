@extends('layouts.guest')

@section('title', 'Login - Sistem Absensi')

@section('content')
<div class="auth-card p-4">
    <div class="text-center mb-4">
        <h3 class="fw-bold text-primary">Sistem Absensi</h3>
        <p class="text-muted">Silakan login untuk melanjutkan</p>
    </div>

    <form method="POST" action="{{ url('/login') }}">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                   id="email" name="email" value="{{ old('email') }}" required autofocus>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                   id="password" name="password" required>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="remember" name="remember">
            <label class="form-check-label" for="remember">Ingat saya</label>
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-3">Login</button>
    </form>

    <div class="text-center">
        <span class="text-muted">Belum punya akun?</span>
        <a href="{{ route('register') }}" class="text-decoration-none">Daftar di sini</a>
    </div>
</div>
@endsection
