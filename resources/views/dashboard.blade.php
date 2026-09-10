@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs text-slate-500 font-medium">Total Laboratorium</span>
            <p class="text-2xl font-black text-slate-800 mt-1">{{ $totalRuang }}</p>
            <span class="text-[11px] text-blue-600 font-medium mt-2 block">Ruang siap pakai</span>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs text-slate-500 font-medium">Total Pengajuan</span>
            <p class="text-2xl font-black text-slate-800 mt-1">{{ $totalReservasi }}</p>
            <span class="text-[11px] text-slate-500 font-medium mt-2 block">Seluruh riwayat</span>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs text-amber-600 font-medium">Menunggu Persetujuan</span>
            <p class="text-2xl font-black text-amber-600 mt-1">{{ $menunggu }}</p>
            <span class="text-[11px] text-amber-700 font-medium mt-2 block">Perlu verifikasi</span>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs text-emerald-600 font-medium">Telah Disetujui</span>
            <p class="text-2xl font-black text-emerald-600 mt-1">{{ $disetujui }}</p>
            <span class="text-[11px] text-emerald-700 font-medium mt-2 block">Jadwal terkonfirmasi</span>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-800">Pengajuan Reservasi Terbaru</h2>
            <a href="{{ route('reservasi.index') }}" class="text-xs font-semibold text-blue-600 hover:underline">Lihat Semua &rarr;</a>
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
                    <tr>
                        <td class="p-3 font-bold text-slate-800">{{ $item->ruang->kode }}</td>
                        <td class="p-3">{{ $item->user->name }}</td>
                        <td class="p-3">{{ $item->tanggal }} ({{ $item->jam_mulai }} - {{ $item->jam_selesai }})</td>
                        <td class="p-3">{{ $item->keperluan }}</td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $item->status == 'Disetujui' ? 'bg-emerald-100 text-emerald-800' : ($item->status == 'Ditolak' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                                {{ $item->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-6 text-center text-slate-400">Belum ada pengajuan reservasi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection