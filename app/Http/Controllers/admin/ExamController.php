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
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\TextRun;

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
        // 1. Validasi input (Disesuaikan untuk MTs dan multi-tipe soal)
        $request->validate([
            'jenis_soal'    => 'required|string', // Pastikan input ini dikirim dari frontend
            'question_text' => 'required',
            'opsi_a'        => 'nullable|string',
            'opsi_b'        => 'nullable|string',
            'opsi_c'        => 'nullable|string',
            'opsi_d'        => 'nullable|string',
            'opsi_e'        => 'nullable|string',
            'jawaban_benar' => 'nullable|string',
            'gambar'        => 'nullable|image|mimes:jpg,png,jpeg|max:2048'
        ]);

        // 2. Ambil data Ujian untuk dapet subject_id dan guru_id
        $exam = Exam::findOrFail($id);

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

    // 3. Hapus Soal (Ubah nama jadi removeQuestions)
    public function removeQuestion($id, $question_id)
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

    public function importWord(Request $request, $id)
    {
        $request->validate([
            'file_word' => 'required|mimes:docx|max:5120'
        ]);

        $exam = Exam::findOrFail($id);
        $guruId = $exam->guru_id; 
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
