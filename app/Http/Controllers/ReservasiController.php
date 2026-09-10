<?php

namespace App\Http\Controllers;

use App\Models\Reservasi;
use App\Models\Ruang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReservasiController extends Controller
{
    // ==========================================
    // 1. TAMPILAN ADMIN (Melihat Semua Data)
    // ==========================================
    public function indexAdmin(Request $request)
    {
        $query = Reservasi::with(['user', 'ruang']);

        // Filter Nama Ruang
        if ($request->filled('ruang_id')) {
            $query->where('ruang_id', $request->ruang_id);
        }

        // Filter Tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search Keperluan atau Nama Peminjam
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('keperluan', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        $reservasis = $query->orderBy('tanggal', 'desc')->paginate(10);
        $ruangs = Ruang::all();

        return view('reservasi.index', compact('reservasis', 'ruangs'));
    }

    // ==========================================
    // 2. TAMPILAN MAHASISWA (Melihat Riwayat Sendiri)
    // ==========================================
    public function indexMahasiswa(Request $request)
    {
        // Hanya tampilkan reservasi milik user yang sedang login
        $reservasis = Reservasi::with(['ruang'])
            ->where('user_id', Auth::id())
            ->orderBy('tanggal', 'desc')
            ->paginate(10);

        // Pastikan kamu membuat file view ini (misal: resources/views/reservasi/riwayat.blade.php)
        return view('reservasi.riwayat', compact('reservasis'));
    }

    // ==========================================
    // 3. FORM PENGAJUAN (Mahasiswa)
    // ==========================================
    public function create($ruang_id)
    {
        $ruang = Ruang::findOrFail($ruang_id);
        return view('reservasi.create', compact('ruang'));
    }

    // ==========================================
    // 4. SIMPAN RESERVASI BARU & ANTI BENTROK
    // ==========================================
    public function store(Request $request)
    {
        $request->validate([
            'ruang_id' => 'required|exists:ruangs,id',
            'tanggal' => 'required|date|after_or_equal:today',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'keperluan' => 'required|string|min:5',
            'berkas' => 'nullable|file|mimes:pdf,docx,doc|max:2048', // Batas 2MB
        ]);

        // LOGIKA ANTI-BENTROK JADWAL:
        $bentrok = Reservasi::where('ruang_id', $request->ruang_id)
            ->where('tanggal', $request->tanggal)
            ->where('status', '!=', 'Ditolak')
            ->where(function ($q) use ($request) {
                $q->where('jam_mulai', '<', $request->jam_selesai)
                  ->where('jam_selesai', '>', $request->jam_mulai);
            })
            ->first();

        if ($bentrok) {
            return back()->withInput()->with('error', "Jadwal bentrok! Ruangan sudah dibooking pada jam {$bentrok->jam_mulai} - {$bentrok->jam_selesai}.");
        }

        $namaBerkas = null;
        if ($request->hasFile('berkas')) {
            $namaBerkas = $request->file('berkas')->store('berkas_pendukung', 'public');
        }

        // Cek Role (Jika yang bikin kebetulan admin, langsung disetujui)
        $statusAwal = Auth::user()->role === 'admin' ? 'Disetujui' : 'Menunggu';

        Reservasi::create([
            'user_id' => Auth::id(),
            'ruang_id' => $request->ruang_id,
            'tanggal' => $request->tanggal,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'keperluan' => $request->keperluan,
            'berkas_pendukung' => $namaBerkas,
            'status' => $statusAwal,
        ]);

        // REVISI: Redirect ke riwayat mahasiswa
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.reservasi.index')->with('success', 'Reservasi berhasil ditambahkan!');
        }
        
        return redirect()->route('mahasiswa.reservasi.riwayat')->with('success', 'Reservasi berhasil diajukan! Menunggu persetujuan Admin.');
    }

    // ==========================================
    // 5. UBAH STATUS (Khusus Admin)
    // ==========================================
    public function updateStatus(Request $request, $id)
    {
        // Pengecekan keamanan ganda menggunakan kolom 'role'
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Hanya Admin yang dapat memproses status reservasi.');
        }

        $request->validate([
            'status' => 'required|in:Menunggu,Disetujui,Ditolak',
            'catatan_admin' => 'nullable|string',
        ]);

        $res = Reservasi::findOrFail($id);
        $res->update([
            'status' => $request->status,
            'catatan_admin' => $request->catatan_admin,
        ]);

        return back()->with('success', "Status reservasi berhasil diubah menjadi {$request->status}.");
    }

    // ==========================================
    // 6. HAPUS RESERVASI
    // ==========================================
    public function destroy($id)
    {
        $res = Reservasi::findOrFail($id);

        if (Auth::user()->role !== 'admin' && $res->user_id !== Auth::id()) {
            abort(403);
        }

        if ($res->berkas_pendukung) {
            Storage::disk('public')->delete($res->berkas_pendukung);
        }

        $res->delete();
        return back()->with('success', 'Data reservasi berhasil dihapus.');
    }
}