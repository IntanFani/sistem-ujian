<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Kelas;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Question;
use Illuminate\Support\Facades\Storage;
use App\Imports\QuestionsImport; 
use Maatwebsite\Excel\Facades\Excel;

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

    public function create()
    {
        // Ambil data guru beserta relasi user (untuk nama) dan subject (untuk mapel)
        $gurus = Guru::with(['user', 'subject'])->get();

        // Sesuaikan nama variabel dengan di Blade kamu: $kelases
        $kelases = Kelas::all();

        return view('admin.exams.create', compact('gurus', 'kelases'));
    }

    // Tambah Ujian Baru
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'title'    => 'required|string|max:255',
            'guru_id'  => 'required|exists:gurus,id',
            'kelas_id' => 'required|exists:kelas,id',
            'duration' => 'required|integer|min:1',
        ]);

        // 1. Ambil data guru untuk subject_id
        $guru = Guru::findOrFail($request->guru_id);

        // 2. Set waktu mulai (sekarang) dan hitung waktu selesai
        $duration = (int) $request->duration;
        $startTime = now();
        $endTime = now()->addMinutes($duration);

        // 3. Simpan ke database
        Exam::create([
            'subject_id' => $guru->subject_id,
            'kelas_id'   => $request->kelas_id,
            'guru_id'    => $request->guru_id,
            'title'      => $request->title,
            'duration'   => $request->duration,
            'start_time' => $startTime,
            'end_time'   => $endTime,
            'token'      => strtoupper(Str::random(6)),
            'status'     => 'nonaktif',
        ]);

        return redirect()->route('admin.exams.index')->with('success', 'Ujian berhasil dibuat!');
    }

    // Form Edit Ujian
    public function edit($id)
    {
        // 1. Ambil data ujian yang mau diedit
        $exam = Exam::findOrFail($id);

        // 2. Ambil data pendukung untuk dropdown
        $gurus = Guru::with('user')->get();
        
        // PASTIKAN NAMA VARIABELNYA $kelases (pakai 'es') agar cocok dengan file Blade
        $kelases = Kelas::all();

        // 3. Kirim semuanya ke view
        return view('admin.exams.edit', compact('exam', 'gurus', 'kelases'));
    }

    // Update Data Ujian
    public function update(Request $request, $id)
    {
        $request->validate([
            'title'    => 'required|string|max:255',
            'guru_id'  => 'required',
            'kelas_id' => 'required',
            'duration' => 'required|integer',
        ]);

        $exam = Exam::findOrFail($id);
        $guru = Guru::findOrFail($request->guru_id);

        // Hitung ulang end_time berdasarkan duration baru
        $endTime = \Carbon\Carbon::parse($exam->start_time)->addMinutes((int)$request->duration);

        $exam->update([
            'title'      => $request->title,
            'guru_id'    => $request->guru_id,
            'subject_id' => $guru->subject_id,
            'kelas_id'   => $request->kelas_id,
            'duration'   => $request->duration,
            'end_time'   => $endTime,
        ]);

        return redirect()->route('admin.exams.index')->with('success', 'Data ujian berhasil diperbarui!');
    }

    // Halaman Kelola Soal
    public function questions($id)
    {
        // Eager loading questions agar tidak berat
        $exam = Exam::with(['questions', 'kelas', 'guru.user'])->findOrFail($id);
        return view('admin.exams.questions', compact('exam'));
    }

    // Hapus Ujian
    public function destroy($id)
    {
        Exam::destroy($id);
        return redirect()->back()->with('success', 'Ujian berhasil dihapus');
    }

    // 1. Simpan Soal Baru
    public function storeQuestion(Request $request, $id)
    {
        // 1. Validasi input
        $request->validate([
            'question_text' => 'required',
            'opsi_a' => 'required',
            'opsi_b' => 'required',
            'opsi_c' => 'required',
            'opsi_d' => 'required',
            'opsi_e' => 'required',
            'jawaban_benar' => 'required',
            'gambar' => 'nullable|image|mimes:jpg,png,jpeg|max:2048'
        ]);

        // 2. Ambil data Ujian untuk dapet subject_id dan guru_id
        $exam =Exam::findOrFail($id);

        // 3. Siapkan data untuk disimpan
        $data = $request->all();
        $data['exam_id']    = $id;
        $data['subject_id'] = $exam->subject_id; // Ambil otomatis dari ujian
        $data['guru_id']    = $exam->guru_id;    // Ambil otomatis dari ujian

        // 4. Handle upload gambar jika ada
        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('questions', 'public');
        }

        // 5. Simpan ke tabel questions
        Question::create($data);

        return redirect()->back()->with('success', 'Butir soal berhasil ditambahkan!');
    }

    // 2. Update Soal
    public function updateQuestion(Request $request, $id, $question_id)
    {
        $question = Question::findOrFail($question_id);

        $request->validate([
            'question_text' => 'required',
            'opsi_a' => 'required',
            'opsi_b' => 'required',
            'opsi_c' => 'required',
            'opsi_d' => 'required',
            'opsi_e' => 'required',
            'jawaban_benar' => 'required',
            'gambar' => 'nullable|image|mimes:jpg,png,jpeg|max:2048'
        ]);

        $data = $request->all();

        // Handle ganti gambar
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($question->gambar) {
                Storage::disk('public')->delete($question->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('questions', 'public');
        }

        $question->update($data);

        return redirect()->back()->with('success', 'Butir soal berhasil diperbarui!');
    }

    // 3. Hapus Soal
    public function destroyQuestion($id, $question_id)
    {
        $question = Question::findOrFail($question_id);

        // Hapus file gambar dari storage jika ada
        if ($question->gambar) {
            Storage::disk('public')->delete($question->gambar);
        }

        $question->delete();

        return redirect()->back()->with('success', 'Butir soal berhasil dihapus!');
    }

    public function importQuestions(Request $request, $id)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls'
        ]);

        $exam =Exam::findOrFail($id);

        // Kirim subject_id dan exam_id
        Excel::import(new QuestionsImport($exam->subject_id, $exam->id), $request->file('file_excel'));
    

        return redirect()->back()->with('success', 'Soal berhasil diimport!');
    }
}
