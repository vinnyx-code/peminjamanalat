<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Kategori;
use Illuminate\Http\Request;

class AlatController extends Controller
{
    public function index()
    {
        return redirect()->route('dashboard');
    }

    public function create()
    {
        return view('admin.alat-form', ['alat' => new Alat(), 'kategoris' => Kategori::orderBy('nama_kategori')->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kategori_id' => 'required|exists:kategori,id',
            'nama_alat' => 'required|string|max:150',
            'deskripsi' => 'nullable|string',
            'stok' => 'required|integer|min:0',
            'status' => 'required|in:ada,rusak,dipinjam,tidak_tersedia',
        ]);
        Alat::create($data);
        return redirect()->route('dashboard')->with('success', 'Alat berhasil ditambahkan.');
    }

    public function edit(Alat $alat)
    {
        return view('admin.alat-form', ['alat' => $alat, 'kategoris' => Kategori::orderBy('nama_kategori')->get()]);
    }

    public function update(Request $request, Alat $alat)
    {
        $data = $request->validate([
            'kategori_id' => 'required|exists:kategori,id',
            'nama_alat' => 'required|string|max:150',
            'deskripsi' => 'nullable|string',
            'stok' => 'required|integer|min:0',
            'status' => 'required|in:ada,rusak,dipinjam,tidak_tersedia',
        ]);
        $alat->update($data);
        return redirect()->route('dashboard')->with('success', 'Alat berhasil diperbarui.');
    }

    public function destroy(Alat $alat)
    {
        $alat->delete();
        return redirect()->route('dashboard')->with('success', 'Alat berhasil dihapus.');
    }
}
