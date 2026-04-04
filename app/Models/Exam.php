<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Exam extends Model
{
    protected $fillable = [
        'title', 'subject_id', 'kelas_id', 'guru_id', 
        'duration', 'start_time', 'end_time', 'token'
    ];

    // Relasi ke Mata Pelajaran
    public function subject() {
        return $this->belongsTo(Subject::class);
    }

    // Relasi ke Kelas
    public function kelas() {
        return $this->belongsTo(Kelas::class);
    }

    // Relasi ke Guru
    public function guru() {
        return $this->belongsTo(Guru::class);
    }

    // Relasi ke Soal (Many-to-Many)
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function exam_sessions()
    {
        // Satu Ujian bisa punya banyak Sesi (hasMany)
        return $this->hasMany(ExamSession::class);
    }
}