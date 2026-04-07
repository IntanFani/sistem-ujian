<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Support\Facades\Auth;
use App\Models\Exam;
use App\Models\ExamSession;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $guru = $user->guru;

        if (!$guru) {
            // Logout-kan saja atau lempar ke halaman error yang rapi
            Auth::logout();
            return redirect('/login')->with('error', 'Akun Anda belum terdaftar sebagai Guru.');
        }

        // 1. Hitung Total Ujian milik guru ini
        $total_ujian = Exam::where('guru_id', $guru->id)->count();

        // 2. Hitung Ujian Aktif (asumsi ada kolom status 'aktif' di table exams)
        $ujian_aktif = Exam::where('guru_id', $guru->id)->where('status', 'aktif')->count();

        // 3. Hitung Total Soal yang pernah dibuat
        $total_soal = Question::where('guru_id', $guru->id)->count();

        // 4. Hitung Total Data Nilai (Siswa yang sudah selesai ujian milik guru ini)
        // Kita ambil ID ujian milik guru ini dulu, lalu hitung session/hasilnya
        $guruExamIds = Exam::where('guru_id', $guru->id)->pluck('id');
        $total_hasil = ExamSession::whereIn('exam_id', $guruExamIds)
                                  ->whereNotNull('completed_at')
                                  ->count();

        // 5. Ambil 5 Ujian Terbaru beserta relasi mapel dan kelasnya
        $recent_exams = Exam::with(['subject', 'kelas'])
                            ->where('guru_id', $guru->id)
                            ->latest() // urutkan dari yang paling baru
                            ->take(5)  // ambil 5 saja
                            ->get();

        // Lempar semua datanya ke view dashboard
        return view('guru.dashboard', compact(
            'total_ujian', 
            'ujian_aktif', 
            'total_soal', 
            'total_hasil', 
            'recent_exams'
        ));
    }

    
}
