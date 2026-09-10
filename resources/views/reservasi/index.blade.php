@extends('layouts.app')

@section('content')
<div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold text-slate-800">Daftar Reservasi Laboratorium (Admin)</h1>
        <div class="flex gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-xs font-semibold rounded-xl">
                🖨️ Cetak / PDF
            </button>
            <!-- Arahkan ke daftar ruang admin jika admin ingin meminjam -->
            <a href="{{ route('admin.ruang.index') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl">
                + Pilih Ruang untuk Reservasi
            </a>
        </div>
    </div>

    {{-- Pesan Sukses --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs rounded-xl font-medium">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filter Form -->
    <form method="GET" action="{{ route('admin.reservasi.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-6">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kegiatan/nama..." class="px-3 py-2 border rounded-xl text-xs">
        <select name="ruang_id" class="px-3 py-2 border rounded-xl text-xs">
            <option value="">Semua Ruangan</option>
            @foreach($ruangs as $r)
                <option value="{{ $r->id }}" {{ request('ruang_id') == $r->id ? 'selected' : '' }}>{{ $r->kode }} - {{ $r->nama }}</option>
            @endforeach
        </select>
        <select name="status" class="px-3 py-2 border rounded-xl text-xs">
            <option value="">Semua Status</option>
            <option value="Menunggu" {{ request('status') == 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
            <option value="Disetujui" {{ request('status') == 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
            <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
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
            <tr class="hover:bg-slate-50">
                <td class="p-3 font-bold">{{ $item->ruang->kode }}</td>
                <td class="p-3">{{ $item->user->name }}</td>
                <td class="p-3">{{ $item->tanggal }} <br> <span class="text-slate-500">({{ $item->jam_mulai }} - {{ $item->jam_selesai }})</span></td>
                <td class="p-3">{{ $item->keperluan }}</td>
                <td class="p-3">
                    <span class="px-2 py-1 rounded-full text-[10px] font-bold {{ $item->status == 'Disetujui' ? 'bg-emerald-100 text-emerald-800' : ($item->status == 'Ditolak' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                        {{ $item->status }}
                    </span>
                </td>
                <td class="p-3 text-right space-x-2">
                    
                    {{-- Tombol Setujui/Tolak hanya muncul jika status masih Menunggu --}}
                    @if($item->status == 'Menunggu')
                        <form action="{{ route('admin.reservasi.updateStatus', $item->id) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="Disetujui">
                            <button type="submit" class="text-emerald-600 font-semibold hover:underline">Setujui</button>
                        </form>
                        <form action="{{ route('admin.reservasi.updateStatus', $item->id) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="Ditolak">
                            <button type="submit" class="text-rose-600 font-semibold hover:underline">Tolak</button>
                        </form>
                        <span class="text-slate-300">|</span>
                    @endif

                    {{-- Tombol Hapus --}}
                    <form id="delete-form-{{ $item->id }}" action="{{ route('admin.reservasi.destroy', $item->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="if(confirm('Yakin ingin menghapus data ini?')) document.getElementById('delete-form-{{ $item->id }}').submit();" class="text-slate-500 hover:text-rose-600 hover:underline">
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
    
    <!-- Pagination (Jika ada) -->
    <div class="mt-4">
        {{ $reservasis->links() }}
    </div>
</div>
@endsection