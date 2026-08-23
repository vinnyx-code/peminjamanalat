@extends('layouts.app')
@section('title', $judul)
@section('content')
<style>
    .admin-log-pagination .pagination {
        margin-bottom: 0;
        gap: 0.25rem;
    }

    .admin-log-pagination .page-link {
        min-width: 2.1rem;
        height: 2.1rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.35rem 0.6rem;
        font-size: 0.82rem;
        line-height: 1;
        border-radius: 0.4rem;
    }

    .admin-log-pagination .page-item.disabled .page-link {
        opacity: 0.65;
    }
</style>
<div class="mb-4"><div class="eyebrow">Administrasi</div><h2 class="fw-bold mb-1">{{ $judul }}</h2><p class="text-muted">Data tersimpan dari seluruh transaksi aplikasi.</p></div>
<div class="card p-2 p-md-3"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr>@if($jenis === 'peminjaman')<th>Peminjam</th><th>Alat</th><th>Periode</th><th>Status</th>@elseif($jenis === 'pengembalian')<th>Peminjam</th><th>Alat</th><th>Tanggal Kembali</th><th>Denda</th><th>Kondisi</th>@else<th>Waktu</th><th>User</th><th>Aksi</th>@endif</tr></thead><tbody>
@forelse($data as $item)@if($jenis === 'peminjaman')<tr><td>{{ $item->user->username ?? '-' }}</td><td>{{ $item->alat->nama_alat ?? '-' }}</td><td>{{ \Carbon\Carbon::parse($item->tgl_pinjam)->format('d M Y') }} - {{ \Carbon\Carbon::parse($item->tgl_harap_kembali)->format('d M Y') }}</td><td><span class="badge text-bg-secondary">{{ ucfirst($item->status) }}</span></td></tr>@elseif($jenis === 'pengembalian')<tr><td>{{ $item->peminjaman->user->username ?? '-' }}</td><td>{{ $item->peminjaman->alat->nama_alat ?? '-' }}</td><td>{{ \Carbon\Carbon::parse($item->tgl_kembali)->format('d M Y') }}</td><td>Rp {{ number_format($item->denda,0,',','.') }}</td><td>{{ $item->kondisi ?: '-' }}</td></tr>@else<tr><td>{{ $item->created_at }}</td><td>{{ $item->user->username ?? '-' }}</td><td>{{ $item->aksi }}</td></tr>@endif @empty<tr><td colspan="5" class="text-center text-muted py-4">Belum ada data.</td></tr>@endforelse</tbody></table></div><div class="admin-log-pagination mt-3">{{ $data->links() }}</div></div>
@endsection
