<?php

namespace App\Http\Controllers;

use App\Models\Ruang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RuangController extends Controller
{
    public function index()
    {   
        $ruangs = Ruang::all();
        return view('ruang.index', compact('ruangs'));
    }

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

        return redirect()->route('ruang.index')->with('success', 'Ruang laboratorium berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $ruang = Ruang::findOrFail($id);
        if ($ruang->foto) {
            Storage::disk('public')->delete($ruang->foto);
        }
        $ruang->delete();
        return back()->with('success', 'Data ruang laboratorium berhasil dihapus.');
    }
}