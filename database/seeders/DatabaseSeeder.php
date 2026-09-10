<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Ruang;
use App\Models\Reservasi;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Admin & Mahasiswa
        $admin = User::create([
            'name' => 'Administrator Lab Komputer',
            'email' => 'admin@lab.ac.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $user = User::create([
            'name' => 'Ahmad Fauzi (Mahasiswa)',
            'email' => 'mahasiswa@student.ac.id',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        // 2. Data Ruang Lab
        $lab1 = Ruang::create([
            'kode' => 'LAB-PL-01',
            'nama' => 'Laboratorium Rekayasa Perangkat Lunak',
            'kapasitas' => 40,
            'lokasi' => 'Gedung Fasilkom Lantai 2',
            'fasilitas' => '40 Unit PC Core i7, Proyektor 4K, AC, Gigabit LAN',
            'foto' => 'lab1.jpg',
        ]);

        $lab2 = Ruang::create([
            'kode' => 'LAB-JK-02',
            'nama' => 'Laboratorium Jaringan & Keamanan Siber',
            'kapasitas' => 32,
            'lokasi' => 'Gedung Fasilkom Lantai 3',
            'fasilitas' => 'Cisco Switch & Router Rack, 32 PC Linux, Fluke Tester, AC',
            'foto' => 'lab2.jpg',
        ]);

        // 3. Data Reservasi Awal
        Reservasi::create([
            'user_id' => $user->id,
            'ruang_id' => $lab1->id,
            'tanggal' => date('Y-m-d'),
            'jam_mulai' => '08:00',
            'jam_selesai' => '11:00',
            'keperluan' => 'Praktikum Mandiri Pemrograman Web & Laravel',
            'status' => 'Disetujui',
            'berkas_pendukung' => 'surat_izin.pdf',
        ]);
    }
}