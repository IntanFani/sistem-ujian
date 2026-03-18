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

class ExamController extends Controller
{
    public function index()
    {
        $guruId = Auth::user()->guru->id;
        
        // Ambil ujian milik guru yang login
        $exams = Exam::where('guru_id', $guruId)
            ->with(['subject', 'kelas'])
            ->latest()
            ->get();

        // Data untuk dropdown di modal
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
            'token' => strtoupper(Str::random(6)), // Generate token otomatis
        ]);

        return back()->with('success', 'Jadwal ujian berhasil dibuat!');
    }

    // Menampilkan halaman pilih soal
    public function manageQuestions($id)
    {
        $exam = Exam::with('questions')->findOrFail($id);
        
        // Ambil bank soal milik guru ini yang BELUM ada di ujian ini
        $bankSoal = Question::where('guru_id', Auth::user()->guru->id)
                    ->where('subject_id', $exam->subject_id)
                    ->whereDoesntHave('exams', function($query) use ($id) {
                        $query->where('exam_id', $id);
                    })->get();

        return view('guru.exams.manage_questions', compact('exam', 'bankSoal'));
    }

    // Menyimpan soal yang dipilih ke ujian
    public function storeQuestions(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);
        
        // Menempelkan (attach) soal-soal yang dipilih
        $exam->questions()->attach($request->question_ids);

        return back()->with('success', 'Soal berhasil ditambahkan ke ujian!');
    }

    public function destroy($id)
    {
        // Pastikan guru hanya bisa menghapus jadwal miliknya sendiri (Security Check)
        $exam = Exam::where('guru_id', Auth::user()->guru->id)->findOrFail($id);
        
        $exam->delete();

        return back()->with('success', 'Jadwal ujian berhasil dihapus!');
    }

    public function removeQuestion(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);
        
        // Melepaskan (detach) satu soal dari ujian
        $exam->questions()->detach($request->question_id);

        return back()->with('success', 'Soal berhasil dihapus dari ujian!');
    }

    public function monitor($id)
    {
        // Ambil detail ujian
        $exam = Exam::with(['kelas', 'subject'])->findOrFail($id);

        // Ambil status siswa dari tabel exam_sessions (asumsi kamu punya relasi ke Siswa)
        // Kita filter berdasarkan ujian ini
        $statusSiswa = \App\Models\ExamSession::where('exam_id', $id)
            ->with('siswa')
            ->get();

        return view('guru.exams.monitor', compact('exam', 'statusSiswa'));
    }

    public function results()
{
    $guruId = Auth::user()->guru->id;
    // Ambil daftar ujian yang sudah pernah dibuat guru ini
    $exams = Exam::where('guru_id', $guruId)
                ->with(['subject', 'kelas'])
                ->withCount('questions') // Biar tahu total soalnya berapa
                ->latest()
                ->get();

    return view('guru.results.index', compact('exams'));
}

    public function showResult($id)
    {
        // Ambil detail ujian dan hasil kerja siswa dari tabel exam_sessions
        $exam = Exam::with(['subject', 'kelas'])->findOrFail($id);
        
        $results = ExamSession::where('exam_id', $id)
                    ->with('siswa')
                    ->orderBy('score', 'desc') // Urutkan dari nilai tertinggi
                    ->get();

        return view('guru.results.show', compact('exam', 'results'));
    }
}
