<?php

namespace App\Http\Controllers;

use App\Models\LogAktifitas;
use App\Models\Pengembalian;
use App\Models\Peminjaman;

class AdminDataController extends Controller
{
    public function peminjaman()
    {
        return view('admin.data-table', ['judul' => 'Data Peminjaman', 'jenis' => 'peminjaman', 'data' => Peminjaman::with(['user', 'alat'])->latest()->paginate(20)]);
    }

    public function pengembalian()
    {
        return view('admin.data-table', ['judul' => 'Data Pengembalian', 'jenis' => 'pengembalian', 'data' => Pengembalian::with('peminjaman.user', 'peminjaman.alat')->latest()->paginate(20)]);
    }

    public function log()
    {
        return view('admin.data-table', ['judul' => 'Log Aktifitas', 'jenis' => 'log', 'data' => LogAktifitas::with('user')->latest('created_at')->paginate(20)]);
    }
}
