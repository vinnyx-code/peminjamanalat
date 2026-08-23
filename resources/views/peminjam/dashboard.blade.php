@extends('layouts.app')

@section('title', 'Dashboard Peminjam')

@section('content')
<div class="mb-4">
  <div class="eyebrow">Ruang peminjam</div>
  <h2 class="fw-bold mb-1">Daftar Alat</h2>
  <p class="text-muted mb-0">Pilih alat yang tersedia dan ajukan peminjaman Anda.</p>
</div>

@if(session('success'))
  <div class="alert alert-success border-0 shadow-sm"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="alert alert-danger border-0 shadow-sm"><i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}</div>
@endif
@if($errors->any())
  <div class="alert alert-danger border-0 shadow-sm">{{ $errors->first() }}</div>
@endif

<section class="row g-3 mb-5">
  @forelse($alats as $alat)
    <div class="col-12 col-sm-6 col-xl-4">
      <article class="card h-100 p-3">
        <div class="d-flex gap-3">
          <div class="equipment-icon"><i class="bi bi-tools"></i></div>
          <div>
            <h6 class="fw-bold mb-1">{{ $alat->nama_alat }}</h6>
            <small class="text-muted">{{ $alat->kategori->nama_kategori ?? 'Umum' }}</small>
          </div>
        </div>
        <p class="small text-muted mt-3 mb-3">{{ $alat->deskripsi ?: 'Tidak ada deskripsi alat.' }}</p>
        <div class="d-flex justify-content-between align-items-center mt-auto">
          <span class="small"><strong>{{ $alat->stok }}</strong> stok tersisa</span>
          <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
            data-bs-target="#modalPeminjaman" data-alat-id="{{ $alat->id }}"
            data-alat-nama="{{ $alat->nama_alat }}"
            @disabled($alat->stok < 1 || $alat->status !== 'ada')>
            Ajukan Pinjam
          </button>
        </div>
      </article>
    </div>
  @empty
    <div class="col-12"><div class="card p-5 text-center text-muted">Belum ada alat tersedia.</div></div>
  @endforelse
</section>

<section id="riwayat">
  <div class="eyebrow">Aktivitas Anda</div>
  <h4 class="fw-bold mb-3">Riwayat Peminjaman</h4>
  <div class="card p-2 p-md-3">
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead><tr><th>Nama Alat</th><th>Tanggal Pinjam</th><th>Harap Kembali</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
        <tbody>
        @forelse($riwayatPeminjaman as $peminjaman)
          @php
            $status = ['pending' => ['Pending', 'warning'], 'disetujui' => ['Approved', 'success'], 'selesai' => ['Returned', 'secondary'], 'ditolak' => ['Rejected', 'danger']][$peminjaman->status] ?? [ucfirst($peminjaman->status), 'secondary'];
          @endphp
          <tr>
            <td class="fw-semibold">{{ $peminjaman->alat->nama_alat ?? '-' }}</td>
            <td>{{ \Carbon\Carbon::parse($peminjaman->tgl_pinjam)->format('d M Y') }}</td>
            <td>{{ \Carbon\Carbon::parse($peminjaman->tgl_harap_kembali)->format('d M Y') }}</td>
            <td><span class="badge rounded-pill text-bg-{{ $status[1] }}">{{ $status[0] }}</span></td>
            <td class="text-end">
              @if($peminjaman->status === 'disetujui')
                <form method="POST" action="{{ route('peminjam.peminjaman.kembalikan', $peminjaman->id) }}" onsubmit="return confirm('Konfirmasi pengembalian alat?')">
                  @csrf
                  <input type="hidden" name="tgl_kembali" value="{{ now()->format('Y-m-d') }}">
                  <button class="btn btn-outline-primary btn-sm">Kembalikan Alat</button>
                </form>
              @else
                <span class="text-muted small">-</span>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="text-center text-muted py-4">Belum ada riwayat peminjaman.</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
  </div>
</section>

<div class="modal fade" id="modalPeminjaman" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 rounded-4">
      <form method="POST" action="{{ route('peminjam.peminjaman.store') }}">
        @csrf
        <div class="modal-header border-0"><h5 class="modal-title fw-bold">Mengajukan Peminjaman</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body pt-0">
          <input type="hidden" name="alat_id" id="alatId">
          <div class="mb-3"><label class="form-label">Nama Alat</label><input id="alatNama" class="form-control" readonly></div>
          <div class="row g-3">
            <div class="col-6"><label class="form-label">Tanggal Pinjam</label><input type="date" name="tgl_pinjam" class="form-control" min="{{ now()->format('Y-m-d') }}" required></div>
            <div class="col-6"><label class="form-label">Tanggal Kembali</label><input type="date" name="tgl_harap_kembali" class="form-control" min="{{ now()->format('Y-m-d') }}" required></div>
          </div>
        </div>
        <div class="modal-footer border-0"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary">Kirim Pengajuan</button></div>
      </form>
    </div>
  </div>
</div>
<script>
document.getElementById('modalPeminjaman').addEventListener('show.bs.modal', function (event) {
  const tombol = event.relatedTarget;
  document.getElementById('alatId').value = tombol.dataset.alatId;
  document.getElementById('alatNama').value = tombol.dataset.alatNama;
});
</script>
@endsection
