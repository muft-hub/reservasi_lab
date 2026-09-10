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
    
    // Redirect otomatis sesuai Role saat mengakses web ( / )
    Route::get('/', function () {
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('mahasiswa.dashboard');
    });

    // ================= ROUTE ADMIN =================
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        
        // Dashboard Admin (Menampilkan Statistik)
        Route::get('/dashboard', function () {
            $totalRuang = Ruang::count();
            $totalReservasi = Reservasi::count();
            
            $menunggu = Reservasi::where('status', 'Menunggu')->count();
            $disetujui = Reservasi::where('status', 'Disetujui')->count();
            $ditolak = Reservasi::where('status', 'Ditolak')->count();
            $terbaru = Reservasi::with(['ruang', 'user'])->latest()->take(5)->get();

            return view('admin.dashboard', compact('totalRuang', 'totalReservasi', 'menunggu', 'disetujui', 'ditolak', 'terbaru'));
        })->name('dashboard');

        // CRUD Ruang Lab (Hapus ruang otomatis tersedia melalui Route::resource)
        Route::resource('ruang', RuangController::class);

        // Kelola Reservasi Admin (Lihat, Update Status, & Hapus)
        Route::get('/reservasi', [ReservasiController::class, 'indexAdmin'])->name('reservasi.index');
        Route::patch('/reservasi/{id}/status', [ReservasiController::class, 'updateStatus'])->name('reservasi.updateStatus');
        Route::delete('/reservasi/{id}', [ReservasiController::class, 'destroy'])->name('reservasi.destroy'); // <-- DITAMBAHKAN
    });

    // ================= ROUTE MAHASISWA =================
    Route::middleware(['role:mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        
        // Dashboard Mahasiswa (Menampilkan daftar ruang)
        Route::get('/dashboard', [RuangController::class, 'indexMahasiswa'])->name('dashboard');

        // Pengajuan & Riwayat Reservasi Mahasiswa
        Route::get('/reservasi/create/{ruang_id}', [ReservasiController::class, 'create'])->name('reservasi.create');
        Route::post('/reservasi', [ReservasiController::class, 'store'])->name('reservasi.store');
        Route::get('/riwayat-reservasi', [ReservasiController::class, 'indexMahasiswa'])->name('reservasi.riwayat');
        
        // Batal/Hapus Reservasi Mahasiswa (Hanya miliknya sendiri)
        Route::delete('/reservasi/{id}', [ReservasiController::class, 'destroy'])->name('reservasi.destroy'); // <-- DITAMBAHKAN
    });
});