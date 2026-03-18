<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamSession extends Model
{
    protected $fillable = ['exam_id', 'siswa_id', 'start_time', 'end_time', 'status', 'score'];

    // Relasi ke Siswa
    public function siswa() {
        return $this->belongsTo(Siswa::class);
    }

    // Relasi ke Ujian
    public function exam() {
        return $this->belongsTo(Exam::class);
    }
}
