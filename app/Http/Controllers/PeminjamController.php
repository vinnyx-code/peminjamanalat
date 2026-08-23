<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\LogAktifitas;
use App\Models\Pengembalian;
use App\Models\Peminjaman;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PeminjamController extends Controller
{
    public function index()
    {
        $peminjam = Auth::user();
        $alats = Alat::with('kategori')->orderBy('nama_alat')->get();
        $riwayatPeminjaman = Peminjaman::with('alat')
            ->where('user_id', $peminjam->id)
            ->latest()
            ->get();
        $user = $peminjam;

        return view('peminjam.dashboard', compact('user', 'peminjam', 'alats', 'riwayatPeminjaman'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'alat_id' => ['required', 'integer', 'exists:alat,id'],
            'tgl_pinjam' => ['required', 'date'],
            'tgl_harap_kembali' => ['required', 'date', 'after_or_equal:tgl_pinjam'],
        ]);

        $alat = Alat::findOrFail($data['alat_id']);
        if ($alat->stok < 1 || $alat->status !== 'ada') {
            return back()->with('error', 'Alat tersebut sedang tidak tersedia.');
        }

        $peminjaman = Peminjaman::create([
            'user_id' => Auth::id(),
            'alat_id' => $alat->id,
            'tgl_pinjam' => $data['tgl_pinjam'],
            'tgl_harap_kembali' => $data['tgl_harap_kembali'],
            'status' => 'pending',
        ]);

        LogAktifitas::create([
            'user_id' => Auth::id(),
            'aksi' => "Mengajukan peminjaman id:{$peminjaman->id}",
        ]);

        return redirect()->route('peminjam.dashboard')->with('success', 'Pengajuan peminjaman berhasil dikirim.');
    }

    public function kembalikan(Request $request, $id)
    {
        $data = $request->validate([
            'tgl_kembali' => ['nullable', 'date'],
            'kondisi' => ['nullable', 'string', 'max:100'],
        ]);

        $peminjaman = Peminjaman::with('alat')->where('user_id', Auth::id())->findOrFail($id);
        $tanggalKembali = Carbon::parse($data['tgl_kembali'] ?? now())->startOfDay();
        $tanggalHarusKembali = Carbon::parse($peminjaman->tgl_harap_kembali)->startOfDay();
        $hariTerlambat = max(0, $tanggalHarusKembali->diffInDays($tanggalKembali, false));
        $denda = $hariTerlambat * 50000;

        DB::transaction(function () use ($peminjaman, $tanggalKembali, $data, $denda) {
            $peminjaman->refresh();
            if ($peminjaman->status !== 'disetujui' || $peminjaman->pengembalian()->exists()) {
                abort(422, 'Peminjaman ini tidak dapat dikembalikan.');
            }

            $peminjaman->update(['status' => 'selesai']);
            Pengembalian::create([
                'peminjaman_id' => $peminjaman->id,
                'tgl_kembali' => $tanggalKembali,
                'kondisi' => $data['kondisi'] ?? 'Baik',
                'denda' => $denda,
            ]);
            LogAktifitas::create([
                'user_id' => Auth::id(),
                'aksi' => "Mengembalikan peminjaman id:{$peminjaman->id}, denda:{$denda}",
            ]);
        });

        $pesan = $denda > 0
            ? 'Alat berhasil dikembalikan. Denda: Rp ' . number_format($denda, 0, ',', '.')
            : 'Alat berhasil dikembalikan tanpa denda.';

        return redirect()->route('peminjam.dashboard')->with('success', $pesan);
    }
}
