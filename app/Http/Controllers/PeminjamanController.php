<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Peminjaman;
use App\Models\Alat;
use App\Models\Pengembalian;
use App\Models\LogAktifitas;

class PeminjamanController extends Controller
{
    // Ajukan peminjaman oleh peminjam
    public function ajukanPeminjaman(Request $request)
    {
        $request->validate([
            'alat_id' => 'required|integer|exists:alat,id',
            'tgl_pinjam' => 'required|date',
            'tgl_harap_kembali' => 'required|date|after_or_equal:tgl_pinjam',
        ]);

        $peminjam = Auth::user();

        $p = Peminjaman::create([
            'user_id' => $peminjam->id,
            'alat_id' => $request->alat_id,
            'tgl_pinjam' => $request->tgl_pinjam,
            'tgl_harap_kembali' => $request->tgl_harap_kembali,
            'status' => 'pending',
        ]);

        LogAktifitas::create(['user_id' => $peminjam->id, 'aksi' => "Mengajukan peminjaman id:{$p->id}"]); 

        return redirect()->back()->with('success','Peminjaman diajukan');
    }

    // Petugas setujui peminjaman
    public function setujuiPeminjaman(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->role !== 'petugas' && $user->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        try {
            DB::transaction(function () use ($id, $user) {
                $p = Peminjaman::lockForUpdate()->findOrFail($id);

                if ($p->status !== 'pending') {
                    throw new \Exception('Peminjaman tidak dalam status pending');
                }

                $alat = Alat::lockForUpdate()->findOrFail($p->alat_id);
                if ($alat->stok <= 0) {
                    throw new \Exception('Stok tidak cukup untuk menyetujui peminjaman');
                }

                $p->status = 'disetujui';
                $p->petugas_id = $user->id;
                $p->save();
                // trigger DB akan mengurangi stok
            });

            LogAktifitas::create(['user_id' => $user->id, 'aksi' => "Menyetujui peminjaman id:{$id}"]);
            return redirect()->back()->with('success','Peminjaman disetujui');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyetujui: '.$e->getMessage());
        }
    }

    // Proses pengembalian (panggil stored procedure)
    public function prosesPengembalian(Request $request, $peminjaman_id)
    {
        $user = Auth::user();
        $p = Peminjaman::findOrFail($peminjaman_id);
        if ($user->role === 'peminjam' && $p->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $tgl_kembali = $request->input('tgl_kembali', now());

        try {
            DB::statement("SET @out_denda = 0;");
            DB::statement("CALL process_return(?, ?, @out_denda);", [$peminjaman_id, $tgl_kembali]);
            $result = DB::select("SELECT @out_denda AS denda;");
            $denda = $result[0]->denda ?? 0;

            LogAktifitas::create(['user_id' => $user->id, 'aksi' => "Memproses pengembalian peminjaman id:{$peminjaman_id}, denda:{$denda}"]);
            return redirect()->back()->with('success','Pengembalian diproses. Denda: Rp '.number_format($denda,0,',','.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error','Gagal memproses pengembalian: '.$e->getMessage());
        }
    }
}
