<?php
/**
 * SETUP OTOMATIS: SISTEM RESERVASI RUANG LABORATORIUM (LARAVEL + MYSQL LARAGON)
 * Tugas Remedial Praktikum Pemrograman Web
 * 
 * CARA MENGGUNAKAN:
 * 1. Letakkan file ini di root folder proyek Laravel Anda (di samping file 'artisan')
 *    Contoh path: C:\laragon\www\reservasi-lab\setup_project.php
 * 2. Buka Terminal di VS Code (tekan Ctrl + ~) atau Terminal Laragon
 * 3. Jalankan perintah: php setup_project.php
 */

if (!file_exists('artisan')) {
    die("\n❌ [GAGAL] File ini harus dijalankan di dalam root folder Laravel (tempat terdapat file 'artisan')!\nPastikan Anda sudah masuk ke folder proyek, misalnya: C:\\laragon\\www\\reservasi-lab\\\n\n");
}

echo "=================================================================\n";
echo "  MEMULAI SETUP OTOMATIS: SISTEM RESERVASI RUANG LABORATORIUM    \n";
echo "=================================================================\n\n";

$files = [
    'database/migrations/2024_01_01_000001_create_ruangs_table.php' => <<<'FILE_CONTENT'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ruangs', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique(); // Kode ruang unik
            $table->string('nama');
            $table->unsignedInteger('kapasitas'); // Angka positif
            $table->string('lokasi');
            $table->text('fasilitas')->nullable();
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ruangs');
    }
};

FILE_CONTENT
,
    'database/migrations/2024_01_01_000002_create_reservasis_table.php' => <<<'FILE_CONTENT'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('ruang_id')->constrained('ruangs')->onDelete('cascade');
            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->text('keperluan');
            $table->enum('status', ['Menunggu', 'Disetujui', 'Ditolak'])->default('Menunggu');
            $table->string('berkas_pendukung')->nullable();
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservasis');
    }
};

FILE_CONTENT
,
    'database/seeders/DatabaseSeeder.php' => <<<'FILE_CONTENT'
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Ruang;
use App\Models\Reservasi;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Admin & Mahasiswa
        $admin = User::create([
            'name' => 'Administrator Lab Komputer',
            'email' => 'admin@lab.ac.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $user = User::create([
            'name' => 'Ahmad Fauzi (Mahasiswa)',
            'email' => 'mahasiswa@student.ac.id',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        // 2. Data Ruang Lab
        $lab1 = Ruang::create([
            'kode' => 'LAB-PL-01',
            'nama' => 'Laboratorium Rekayasa Perangkat Lunak',
            'kapasitas' => 40,
            'lokasi' => 'Gedung Fasilkom Lantai 2',
            'fasilitas' => '40 Unit PC Core i7, Proyektor 4K, AC, Gigabit LAN',
            'foto' => 'lab1.jpg',
        ]);

        $lab2 = Ruang::create([
            'kode' => 'LAB-JK-02',
            'nama' => 'Laboratorium Jaringan & Keamanan Siber',
            'kapasitas' => 32,
            'lokasi' => 'Gedung Fasilkom Lantai 3',
            'fasilitas' => 'Cisco Switch & Router Rack, 32 PC Linux, Fluke Tester, AC',
            'foto' => 'lab2.jpg',
        ]);

        // 3. Data Reservasi Awal
        Reservasi::create([
            'user_id' => $user->id,
            'ruang_id' => $lab1->id,
            'tanggal' => date('Y-m-d'),
            'jam_mulai' => '08:00',
            'jam_selesai' => '11:00',
            'keperluan' => 'Praktikum Mandiri Pemrograman Web & Laravel',
            'status' => 'Disetujui',
            'berkas_pendukung' => 'surat_izin.pdf',
        ]);
    }
}

FILE_CONTENT
,
    'app/Models/Ruang.php' => <<<'FILE_CONTENT'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruang extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'nama',
        'kapasitas',
        'lokasi',
        'fasilitas',
        'foto',
    ];

    // Relasi 1 ruang memiliki banyak reservasi
    public function reservasis()
    {
        return $this->hasMany(Reservasi::class);
    }
}

FILE_CONTENT
,
    'app/Models/Reservasi.php' => <<<'FILE_CONTENT'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ruang_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'keperluan',
        'status',
        'berkas_pendukung',
        'catatan_admin',
    ];

    // Relasi ke User peminjam
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Ruang yang dipinjam
    public function ruang()
    {
        return $this->belongsTo(Ruang::class);
    }
}

FILE_CONTENT
,
    'app/Http/Controllers/AuthController.php' => <<<'FILE_CONTENT'
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin() { return view('auth.login'); }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard')->with('success', 'Selamat datang kembali!');
        }

        return back()->withErrors(['email' => 'Email atau kata sandi tidak cocok.'])->withInput();
    }

    public function showRegister() { return view('auth.register'); }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user', // Default mahasiswa
        ]);

        Auth::login($user);
        return redirect('/dashboard')->with('success', 'Pendaftaran berhasil!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->with('success', 'Anda telah logout.');
    }
}

FILE_CONTENT
,
    'app/Http/Controllers/RuangController.php' => <<<'FILE_CONTENT'
<?php

namespace App\Http\Controllers;

use App\Models\Ruang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RuangController extends Controller
{
    public function index()
    {
        $ruangs = Ruang::all();
        return view('ruang.index', compact('ruangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|unique:ruangs,kode', // Kode unik
            'nama' => 'required|string|max:255',
            'kapasitas' => 'required|integer|min:1', // Angka positif
            'lokasi' => 'required|string|max:255',
            'fasilitas' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Maks 2MB
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('foto_ruang', 'public');
        }

        Ruang::create([
            'kode' => strtoupper($request->kode),
            'nama' => $request->nama,
            'kapasitas' => $request->kapasitas,
            'lokasi' => $request->lokasi,
            'fasilitas' => $request->fasilitas,
            'foto' => $fotoPath,
        ]);

        return redirect()->route('ruang.index')->with('success', 'Ruang laboratorium berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $ruang = Ruang::findOrFail($id);
        if ($ruang->foto) {
            Storage::disk('public')->delete($ruang->foto);
        }
        $ruang->delete();
        return back()->with('success', 'Data ruang laboratorium berhasil dihapus.');
    }
}

FILE_CONTENT
,
    'app/Http/Controllers/ReservasiController.php' => <<<'FILE_CONTENT'
<?php

namespace App\Http\Controllers;

use App\Models\Reservasi;
use App\Models\Ruang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReservasiController extends Controller
{
    // 1. Tampilkan Daftar Reservasi dengan Pencarian & Filter
    public function index(Request $request)
    {
        $query = Reservasi::with(['user', 'ruang']);

        // Filter Nama Ruang
        if ($request->filled('ruang_id')) {
            $query->where('ruang_id', $request->ruang_id);
        }

        // Filter Tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search Keperluan atau Nama Peminjam
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('keperluan', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        $reservasis = $query->orderBy('tanggal', 'desc')->paginate(10);
        $ruangs = Ruang::all();

        return view('reservasi.index', compact('reservasis', 'ruangs'));
    }

    // 2. Simpan Reservasi Baru dengan PENCEGAHAN BENTROK JADWAL
    public function store(Request $request)
    {
        $request->validate([
            'ruang_id' => 'required|exists:ruangs,id',
            'tanggal' => 'required|date|after_or_equal:today',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'keperluan' => 'required|string|min:5',
            'berkas' => 'nullable|file|mimes:pdf,docx,doc|max:2048', // Batas 2MB
        ]);

        // LOGIKA ANTI-BENTROK JADWAL (Poin Kunci Rubrik):
        // Cek apakah ada jadwal aktif (bukan Ditolak) pada ruang & tanggal sama yang bertabrakan
        $bentrok = Reservasi::where('ruang_id', $request->ruang_id)
            ->where('tanggal', $request->tanggal)
            ->where('status', '!=', 'Ditolak')
            ->where(function ($q) use ($request) {
                $q->where('jam_mulai', '<', $request->jam_selesai)
                  ->where('jam_selesai', '>', $request->jam_mulai);
            })
            ->first();

        if ($bentrok) {
            return back()->withInput()->with('error', "Jadwal bentrok! Ruangan sudah dibooking pada jam {$bentrok->jam_mulai} - {$bentrok->jam_selesai}.");
        }

        $namaBerkas = null;
        if ($request->hasFile('berkas')) {
            $namaBerkas = $request->file('berkas')->store('berkas_pendukung', 'public');
        }

        Reservasi::create([
            'user_id' => Auth::id(),
            'ruang_id' => $request->ruang_id,
            'tanggal' => $request->tanggal,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'keperluan' => $request->keperluan,
            'berkas_pendukung' => $namaBerkas,
            'status' => Auth::user()->isAdmin() ? 'Disetujui' : 'Menunggu',
        ]);

        return redirect()->route('reservasi.index')->with('success', 'Reservasi berhasil diajukan!');
    }

    // 3. Ubah Status Reservasi (Khusus Admin)
    public function updateStatus(Request $request, $id)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Hanya Admin yang dapat memproses status reservasi.');
        }

        $request->validate([
            'status' => 'required|in:Menunggu,Disetujui,Ditolak',
            'catatan_admin' => 'nullable|string',
        ]);

        $res = Reservasi::findOrFail($id);
        $res->update([
            'status' => $request->status,
            'catatan_admin' => $request->catatan_admin,
        ]);

        return back()->with('success', "Status reservasi berhasil diubah menjadi {$request->status}.");
    }

    // 4. Hapus Reservasi
    public function destroy($id)
    {
        $res = Reservasi::findOrFail($id);

        if (!Auth::user()->isAdmin() && $res->user_id !== Auth::id()) {
            abort(403);
        }

        if ($res->berkas_pendukung) {
            Storage::disk('public')->delete($res->berkas_pendukung);
        }

        $res->delete();
        return back()->with('success', 'Data reservasi berhasil dihapus.');
    }
}

FILE_CONTENT
,
    'routes/web.php' => <<<'FILE_CONTENT'
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

FILE_CONTENT
,
    'resources/views/layouts/app.blade.php' => <<<'FILE_CONTENT'
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Reservasi Ruang Lab</title>
    <!-- Tailwind CSS CDN (Paling Praktis) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- SweetAlert2 CDN (Memenuhi Syarat Teknis Library JS) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Chart.js CDN untuk Dashboard -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col">
    <!-- Navbar Responsif -->
    <nav class="bg-white border-b border-slate-200 px-6 py-4 flex items-center justify-between">
        <a href="{{ route('dashboard') }}" class="font-bold text-lg text-blue-600 flex items-center gap-2">
            🏢 LabReserve Informatika
        </a>
        <div class="flex items-center gap-4 text-sm font-medium">
            <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
            <a href="{{ route('ruang.index') }}" class="hover:text-blue-600">Data Ruang</a>
            <a href="{{ route('reservasi.index') }}" class="hover:text-blue-600">Reservasi</a>
            <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-800 text-xs font-bold">
                {{ Auth::user()->name }} ({{ strtoupper(Auth::user()->role) }})
            </span>
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-rose-600 hover:underline">Keluar</button>
            </form>
        </div>
    </nav>

    <!-- Content -->
    <main class="flex-1 max-w-7xl w-full mx-auto p-6">
        @yield('content')
    </main>

    <!-- SweetAlert Feedback Toast -->
    <script>
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Perhatian!',
                text: "{{ session('error') }}"
            });
        @endif

        // Fungsi Konfirmasi Hapus SweetAlert
        function confirmDelete(formId) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }
    </script>
</body>
</html>

FILE_CONTENT
,
    'resources/views/auth/login.blade.php' => <<<'FILE_CONTENT'
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Reservasi Ruang Lab</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 flex items-center justify-center min-h-screen p-4 font-sans text-slate-800">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-md border border-slate-200 p-8">
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-12 h-12 bg-blue-100 text-blue-600 rounded-xl mb-3 text-2xl">
                🏢
            </div>
            <h1 class="text-xl font-bold text-slate-800">Sistem Reservasi Lab</h1>
            <p class="text-xs text-slate-500 mt-1">Masuk untuk mengelola & meminjam lab</p>
        </div>

        @if($errors->any())
            <div class="mb-4 p-3 bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-xl font-medium">
                {{ $errors->first() }}
            </div>
        @endif

        @if(session('success'))
            <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs rounded-xl font-medium">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-3 py-2 border rounded-xl text-xs focus:ring-2 focus:ring-blue-500 outline-none" placeholder="admin@lab.ac.id">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi</label>
                <input type="password" name="password" required class="w-full px-3 py-2 border rounded-xl text-xs focus:ring-2 focus:ring-blue-500 outline-none" placeholder="••••••••">
            </div>
            <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition">
                Masuk ke Aplikasi
            </button>
        </form>

        <div class="mt-6 pt-4 border-t border-slate-100 text-center text-xs text-slate-500">
            Belum punya akun? <a href="{{ route('register') }}" class="text-blue-600 font-semibold hover:underline">Daftar Mahasiswa</a>
        </div>

        <div class="mt-4 p-3 bg-slate-50 rounded-xl border border-slate-200 text-[11px] text-slate-600 space-y-1">
            <p class="font-bold text-slate-700">Akun Pengujian:</p>
            <p>Admin: <span class="font-mono font-semibold">admin@lab.ac.id</span> | pass: <span class="font-mono">password</span></p>
            <p>Mahasiswa: <span class="font-mono font-semibold">mahasiswa@student.ac.id</span> | pass: <span class="font-mono">password</span></p>
        </div>
    </div>
</body>
</html>

FILE_CONTENT
,
    'resources/views/auth/register.blade.php' => <<<'FILE_CONTENT'
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Sistem Reservasi Ruang Lab</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 flex items-center justify-center min-h-screen p-4 font-sans text-slate-800">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-md border border-slate-200 p-8">
        <div class="text-center mb-6">
            <h1 class="text-xl font-bold text-slate-800">Registrasi Mahasiswa</h1>
            <p class="text-xs text-slate-500 mt-1">Buat akun baru untuk mengajukan jadwal lab</p>
        </div>

        @if($errors->any())
            <div class="mb-4 p-3 bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-xl font-medium">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-3 py-2 border rounded-xl text-xs focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Ahmad Fauzi">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Email Kampus</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-3 py-2 border rounded-xl text-xs focus:ring-2 focus:ring-blue-500 outline-none" placeholder="mahasiswa@student.ac.id">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Password (min 6 karakter)</label>
                <input type="password" name="password" required class="w-full px-3 py-2 border rounded-xl text-xs focus:ring-2 focus:ring-blue-500 outline-none" placeholder="••••••••">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required class="w-full px-3 py-2 border rounded-xl text-xs focus:ring-2 focus:ring-blue-500 outline-none" placeholder="••••••••">
            </div>
            <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition">
                Daftar Sekarang
            </button>
        </form>

        <div class="mt-6 pt-4 border-t border-slate-100 text-center text-xs text-slate-500">
            Sudah punya akun? <a href="{{ route('login') }}" class="text-blue-600 font-semibold hover:underline">Masuk</a>
        </div>
    </div>
</body>
</html>

FILE_CONTENT
,
    'resources/views/dashboard.blade.php' => <<<'FILE_CONTENT'
@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs text-slate-500 font-medium">Total Laboratorium</span>
            <p class="text-2xl font-black text-slate-800 mt-1">{{ $totalRuang }}</p>
            <span class="text-[11px] text-blue-600 font-medium mt-2 block">Ruang siap pakai</span>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs text-slate-500 font-medium">Total Pengajuan</span>
            <p class="text-2xl font-black text-slate-800 mt-1">{{ $totalReservasi }}</p>
            <span class="text-[11px] text-slate-500 font-medium mt-2 block">Seluruh riwayat</span>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs text-amber-600 font-medium">Menunggu Persetujuan</span>
            <p class="text-2xl font-black text-amber-600 mt-1">{{ $menunggu }}</p>
            <span class="text-[11px] text-amber-700 font-medium mt-2 block">Perlu verifikasi</span>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs text-emerald-600 font-medium">Telah Disetujui</span>
            <p class="text-2xl font-black text-emerald-600 mt-1">{{ $disetujui }}</p>
            <span class="text-[11px] text-emerald-700 font-medium mt-2 block">Jadwal terkonfirmasi</span>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-800">Pengajuan Reservasi Terbaru</h2>
            <a href="{{ route('reservasi.index') }}" class="text-xs font-semibold text-blue-600 hover:underline">Lihat Semua &rarr;</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b">
                    <tr>
                        <th class="p-3">Ruang Lab</th>
                        <th class="p-3">Peminjam</th>
                        <th class="p-3">Tanggal & Jam</th>
                        <th class="p-3">Keperluan</th>
                        <th class="p-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($terbaru as $item)
                    <tr>
                        <td class="p-3 font-bold text-slate-800">{{ $item->ruang->kode }}</td>
                        <td class="p-3">{{ $item->user->name }}</td>
                        <td class="p-3">{{ $item->tanggal }} ({{ $item->jam_mulai }} - {{ $item->jam_selesai }})</td>
                        <td class="p-3">{{ $item->keperluan }}</td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $item->status == 'Disetujui' ? 'bg-emerald-100 text-emerald-800' : ($item->status == 'Ditolak' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                                {{ $item->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-6 text-center text-slate-400">Belum ada pengajuan reservasi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

FILE_CONTENT
,
    'resources/views/ruang/index.blade.php' => <<<'FILE_CONTENT'
@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Daftar Ruang Laboratorium</h1>
            <p class="text-xs text-slate-500">Kelola data master ruang lab, kapasitas, dan fasilitas</p>
        </div>
        @if(Auth::user()->isAdmin())
        <button onclick="document.getElementById('modal-tambah-ruang').classList.remove('hidden')" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl">
            + Tambah Ruang Baru
        </button>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($ruangs as $r)
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs flex flex-col">
            <div class="h-44 bg-slate-100 relative">
                @if($r->foto)
                    <img src="{{ asset('storage/' . $r->foto) }}" alt="{{ $r->nama }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-slate-400 text-3xl font-bold">
                        🏢
                    </div>
                @endif
                <span class="absolute top-3 left-3 bg-slate-900/80 backdrop-blur-md text-white text-[10px] font-bold px-2 py-0.5 rounded-md">
                    {{ $r->kode }}
                </span>
            </div>
            <div class="p-4 flex-1 flex flex-col justify-between">
                <div class="space-y-2">
                    <h3 class="font-bold text-slate-800 text-sm">{{ $r->nama }}</h3>
                    <p class="text-xs text-slate-500">📍 {{ $r->lokasi }}</p>
                    <p class="text-xs text-slate-600">👥 Kapasitas: <strong>{{ $r->kapasitas }} Mahasiswa</strong></p>
                    @if($r->fasilitas)
                    <div class="pt-2 border-t text-[11px] text-slate-500">
                        <strong>Fasilitas:</strong> {{ $r->fasilitas }}
                    </div>
                    @endif
                </div>
                @if(Auth::user()->isAdmin())
                <div class="pt-4 mt-3 border-t flex justify-end">
                    <form id="delete-ruang-{{ $r->id }}" action="{{ route('ruang.destroy', $r->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="confirmDelete('delete-ruang-{{ $r->id }}')" class="text-xs text-rose-600 hover:underline">
                            Hapus Ruang
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-3 p-12 bg-white rounded-2xl border border-slate-200 text-center text-slate-400">
            Belum ada data laboratorium.
        </div>
        @endforelse
    </div>
</div>

@if(Auth::user()->isAdmin())
<div id="modal-tambah-ruang" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b">
            <h3 class="font-bold text-slate-800 text-sm">Tambah Ruang Laboratorium Baru</h3>
            <button onclick="document.getElementById('modal-tambah-ruang').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
        </div>
        <form action="{{ route('ruang.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Kode Ruang (Unik) *</label>
                <input type="text" name="kode" required placeholder="LAB-AI-01" class="w-full px-3 py-2 border rounded-xl text-xs uppercase">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Laboratorium *</label>
                <input type="text" name="nama" required placeholder="Laboratorium Artificial Intelligence" class="w-full px-3 py-2 border rounded-xl text-xs">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kapasitas (Positif) *</label>
                    <input type="number" name="kapasitas" min="1" required placeholder="35" class="w-full px-3 py-2 border rounded-xl text-xs">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Lokasi Gedung *</label>
                    <input type="text" name="lokasi" required placeholder="Gedung C Lt. 3" class="w-full px-3 py-2 border rounded-xl text-xs">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Fasilitas</label>
                <textarea name="fasilitas" rows="2" placeholder="PC High-End, AC, Proyektor" class="w-full px-3 py-2 border rounded-xl text-xs"></textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Foto Ruangan (Maks 2MB)</label>
                <input type="file" name="foto" accept="image/*" class="w-full text-xs text-slate-500">
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t">
                <button type="button" onclick="document.getElementById('modal-tambah-ruang').classList.add('hidden')" class="px-4 py-2 border rounded-xl text-xs font-semibold">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold">Simpan Ruang</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

FILE_CONTENT
,
    'resources/views/reservasi/create.blade.php' => <<<'FILE_CONTENT'
@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm space-y-6">
    <div class="border-b pb-4">
        <h1 class="text-lg font-bold text-slate-800">Formulir Pengajuan Reservasi Ruang Lab</h1>
        <p class="text-xs text-slate-500">Sistem akan otomatis mengecek bentrok jadwal sebelum menyimpan.</p>
    </div>

    @if($errors->any())
        <div class="p-3 bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-xl font-medium">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('reservasi.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Laboratorium *</label>
            <select name="ruang_id" required class="w-full px-3 py-2 border rounded-xl text-xs">
                <option value="">-- Pilih Laboratorium --</option>
                @foreach(\App\Models\Ruang::all() as $r)
                    <option value="{{ $r->id }}" {{ old('ruang_id') == $r->id ? 'selected' : '' }}>
                        {{ $r->kode }} - {{ $r->nama }} (Kapasitas: {{ $r->kapasitas }})
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Penggunaan *</label>
            <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 border rounded-xl text-xs">
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Jam Mulai *</label>
                <input type="time" name="jam_mulai" value="{{ old('jam_mulai', '08:00') }}" required class="w-full px-3 py-2 border rounded-xl text-xs">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Jam Selesai *</label>
                <input type="time" name="jam_selesai" value="{{ old('jam_selesai', '10:00') }}" required class="w-full px-3 py-2 border rounded-xl text-xs">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Keperluan / Nama Kegiatan *</label>
            <textarea name="keperluan" rows="3" required placeholder="Contoh: Praktikum Mandiri Pemrograman Web" class="w-full px-3 py-2 border rounded-xl text-xs">{{ old('keperluan') }}</textarea>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Unggah Berkas Pendukung (Surat Izin / Proposal - PDF/DOCX maks 2MB)</label>
            <input type="file" name="berkas" accept=".pdf,.docx,.doc" class="w-full text-xs text-slate-500">
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t">
            <a href="{{ route('reservasi.index') }}" class="px-4 py-2 border rounded-xl text-xs font-semibold">Batal</a>
            <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-sm">
                Kirim Pengajuan Reservasi
            </button>
        </div>
    </form>
</div>
@endsection

FILE_CONTENT
,
    'resources/views/reservasi/index.blade.php' => <<<'FILE_CONTENT'
@extends('layouts.app')

@section('content')
<div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold text-slate-800">Daftar Reservasi Laboratorium</h1>
        <div class="flex gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-xs font-semibold rounded-xl">
                🖨️ Cetak / PDF
            </button>
            <a href="{{ route('reservasi.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl">
                + Ajukan Reservasi Baru
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('reservasi.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-6">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kegiatan/nama..." class="px-3 py-2 border rounded-xl text-xs">
        <select name="ruang_id" class="px-3 py-2 border rounded-xl text-xs">
            <option value="">Semua Ruangan</option>
            @foreach($ruangs as $r)
                <option value="{{ $r->id }}" {{ request('ruang_id') == $r->id ? 'selected' : '' }}>{{ $r->kode }} - {{ $r->nama }}</option>
            @endforeach
        </select>
        <select name="status" class="px-3 py-2 border rounded-xl text-xs">
            <option value="">Semua Status</option>
            <option value="Menunggu">Menunggu</option>
            <option value="Disetujui">Disetujui</option>
            <option value="Ditolak">Ditolak</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-xs font-semibold rounded-xl">Filter</button>
    </form>

    <!-- Table -->
    <table class="w-full text-left text-xs border-collapse">
        <thead class="bg-slate-50 border-b">
            <tr>
                <th class="p-3">Ruang</th>
                <th class="p-3">Peminjam</th>
                <th class="p-3">Jadwal</th>
                <th class="p-3">Keperluan</th>
                <th class="p-3">Status</th>
                <th class="p-3 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($reservasis as $item)
            <tr>
                <td class="p-3 font-bold">{{ $item->ruang->kode }}</td>
                <td class="p-3">{{ $item->user->name }}</td>
                <td class="p-3">{{ $item->tanggal }} ({{ $item->jam_mulai }} - {{ $item->jam_selesai }})</td>
                <td class="p-3">{{ $item->keperluan }}</td>
                <td class="p-3">
                    <span class="px-2 py-1 rounded-full text-[10px] font-bold {{ $item->status == 'Disetujui' ? 'bg-emerald-100 text-emerald-800' : ($item->status == 'Ditolak' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                        {{ $item->status }}
                    </span>
                </td>
                <td class="p-3 text-right">
                    <form id="delete-form-{{ $item->id }}" action="{{ route('reservasi.destroy', $item->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="confirmDelete('delete-form-{{ $item->id }}')" class="text-rose-600 hover:underline">
                            Hapus
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="p-6 text-center text-slate-400">Tidak ada data reservasi.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

FILE_CONTENT
];

$count = 0;
foreach ($files as $filePath => $content) {
    $dir = dirname($filePath);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    file_put_contents($filePath, trim($content));
    echo "✓ Berhasil dibuat: " . $filePath . "\n";
    $count++;
}

echo "\n=================================================================\n";
echo "🎉 BERHASIL: " . $count . " file arsitektur Laravel telah digenerate!\n";
echo "=================================================================\n\n";
echo "Sekarang jalankan 3 perintah ini di Terminal VS Code:\n";
echo "1. Pastikan database 'db_reservasi_lab' sudah dibuat di Laragon/HeidiSQL.\n";
echo "2. php artisan migrate:fresh --seed\n";
echo "3. php artisan storage:link\n";
echo "4. php artisan serve\n\n";
echo "Buka aplikasi di browser: http://127.0.0.1:8000\n";
echo "Akun Admin     : admin@lab.ac.id | password: password\n";
echo "Akun Mahasiswa : mahasiswa@student.ac.id | password: password\n\n";
