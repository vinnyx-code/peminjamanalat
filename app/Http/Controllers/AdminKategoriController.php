<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class AdminKategoriController extends Controller
{
    public function index()
    {
        return view('admin.kategori', ['kategoris' => Kategori::withCount('alat')->latest()->paginate(15)]);
    }

    public function create()
    {
        return view('admin.kategori-form', ['kategori' => new Kategori()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate(['nama_kategori' => 'required|string|max:100|unique:kategori,nama_kategori']);
        Kategori::create($data);
        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Kategori $kategori)
    {
        return view('admin.kategori-form', compact('kategori'));
    }

    public function update(Request $request, Kategori $kategori)
    {
        $data = $request->validate(['nama_kategori' => 'required|string|max:100|unique:kategori,nama_kategori,' . $kategori->id]);
        $kategori->update($data);
        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Kategori $kategori)
    {
        if ($kategori->alat()->exists()) {
            return back()->with('error', 'Kategori masih digunakan oleh alat.');
        }
        $kategori->delete();
        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}
