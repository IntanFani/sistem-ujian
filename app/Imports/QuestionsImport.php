<?php

namespace App\Imports;

use App\Models\Question;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class QuestionsImport implements ToModel, WithHeadingRow
{
    protected $subject_id;
    protected $exam_id;

    // Tambahkan exam_id di construct
    public function __construct($subject_id, $exam_id)
    {
        $this->subject_id = $subject_id;
        $this->exam_id = $exam_id;
    }

    public function model(array $row)
    {
        // 1. Cari data ujian berdasarkan exam_id yang dikirim dari controller
        $exam = \App\Models\Exam::find($this->exam_id);

        return new Question([
            'subject_id'    => $this->subject_id,
            'exam_id'       => $this->exam_id,
            // 2. Ambil guru_id langsung dari data ujian tersebut
            'guru_id'       => $exam->guru_id,
            'question_text' => $row['pertanyaan'],
            'opsi_a'        => $row['opsi_a'],
            'opsi_b'        => $row['opsi_b'],
            'opsi_c'        => $row['opsi_c'],
            'opsi_d'        => $row['opsi_d'],
            'opsi_e'        => $row['opsi_e'] ?? '-',
            'jawaban_benar' => strtolower($row['jawaban']),
        ]);
    }
}
