<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\Kelas;
use App\Models\Question;
use App\Models\ExamSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Exports\ExamResultsExport;
use Maatwebsite\Excel\Facades\Excel;

class ExamController extends Controller
{
    public function index()
    {
        $guruId = Auth::user()->guru->id;
        
        $exams = Exam::where('guru_id', $guruId)
            ->with(['subject', 'kelas'])
            ->latest()
            ->get();

        $subjects = Subject::all();
        $kelas = Kelas::all();

        return view('guru.exams.index', compact('exams', 'subjects', 'kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subject_id' => 'required',
            'kelas_id' => 'required',
            'duration' => 'required|numeric',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        Exam::create([
            'title' => $request->title,
            'subject_id' => $request->subject_id,
            'kelas_id' => $request->kelas_id,
            'guru_id' => Auth::user()->guru->id,
            'duration' => $request->duration,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'token' => strtoupper(Str::random(6)),
        ]);

        return back()->with('success', 'Jadwal ujian berhasil dibuat!');
    }

    public function manageQuestions($id)
    {
        $exam = Exam::with('questions')->findOrFail($id);
        
        $bankSoal = Question::where('guru_id', Auth::user()->guru->id)
                    ->where('subject_id', $exam->subject_id)
                    ->whereDoesntHave('exams', function($query) use ($id) {
                        $query->where('exam_id', $id);
                    })->get();

        return view('guru.exams.manage_questions', compact('exam', 'bankSoal'));
    }

    public function storeQuestions(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);
        $exam->questions()->attach($request->question_ids);
        return back()->with('success', 'Soal berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        $exam = Exam::where('guru_id', Auth::user()->guru->id)->findOrFail($id);
        $exam->delete();
        return back()->with('success', 'Jadwal ujian berhasil dihapus!');
    }

    public function removeQuestion(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);
        $exam->questions()->detach($request->question_id);
        return back()->with('success', 'Soal berhasil dihapus dari ujian!');
    }

    public function results()
    {
        $guruId = Auth::user()->guru->id;
        $exams = Exam::where('guru_id', $guruId)
                    ->with(['subject', 'kelas'])
                    ->withCount('questions')
                    ->latest()
                    ->get();

        return view('guru.results.index', compact('exams'));
    }

    // PERBAIKAN: Fungsi Show Result (Relasi diperbaiki)
    public function showResult($id)
    {
        $exam = Exam::with(['subject', 'kelas'])->findOrFail($id);
        
        $results = ExamSession::where('exam_id', $id)
                    ->with('user.siswa') // Menggunakan jembatan user
                    ->orderBy('score', 'desc')
                    ->get();

        return view('guru.results.show', compact('exam', 'results'));
    }

    public function resetSession($id)
    {
        // Cari session berdasarkan ID
        $session = ExamSession::findOrFail($id);

        // Keamanan: Pastikan guru yang login adalah pemilik ujian ini
        if ($session->exam->guru_id !== Auth::user()->guru->id) {
            return back()->with('error', 'Anda tidak memiliki akses untuk meriset sesi ini.');
        }

        // Hapus sesi (Jika migrasi kamu pake onDelete('cascade'), 
        // maka jawaban di exam_answers bakal ikut terhapus otomatis)
        $session->delete();

        return back()->with('success', 'Sesi ujian siswa berhasil direset. Siswa bisa login dan mengulangi ujian.');
    }

    public function resetAllSessions($id)
    {
        // 1. Pastikan ujian ini memang milik guru yang login (Security)
        $exam = Exam::where('id', $id)
                    ->where('guru_id', Auth::user()->guru->id)
                    ->firstOrFail();

        // 2. Hapus SEMUA sesi siswa untuk ujian ini
        // Ini akan menghapus baris di exam_sessions yang exam_id-nya sesuai
        $deletedCount = ExamSession::where('exam_id', $id)->delete();

        if ($deletedCount > 0) {
            return back()->with('success', "Berhasil meriset $deletedCount sesi ujian. Semua siswa sekarang bisa mengulang dari awal.");
        }

        return back()->with('info', 'Tidak ada sesi ujian yang perlu diriset.');
    }

    public function exportExcel($id)
    {
        $exam = Exam::findOrFail($id);
        $namaFile = 'Hasil_Ujian_' . str_replace(' ', '_', $exam->title) . '.xlsx';
        
        return Excel::download(new ExamResultsExport($id), $namaFile);
    }
}