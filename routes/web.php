<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RuangController;
use App\Http\Controllers\ReservasiController;
use Illuminate\Support\Facades\Auth;
use App\Models\Ruang;
use App\Models\Reservasi;

// ================= AUTH ROUTES =================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ================= PROTECTED ROUTES (WAJIB LOGIN) =================
Route::middleware('auth')->group(function () {
    
    // 1. Redirect otomatis saat mengakses root ( / )
    Route::get('/', function () {
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('mahasiswa.dashboard');
    });

    // 2. Redirect jika ada yang mengetik /dashboard langsung
    Route::get('/dashboard', function () {
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('mahasiswa.dashboard');
    });

    // ================= ROUTE ADMIN =================
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        
        // Akses langsung /admin diarahkan ke dashboard
        Route::get('/', function () {
            return redirect()->route('admin.dashboard');
        });

        // Dashboard Admin
        Route::get('/dashboard', function () {
            $totalRuang = Ruang::count();
            $totalReservasi = Reservasi::count();
            
            $menunggu = Reservasi::where('status', 'Menunggu')->count();
            $disetujui = Reservasi::where('status', 'Disetujui')->count();
            $ditolak = Reservasi::where('status', 'Ditolak')->count();
            $terbaru = Reservasi::with(['ruang', 'user'])->latest()->take(5)->get();

            return view('admin.dashboard', compact('totalRuang', 'totalReservasi', 'menunggu', 'disetujui', 'ditolak', 'terbaru'));
        })->name('admin.dashboard');

        // 1. Daftarkan Resource Ruang dengan nama default: ruang.*
        // Ini otomatis membuat: ruang.index, ruang.create, ruang.store, ruang.show, ruang.edit, ruang.update, ruang.destroy
        Route::resource('ruang', RuangController::class);

        // 2. Daftarkan Alias dengan nama: admin.ruang.*
        // Agar file Blade yang memanggil admin.ruang.* juga tetap bekerja tanpa error!
        Route::get('/data-ruang', [RuangController::class, 'index'])->name('admin.ruang.index');
        Route::get('/data-ruang/create', [RuangController::class, 'create'])->name('admin.ruang.create');
        Route::post('/data-ruang', [RuangController::class, 'store'])->name('admin.ruang.store');
        Route::get('/data-ruang/{ruang}/edit', [RuangController::class, 'edit'])->name('admin.ruang.edit');
        Route::put('/data-ruang/{ruang}', [RuangController::class, 'update'])->name('admin.ruang.update');
        Route::delete('/data-ruang/{ruang}', [RuangController::class, 'destroy'])->name('admin.ruang.destroy');

        // Kelola Reservasi Admin (Dukungan kedua nama: admin.reservasi.* dan reservasi.*)
        Route::get('/reservasi', [ReservasiController::class, 'indexAdmin'])->name('admin.reservasi.index');
        Route::get('/daftar-reservasi', [ReservasiController::class, 'indexAdmin'])->name('reservasi.index');
        Route::patch('/reservasi/{id}/status', [ReservasiController::class, 'updateStatus'])->name('admin.reservasi.updateStatus');
        Route::delete('/reservasi/{id}', [ReservasiController::class, 'destroy'])->name('admin.reservasi.destroy');
    });

    // ================= ROUTE MAHASISWA =================
    Route::middleware(['role:mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        
        Route::get('/', function () {
            return redirect()->route('mahasiswa.dashboard');
        });

        // Dashboard Mahasiswa (Daftar ruang lab)
        Route::get('/dashboard', [RuangController::class, 'indexMahasiswa'])->name('dashboard');

        // Pengajuan & Riwayat Reservasi Mahasiswa
        Route::get('/reservasi/create/{ruang_id?}', [ReservasiController::class, 'create'])->name('reservasi.create');
        Route::post('/reservasi', [ReservasiController::class, 'store'])->name('reservasi.store');
        Route::get('/riwayat-reservasi', [ReservasiController::class, 'indexMahasiswa'])->name('reservasi.riwayat');
        Route::delete('/reservasi/{id}', [ReservasiController::class, 'destroy'])->name('reservasi.destroy');
    });
});