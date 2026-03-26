<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamSession extends Model
{
    protected $fillable = ['exam_id', 'user_id', 'started_at', 'completed_at', 'score', 'created_at', 'updated_at'];
    
     /**
     * Relasi ke User
     * Satu sesi ujian dimiliki oleh satu user
     */

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function answers()
    {
        return $this->hasMany(ExamAnswer::class);
    }

}
