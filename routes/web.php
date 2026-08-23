<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AlatController;
use App\Http\Controllers\PeminjamController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminKategoriController;
use App\Http\Controllers\AdminDataController;
use App\Http\Controllers\PetugasController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return view('auth.signin');
});

// Auth
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('login.post');
Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
// Registration (peminjam)
Route::post('register', [AuthController::class, 'register'])->name('register.post');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('role:peminjam')->prefix('peminjam')->name('peminjam.')->group(function () {
        Route::get('/dashboard', [PeminjamController::class, 'index'])->name('dashboard');
        Route::post('/peminjaman', [PeminjamController::class, 'store'])->name('peminjaman.store');
        Route::post('/peminjaman/{id}/kembalikan', [PeminjamController::class, 'kembalikan'])->name('peminjaman.kembalikan');
    });

    Route::middleware('role:petugas')->prefix('petugas')->name('petugas.')->group(function () {
        Route::get('/laporan', [PetugasController::class, 'laporan'])->name('laporan');
    });

    Route::resource('/admin/alat', AlatController::class)->except(['show'])->names('admin.alat')->middleware('role:admin');
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', AdminUserController::class)->except(['show'])->names('users');
        Route::resource('kategori', AdminKategoriController::class)->except(['show'])->names('kategori');
        Route::get('peminjaman', [AdminDataController::class, 'peminjaman'])->name('peminjaman.index');
        Route::get('pengembalian', [AdminDataController::class, 'pengembalian'])->name('pengembalian.index');
        Route::get('log', [AdminDataController::class, 'log'])->name('log.index');
    });

    // Peminjaman
    Route::post('/peminjaman/ajukan', [PeminjamanController::class,'ajukanPeminjaman'])->name('peminjaman.ajukan');
    Route::post('/peminjaman/{id}/setujui', [PeminjamanController::class,'setujuiPeminjaman'])->name('peminjaman.setujui')->middleware('role:petugas,admin');
    Route::post('/peminjaman/{id}/tolak', [PeminjamanController::class,'tolakPeminjaman'])->name('peminjaman.tolak')->middleware('role:petugas,admin');
    Route::post('/peminjaman/{id}/pengembalian', [PeminjamanController::class,'prosesPengembalian'])->name('peminjaman.pengembalian');
});
