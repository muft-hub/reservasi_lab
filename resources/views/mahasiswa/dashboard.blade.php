@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between border-b pb-4">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Daftar Laboratorium Tersedia</h1>
            <p class="text-xs text-slate-500">Pilih laboratorium yang ingin kamu gunakan untuk kegiatan praktikum atau riset.</p>
        </div>
        <a href="{{ route('mahasiswa.reservasi.riwayat') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-xl transition-colors">
            Lihat Riwayat Reservasi Saya
        </a>
    </div>

    {{-- Grid Daftar Ruangan --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($ruangs as $r)
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm flex flex-col">
            <!-- Foto Ruangan -->
            @if($r->foto)
                <img src="{{ asset('storage/' . $r->foto) }}" alt="{{ $r->nama }}" class="w-full h-48 object-cover">
            @else
                <div class="w-full h-48 bg-slate-100 flex items-center justify-center text-slate-400 text-xs font-medium">
                    Tidak ada foto
                </div>
            @endif

            <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="px-2 py-0.5 bg-blue-50 text-blue-700 font-bold text-[10px] rounded-md border border-blue-100">
                            {{ $r->kode }}
                        </span>
                        <span class="text-xs text-slate-500">Kapasitas: <strong>{{ $r->kapasitas }} orang</strong></span>
                    </div>
                    
                    <h3 class="font-bold text-slate-800 text-base">{{ $r->nama }}</h3>
                    <p class="text-xs text-slate-600 line-clamp-2">📍 {{ $r->lokasi }}</p>
                    
                    @if($r->fasilitas)
                        <p class="text-xs text-slate-500 bg-slate-50 p-2 rounded-xl">
                            <strong>Fasilitas:</strong> {{ $r->fasilitas }}
                        </p>
                    @endif
                </div>

                <div class="pt-3 border-t">
                    <a href="{{ route('mahasiswa.reservasi.create', $r->id) }}" class="w-full block text-center py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition-colors shadow-sm">
                        Ajukan Reservasi Ruangan Ini
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-3 text-center py-12 bg-white rounded-2xl border border-slate-200 text-slate-400 text-xs">
            Belum ada data laboratorium yang ditambahkan oleh admin.
        </div>
        @endforelse
    </div>
</div>
@endsection