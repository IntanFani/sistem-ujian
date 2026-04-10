<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    // Sesuaikan dengan nama tabel di migrasi kamu (biasanya 'siswas')
    protected $table = 'siswas';

    protected $fillable = [
        'user_id',
        'kelas_id',
        'nisn',
        'nama',
        'password_text',
    ];

    /**
     * Relasi ke User (Akun Login)
     * Satu siswa memiliki satu akun user
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke Kelas
     * Satu siswa menempati satu kelas
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }
}