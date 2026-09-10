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
    <nav class="bg-white border-b border-slate-200 px-6 py-4 flex items-center justify-between shadow-sm">
        <!-- Logo diarahkan ke url('/') agar dilempar otomatis ke dashboard masing-masing -->
        <a href="{{ url('/') }}" class="font-bold text-lg text-blue-600 flex items-center gap-2">
            🏢 LabReserve Informatika
        </a>
        
        <div class="flex items-center gap-4 text-sm font-medium">
            
            <!-- MENU DINAMIS BERDASARKAN ROLE -->
            @if(Auth::user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 transition-colors">Dashboard Admin</a>
                <a href="{{ route('admin.ruang.index') }}" class="hover:text-blue-600 transition-colors">Data Ruang</a>
                <a href="{{ route('admin.reservasi.index') }}" class="hover:text-blue-600 transition-colors">Kelola Reservasi</a>
            @else
                <a href="{{ route('mahasiswa.dashboard') }}" class="hover:text-blue-600 transition-colors">Daftar Ruang</a>
                <a href="{{ route('mahasiswa.reservasi.riwayat') }}" class="hover:text-blue-600 transition-colors">Riwayat Saya</a>
            @endif

            <span class="px-2 py-0.5 rounded-md bg-blue-100 text-blue-800 text-xs font-bold border border-blue-200 ml-2">
                {{ Auth::user()->name }} ({{ strtoupper(Auth::user()->role) }})
            </span>
            
            <form action="{{ route('logout') }}" method="POST" class="inline ml-2">
                @csrf
                <button type="submit" class="text-rose-600 font-semibold hover:text-rose-800 hover:underline transition-colors">Keluar</button>
            </form>
        </div>
    </nav>

    <!-- Content -->
    <main class="flex-1 max-w-7xl w-full mx-auto p-6">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 text-center py-4 text-xs text-slate-500 mt-auto">
        &copy; {{ date('Y') }} LabReserve Informatika. Sistem Peminjaman Ruangan.
    </footer>

    <!-- SweetAlert Feedback Toast -->
    <script>
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end' // Dibuat ala toast agar tidak mengganggu layar
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
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }
    </script>
</body>
</html>