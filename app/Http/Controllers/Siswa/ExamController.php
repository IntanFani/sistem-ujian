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

        // 1. Verifikasi Token & Kelas Siswa
        if ($exam->token !== strtoupper($request->token)) {
            return back()->with('error', 'Token salah, Fan! Coba cek lagi.');
        }

        // Pastikan ujian yang diakses sesuai dengan kelas siswa
        if ($exam->kelas_id !== Auth::user()->siswa->kelas_id) {
             return back()->with('error', 'Akses ditolak! Ujian ini bukan untuk kelas Anda.');
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
        // 1. Ambil session dari request jika ada (ini sangat menghemat query DB)
        $session = null;
        if ($request->has('session_id')) {
            $session = ExamSession::where('id', $request->session_id)
                ->where('user_id', Auth::id())
                ->whereNull('completed_at')
                ->first();
        }

        // 2. Fallback: Cari session yang sedang AKTIF jika session_id tidak dikirim
        if (!$session) {
            $session = ExamSession::where('user_id', Auth::id())
                ->whereNull('completed_at')
                ->latest()
                ->first();
        }

        // Jika session tidak ketemu, jangan lanjut (biar gak error 500)
        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi ujian tidak aktif atau sudah berakhir.'
            ], 404);
        }

        $question = Question::findOrFail($request->question_id);

        // Keamanan (Mencegah IDOR): Pastikan question_id benar-benar bagian dari exam_id di session ini
        if ($question->exam_id != $session->exam_id) {
             return response()->json([
                 'success' => false,
                 'message' => 'Akses ditolak: Soal ini tidak termasuk dalam ujian Anda.'
             ], 403);
        }

        // Simpan jawaban (perhatikan essay jangan di-uppercase)
        $answerValue = $request->answer;
        $isCorrect = false;

        if ($question->jenis_soal === 'essay') {
            $isCorrect = false; // Essay butuh penilaian manual oleh guru nantinya
        } else {
            $answerValue = strtoupper($answerValue);
            $isCorrect = ($answerValue == strtoupper($question->jawaban_benar));
        }

        // Simpan ke tabel exam_answers
        ExamAnswer::updateOrCreate(
            [
                'exam_session_id' => $session->id,
                'question_id'     => $request->question_id,
            ],
            [
                'answer'     => $answerValue,
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
        // Pisahkan soal otomatis (PG/Benar Salah) dan soal manual (Essay)
        $soalOtomatis = $session->exam->questions->whereIn('jenis_soal', ['pilihan_ganda', 'benar_salah']);
        $totalSoalOtomatis = $soalOtomatis->count();
        $adaEssay = $session->exam->questions->where('jenis_soal', 'essay')->count() > 0;

        // Hitung berapa yang is_correct-nya bernilai 1 (True) di tabel exam_answers
        $jawabanBenar = $session->answers->where('is_correct', 1)->count();

        // Rumus: (Benar / Total Soal Otomatis) * 100
        $score = 0;
        if ($totalSoalOtomatis > 0) {
            $score = ($jawabanBenar / $totalSoalOtomatis) * 100;
        }

        // 3. Simpan ke Database
        $session->update([
            'completed_at' => now(),
            'score'        => round($score, 2), // Nilai sementara (PG saja)
        ]);

        $pesan = 'Ujian selesai! Nilai PG kamu: ' . round($score, 2);
        if ($adaEssay) {
            $pesan .= '. (Jawaban Essay menunggu penilaian guru)';
        }

        return response()->json([
            'success' => true,
            'message' => $pesan
        ]);
    }

    public function riwayat()
    {
        // Ambil data sesi ujian milik user (siswa) yang sedang login
        // Syarat: completed_at tidak boleh kosong (artinya sudah selesai)
        $sessions = ExamSession::with(['exam.subject'])
            ->where('user_id', Auth::id())
            ->whereNotNull('completed_at')
            ->orderBy('completed_at', 'desc') // Urutkan dari yang terbaru
            ->get();

        return view('siswa.riwayat', compact('sessions'));
    }
}
