<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\LogAktifitas;
use App\Models\Kategori;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->role === 'peminjam') {
            return redirect()->route('peminjam.dashboard');
        }

        $alats = Alat::with('kategori')->orderBy('nama_alat')->get();
        $riwayats = Peminjaman::with('alat')->where('user_id', $user->id)->latest()->get();
        $peminjamanPending = Peminjaman::with(['user', 'alat'])->where('status', 'pending')->latest()->get();
        $peminjamanAktif = Peminjaman::with(['user', 'alat'])->where('status', 'disetujui')->latest()->get();
        $totalUser = User::count();
        $totalKategori = Kategori::count();
        $totalPengembalian = Pengembalian::count();
        $logTerbaru = LogAktifitas::with('user')->latest('created_at')->limit(5)->get();

        return view('dashboard', compact('user', 'alats', 'riwayats', 'peminjamanPending', 'peminjamanAktif', 'totalUser', 'totalKategori', 'totalPengembalian', 'logTerbaru'));
    }
}
