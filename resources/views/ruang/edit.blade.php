@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-6 rounded-2xl shadow-sm border">
    <h2 class="text-lg font-bold mb-4">Edit Data Laboratorium</h2>

    <form action="{{ route('admin.ruang.update', $ruang->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-xs font-semibold mb-1">Kode Ruang</label>
            <input type="text" name="kode" value="{{ old('kode', $ruang->kode) }}" required class="w-full px-3 py-2 border rounded-xl text-xs">
        </div>

        <div>
            <label class="block text-xs font-semibold mb-1">Nama Laboratorium</label>
            <input type="text" name="nama" value="{{ old('nama', $ruang->nama) }}" required class="w-full px-3 py-2 border rounded-xl text-xs">
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-semibold mb-1">Kapasitas</label>
                <input type="number" name="kapasitas" value="{{ old('kapasitas', $ruang->kapasitas) }}" min="1" required class="w-full px-3 py-2 border rounded-xl text-xs">
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1">Lokasi</label>
                <input type="text" name="lokasi" value="{{ old('lokasi', $ruang->lokasi) }}" required class="w-full px-3 py-2 border rounded-xl text-xs">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold mb-1">Fasilitas</label>
            <textarea name="fasilitas" rows="3" class="w-full px-3 py-2 border rounded-xl text-xs">{{ old('fasilitas', $ruang->fasilitas) }}</textarea>
        </div>

        <div>
            <label class="block text-xs font-semibold mb-1">Ganti Foto Ruangan (Opsional)</label>
            @if($ruang->foto)
                <img src="{{ asset('storage/' . $ruang->foto) }}" alt="Foto Ruang" class="w-32 h-20 object-cover rounded-lg mb-2">
            @endif
            <input type="file" name="foto" accept="image/*" class="text-xs">
        </div>

        <div class="flex justify-end gap-2 pt-4 border-t">
            <a href="{{ route('admin.ruang.index') }}" class="px-4 py-2 border rounded-xl text-xs">Batal</a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-xl text-xs font-semibold">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection