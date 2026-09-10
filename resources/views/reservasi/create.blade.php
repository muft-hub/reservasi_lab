@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm space-y-6">
    <div class="border-b pb-4">
        <h1 class="text-lg font-bold text-slate-800">Formulir Pengajuan Reservasi Ruang Lab</h1>
        <p class="text-xs text-slate-500">Sistem akan otomatis mengecek bentrok jadwal sebelum menyimpan.</p>
    </div>

    {{-- Pesan Error Validasi atau Bentrok --}}
    @if(session('error'))
        <div class="p-3 bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-xl font-medium">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="p-3 bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-xl font-medium">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- REVISI 1: Ubah route menjadi route mahasiswa -->
    <form action="{{ route('mahasiswa.reservasi.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Laboratorium *</label>
            <select name="ruang_id" required class="w-full px-3 py-2 border rounded-xl text-xs bg-slate-50">
                <option value="">-- Pilih Laboratorium --</option>
                @foreach(\App\Models\Ruang::all() as $r)
                    <!-- REVISI 3: Otomatis memilih ruangan berdasarkan data dari controller -->
                    <option value="{{ $r->id }}" {{ (old('ruang_id') ?? ($ruang->id ?? '')) == $r->id ? 'selected' : '' }}>
                        {{ $r->kode }} - {{ $r->nama }} (Kapasitas: {{ $r->kapasitas }})
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Penggunaan *</label>
            <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 border rounded-xl text-xs">
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Jam Mulai *</label>
                <input type="time" name="jam_mulai" value="{{ old('jam_mulai', '08:00') }}" required class="w-full px-3 py-2 border rounded-xl text-xs">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Jam Selesai *</label>
                <input type="time" name="jam_selesai" value="{{ old('jam_selesai', '10:00') }}" required class="w-full px-3 py-2 border rounded-xl text-xs">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Keperluan / Nama Kegiatan *</label>
            <textarea name="keperluan" rows="3" required placeholder="Contoh: Praktikum Mandiri Pemrograman Web" class="w-full px-3 py-2 border rounded-xl text-xs">{{ old('keperluan') }}</textarea>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Unggah Berkas Pendukung (Surat Izin / Proposal - PDF/DOCX maks 2MB)</label>
            <input type="file" name="berkas" accept=".pdf,.docx,.doc" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t">
            <!-- REVISI 2: Arahkan tombol batal ke riwayat mahasiswa -->
            <a href="{{ route('mahasiswa.reservasi.riwayat') }}" class="px-4 py-2 border rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-50">Batal</a>
            <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-sm transition-colors">
                Kirim Pengajuan Reservasi
            </button>
        </div>
    </form>
</div>
@endsection