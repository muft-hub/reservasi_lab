<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ruang_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'keperluan',
        'status',
        'berkas_pendukung',
        'catatan_admin',
    ];

    // Relasi ke User peminjam
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Ruang yang dipinjam
    public function ruang()
    {
        return $this->belongsTo(Ruang::class);
    }
}