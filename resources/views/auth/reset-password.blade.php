@extends('layouts.app')

@section('content')
<div class="container mt-5" style="max-width: 500px;">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-3 text-center">Reset Password</h4>

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-3">
                    <label for="email" class="form-label">Alamat Email</label>
                    <input type="email" name="email" id="email" class="form-control"
                           placeholder="Masukkan email Anda" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password Baru</label>
                    <input type="password" name="password" id="password" class="form-control"
                           placeholder="Minimal 6 karakter" required>
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="form-control" required>
                </div>

                <button type="submit" class="btn btn-success w-100">Reset Password</button>
            </form>

            <div class="mt-3 text-center">
                <a href="{{ route('login.form') }}">← Kembali ke Login</a>
            </div>
        </div>
    </div>
</div>
@endsection
