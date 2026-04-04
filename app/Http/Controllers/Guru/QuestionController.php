<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Subject;
use App\Models\Exam; // Tambahkan Model Exam
use App\Imports\QuestionsImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller
{
    // Fungsi untuk menampilkan soal-soal yang sudah ditambahkan ke ujian tertentu
    public function index(Request $request, $exam_id) 
    {
        // Ambil data ujian yang sedang dikelola
        $exam = Exam::with('subject', 'kelas')->findOrFail($exam_id);
        
        // Ambil soal yang hanya milik ujian ini saja
        $questions = Question::where('exam_id', $exam_id)
            ->when($request->search, function ($query) use ($request) {
                return $query->where('question_text', 'like', '%' . $request->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('guru.questions.index', compact('questions', 'exam'));
    }

    // Fungsi menambahkan soal ke ujian tertentu
    public function store(Request $request, $exam_id)
    {
        $exam = Exam::findOrFail($exam_id);

        $validated = $request->validate([
            'question_text' => 'required',
            'opsi_a'        => 'required',
            'opsi_b'        => 'required',
            'opsi_c'        => 'required',
            'opsi_d'        => 'required',
            'opsi_e'        => 'required',
            'jawaban_benar' => 'required|in:a,b,c,d,e',
        ]);

        // Otomatis isi data pendukung dari data ujian dan auth
        $validated['exam_id']    = $exam->id;
        $validated['subject_id'] = $exam->subject_id;
        $validated['guru_id']    = Auth::user()->id; // Sesuaikan jika relasi ke guru_id pake user_id

        Question::create($validated);

        return back()->with('success', 'Soal berhasil ditambahkan ke ujian!');
    }

    // Fungsi update soal
    public function update(Request $request, $id)
    {
        $question = Question::findOrFail($id);

        $validated = $request->validate([
            'question_text' => 'required',
            'opsi_a'        => 'required',
            'opsi_b'        => 'required',
            'opsi_c'        => 'required',
            'opsi_d'        => 'required',
            'opsi_e'        => 'required',
            'jawaban_benar' => 'required|in:a,b,c,d,e',
        ]);

        $question->update($validated);

        return back()->with('success', 'Soal berhasil diperbarui!');
    }

    // Fungsi hapus soal
    public function destroy($id)
    {
        $question = Question::findOrFail($id);
        $question->delete();

        return back()->with('success', 'Soal berhasil dihapus!');
    }

    // Update Import jika nanti kamu butuh (Optional)
    public function import(Request $request, $exam_id) 
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls'
        ]);

        $exam = Exam::findOrFail($exam_id);
        
        // Kirim exam_id ke class Import
        Excel::import(new QuestionsImport($exam->id, $exam->subject_id), $request->file('file_excel'));
        
        return back()->with('success', 'Data soal berhasil diimport ke ujian ini!');
    }
}