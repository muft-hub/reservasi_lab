<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RuangController;
use App\Http\Controllers\ReservasiController;
use App\Models\Ruang;
use App\Models\Reservasi;

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return redirect('/dashboard');
    });

    Route::get('/dashboard', function () {
        $totalRuang = Ruang::count();
        $totalReservasi = Reservasi::count();
        $menunggu = Reservasi::where('status', 'Menunggu')->count();
        $disetujui = Reservasi::where('status', 'Disetujui')->count();
        $ditolak = Reservasi::where('status', 'Ditolak')->count();
        $terbaru = Reservasi::with(['ruang', 'user'])->latest()->take(5)->get();

        return view('dashboard', compact('totalRuang', 'totalReservasi', 'menunggu', 'disetujui', 'ditolak', 'terbaru'));
    })->name('dashboard');

    // CRUD Ruang Lab
    Route::resource('ruang', RuangController::class);

    // CRUD Reservasi & Status
    Route::resource('reservasi', ReservasiController::class);
    Route::post('reservasi/{id}/status', [ReservasiController::class, 'updateStatus'])->name('reservasi.updateStatus');
});