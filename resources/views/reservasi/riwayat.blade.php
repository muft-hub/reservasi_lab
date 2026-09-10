@extends('layouts.app')

@section('content')
<div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 space-y-6">
    <div class="flex items-center justify-between border-b pb-4">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Riwayat Pengajuan Reservasi Saya</h1>
            <p class="text-xs text-slate-500">Pantau status persetujuan peminjaman laboratorium yang telah kamu ajukan.</p>
        </div>
        <a href="{{ route('mahasiswa.dashboard') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl transition-colors">
            + Buat Reservasi Baru
        </a>
    </div>

    <!-- Tabel Riwayat -->
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs border-collapse">
            <thead class="bg-slate-50 border-b">
                <tr>
                    <th class="p-3">Laboratorium</th>
                    <th class="p-3">Jadwal Penggunaan</th>
                    <th class="p-3">Keperluan</th>
                    <th class="p-3">Berkas</th>
                    <th class="p-3">Status</th>
                    <th class="p-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($reservasis as $item)
                <tr class="hover:bg-slate-50">
                    <td class="p-3">
                        <span class="font-bold text-slate-800">{{ $item->ruang->nama }}</span>
                        <span class="block text-[10px] text-slate-400">{{ $item->ruang->kode }}</span>
                    </td>
                    <td class="p-3">
                        {{ $item->tanggal }}
                        <span class="block text-slate-500">{{ $item->jam_mulai }} - {{ $item->jam_selesai }} WIB</span>
                    </td>
                    <td class="p-3 max-w-xs truncate">{{ $item->keperluan }}</td>
                    <td class="p-3">
                        @if($item->berkas_pendukung)
                            <a href="{{ asset('storage/' . $item->berkas_pendukung) }}" target="_blank" class="text-blue-600 hover:underline font-semibold">
                                Lihat Berkas
                            </a>
                        @else
                            <span class="text-slate-400">-</span>
                        @endif
                    </td>
                    <td class="p-3">
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $item->status == 'Disetujui' ? 'bg-emerald-100 text-emerald-800' : ($item->status == 'Ditolak' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                            {{ $item->status }}
                        </span>
                    </td>
                    <td class="p-3 text-right">
                        {{-- Mahasiswa hanya bisa menghapus jika status masih Menunggu --}}
                        @if($item->status == 'Menunggu')
                            <form id="delete-form-{{ $item->id }}" action="{{ route('mahasiswa.reservasi.destroy', $item->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmDelete('delete-form-{{ $item->id }}')" class="text-rose-600 hover:underline font-semibold">
                                    Batalkan
                                </button>
                            </form>
                        @else
                            <span class="text-slate-400 italic">Terkunci</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-8 text-center text-slate-400">
                        Kamu belum pernah mengajukan reservasi laboratorium.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $reservasis->links() }}
    </div>
</div>
@endsection