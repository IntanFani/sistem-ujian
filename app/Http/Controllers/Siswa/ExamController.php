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
        // Eager load siswa relation from Auth user to avoid lazy load query
        $user = Auth::user()->loadMissing('siswa');
        $siswa = $user->siswa;
        $userId = $user->id;

        // Fetch only active exams or exams where student already has a session
        // Only select required columns to save memory & database bandwidth
        $exams = Exam::where('kelas_id', $siswa->kelas_id)
            ->where(function ($query) use ($userId) {
                $query->where('status', 'aktif')
                      ->orWhereHas('exam_sessions', function ($q) use ($userId) {
                          $q->where('user_id', $userId);
                      });
            })
            ->with([
                'subject:id,name', 
                'exam_sessions' => function ($q) use ($userId) {
                    $q->where('user_id', $userId)->select('id', 'exam_id', 'completed_at', 'score');
                }
            ])
            ->select('id', 'title', 'subject_id', 'duration', 'status')
            ->latest()
            ->get();

        return view('siswa.dashboard', compact('exams'));
    }

    public function start(Request $request, $id)
    {
        // Select only required columns to reduce memory usage
        $exam = Exam::select('id', 'token', 'kelas_id', 'status', 'duration')->findOrFail($id);
        $user = Auth::user()->loadMissing('siswa');
        $userId = $user->id;

        // Security check: Pastikan ujian ini aktif
        if ($exam->status !== 'aktif') {
            return back()->with('error', 'Ujian ini sedang tidak aktif.');
        }

        // 1. Verifikasi Token & Kelas Siswa
        if ($exam->token !== strtoupper($request->token)) {
            return back()->with('error', 'Token salah, Fan! Coba cek lagi.');
        }

        // Pastikan ujian yang diakses sesuai dengan kelas siswa
        if ($exam->kelas_id !== $user->siswa->kelas_id) {
             return back()->with('error', 'Akses ditolak! Ujian ini bukan untuk kelas Anda.');
        }

        // 2. Cek session pake user_id
        $session = ExamSession::where('exam_id', $id)
            ->where('user_id', $userId)
            ->select('id', 'completed_at')
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
        $userId = Auth::id();

        // 1. Ambil session dari request jika ada (ini sangat menghemat query DB)
        $session = null;
        if ($request->has('session_id')) {
            $session = ExamSession::where('id', $request->session_id)
                ->where('user_id', $userId)
                ->whereNull('completed_at')
                ->select('id', 'exam_id')
                ->first();
        }

        // 2. Fallback: Cari session yang sedang AKTIF jika session_id tidak dikirim
        if (!$session) {
            $session = ExamSession::where('user_id', $userId)
                ->whereNull('completed_at')
                ->select('id', 'exam_id')
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

        // OPTIMASI: Select hanya kolom yang dibutuhkan untuk validasi & scoring
        // Menghindari load field teks soal dan opsi pilihan ganda yang sangat besar dari DB
        $question = Question::select('id', 'exam_id', 'jenis_soal', 'jawaban_benar')
            ->findOrFail($request->question_id);

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
        // Composite index pada (exam_session_id, question_id) membuat query ini sangat cepat
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
        $userId = Auth::id();
        
        // Eager load only necessary columns, excluding 'jawaban_benar' for student security
        $exam = Exam::select('id', 'title', 'subject_id', 'duration', 'status')
            ->with(['subject:id,name', 'questions' => function ($query) {
                $query->select('questions.id', 'questions.exam_id', 'questions.jenis_soal', 'questions.question_text', 'questions.gambar', 'questions.opsi_a', 'questions.opsi_b', 'questions.opsi_c', 'questions.opsi_d', 'questions.opsi_e');
            }])
            ->findOrFail($id);

        // Security check: Pastikan ujian ini aktif
        if ($exam->status !== 'aktif') {
            return redirect()->route('siswa.dashboard')->with('error', 'Ujian ini sedang tidak aktif.');
        }

        // Ambil session pengerjaan
        $session = ExamSession::where('exam_id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();

        // Jika sudah selesai, jangan kasih masuk lagi
        if ($session->completed_at) {
            return redirect()->route('siswa.dashboard')->with('error', 'Ujian sudah selesai.');
        }

        // Calculate actual time left on the server (prevents timer manipulation via page refresh)
        $startedAt = \Carbon\Carbon::parse($session->started_at);
        $durationSeconds = $exam->duration * 60;
        $elapsedSeconds = now()->diffInSeconds($startedAt);
        $timeLeft = max(0, $durationSeconds - $elapsedSeconds);

        // Pluck only answers and map them by question_id (extremely memory efficient, avoids loading full objects)
        $userAnswers = ExamAnswer::where('exam_session_id', $session->id)
            ->pluck('answer', 'question_id');

        return view('siswa.exams.show', compact('exam', 'session', 'timeLeft', 'userAnswers'));
    }

    public function finish($id)
    {
        $userId = Auth::id();

        // 1. Cari sesi ujian siswa ini
        $session = ExamSession::where('exam_id', $id)
            ->where('user_id', $userId)
            ->select('id', 'completed_at')
            ->firstOrFail();

        // Cegah submit ganda jika tab direfresh saat loading
        if ($session->completed_at) {
            return response()->json([
                'success' => false,
                'message' => 'Ujian sudah diselesaikan sebelumnya.'
            ], 400);
        }

        // 2. OPTIMASI LOGIKA HITUNG NILAI
        // Menggunakan query aggregate COUNT dan EXISTS langsung di DB,
        // alih-alih meload seluruh model Question dan Answer ke memori PHP.
        // Ini mengurangi konsumsi RAM hingga 99% dan mencegah crash/bottleneck saat submit massal.
        $totalSoalOtomatis = Question::where('exam_id', $id)
            ->whereIn('jenis_soal', ['pilihan_ganda', 'benar_salah'])
            ->count();

        $adaEssay = Question::where('exam_id', $id)
            ->where('jenis_soal', 'essay')
            ->exists();

        // Hitung berapa jawaban yang benar langsung dari DB
        $jawabanBenar = ExamAnswer::where('exam_session_id', $session->id)
            ->where('is_correct', true)
            ->count();

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
        // Optimasi select kolom relasi untuk menghemat RAM
        $sessions = ExamSession::with(['exam:id,title,subject_id', 'exam.subject:id,name'])
            ->where('user_id', Auth::id())
            ->whereNotNull('completed_at')
            ->select('id', 'exam_id', 'completed_at', 'score')
            ->orderBy('completed_at', 'desc') // Urutkan dari yang terbaru
            ->get();

        return view('siswa.riwayat', compact('sessions'));
    }
}
