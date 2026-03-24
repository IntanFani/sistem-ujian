<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamAnswer extends Model
{
    use HasFactory;

    // Daftarkan kolom yang boleh diisi (Mass Assignment)
    protected $fillable = ['exam_session_id', 'question_id', 'answer', 'is_correct'];

    /**
     * Relasi balik ke Sesi Ujian
     */
    public function exam_session()
    {
        return $this->belongsTo(ExamSession::class);
    }

    /**
     * Relasi ke data Soal
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}