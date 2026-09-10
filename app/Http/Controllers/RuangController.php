<?php

namespace App\Http\Controllers;

use App\Models\Ruang;
use App\Models\Reservasi; // [DITAMBAHKAN] Model Reservasi untuk data kalender
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RuangController extends Controller
{
    // ================== TAMPILAN ADMIN (DATA RUANG) ==================
    public function index()
    {   
        $ruangs = Ruang::all();
        // Arahkan ke view admin
        return view('ruang.index', compact('ruangs'));
    }

    // ================== TAMPILAN MAHASISWA (DASHBOARD + KALENDER) ==================
    public function indexMahasiswa()
    {
    $ruangs = Ruang::all();
    
    // Ambil data peminjaman untuk kalender
    $reservasis = Reservasi::with(['ruang', 'user'])
        ->whereIn('status', ['Disetujui', 'Menunggu'])
        ->get();

    return view('mahasiswa.dashboard', compact('ruangs', 'reservasis'));
   }

    // ================== KELOLA DATA (HANYA ADMIN) ==================
    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|unique:ruangs,kode', // Kode unik
            'nama' => 'required|string|max:255',
            'kapasitas' => 'required|integer|min:1', // Angka positif
            'lokasi' => 'required|string|max:255',
            'fasilitas' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Maks 2MB
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('foto_ruang', 'public');
        }

        Ruang::create([
            'kode' => strtoupper($request->kode),
            'nama' => $request->nama,
            'kapasitas' => $request->kapasitas,
            'lokasi' => $request->lokasi,
            'fasilitas' => $request->fasilitas,
            'foto' => $fotoPath,
        ]);

        return redirect()->route('admin.ruang.index')->with('success', 'Ruang laboratorium berhasil ditambahkan.');
    }

    // Tampilkan Halaman/Form Edit
    public function edit($id)
    {
        $ruang = Ruang::findOrFail($id);
        return view('ruang.edit', compact('ruang'));
    }

    // Simpan Perubahan Keterangan Ruangan
    public function update(Request $request, $id)
    {
        $ruang = Ruang::findOrFail($id);

        $request->validate([
            // Abaikan pengecekan unik kode untuk ruang ini sendiri saat di-update
            'kode' => 'required|string|unique:ruangs,kode,' . $ruang->id,
            'nama' => 'required|string|max:255',
            'kapasitas' => 'required|integer|min:1',
            'lokasi' => 'required|string|max:255',
            'fasilitas' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Maks 2MB
        ]);

        $data = [
            'kode' => strtoupper($request->kode),
            'nama' => $request->nama,
            'kapasitas' => $request->kapasitas,
            'lokasi' => $request->lokasi,
            'fasilitas' => $request->fasilitas,
        ];

        // Jika ada upload foto baru
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($ruang->foto && Storage::disk('public')->exists($ruang->foto)) {
                Storage::disk('public')->delete($ruang->foto);
            }
            // Simpan foto baru
            $data['foto'] = $request->file('foto')->store('foto_ruang', 'public');
        }

        $ruang->update($data);

        return redirect()->route('admin.ruang.index')->with('success', 'Keterangan ruang laboratorium berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $ruang = Ruang::findOrFail($id);
        if ($ruang->foto && Storage::disk('public')->exists($ruang->foto)) {
            Storage::disk('public')->delete($ruang->foto);
        }
        $ruang->delete();
        
        return back()->with('success', 'Data ruang laboratorium berhasil dihapus.');
    }
}