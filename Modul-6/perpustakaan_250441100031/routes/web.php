<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\BukuWebController; // Controller web untuk manajemen buku admin/petugas
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\Buku;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\PeminjamanController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/tes-error', function () {
    throw new \RuntimeException('Uji coba error sistem');
});


Route::middleware(['auth', 'verified'])->group(function () {



    // Redirect Dashboard Otomatis Sesuai Role
    Route::get('/dashboard', function () {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->hasRole('petugas')) {
            return redirect()->route('petugas.dashboard');
        }
        return redirect()->route('siswa.dashboard');
    })->name('dashboard');

    Route::middleware(['can:kelola-buku'])
        ->prefix('kelola')
        ->name('kelola.')
        ->group(function () {
            Route::resource('buku', BukuWebController::class);
        });

    // ==================== FITUR ADMIN ====================
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/users', [AdminController::class, 'users'])->name('users.index');
        Route::put('/users/{id}/role', [AdminController::class, 'updateUserRole'])->name('users.updateRole');
        Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])->name('users.destroy');
    });

    // ==================== FITUR PETUGAS ====================
    Route::middleware(['role:admin|petugas'])->prefix('petugas')->name('petugas.')->group(function () {
        Route::get('/dashboard', [PetugasController::class, 'dashboard'])->name('dashboard');
        Route::get('/peminjaman', [PetugasController::class, 'peminjamanIndex'])->name('peminjaman.index');
        Route::patch('/peminjaman/{id}', [PetugasController::class, 'updateStatusPeminjaman'])->name('peminjaman.updateStatus');
    });

    Route::middleware(['role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
        Route::get('/dashboard', [SiswaController::class, 'dashboard'])->name('dashboard');
        Route::post('/peminjaman/ajukan/{buku_id}', [SiswaController::class, 'ajukanPeminjaman'])->name('peminjaman.ajukan');

        // Ubah rute ini agar menerima parameter id (contoh: /siswa/{id}/riwayat)
        Route::get('/{id}/riwayat', [SiswaController::class, 'riwayatPeminjaman'])->name('riwayat');
    });

    Route::get('/buku', function () {
        $bukus = Buku::with('kategori')->get();

        return view('buku.index', compact('bukus'));
    })->name('buku.index');

    Route::get('/buku/{id}', function ($id) {
        $buku = \App\Models\Buku::with('kategori')->findOrFail($id);
        return view('buku.show', compact('buku'));
    })->name('buku.show');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('auth')->get(
        '/buku-cari',
        [BukuWebController::class, 'cari']
    );

    Route::middleware('auth')->get(
        '/peminjaman/{id}',
        [PeminjamanController::class, 'show']
    )->name('peminjaman.show');
});

require __DIR__ . '/auth.php';

