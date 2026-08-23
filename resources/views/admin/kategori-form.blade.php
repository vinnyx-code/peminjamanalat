@extends('layouts.app')
@section('title', $kategori->exists ? 'Edit Kategori' : 'Tambah Kategori')
@section('content')
<div class="mb-4"><a href="{{ route('admin.kategori.index') }}" class="small text-decoration-none"><i class="bi bi-arrow-left me-1"></i>Kembali</a><h2 class="fw-bold mt-3">{{ $kategori->exists ? 'Edit Kategori' : 'Tambah Kategori' }}</h2></div>
<div class="card p-4" style="max-width:560px"><form method="POST" action="{{ $kategori->exists ? route('admin.kategori.update',$kategori) : route('admin.kategori.store') }}">@csrf @if($kategori->exists) @method('PUT') @endif<label class="form-label">Nama kategori</label><input name="nama_kategori" value="{{ old('nama_kategori',$kategori->nama_kategori) }}" class="form-control" required><button class="btn btn-primary mt-4">Simpan</button> <a href="{{ route('admin.kategori.index') }}" class="btn btn-light mt-4">Batal</a></form></div>
@endsection
