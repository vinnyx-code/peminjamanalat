@extends('layouts.app')

@section('title', 'Laporan Peminjaman')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
  <div>
    <div class="eyebrow">Petugas</div>
    <h2 class="fw-bold mb-1">Laporan Peminjaman</h2>
    <p class="text-muted mb-0">Ringkasan seluruh transaksi peminjaman dan pengembalian alat.</p>
  </div>
  <button type="button" class="btn btn-outline-primary" onclick="window.print()">
    <i class="bi bi-printer me-2"></i>Cetak Laporan
  </button>
</div>

<div class="card p-2 p-md-3">
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead>
        <tr>
          <th>Peminjam</th>
          <th>Alat</th>
          <th>Tanggal Pinjam</th>
          <th>Rencana Kembali</th>
          <th>Tanggal Kembali</th>
          <th>Status</th>
          <th>Denda</th>
        </tr>
      </thead>
      <tbody>
        @forelse($peminjaman as $item)
          <tr>
            <td>{{ $item->user->username ?? '-' }}</td>
            <td>{{ $item->alat->nama_alat ?? '-' }}</td>
            <td>{{ \Carbon\Carbon::parse($item->tgl_pinjam)->format('d M Y') }}</td>
            <td>{{ \Carbon\Carbon::parse($item->tgl_harap_kembali)->format('d M Y') }}</td>
            <td>{{ $item->pengembalian ? \Carbon\Carbon::parse($item->pengembalian->tgl_kembali)->format('d M Y') : '-' }}</td>
            <td><span class="badge text-bg-{{ $item->status === 'selesai' ? 'secondary' : ($item->status === 'disetujui' ? 'success' : 'warning') }}">{{ ucfirst($item->status) }}</span></td>
            <td>Rp {{ number_format($item->pengembalian->denda ?? 0, 0, ',', '.') }}</td>
          </tr>
        @empty
          <tr><td colspan="7" class="text-center text-muted py-4">Belum ada transaksi.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="mt-3">{{ $peminjaman->links() }}</div>
</div>

<style media="print">
  .sidebar, .topbar, .btn, nav, .pagination { display: none !important; }
  .main { margin-left: 0 !important; }
  body { background: #fff !important; }
  .card { box-shadow: none !important; }
</style>
@endsection
