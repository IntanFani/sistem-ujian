<?php

namespace App\Exports;

use App\Models\ExamSession;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExamResultsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $examId;

    public function __construct($examId)
    {
        $this->examId = $examId;
    }

    public function collection()
    {
        // Ambil data sesi yang sudah selesai untuk ujian ini
        return ExamSession::where('exam_id', $this->examId)
            ->with('user.siswa')
            ->get();
    }

    public function headings(): array
    {
        return ["No", "Nama Siswa", "NISN", "Skor", "Waktu Selesai"];
    }

    public function map($res): array
    {
        static $no = 1;
        return [
            $no++,
            $res->user->siswa->nama ?? 'N/A',
            $res->user->siswa->nisn ?? '-',
            $res->score ?? 0,
            $res->completed_at ? \Carbon\Carbon::parse($res->completed_at)->format('H:i') . ' WIB' : '-',
        ];
    }
}