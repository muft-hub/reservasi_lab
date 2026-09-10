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