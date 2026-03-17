<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';

    protected $fillable = [
        'nama_kelas',
        'guru_id'
    ];

    public function waliKelas()
    {
        // Parameter kedua adalah 'guru_id' (foreign key di tabel kelas)
        return $this->belongsTo(Guru::class, 'guru_id');
    }
}