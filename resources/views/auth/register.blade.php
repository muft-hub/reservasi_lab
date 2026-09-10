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