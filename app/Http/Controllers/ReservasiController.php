<?php

namespace App\Http\Controllers;

use App\Models\Reservasi;
use App\Models\Ruang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReservasiController extends Controller
{
    // 1. Tampilkan Daftar Reservasi dengan Pencarian & Filter
    public function index(Request $request)
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

    // 2. Simpan Reservasi Baru dengan PENCEGAHAN BENTROK JADWAL
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

        // LOGIKA ANTI-BENTROK JADWAL (Poin Kunci Rubrik):
        // Cek apakah ada jadwal aktif (bukan Ditolak) pada ruang & tanggal sama yang bertabrakan
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

        Reservasi::create([
            'user_id' => Auth::id(),
            'ruang_id' => $request->ruang_id,
            'tanggal' => $request->tanggal,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'keperluan' => $request->keperluan,
            'berkas_pendukung' => $namaBerkas,
            'status' => Auth::user()->isAdmin() ? 'Disetujui' : 'Menunggu',
        ]);

        return redirect()->route('reservasi.index')->with('success', 'Reservasi berhasil diajukan!');
    }

    // 3. Ubah Status Reservasi (Khusus Admin)
    public function updateStatus(Request $request, $id)
    {
        if (!Auth::user()->isAdmin()) {
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

    // 4. Hapus Reservasi
    public function destroy($id)
    {
        $res = Reservasi::findOrFail($id);

        if (!Auth::user()->isAdmin() && $res->user_id !== Auth::id()) {
            abort(403);
        }

        if ($res->berkas_pendukung) {
            Storage::disk('public')->delete($res->berkas_pendukung);
        }

        $res->delete();
        return back()->with('success', 'Data reservasi berhasil dihapus.');
    }
}