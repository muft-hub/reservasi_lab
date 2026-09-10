<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Sistem Reservasi Lab') }}</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col font-sans">
    
    <!-- ================= NAVBAR ================= -->
    <nav class="bg-white/95 backdrop-blur-md border-b border-slate-200 sticky top-0 z-40 px-4 sm:px-6 py-3 shadow-xs">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            
            <!-- Logo Minimalis & Toggle HP -->
            <div class="flex items-center justify-between">
                <a href="{{ url('/') }}" class="flex items-center gap-2.5 group">
                    <div class="w-9 h-9 rounded-xl bg-slate-900 text-white flex items-center justify-center shadow-xs group-hover:bg-blue-600 transition-colors">
                        <!-- Ikon Gedung Minimalis -->
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div>
                        <span class="font-bold text-slate-900 tracking-tight text-base flex items-center gap-1.5">
                            LabReserve
                            <span class="text-[10px] uppercase font-semibold tracking-wider px-1.5 py-0.5 rounded bg-slate-100 text-slate-700">Informatika</span>
                        </span>
                        <p class="text-[10px] text-slate-400 font-medium">Sistem Reservasi Laboratorium</p>
                    </div>
                </a>

                <!-- Tombol Menu Mobile -->
                <button type="button" onclick="document.getElementById('nav-menu').classList.toggle('hidden')" class="md:hidden p-2 rounded-xl text-slate-500 hover:bg-slate-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>

            <!-- Menu Navigasi & Profil -->
            <div id="nav-menu" class="hidden md:flex flex-col md:flex-row md:items-center justify-between gap-4 w-full md:w-auto">
                
                @auth
                <!-- Menu Halaman Sesuai Role (Ikon SVG Minimalis) -->
                <div class="flex flex-wrap items-center gap-1 bg-slate-100/80 p-1 rounded-xl text-xs font-semibold">
                    @if(strtolower(trim(Auth::user()->role)) === 'admin')
                        <!-- Dashboard -->
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('admin.dashboard') ? 'bg-white text-slate-900 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                            Dashboard
                        </a>
                        <!-- Data Ruang -->
                        <a href="{{ route('admin.ruang.index') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('admin.ruang.*') || request()->routeIs('ruang.*') ? 'bg-white text-slate-900 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            Data Ruang
                        </a>
                        <!-- Kelola Reservasi -->
                        <a href="{{ route('admin.reservasi.index') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('admin.reservasi.*') || request()->routeIs('reservasi.*') ? 'bg-white text-slate-900 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                            Kelola Reservasi
                        </a>
                    @else
                        <!-- Daftar Ruang Mahasiswa -->
                        <a href="{{ route('mahasiswa.dashboard') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('mahasiswa.dashboard') ? 'bg-white text-slate-900 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            Daftar Ruang
                        </a>
                        <!-- Riwayat Mahasiswa -->
                        <a href="{{ route('mahasiswa.reservasi.riwayat') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition {{ request()->routeIs('mahasiswa.reservasi.*') ? 'bg-white text-slate-900 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Riwayat Saya
                        </a>
                    @endif
                </div>

                <!-- Info Profil Pengguna & Logout -->
                <div class="flex items-center gap-2 pt-2 md:pt-0 border-t md:border-t-0 border-slate-100">
                    
                    <!-- Avatar & Nama Pengguna -->
                    <div class="flex items-center gap-2 px-2.5 py-1 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="w-7 h-7 rounded-lg {{ strtolower(trim(Auth::user()->role)) === 'admin' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800' }} flex items-center justify-center font-bold text-xs">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="text-left leading-tight pr-1">
                            <p class="text-xs font-bold text-slate-800 truncate max-w-[130px]">{{ Auth::user()->name }}</p>
                            <p class="text-[10px] font-semibold {{ strtolower(trim(Auth::user()->role)) === 'admin' ? 'text-amber-600' : 'text-blue-600' }} uppercase tracking-wider">
                                {{ Auth::user()->role }}
                            </p>
                        </div>
                    </div>

                    <!-- Tombol Logout -->
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-colors" title="Keluar">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                        </button>
                    </form>
                </div>
                @else
                <!-- Saat Belum Login -->
                <div class="flex items-center gap-2">
                    <a href="{{ route('login') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-xs">
                        Masuk / Login
                    </a>
                    <a href="{{ route('register') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition">
                        Daftar Akun
                    </a>
                </div>
                @endauth
            </div>
        </div>
    </nav>

    <!-- ================= NOTIFIKASI PESAN ================= -->
    <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 pt-4">
        @if(session('success'))
            <div class="p-3 mb-2 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs rounded-xl font-medium flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="p-3 mb-2 bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-xl font-medium flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif
    </div>

    <!-- ================= KONTEN UTAMA ================= -->
    <main class="flex-1 max-w-7xl w-full mx-auto p-4 sm:p-6">
        @yield('content')
    </main>

    <!-- ================= FOOTER ================= -->
    <footer class="bg-white border-t border-slate-200 py-4 text-center text-xs text-slate-400 mt-auto">
        &copy; {{ date('Y') }} Sistem Informasi Reservasi Laboratorium Informatika.
    </footer>

    <!-- ================= JAVASCRIPT SWEETALERT ================= -->
    <script>
    function confirmDelete(formId, pesanCustom) {
        Swal.fire({
            title: 'Konfirmasi Penghapusan',
            text: pesanCustom || 'Apakah Anda yakin ingin menghapus data ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus',
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