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