@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Daftar Ruang Laboratorium</h1>
            <p class="text-xs text-slate-500">Kelola data master ruang lab, kapasitas, dan fasilitas</p>
        </div>
        @if((Auth::user()->role ?? '') === 'admin')
        <button onclick="document.getElementById('modal-tambah-ruang').classList.remove('hidden')" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl transition shadow-xs">
            + Tambah Ruang Baru
        </button>
        @endif
    </div>

    {{-- Pesan Flash Sukses / Error --}}
    @if(session('success'))
        <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs rounded-xl font-medium">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="p-3 bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-xl font-medium">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($ruangs as $r)
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs flex flex-col justify-between">
            <div>
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
                <div class="p-4 space-y-2">
                    <h3 class="font-bold text-slate-800 text-sm">{{ $r->nama }}</h3>
                    <p class="text-xs text-slate-500">📍 {{ $r->lokasi }}</p>
                    <p class="text-xs text-slate-600">👥 Kapasitas: <strong>{{ $r->kapasitas }} Mahasiswa</strong></p>
                    @if($r->fasilitas)
                    <div class="pt-2 border-t text-[11px] text-slate-500">
                        <strong>Fasilitas:</strong> {{ $r->fasilitas }}
                    </div>
                    @endif
                </div>
            </div>

            @if((Auth::user()->role ?? '') === 'admin')
            <div class="p-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                {{-- Tombol Buka Modal Edit --}}
                <button type="button" 
                        onclick='openEditModal(@json($r))' 
                        class="text-xs text-blue-600 hover:text-blue-800 font-semibold hover:underline flex items-center gap-1">
                    ✏️ Edit Data
                </button>

                {{-- Form Hapus Ruang --}}
                <form id="delete-ruang-{{ $r->id }}" action="{{ route('ruang.destroy', $r->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="confirmDelete('delete-ruang-{{ $r->id }}')" class="text-xs text-rose-600 hover:text-rose-800 font-semibold hover:underline">
                        Hapus Ruang
                    </button>
                </form>
            </div>
            @endif
        </div>
        @empty
        <div class="col-span-3 p-12 bg-white rounded-2xl border border-slate-200 text-center text-slate-400">
            Belum ada data laboratorium.
        </div>
        @endforelse
    </div>
</div>

@if((Auth::user()->role ?? '') === 'admin')
{{-- ================= MODAL TAMBAH RUANG ================= --}}
<div id="modal-tambah-ruang" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b">
            <h3 class="font-bold text-slate-800 text-sm">Tambah Ruang Laboratorium Baru</h3>
            <button type="button" onclick="document.getElementById('modal-tambah-ruang').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>
        <form action="{{ route('ruang.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Kode Ruang (Unik) *</label>
                <input type="text" name="kode" required placeholder="LAB-AI-01" class="w-full px-3 py-2 border rounded-xl text-xs uppercase outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Laboratorium *</label>
                <input type="text" name="nama" required placeholder="Laboratorium Artificial Intelligence" class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kapasitas (Mahasiswa) *</label>
                    <input type="number" name="kapasitas" min="1" required placeholder="35" class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Lokasi Gedung *</label>
                    <input type="text" name="lokasi" required placeholder="Gedung C Lt. 3" class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Fasilitas</label>
                <textarea name="fasilitas" rows="2" placeholder="PC High-End, AC, Proyektor" class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Foto Ruangan (Maks 2MB)</label>
                <input type="file" name="foto" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700">
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t">
                <button type="button" onclick="document.getElementById('modal-tambah-ruang').classList.add('hidden')" class="px-4 py-2 border rounded-xl text-xs font-semibold">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold">Simpan Ruang</button>
            </div>
        </form>
    </div>
</div>

{{-- ================= MODAL EDIT RUANG (DITAMBAHKAN) ================= --}}
<div id="modal-edit-ruang" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b">
            <h3 class="font-bold text-slate-800 text-sm">Edit Keterangan Ruang Laboratorium</h3>
            <button type="button" onclick="document.getElementById('modal-edit-ruang').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>
        <form id="form-edit-ruang" action="" method="POST" enctype="multipart/form-data" class="space-y-3">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Kode Ruang (Unik) *</label>
                <input type="text" id="edit-kode" name="kode" required class="w-full px-3 py-2 border rounded-xl text-xs uppercase outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Laboratorium *</label>
                <input type="text" id="edit-nama" name="nama" required class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kapasitas (Mahasiswa) *</label>
                    <input type="number" id="edit-kapasitas" name="kapasitas" min="1" required class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Lokasi Gedung *</label>
                    <input type="text" id="edit-lokasi" name="lokasi" required class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Fasilitas</label>
                <textarea id="edit-fasilitas" name="fasilitas" rows="2" class="w-full px-3 py-2 border rounded-xl text-xs outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Ganti Foto Ruangan (Opsional)</label>
                <input type="file" name="foto" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700">
                <p class="text-[10px] text-slate-400 mt-1">Biarkan kosong jika tidak ingin mengganti foto saat ini.</p>
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t">
                <button type="button" onclick="document.getElementById('modal-edit-ruang').classList.add('hidden')" class="px-4 py-2 border rounded-xl text-xs font-semibold">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
// Fungsi mengisi otomatis data lama ke modal edit
function openEditModal(ruang) {
    var form = document.getElementById('form-edit-ruang');
    // Arahkan action form ke rute update ID ruang terkait
    form.action = "{{ url('admin/ruang') }}/" + ruang.id;
    
    document.getElementById('edit-kode').value = ruang.kode || '';
    document.getElementById('edit-nama').value = ruang.nama || '';
    document.getElementById('edit-kapasitas').value = ruang.kapasitas || 30;
    document.getElementById('edit-lokasi').value = ruang.lokasi || '';
    document.getElementById('edit-fasilitas').value = ruang.fasilitas || '';
    
    document.getElementById('modal-edit-ruang').classList.remove('hidden');
}

// Fungsi konfirmasi hapus aman (mendukung SweetAlert2 atau konfirmasi bawaan browser)
function confirmDelete(formId) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Yakin ingin menghapus ruang ini?',
            text: "Data ruang laboratorium akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    } else {
        if (confirm('Apakah Anda yakin ingin menghapus data ruang laboratorium ini?')) {
            document.getElementById(formId).submit();
        }
    }
}
</script>
@endif
@endsection