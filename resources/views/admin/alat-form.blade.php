@extends('layouts.app')
@section('title', $alat->exists ? 'Edit Alat' : 'Tambah Alat')
@section('content')
<div class="mb-4"><a href="{{ route('dashboard') }}" class="text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Kembali ke daftar alat</a><h2 class="fw-bold mt-3">{{ $alat->exists ? 'Edit Alat' : 'Tambah Alat' }}</h2></div>
<div class="card p-3 p-md-4" style="max-width:720px"><form method="POST" action="{{ $alat->exists ? route('admin.alat.update',$alat) : route('admin.alat.store') }}">@csrf @if($alat->exists) @method('PUT') @endif
<div class="mb-3"><label class="form-label">Nama alat</label><input name="nama_alat" value="{{ old('nama_alat',$alat->nama_alat) }}" class="form-control" required></div>
<div class="row g-3 mb-3"><div class="col-md-6"><label class="form-label">Kategori</label><select name="kategori_id" class="form-select" required><option value="">Pilih kategori</option>@foreach($kategoris as $kategori)<option value="{{ $kategori->id }}" @selected(old('kategori_id',$alat->kategori_id)==$kategori->id)>{{ $kategori->nama_kategori }}</option>@endforeach</select></div><div class="col-md-6"><label class="form-label">Stok</label><input type="number" min="0" name="stok" value="{{ old('stok',$alat->stok ?? 0) }}" class="form-control" required></div></div>
<div class="mb-3"><label class="form-label">Status</label><select name="status" class="form-select">@foreach(['ada','rusak','dipinjam','tidak_tersedia'] as $status)<option value="{{ $status }}" @selected(old('status',$alat->status ?: 'ada')===$status)>{{ str_replace('_',' ',ucfirst($status)) }}</option>@endforeach</select></div>
<div class="mb-4"><label class="form-label">Deskripsi <span class="text-muted">(opsional)</span></label><textarea name="deskripsi" rows="4" class="form-control">{{ old('deskripsi',$alat->deskripsi) }}</textarea></div><button class="btn btn-primary">Simpan alat</button> <a href="{{ route('dashboard') }}" class="btn btn-light">Batal</a></form></div>
@endsection
