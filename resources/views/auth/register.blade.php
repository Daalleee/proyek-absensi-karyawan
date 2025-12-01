@extends('layouts.guest')

@section('title', 'Register - Sistem Absensi')

@section('content')
<div class="auth-card p-4">
    <div class="text-center mb-4">
        <h3 class="fw-bold text-primary">Daftar Akun</h3>
        <p class="text-muted">Buat akun baru untuk mulai absensi</p>
    </div>

    <form method="POST" action="{{ url('/register') }}">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                   id="name" name="name" value="{{ old('name') }}" required autofocus>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                   id="email" name="email" value="{{ old('email') }}" required>
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

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
            <input type="password" class="form-control" 
                   id="password_confirmation" name="password_confirmation" required>
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-3">Daftar</button>
    </form>

    <div class="text-center">
        <span class="text-muted">Sudah punya akun?</span>
        <a href="{{ route('login') }}" class="text-decoration-none">Login di sini</a>
    </div>
</div>
@endsection
