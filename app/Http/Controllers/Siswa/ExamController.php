<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\ExamSession;
use App\Models\Question;
use App\Models\ExamAnswer;



class ExamController extends Controller
{
    public function index()
    {
        $siswa = Auth::user()->siswa;
        $userId = Auth::id();

        // Ambil ujian, sekaligus cek apakah user ini punya session di ujian tersebut
        $exams = Exam::where('kelas_id', $siswa->kelas_id)
            ->with(['subject', 'exam_sessions' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }])
            ->latest()
            ->get();

        return view('siswa.dashboard', compact('exams'));
    }

    public function start(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);
        $userId = Auth::id(); // Ambil ID User yang login

        // 1. Verifikasi Token
        if ($exam->token !== strtoupper($request->token)) {
            return back()->with('error', 'Token salah, Fan! Coba cek lagi.');
        }

        // 2. Cek session pake user_id
        $session = ExamSession::where('exam_id', $id)
            ->where('user_id', $userId)
            ->first();

        if ($session?->completed_at) {
            return redirect()->route('siswa.dashboard')->with('error', 'Ujian sudah selesai.');
        }

        if (!$session) {
            ExamSession::create([
                'exam_id' => $id,
                'user_id' => $userId,
                'started_at' => now(),
                'status' => 'started'
            ]);
        }

        return redirect()->route('siswa.exams.show', $id);
    }

    public function saveAnswer(Request $request)
    {
        // Cari session yang sedang AKTIF (completed_at masih NULL)
        $session = ExamSession::where('user_id', Auth::id())
            ->whereNull('completed_at')
            ->latest()
            ->first();

        // Jika session tidak ketemu, jangan lanjut (biar gak error 500)
        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi ujian tidak aktif atau sudah berakhir.'
            ], 404);
        }

        $question = Question::findOrFail($request->question_id);

        // Cek jawaban benar/salah
        $isCorrect = (strtoupper($request->answer) == strtoupper($question->jawaban_benar));

        // Simpan ke tabel exam_answers
        ExamAnswer::updateOrCreate(
            [
                'exam_session_id' => $session->id,
                'question_id'     => $request->question_id,
            ],
            [
                'answer'     => strtoupper($request->answer),
                'is_correct' => $isCorrect
            ]
        );

        return response()->json(['success' => true]);
    }

    public function show($id)
    {
        $exam = Exam::with(['subject', 'questions'])->findOrFail($id);
        $siswa = Auth::user()->siswa;

        // Ambil session pengerjaan
        $session = ExamSession::where('exam_id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Jika sudah selesai, jangan kasih masuk lagi
        if ($session->completed_at) {
            return redirect()->route('siswa.dashboard')->with('error', 'Ujian sudah selesai.');
        }

        return view('siswa.exams.show', compact('exam', 'session'));
    }

    public function finish($id)
    {
        // 1. Cari sesi ujian siswa ini
        $session = ExamSession::with(['exam.questions', 'answers'])
            ->where('exam_id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // 2. LOGIKA HITUNG NILAI
        $totalSoal = $session->exam->questions->count();

        // Hitung berapa yang is_correct-nya bernilai 1 (True) di tabel exam_answers
        $jawabanBenar = $session->answers->where('is_correct', 1)->count();

        // Rumus: (Benar / Total Soal) * 100
        // Kita pake round biar gak kepanjangan komanya
        $score = 0;
        if ($totalSoal > 0) {
            $score = ($jawabanBenar / $totalSoal) * 100;
        }

        // 3. Simpan ke Database
        $session->update([
            'completed_at' => now(),
            'score'        => round($score, 2), // Contoh: 85.50
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ujian selesai! Nilai kamu: ' . round($score, 2)
        ]);
    }
}
