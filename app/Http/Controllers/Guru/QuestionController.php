<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller
{
    public function index(Request $request) // Tambahkan Request untuk filter
    {
        $subjects = Subject::all();
        $guru = Auth::user()->guru;

        // Cek jika profile guru tidak ada (biar gak error null)
        if (!$guru) {
            return redirect()->route('login')->with('error', 'Profil Guru tidak ditemukan.');
        }

        $questions = Question::where('guru_id', $guru->id)
            ->with('subject')
            // Logika Filter Pencarian
            ->when($request->search, function ($query) use ($request) {
                return $query->where('question_text', 'like', '%' . $request->search . '%');
            })
            // Logika Filter Mapel
            ->when($request->subject_id, function ($query) use ($request) {
                return $query->where('subject_id', $request->subject_id);
            })
            ->latest()
            ->paginate(10);

        return view('guru.questions.index', compact('questions', 'subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id'    => 'required|exists:subjects,id',
            'question_text' => 'required',
            'opsi_a'        => 'required',
            'opsi_b'        => 'required',
            'opsi_c'        => 'required',
            'opsi_d'        => 'required',
            'opsi_e'        => 'required',
            'jawaban_benar' => 'required|in:a,b,c,d,e',
        ]);

        $validated['guru_id'] = Auth::user()->guru->id;

        Question::create($validated);

        return back()->with('success', 'Soal berhasil disimpan!');
    }

    // FUNGSI UPDATE (Untuk Modal Edit)
    public function update(Request $request, $id)
    {
        $question = Question::where('guru_id', Auth::user()->guru->id)->findOrFail($id);

        $validated = $request->validate([
            'subject_id'    => 'required|exists:subjects,id',
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

    // FUNGSI HAPUS
    public function destroy($id)
    {
        // Pastikan guru hanya bisa menghapus soal MILIKNYA sendiri
        $question = Question::where('guru_id', Auth::user()->guru->id)->findOrFail($id);
        
        $question->delete();

        return back()->with('success', 'Soal berhasil dihapus!');
    }
}