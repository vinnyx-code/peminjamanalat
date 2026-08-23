@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row justify-content-center align-items-center vh-100">
    <div class="col-md-5 col-lg-4">
      <div class="card shadow-sm">
        <div class="card-body p-4">
          <h4 class="mb-3 text-center">Login</h4>
          <p class="text-muted text-center small mb-4">Masuk menggunakan email dan password</p>

          @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
          @endif

          <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus />
            </div>
            <div class="mb-3">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control" required />
            </div>
            <div class="d-grid mb-2">
              <button class="btn btn-primary">Masuk</button>
            </div>
          </form>

          <div class="text-center small">
            Belum punya akun? <a href="{{ url('/') }}">Daftar sebagai peminjam</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
