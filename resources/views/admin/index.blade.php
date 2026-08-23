@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12 p-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Daftar Alat</h4>
        <a href="{{ route('admin.alat.create') }}" class="btn btn-primary">Tambah Alat</a>
      </div>

      <div class="card">
        <div class="card-body">
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th>#</th>
                <th>Nama Alat</th>
                <th>Kategori</th>
                <th>Stok</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach($alats as $index => $alat)
              <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $alat->nama_alat }}</td>
                <td>{{ $alat->kategori->nama_kategori ?? '-' }}</td>
                <td>{{ $alat->stok }}</td>
                <td><span class="badge bg-{{ $alat->status=='ada' ? 'success' : ($alat->status=='rusak' ? 'danger' : 'secondary') }}">{{ $alat->status }}</span></td>
                <td>
                  <a href="{{ route('admin.alat.edit', $alat->id) }}" class="btn btn-sm btn-warning">Edit</a>
                  <form action="{{ route('admin.alat.destroy', $alat->id) }}" method="POST" style="display:inline-block">@csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus alat?')">Hapus</button>
                  </form>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection
