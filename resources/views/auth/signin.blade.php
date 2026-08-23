@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row justify-content-center align-items-center vh-100">
    <div class="col-md-6 col-lg-5">
      <div class="card shadow-sm">
        <div class="card-body p-4">
          <h3 class="card-title mb-3 text-center">Daftar Peminjam</h3>
          <p class="text-muted text-center mb-4">Buat akun untuk mulai meminjam alat</p>

          @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
          @endif

          <form method="POST" action="{{ route('register.post') }}">
            @csrf
            <div class="mb-3">
              <label class="form-label">Nama Lengkap</label>
              <input name="nama" value="{{ old('nama') }}" class="form-control" required />
            </div>
            <div class="mb-3">
              <label class="form-label">Username</label>
              <input name="username" value="{{ old('username') }}" class="form-control" required />
            </div>
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" value="{{ old('email') }}" class="form-control" required />
            </div>
            <div class="mb-3">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control" required />
            </div>
            <div class="mb-3">
              <label class="form-label">Konfirmasi Password</label>
              <input type="password" name="password_confirmation" class="form-control" required />
            </div>

            <div class="d-grid">
              <button class="btn btn-primary">Daftar</button>
            </div>
          </form>

          <hr class="my-4">
          <p class="text-center small">Sudah punya akun? <a href="{{ route('login') }}">Login disini</a></p>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
