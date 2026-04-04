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
        // Pastikan relasi 'guru' ada di model User
        $guruId = Auth::user()->guru->id;
        
        $exams = Exam::where('guru_id', $guruId)
            ->with(['kelas', 'questions']) // subject biasanya nempel di Exam
            ->latest()
            ->get();

        $classes = Kelas::all();
        // Ambil mapel yang diampu guru ini (asumsi ada tabel subjects)
        $subjects = Subject::all(); 

        return view('guru.exams.index', compact('exams', 'classes', 'subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subject_id' => 'required',
            'kelas_id' => 'required',
            'duration' => 'required|numeric',
        ]);

        // Simpan data ujian ke variabel $ujian agar kita bisa ambil ID-nya
        $ujian = Exam::create([
            'title' => $request->title,
            'subject_id' => $request->subject_id,
            'kelas_id' => $request->kelas_id,
            'guru_id' => Auth::user()->guru->id,
            'duration' => $request->duration,
            'token' => strtoupper(Str::random(6)),
            'start_time' => now(), // Atau sesuai input jika ada
            'end_time' => now()->addHours(24),
        ]);

        // REDIRECT LANGSUNG KE HALAMAN KELOLA SOAL
        return redirect()->route('guru.exams.questions', $ujian->id)
            ->with('success', 'Jadwal berhasil dibuat! Silakan mulai mengisi butir soal.');
    }

    public function create()
    {
        $classes = Kelas::all();
        $subjects = Subject::all();
        return view('guru.exams.create', compact('classes', 'subjects'));
    }

    public function edit($id)
    {
        $exam = Exam::where('guru_id', Auth::user()->guru->id)->findOrFail($id);
        $classes = Kelas::all();
        $subjects = Subject::all();
        return view('guru.exams.edit', compact('exam', 'classes', 'subjects'));
    }

    public function update(Request $request, $id)
    {
        $exam = Exam::where('guru_id', Auth::user()->guru->id)->findOrFail($id);
        $exam->update($request->all());
        return redirect()->route('guru.exams.index')->with('success', 'Jadwal ujian diperbarui!');
    }

    public function destroy($id)
    {
        $exam = Exam::where('guru_id', Auth::user()->guru->id)->findOrFail($id);
        $exam->delete();
        return back()->with('success', 'Jadwal ujian berhasil dihapus!');
    }

    // --- LOGIKA INPUT SOAL LANGSUNG (EVENT-BASED) ---
    public function manageQuestions($id)
    {
        $exam = Exam::with('questions')->findOrFail($id);
        return view('guru.exams.manage_questions', compact('exam'));
    }

    public function storeQuestions(Request $request, $id)
    {
        $request->validate([
            'question_text' => 'required',
            'jawaban_benar' => 'required',
            'opsi_a' => 'required',
            'opsi_b' => 'required',
            // ... dst sesuai kolom db kamu
        ]);

        $exam = Exam::findOrFail($id);

        // Simpan soal baru langsung nempel ke Exam ID ini
        $question = new Question();
        $question->exam_id = $exam->id;
        $question->subject_id = $exam->subject_id;
        $question->guru_id = Auth::user()->guru->id;
        $question->question_text = $request->question_text;
        $question->opsi_a = $request->opsi_a;
        $question->opsi_b = $request->opsi_b;
        $question->opsi_c = $request->opsi_c;
        $question->opsi_d = $request->opsi_d;
        $question->opsi_e = $request->opsi_e;
        $question->jawaban_benar = $request->jawaban_benar;

        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('questions', 'public');
            $question->gambar = $path;
        }

        $question->save();

        return back()->with('success', 'Soal berhasil ditambahkan ke ujian ini!');
    }

    public function updateQuestion(Request $request, $id, $question_id)
    {
        $question = \App\Models\Question::findOrFail($question_id);
        
        // Update data teks dan opsi
        $question->update([
            'question_text' => $request->question_text,
            'opsi_a' => $request->opsi_a,
            'opsi_b' => $request->opsi_b,
            'opsi_c' => $request->opsi_c,
            'opsi_d' => $request->opsi_d,
            'opsi_e' => $request->opsi_e,
            'jawaban_benar' => $request->jawaban_benar,
        ]);

        // Update gambar jika ada upload baru
        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('questions', 'public');
            $question->update(['gambar' => $path]);
        }

        return back()->with('success', 'Soal berhasil diperbarui!');
    }


    public function removeQuestion($question_id)
    {
        $question = Question::findOrFail($question_id);
        $question->delete();

        return back()->with('success', 'Soal berhasil dihapus!');
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