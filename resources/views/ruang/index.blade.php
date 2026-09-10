@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Daftar Ruang Laboratorium</h1>
            <p class="text-xs text-slate-500">Kelola data master ruang lab, kapasitas, dan fasilitas</p>
        </div>
        @if((Auth::user()->role ?? '') === 'admin')
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
                @if((Auth::user()->role ?? '') === 'admin')
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

@if((Auth::user()->role ?? '') === 'admin')
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