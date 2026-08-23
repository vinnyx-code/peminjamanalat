<?php

namespace App\Http\Controllers;

use App\Models\LogAktifitas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index()
    {
        return view('admin.users', ['users' => User::latest()->paginate(15)]);
    }

    public function create()
    {
        return view('admin.user-form', ['userData' => new User()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:150',
            'username' => 'required|string|max:100|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin,petugas,peminjam',
            'password' => 'required|string|min:6',
        ]);
        $data['name'] = $data['nama'];
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        LogAktifitas::create(['user_id' => auth()->id(), 'aksi' => "Menambahkan user id:{$user->id}"]);
        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('admin.user-form', ['userData' => $user]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:150',
            'username' => 'required|string|max:100|unique:users,username,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,petugas,peminjam',
            'password' => 'nullable|string|min:6',
        ]);
        $data['name'] = $data['nama'];
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        $user->update($data);
        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Akun yang sedang digunakan tidak dapat dihapus.');
        }
        $user->delete();
        return back()->with('success', 'User berhasil dihapus.');
    }
}
