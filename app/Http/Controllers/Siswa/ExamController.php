<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\ExamSession;


class ExamController extends Controller
{
    public function index()
    {
        // Ambil data siswa yang login
        $siswa = Auth::user()->siswa; 
        
        // Ambil ujian yang tersedia untuk KELAS siswa ini
        $exams = Exam::where('kelas_id', $siswa->kelas_id)
                    ->with('subject')
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
                            ->where('user_id', $userId) // Ganti ke user_id
                            ->first();

        if ($session && $session->status == 'finished') {
            return redirect()->route('siswa.dashboard')->with('error', 'Ujian sudah selesai.');
        }

        if (!$session) {
            ExamSession::create([
                'exam_id' => $id,
                'user_id' => $userId, // Simpan sebagai user_id
                'start_time' => now(),
                'status' => 'started'
            ]);
        }

        return redirect()->route('siswa.exams.show', $id);
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
        if ($session->status == 'finished') {
            return redirect()->route('siswa.dashboard')->with('error', 'Ujian ini sudah selesai.');
        }

        return view('siswa.exams.show', compact('exam', 'session'));
    }
}