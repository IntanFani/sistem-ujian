<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Subject;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        // Ambil data untuk dropdown filter
        $subjects = Subject::all();
        $gurus = Guru::all();

        // Query dasar dengan Eager Loading
        $questions = Question::with(['subject', 'guru'])
            // Filter berdasarkan Mata Pelajaran
            ->when($request->subject_id, function ($query) use ($request) {
                return $query->where('subject_id', $request->subject_id);
            })
            // Filter berdasarkan Guru
            ->when($request->guru_id, function ($query) use ($request) {
                return $query->where('guru_id', $request->guru_id);
            })
            // Search berdasarkan teks pertanyaan
            ->when($request->search, function ($query) use ($request) {
                return $query->where('question_text', 'like', '%' . $request->search . '%');
            })
            ->latest()
            ->paginate(10); // Pakai pagination 

        return view('admin.questions.index', compact('questions', 'subjects', 'gurus'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'exam_id'       => 'required|exists:exams,id', // Wajib ada karena soal langsung terikat ke jadwal ujian
            'subject_id'    => 'required|exists:subjects,id',
            'guru_id'       => 'required|exists:gurus,id',
            'jenis_soal'    => 'required|string', // Penanda: pilihan_ganda, benar_salah, essay
            'question_text' => 'required',
            'gambar'        => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'opsi_a'        => 'nullable|string',
            'opsi_b'        => 'nullable|string',
            'opsi_c'        => 'nullable|string',
            'opsi_d'        => 'nullable|string',
            'opsi_e'        => 'nullable|string', // Dibiarkan nullable untuk naskah MTs
            'jawaban_benar' => 'nullable|string', // Nullable agar aman saat insert soal essay
        ]);

        // Logika unggah gambar jika ada
        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('public/soal');
            $validated['gambar'] = basename($path);
        }

        Question::create($validated);

        return back()->with('success', 'Soal berhasil ditambahkan ke dalam ujian.');
    }

    public function update(Request $request, $id)
    {
        $question = Question::findOrFail($id);

        $validated = $request->validate([
            'exam_id'       => 'required|exists:exams,id',
            'subject_id'    => 'required|exists:subjects,id',
            'guru_id'       => 'required|exists:gurus,id',
            'jenis_soal'    => 'required|string',
            'question_text' => 'required',
            'gambar'        => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'opsi_a'        => 'nullable|string',
            'opsi_b'        => 'nullable|string',
            'opsi_c'        => 'nullable|string',
            'opsi_d'        => 'nullable|string',
            'opsi_e'        => 'nullable|string',
            'jawaban_benar' => 'nullable|string',
        ]);

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($question->gambar) {
                Storage::delete('public/soal/' . $question->gambar);
            }
            $path = $request->file('gambar')->store('public/soal');
            $validated['gambar'] = basename($path);
        }

        $question->update($validated);

        return back()->with('success', 'Data soal berhasil diperbarui.');
    }

    public function removeQuestions($id)
    {
        $question = Question::findOrFail($id);

        // Hapus file gambar dari storage sebelum menghapus record database
        if ($question->gambar) {
            Storage::delete('public/soal/' . $question->gambar);
        }

        $question->delete();

        return back()->with('success', 'Soal telah dihapus dari sistem.');
    }
}
