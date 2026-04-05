<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use Illuminate\Support\Str; // Tambahkan ini untuk fungsi random text

class ExamController extends Controller
{
    public function index()
    {
        // Ambil semua data ujian beserta relasi mata pelajaran dan guru pembuatnya dan kelas 
       $exams = Exam::with(['subject', 'guru.user', 'kelas'])->latest()->get();
        return view('admin.exams.index', compact('exams'));
    }

    // Fungsi untuk Buka/Tutup Ujian
    public function toggleStatus($id)
    {
        $exam = Exam::findOrFail($id);
        
        // Jika statusnya 'aktif', ubah jadi 'nonaktif', dan sebaliknya
        $exam->status = $exam->status == 'aktif' ? 'nonaktif' : 'aktif';
        $exam->save();

        return redirect()->back()->with('success', 'Status ujian berhasil diperbarui!');
    }

    // Fungsi untuk membuat Token baru secara acak (6 Karakter)
    public function generateToken($id)
    {
        $exam = Exam::findOrFail($id);
        $exam->token = strtoupper(Str::random(6)); // Contoh output: X7B9K2
        $exam->save();

        return redirect()->back()->with('success', 'Token ujian berhasil di-generate!');
    }

    // Menampilkan halaman live monitoring peserta
    public function monitor($id)
    {
        $exam = Exam::with(['subject', 'guru'])->findOrFail($id);
        
        // UBAH BAGIAN INI: ganti 'siswa' jadi 'user.siswa.kelas'
        $sessions = \App\Models\ExamSession::with('user.siswa.kelas')
                        ->where('exam_id', $id)
                        ->latest()
                        ->get();

        return view('admin.exams.monitor', compact('exam', 'sessions'));
    }

    // Mereset sesi ujian siswa (menghapus log agar siswa bisa ujian dari awal)
    public function resetSession($id)
    {
        $session = \App\Models\ExamSession::findOrFail($id);
        $session->delete(); // Menghapus sesi ujian

        return redirect()->back()->with('success', 'Sesi ujian siswa berhasil direset!');
    }

    // Mereset SEMUA sesi ujian untuk satu ujian (satu kelas) sekaligus
    public function resetAllSessions($id)
    {
        // Langsung hapus semua sesi yang terkait dengan ujian ini
        \App\Models\ExamSession::where('exam_id', $id)->delete();

        return redirect()->back()->with('success', 'Semua sesi ujian peserta berhasil direset!');
    }
}