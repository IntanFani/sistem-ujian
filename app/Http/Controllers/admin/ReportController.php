<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamSession;
use App\Exports\AdminExamResultExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    // Menampilkan daftar ujian untuk direkap
    public function index()
    {
        $exams = Exam::with(['subject', 'guru.user', 'kelas'])->latest()->get();
        return view('admin.reports.index', compact('exams'));
    }

    // Menampilkan detail nilai dari satu ujian
    public function show($id)
    {
        $exam = Exam::with(['subject', 'guru.user', 'kelas'])->findOrFail($id);
        
        // Hanya ambil sesi ujian yang sudah SELESAI (ada waktunya), urutkan dari nilai tertinggi
        $sessions = ExamSession::with('user.siswa.kelas')
                        ->where('exam_id', $id)
                        ->whereNotNull('completed_at') 
                        ->orderBy('score', 'desc')
                        ->get();

        return view('admin.reports.show', compact('exam', 'sessions'));
    }

    // Fungsi untuk export nilai ke format Excel (.xls)
    public function exportExcel($id)
    {
        $exam = Exam::with('kelas')->findOrFail($id);
        
        // Nama file saat di-download
        $filename = "Nilai_" . str_replace(' ', '_', $exam->title) . "_" . ($exam->kelas->nama_kelas ?? 'Kelas') . ".xlsx";

        // Gunakan AdminExamResultExport yang baru saja kita buat
        return Excel::download(new AdminExamResultExport($id), $filename);
    }
}