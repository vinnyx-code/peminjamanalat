<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\LogAktifitas;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['email' => 'Email atau password salah'])->withInput();
        }

        Auth::login($user);
        if (!$user->username || $user->username === $user->email) {
            $user->username = Str::before($user->email, '@');
            $user->save();
        }
        LogAktifitas::create(['user_id' => $user->id, 'aksi' => 'login']);

        return redirect()->intended('/dashboard');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:150',
            'username' => 'required|string|max:100|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|confirmed|min:6',
        ]);

        $user = User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => User::ROLE_PEMINJAM,
        ]);

        LogAktifitas::create(['user_id' => $user->id, 'aksi' => 'register']);

        Auth::login($user);
        LogAktifitas::create(['user_id' => $user->id, 'aksi' => 'login']);

        return redirect()->intended('/dashboard');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            LogAktifitas::create(['user_id' => $user->id, 'aksi' => 'logout']);
        }
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
