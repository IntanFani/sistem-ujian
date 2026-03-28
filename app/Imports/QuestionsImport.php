<?php

namespace App\Imports;

use App\Models\Question;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow; // Agar bisa baca header di baris pertama


class QuestionsImport implements ToModel, WithHeadingRow
{
    protected $subject_id;

    // Constructor untuk menerima ID dari Controller
    public function __construct($subject_id)
    {
        $this->subject_id = $subject_id;
    }

    public function model(array $row)
    {
    
        return new Question([
            'subject_id'    => $this->subject_id, 
            'guru_id'       => Auth::user()->guru->id,
            'question_text' => $row['pertanyaan'],
            'opsi_a'        => $row['opsi_a'],
            'opsi_b'        => $row['opsi_b'],
            'opsi_c'        => $row['opsi_c'],
            'opsi_d'        => $row['opsi_d'],
            'opsi_e'        => $row['opsi_e'] ?? '-', 
            'jawaban_benar' => $row['jawaban'],
        ]);
    }
}