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
use App\Imports\QuestionsImport;
use App\Exports\ExamResultsExport;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\TextRun;

class ExamController extends Controller
{
    public function index()
    {
        // Pastikan relasi 'guru' ada di model User
        $guruId = Auth::user()->guru->id;
        
        // Eager load only kelas and the count of questions to reduce PHP memory load
        $exams = Exam::where('guru_id', $guruId)
            ->with(['kelas'])
            ->withCount('questions')
            ->latest()
            ->get();

        $classes = Kelas::all();
        // Ambil mapel yang diampu guru ini (asumsi ada tabel subjects)
        $subjects = Subject::all(); 

        return view('guru.exams.index', compact('exams', 'classes', 'subjects'));
    }

    public function toggleStatus($id)
    {
        $guruId = Auth::user()->guru->id;
        
        // Pastikan hanya ujian milik guru ini yang bisa diubah statusnya
        $exam = Exam::where('guru_id', $guruId)->findOrFail($id);

        // Jika statusnya 'aktif', ubah jadi 'nonaktif', dan sebaliknya
        $exam->status = $exam->status == 'aktif' ? 'nonaktif' : 'aktif';
        $exam->save();

        return redirect()->back()->with('success', 'Status ujian berhasil diperbarui!');
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

    public function importQuestions(Request $request, $id)
    {
        // 1. Validasi file
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        // 2. Keamanan: Pastikan ujian ini milik guru yang sedang login
        $guruId = Auth::user()->guru->id;
        $exam = Exam::where('guru_id', $guruId)->findOrFail($id);

        try {
            // 3. Proses Import (menggunakan class QuestionsImport yang sama dengan Admin)
            Excel::import(new QuestionsImport($exam->subject_id, $exam->id), $request->file('file_excel'));
            
            return redirect()->back()->with('success', 'Data soal dari Excel berhasil diimport!');
        } catch (\Exception $e) {
            // Tangkap pesan error jika format Excel salah
            return redirect()->back()->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
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
        $request->validate([
            'title' => 'required|string|max:255',
            'subject_id' => 'required',
            'kelas_id' => 'required',
            'duration' => 'required|numeric',
        ]);

        $exam = Exam::where('guru_id', Auth::user()->guru->id)->findOrFail($id);
        
        $exam->update([
            'title' => $request->title,
            'subject_id' => $request->subject_id,
            'kelas_id' => $request->kelas_id,
            'duration' => $request->duration,
        ]);

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
        // Validasi disesuaikan untuk MTs (Opsi E boleh kosong) dan multi-tipe soal
        $request->validate([
            'jenis_soal'    => 'required|string', // Pastikan dikirim dari form frontend
            'question_text' => 'required',
            'opsi_a'        => 'nullable|string',
            'opsi_b'        => 'nullable|string',
            'opsi_c'        => 'nullable|string',
            'opsi_d'        => 'nullable|string',
            'opsi_e'        => 'nullable|string',
            'jawaban_benar' => 'nullable|string',
            'gambar'        => 'nullable|image|mimes:jpg,png,jpeg|max:2048'
        ]);

        $exam = Exam::findOrFail($id);

        // Simpan soal baru langsung nempel ke Exam ID ini
        $question = new Question();
        $question->exam_id = $exam->id;
        $question->subject_id = $exam->subject_id;
        $question->guru_id = Auth::user()->guru->id;
        
        $question->jenis_soal = $request->jenis_soal; // Simpan jenis soal
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
        $question = Question::findOrFail($question_id);
        
        // Validasi disesuaikan seperti saat simpan
        $request->validate([
            'jenis_soal'    => 'required|string',
            'question_text' => 'required',
            'opsi_a'        => 'nullable|string',
            'opsi_b'        => 'nullable|string',
            'opsi_c'        => 'nullable|string',
            'opsi_d'        => 'nullable|string',
            'opsi_e'        => 'nullable|string',
            'jawaban_benar' => 'nullable|string',
            'gambar'        => 'nullable|image|mimes:jpg,png,jpeg|max:2048'
        ]);

        // Update data teks dan opsi
        $question->update([
            'jenis_soal'    => $request->jenis_soal, // Update jenis soal
            'question_text' => $request->question_text,
            'opsi_a'        => $request->opsi_a,
            'opsi_b'        => $request->opsi_b,
            'opsi_c'        => $request->opsi_c,
            'opsi_d'        => $request->opsi_d,
            'opsi_e'        => $request->opsi_e,
            'jawaban_benar' => $request->jawaban_benar,
        ]);

        // Update gambar jika ada upload baru
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama (jika ada) dari storage
            if ($question->gambar) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($question->gambar);
            }
            $path = $request->file('gambar')->store('questions', 'public');
            $question->update(['gambar' => $path]);
        }

        return back()->with('success', 'Soal berhasil diperbarui!');
    }

    // Ubah nama fungsi menjadi removeQuestions agar sesuai dengan web.php
    public function removeQuestion($question_id)
    {
        $question = Question::findOrFail($question_id);
        
        // Bersihkan gambar dari server sebelum menghapus datanya
        if ($question->gambar) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($question->gambar);
        }
        
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

    public function analysis($id)
    {
        $exam = Exam::select('id', 'title', 'subject_id')->findOrFail($id);
        
        // Ambil ID sesi ujian yang sudah selesai (completed_at tidak null)
        $sessionIds = ExamSession::where('exam_id', $id)
                                ->whereNotNull('completed_at')
                                ->pluck('id')
                                ->toArray();
                                
        $total_peserta = count($sessionIds);

        // OPTIMASI: Selesaikan N+1 query loop. Tarik semua jawaban siswa dalam 1 single query
        // lalu kelompokkan berdasarkan question_id di memori PHP menggunakan Collection.
        $allAnswers = \App\Models\ExamAnswer::whereIn('exam_session_id', $sessionIds)
            ->select('id', 'exam_session_id', 'question_id', 'answer', 'is_correct')
            ->get()
            ->groupBy('question_id');

        // Ambil semua pertanyaan pada ujian ini, lalu hitung statistik jawabannya dari memori
        $questions = Question::where('exam_id', $id)
            ->select('id', 'jenis_soal', 'question_text', 'opsi_a', 'opsi_b', 'opsi_c', 'opsi_d', 'opsi_e', 'jawaban_benar')
            ->get()
            ->map(function ($q) use ($allAnswers) {
                // Ambil jawaban untuk soal ini dari data yang sudah di-group di memori
                $answers = $allAnswers->get($q->id, collect());

                $q->answers_count = $answers->count();
                $q->benar_count = $answers->where('is_correct', true)->count();
                
                // Case-insensitive check di PHP memory
                $q->jawab_a_count = $answers->filter(fn($ans) => strcasecmp($ans->answer, 'a') === 0)->count();
                $q->jawab_b_count = $answers->filter(fn($ans) => strcasecmp($ans->answer, 'b') === 0)->count();
                $q->jawab_c_count = $answers->filter(fn($ans) => strcasecmp($ans->answer, 'c') === 0)->count();
                $q->jawab_d_count = $answers->filter(fn($ans) => strcasecmp($ans->answer, 'd') === 0)->count();
                $q->jawab_e_count = $answers->filter(fn($ans) => strcasecmp($ans->answer, 'e') === 0)->count();

                return $q;
            });

        return view('guru.results.analysis', compact('exam', 'questions', 'total_peserta'));
    }

    public function importWord(Request $request, $id)
    {
        $request->validate([
            'file_word' => 'required|mimes:docx|max:5120'
        ]);

        $exam = Exam::findOrFail($id);
        $guruId = Auth::user()->guru->id;
        $file = $request->file('file_word');

        try {
            $phpWord = IOFactory::load($file->getPathname());
            $berhasil = 0;

            $dataSoal = null;
            $currentMode = null;
            $tagRegex = '/\[(JENIS|SOAL|OPSI_A|OPSI_B|OPSI_C|OPSI_D|OPSI_E|KUNCI)\]/i';

            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    $text = trim($this->getElementText($element));
                    if (empty($text)) continue;

                    if (preg_match($tagRegex, $text, $matches)) {
                        $tag = strtoupper($matches[1]);
                        // Buang tag-nya untuk mengambil isi teks di baris yang sama
                        $content = trim(str_ireplace('['.$tag.']', '', $text));

                        // Jika tag JENIS, berarti mulai soal baru
                        if ($tag === 'JENIS') {
                            if ($dataSoal !== null && !empty($dataSoal['soal'])) {
                                $this->simpanSoalKeDB($exam, $guruId, $dataSoal['jenis'], $dataSoal['soal'], $dataSoal['opsi'], $dataSoal['kunci'], $dataSoal['gambar']);
                                $berhasil++;
                            }
                            
                            $dataSoal = [
                                'jenis' => 'pilihan_ganda',
                                'soal' => '',
                                'opsi' => [null, null, null, null, null],
                                'kunci' => null,
                                'gambar' => null
                            ];
                            $currentMode = 'JENIS';
                        } else {
                            $currentMode = $tag;
                        }

                        // Simpan teks yang sebaris dengan tag
                        if (!empty($content) && $dataSoal !== null) {
                            $this->assignContentToMode($dataSoal, $currentMode, $content);
                        }
                    } else {
                        // Teks tanpa tag, berarti lanjutan dari mode sebelumnya (baris baru)
                        if ($dataSoal !== null && $currentMode !== null) {
                            $this->assignContentToMode($dataSoal, $currentMode, $text, true, $element);
                        }
                    }

                    // Ekstraksi Gambar
                    $imageElement = $this->extractImageFromElement($element);
                    if ($imageElement !== null && $dataSoal !== null) {
                        // Hanya ambil gambar pertama untuk soal ini
                        if (empty($dataSoal['gambar'])) {
                            try {
                                $imageData = $imageElement->getImageStringData(true);
                                $extension = $imageElement->getImageExtension();
                                $filename = 'exams/word_img_' . uniqid() . '.' . $extension;
                                \Illuminate\Support\Facades\Storage::disk('public')->put($filename, base64_decode($imageData));
                                $dataSoal['gambar'] = $filename;
                            } catch (\Exception $e) {
                                // Abaikan jika gagal memproses gambar
                            }
                        }
                    }
                }
            }

            // Simpan soal yang terakhir
            if ($dataSoal !== null && !empty($dataSoal['soal'])) {
                $this->simpanSoalKeDB($exam, $guruId, $dataSoal['jenis'], $dataSoal['soal'], $dataSoal['opsi'], $dataSoal['kunci'], $dataSoal['gambar']);
                $berhasil++;
            }

            return redirect()->back()->with('success', "$berhasil Soal berhasil diimport dari format paragraf!");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses file Word. Pastikan Anda menggunakan Template Baru. Error: ' . $e->getMessage());
        }
    }

    private function getElementText($element) {
        $text = '';
        if (method_exists($element, 'getElements')) {
            foreach ($element->getElements() as $child) {
                if (method_exists($child, 'getText')) {
                    $text .= $child->getText();
                }
            }
        } elseif (method_exists($element, 'getText')) {
            $text .= $element->getText();
        }
        return $text;
    }

    private function extractImageFromElement($element) {
        if ($element instanceof \PhpOffice\PhpWord\Element\Image) {
            return $element;
        }
        if (method_exists($element, 'getElements')) {
            foreach ($element->getElements() as $child) {
                $img = $this->extractImageFromElement($child);
                if ($img !== null) {
                    return $img;
                }
            }
        }
        return null;
    }

    private function assignContentToMode(&$dataSoal, $currentMode, $text, $append = false, $element = null) {
        $separator = $append ? '<br>' : ' ';
        if ($currentMode === 'JENIS') {
            $jenisRaw = strtolower($text);
            if (str_contains($jenisRaw, 'essay') || str_contains($jenisRaw, 'uraian')) {
                $dataSoal['jenis'] = 'essay';
            } elseif (str_contains($jenisRaw, 'benar')) {
                $dataSoal['jenis'] = 'benar_salah';
            } else {
                $dataSoal['jenis'] = 'pilihan_ganda';
            }
        } elseif ($currentMode === 'SOAL') {
            $isOptionList = ($element instanceof \PhpOffice\PhpWord\Element\ListItemRun);
            $isOptionManual = preg_match('/^([A-Ea-e])[\.\)]\s*(.*)/', $text, $m);

            if ($dataSoal['jenis'] !== 'essay') {
                if ($isOptionList) {
                    $found = false;
                    for ($i = 0; $i < 5; $i++) {
                        if (empty($dataSoal['opsi'][$i])) {
                            $dataSoal['opsi'][$i] = trim($text);
                            $found = true;
                            break;
                        }
                    }
                    // Jika semua opsi penuh (A-E terisi), berarti list ini bagian dari soal
                    if (!$found) $dataSoal['soal'] .= empty($dataSoal['soal']) ? $text : $separator . $text;
                } elseif ($isOptionManual) {
                    $letter = strtoupper($m[1]);
                    $idx = ord($letter) - 65; // A=0, B=1, dst.
                    $dataSoal['opsi'][$idx] = trim($m[2]);
                } else {
                    $dataSoal['soal'] .= empty($dataSoal['soal']) ? $text : $separator . $text;
                }
            } else {
                // Untuk Essay, list item otomatis menjadi format list HTML biasa di teks soal
                $prefix = $isOptionList ? '- ' : '';
                $dataSoal['soal'] .= empty($dataSoal['soal']) ? $prefix . $text : $separator . $prefix . $text;
            }
        } elseif ($currentMode === 'KUNCI') {
            if ($dataSoal['jenis'] !== 'essay') { // Kunci untuk essay akan ditiadakan (null)
                $kunciRaw = strtolower(substr(trim($text), 0, 1));
                if (in_array($kunciRaw, ['a', 'b', 'c', 'd', 'e'])) {
                    $dataSoal['kunci'] = $kunciRaw;
                }
            }
        }
    }

    // Fungsi Pembantu untuk merapikan penyimpanan ke DB
    private function simpanSoalKeDB($exam, $guruId, $jenisSoal, $pertanyaan, $opsi, $kunciJawaban = null, $gambar = null)
    {
        Question::create([
            'exam_id'       => $exam->id,
            'subject_id'    => $exam->subject_id,
            'guru_id'       => $guruId,
            'jenis_soal'    => $jenisSoal,
            'question_text' => $pertanyaan,
            'opsi_a'        => $opsi[0] ?? null,
            'opsi_b'        => $opsi[1] ?? null,
            'opsi_c'        => $opsi[2] ?? null,
            'opsi_d'        => $opsi[3] ?? null,
            'opsi_e'        => $opsi[4] ?? null,
            'jawaban_benar' => $kunciJawaban, // Untuk essay akan otomatis berisi null
            'gambar'        => $gambar
        ]);
    }
}