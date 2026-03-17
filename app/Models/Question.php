<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $table = 'questions';

    // Mendefinisikan kolom yang boleh diisi
    protected $fillable = [
        'subject_id',
        'guru_id',
        'question_text',
        'gambar',
        'opsi_a',
        'opsi_b',
        'opsi_c',
        'opsi_d',
        'opsi_e',
        'jawaban_benar',
    ];

    
    // Relasi ke model Subject (Mata Pelajaran)
    
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    
    // Relasi ke model Guru
    
    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function exams()
    {
        // Question terhubung ke Exam lewat tabel exam_question
        return $this->belongsToMany(Exam::class, 'exam_question');
}
}