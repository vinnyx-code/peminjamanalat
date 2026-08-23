@extends('layouts.app')
@section('title', 'Kelola User')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4"><div><div class="eyebrow">Master data</div><h2 class="fw-bold mb-1">Kelola User</h2><p class="text-muted mb-0">Atur akun dan hak akses pengguna.</p></div><a href="{{ route('admin.users.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-2"></i>Tambah User</a></div>
@if(session('success'))<div class="alert alert-success border-0">{{ session('success') }}</div>@endif @if(session('error'))<div class="alert alert-danger border-0">{{ session('error') }}</div>@endif
<div class="card p-2 p-md-3"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Nama</th><th>Username</th><th>Email</th><th>Role</th><th class="text-end">Aksi</th></tr></thead><tbody>
@forelse($users as $userData)<tr><td class="fw-semibold">{{ $userData->nama ?: $userData->name }}</td><td>{{ $userData->username }}</td><td>{{ $userData->email }}</td><td><span class="badge text-bg-{{ $userData->role === 'admin' ? 'dark' : ($userData->role === 'petugas' ? 'primary' : 'secondary') }}">{{ ucfirst($userData->role) }}</span></td><td class="text-end"><a href="{{ route('admin.users.edit',$userData) }}" class="btn btn-sm btn-light text-primary">Edit</a><form class="d-inline" method="POST" action="{{ route('admin.users.destroy',$userData) }}" onsubmit="return confirm('Hapus user ini?')">@csrf @method('DELETE')<button class="btn btn-sm btn-light text-danger">Hapus</button></form></td></tr>@empty<tr><td colspan="5" class="text-center text-muted py-4">Belum ada user.</td></tr>@endforelse
</tbody></table></div>{{ $users->links() }}</div>
@endsection
