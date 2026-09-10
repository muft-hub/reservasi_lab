<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruang extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'nama',
        'kapasitas',
        'lokasi',
        'fasilitas',
        'foto',
    ];

    // Relasi 1 ruang memiliki banyak reservasi
    public function reservasis()
    {
        return $this->hasMany(Reservasi::class);
    }
}