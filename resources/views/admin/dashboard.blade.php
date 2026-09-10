@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="border-b pb-4">
        <h1 class="text-2xl font-bold text-slate-800">Dashboard Admin</h1>
        <p class="text-sm text-slate-500">Ringkasan sistem reservasi laboratorium.</p>
    </div>

    <!-- Statistik Card -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs font-semibold text-slate-500 uppercase">Total Ruangan</p>
            <h3 class="text-2xl font-bold text-slate-800">{{ $totalRuang }}</h3>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs font-semibold text-amber-500 uppercase">Menunggu Persetujuan</p>
            <h3 class="text-2xl font-bold text-amber-600">{{ $menunggu }}</h3>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs font-semibold text-emerald-500 uppercase">Reservasi Disetujui</p>
            <h3 class="text-2xl font-bold text-emerald-600">{{ $disetujui }}</h3>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs font-semibold text-rose-500 uppercase">Reservasi Ditolak</p>
            <h3 class="text-2xl font-bold text-rose-600">{{ $ditolak }}</h3>
        </div>
    </div>

    <!-- Tombol Aksi Cepat -->
    <div class="flex gap-3">
        <a href="{{ route('admin.ruang.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold rounded-xl">
            Kelola Ruang Lab
        </a>
        <a href="{{ route('admin.reservasi.index') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl">
            Proses Reservasi
        </a>
    </div>
</div>
@endsection