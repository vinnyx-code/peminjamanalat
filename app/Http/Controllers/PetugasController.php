<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;

class PetugasController extends Controller
{
    public function laporan()
    {
        $peminjaman = Peminjaman::with(['user', 'alat', 'pengembalian'])
            ->latest()
            ->paginate(25);

        return view('petugas.laporan', compact('peminjaman'));
    }
}
