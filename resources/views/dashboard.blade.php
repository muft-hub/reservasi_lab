@extends('layouts.app')

@section('content')
<div class="space-y-6">
    {{-- Baris Kartu Statistik --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs text-slate-500 font-medium">Total Laboratorium</span>
            <p class="text-2xl font-black text-slate-800 mt-1">{{ $totalRuang ?? 0 }}</p>
            <span class="text-[11px] text-blue-600 font-medium mt-2 block">Ruang siap pakai</span>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs text-slate-500 font-medium">Total Pengajuan</span>
            <p class="text-2xl font-black text-slate-800 mt-1">{{ $totalReservasi ?? 0 }}</p>
            <span class="text-[11px] text-slate-500 font-medium mt-2 block">Seluruh riwayat</span>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs text-amber-600 font-medium">Menunggu Persetujuan</span>
            <p class="text-2xl font-black text-amber-600 mt-1">{{ $menunggu ?? 0 }}</p>
            <span class="text-[11px] text-amber-700 font-medium mt-2 block">Perlu verifikasi</span>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs text-emerald-600 font-medium">Telah Disetujui</span>
            <p class="text-2xl font-black text-emerald-600 mt-1">{{ $disetujui ?? 0 }}</p>
            <span class="text-[11px] text-emerald-700 font-medium mt-2 block">Jadwal terkonfirmasi</span>
        </div>
    </div>

    {{-- Tabel Pengajuan Terbaru --}}
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-800">Pengajuan Reservasi Terbaru</h2>
            
            {{-- REVISI: Menggunakan admin.reservasi.index agar tidak error route not defined --}}
            <a href="{{ Route::has('admin.reservasi.index') ? route('admin.reservasi.index') : (Route::has('reservasi.index') ? route('reservasi.index') : '#') }}" 
               class="text-xs font-semibold text-blue-600 hover:underline">
                Lihat Semua &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b">
                    <tr>
                        <th class="p-3">Ruang Lab</th>
                        <th class="p-3">Peminjam</th>
                        <th class="p-3">Tanggal & Jam</th>
                        <th class="p-3">Keperluan</th>
                        <th class="p-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($terbaru as $item)
                    <tr class="hover:bg-slate-50">
                        {{-- REVISI: Menggunakan optional() agar tidak crash jika data relasi terhapus --}}
                        <td class="p-3 font-bold text-slate-800">
                            {{ optional($item->ruang)->kode ?? 'Ruang Terhapus' }}
                        </td>
                        <td class="p-3">
                            {{ optional($item->user)->name ?? 'Pengguna Terhapus' }}
                        </td>
                        <td class="p-3">
                            {{ $item->tanggal }} <br>
                            <span class="text-slate-500 text-[11px]">({{ $item->jam_mulai }} - {{ $item->jam_selesai }})</span>
                        </td>
                        <td class="p-3">{{ $item->keperluan }}</td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold 
                                {{ $item->status == 'Disetujui' ? 'bg-emerald-100 text-emerald-800' : ($item->status == 'Ditolak' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                                {{ $item->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-6 text-center text-slate-400">Belum ada pengajuan reservasi terbaru.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection