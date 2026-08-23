<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Dashboard') · {{ config('app.name', 'PinjamAlat') }}</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <style>
    :root { --ink:#182230; --muted:#718096; --blue:#2563eb; --soft:#f5f7fb; }
    body { background:var(--soft); color:var(--ink); font-family:Inter,system-ui,-apple-system,sans-serif; }
    .sidebar { width:258px; background:#111827; min-height:100vh; position:fixed; z-index:1030; }
    .brand { color:#fff; font-weight:800; letter-spacing:-.03em; font-size:1.25rem; }
    .brand i { color:#60a5fa; } .nav-section { color:#7d8aa2; font-size:.68rem; text-transform:uppercase; letter-spacing:.12em; }
    .sidebar .nav-link { color:#b9c3d4; border-radius:10px; padding:.7rem .85rem; margin:.12rem 0; font-size:.9rem; display:flex; align-items:center; gap:.65rem; width:100%; text-decoration:none; line-height:1.2; }
    .sidebar .nav-link:hover,.sidebar .nav-link.active { color:#fff; background:#263653; }
    .sidebar .nav-link i { width:20px; font-size:1rem; text-align:center; } .main { margin-left:258px; min-height:100vh; }
    .topbar { background:#fff; border-bottom:1px solid #e7ebf2; } .card { border:0; border-radius:16px; box-shadow:0 5px 22px rgba(30,45,75,.05); }
    .btn { font-size:.9rem; border-radius:10px; padding:.55rem .9rem; font-weight:600; transition:.2s ease; }
    .btn-sm { padding:.45rem .7rem; font-size:.8rem; }
    .eyebrow { color:var(--muted); font-size:.76rem; text-transform:uppercase; letter-spacing:.08em; font-weight:700; }
    .stat-icon { width:42px; height:42px; display:grid; place-items:center; border-radius:12px; font-size:1.2rem; }
    .table thead th { color:#8a95a7; font-size:.72rem; text-transform:uppercase; letter-spacing:.06em; border-bottom-width:1px; }
    .table td { vertical-align:middle; padding:1rem .75rem; } .equipment-icon { width:44px;height:44px;border-radius:12px;background:#eff6ff;color:#2563eb;display:grid;place-items:center;font-size:1.3rem; }
    @media (max-width: 991.98px) { .sidebar { transform:translateX(-100%); transition:.2s; } .sidebar.show { transform:translateX(0); } .main { margin-left:0; } }
  </style>
</head>
<body>
  @auth
  @php($user = auth()->user())
  <aside class="sidebar p-3" id="sidebar">
    <a class="brand text-decoration-none d-block px-2 py-2 mb-4" href="{{ route('dashboard') }}"><i class="bi bi-box-seam me-2"></i>PinjamAlat</a>
    <div class="nav-section px-2 mb-2">Menu utama</div>
    @if($user->role === 'admin')
      <a class="nav-link active" href="{{ route('dashboard') }}"><i class="bi bi-grid-1x2"></i>Ringkasan</a>
      <a class="nav-link" href="{{ route('admin.alat.index') }}"><i class="bi bi-tools"></i>CRUD Alat</a>
      <a class="nav-link" href="{{ route('admin.users.index') }}"><i class="bi bi-people"></i>CRUD User</a>
      <a class="nav-link" href="{{ route('admin.kategori.index') }}"><i class="bi bi-tags"></i>CRUD Kategori</a>
      <a class="nav-link" href="{{ route('admin.peminjaman.index') }}"><i class="bi bi-arrow-left-right"></i>CRUD Peminjaman</a>
      <a class="nav-link" href="{{ route('admin.pengembalian.index') }}"><i class="bi bi-box-arrow-in-down"></i>CRUD Pengembalian</a>
      <a class="nav-link" href="{{ route('admin.log.index') }}"><i class="bi bi-clock-history"></i>Log Aktifitas</a>
    @elseif($user->role === 'petugas')
      <a class="nav-link active" href="{{ route('dashboard') }}"><i class="bi bi-grid-1x2"></i>Ringkasan</a>
      <a class="nav-link" href="{{ route('dashboard') }}#persetujuan"><i class="bi bi-check2-circle"></i>Menyetujui Peminjaman</a>
      <a class="nav-link" href="{{ route('dashboard') }}#pengembalian"><i class="bi bi-arrow-return-left"></i>Memantau Pengembalian</a>
      <a class="nav-link" href="{{ route('petugas.laporan') }}"><i class="bi bi-printer"></i>Mencetak Laporan</a>
    @else
      <a class="nav-link active" href="{{ route('dashboard') }}"><i class="bi bi-grid-1x2"></i>Daftar Alat</a>
      <a class="nav-link" href="{{ route('peminjam.dashboard') }}#riwayat"><i class="bi bi-clock-history"></i>Riwayat Peminjaman</a>
    @endif
    <div class="border-top border-secondary opacity-50 mt-4 pt-3">
      <form method="POST" action="{{ route('logout') }}">@csrf<button class="nav-link w-100 text-start border-0 bg-transparent"><i class="bi bi-box-arrow-right"></i>Keluar</button></form>
    </div>
  </aside>
  @endauth
  <div class="main">
    @auth
    <header class="topbar px-3 px-lg-4 py-3 d-flex justify-content-between align-items-center">
      <button class="btn btn-light d-lg-none" id="sidebarToggle"><i class="bi bi-list fs-5"></i></button>
      <div class="ms-auto d-flex align-items-center gap-3"><span class="text-muted small d-none d-sm-inline">{{ now()->translatedFormat('l, d F Y') }}</span><div class="rounded-circle bg-primary text-white d-grid place-items-center" style="width:36px;height:36px">{{ strtoupper(substr($user->username ?: ($user->nama ?: ($user->name ?: 'U')),0,1)) }}</div><div class="small fw-semibold d-none d-md-block">{{ $user->username ?: ($user->nama ?: $user->name) }}</div></div>
    </header>
    @endauth
    <main class="p-3 p-lg-4">@yield('content')</main>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>document.getElementById('sidebarToggle')?.addEventListener('click',()=>document.getElementById('sidebar').classList.toggle('show'));</script>
</body>
</html>
