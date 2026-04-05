<?php

namespace App\Exports;

use App\Models\Exam;
use App\Models\ExamSession;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AdminExamResultExport implements FromView, ShouldAutoSize
{
    protected $exam_id;

    public function __construct($exam_id)
    {
        $this->exam_id = $exam_id;
    }

    public function view(): View
    {
        $exam = Exam::with(['subject', 'guru.user', 'kelas'])->findOrFail($this->exam_id);
        
        $sessions = ExamSession::with('user.siswa.kelas')
                        ->where('exam_id', $this->exam_id)
                        ->whereNotNull('completed_at')
                        ->orderBy('score', 'desc')
                        ->get();

        // Melempar data ke view excel.blade.php yang tadi kita buat di folder admin
        return view('admin.reports.excel', compact('exam', 'sessions'));
    }
}