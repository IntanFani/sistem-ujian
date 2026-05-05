@extends('layouts.admin')

@section('title', 'Kelola Soal - ' . $exam->title)

@section('content')
    <div class="container-fluid py-2">

        {{-- Header dengan Tombol Kembali --}}
        <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.exams.index') }}"
                    class="btn btn-white border shadow-sm rounded-circle d-flex align-items-center justify-content-center me-3 transition-3d text-secondary"
                    style="width: 45px; height: 45px;" title="Kembali">
                    <i class="bi bi-arrow-left fs-5"></i>
                </a>
                <div>
                    <h4 class="fw-bold mb-1 text-dark" style="letter-spacing: 0.5px;">KELOLA SOAL UJIAN (ADMIN)</h4>
                    <div class="d-flex align-items-center text-muted small mt-1">
                        <span
                            class="badge bg-primary bg-opacity-10 text-primary px-2 py-1 rounded-2 fw-medium me-2">{{ $exam->title }}</span>
                        <span><i class="bi bi-person me-1"></i> Guru: {{ $exam->guru->user->name ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <div class="d-none d-md-flex flex-column align-items-end me-3">
                    <span class="text-muted small fw-medium">Total Soal</span>
                    <span class="fw-bold text-dark fs-5">{{ $exam->questions->count() }}</span>
                </div>
                
                {{-- TOMBOL IMPORT EXCEL BARU --}}
                <button
                    class="btn btn-outline-success rounded-pill px-3 py-2 shadow-sm fw-bold transition-3d bg-white d-flex align-items-center"
                    data-bs-toggle="modal" data-bs-target="#modalImportSoal">
                    <i class="bi bi-file-earmark-excel-fill me-2"></i>Import Excel
                </button>

                {{-- TOMBOL IMPORT WORD BARU --}}
                <button
                    class="btn btn-outline-primary rounded-pill px-3 py-2 shadow-sm fw-bold transition-3d bg-white d-flex align-items-center"
                    data-bs-toggle="modal" data-bs-target="#modalImportWord">
                    <i class="bi bi-file-earmark-word-fill me-2"></i>Import Word
                </button>

                <button
                    class="btn btn-success rounded-pill px-4 py-2 shadow-sm fw-bold transition-3d border-0 d-flex align-items-center"
                    data-bs-toggle="modal" data-bs-target="#modalTambahSoal">
                    <i class="bi bi-plus-circle-fill me-2"></i>Tambah Soal
                </button>
            </div>
        </div>

        {{-- Daftar Soal --}}
        <div class="row">
            <div class="col-12">
                @forelse($exam->questions as $index => $q)
                    <div class="card border-0 shadow-sm rounded-4 question-card mb-4">
                        <div class="card-body p-4">

                            {{-- Header Soal --}}
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                <span class="badge rounded-pill bg-light text-dark border px-3 py-2 fw-bold"
                                    style="letter-spacing: 0.5px;">PERTANYAAN #{{ $index + 1 }}</span>

                                <div class="d-flex gap-2">
                                    <button type="button"
                                        class="btn btn-sm btn-outline-warning rounded-3 fw-medium d-flex align-items-center btn-edit-soal transition-3d shadow-sm"
                                        data-id="{{ $q->id }}" data-jenis="{{ $q->jenis_soal }}" data-text="{{ $q->question_text }}"
                                        data-a="{{ $q->opsi_a }}" data-b="{{ $q->opsi_b }}"
                                        data-c="{{ $q->opsi_c }}" data-d="{{ $q->opsi_d }}"
                                        data-e="{{ $q->opsi_e }}" data-kunci="{{ $q->jawaban_benar }}">
                                        <i class="bi bi-pencil-square me-1"></i> Edit
                                    </button>

                                    <button type="button"
                                        class="btn btn-sm btn-outline-danger rounded-3 fw-medium d-flex align-items-center transition-3d shadow-sm"
                                        onclick="confirmDeleteQuestion({{ $q->id }})">
                                        <i class="bi bi-trash3-fill me-1"></i> Hapus
                                    </button>
                                </div>
                            </div>

                            {{-- Teks Pertanyaan --}}
                            <div class="question-text text-dark mb-3" style="font-size: 1.05rem; line-height: 1.6;" dir="auto">
                                {!! $q->question_text !!}
                            </div>

                            {{-- Lampiran Gambar --}}
                            @if ($q->gambar)
                                <div class="mb-3">
                                    <img src="{{ asset('storage/' . $q->gambar) }}"
                                        class="rounded-3 img-fluid border shadow-sm"
                                        style="max-height: 200px; object-fit: contain;">
                                </div>
                            @endif

                            {{-- Opsi Jawaban (Hanya untuk PG dan Benar/Salah) --}}
                            @if($q->jenis_soal !== 'essay')
                            <div class="row g-2 mt-1">
                                @php
                                    $options = ['a', 'b', 'c', 'd', 'e'];
                                    if($q->jenis_soal == 'benar_salah') $options = ['a', 'b'];
                                @endphp
                                @foreach ($options as $opt)
                                    @php $isCorrect = ($q->jawaban_benar == $opt); @endphp
                                    <div class="col-md-6">
                                        <div
                                            class="option-item py-2 px-3 rounded-3 border d-flex align-items-center {{ $isCorrect ? 'option-correct shadow-sm' : 'bg-light border-light' }}">
                                            <div
                                                class="option-badge me-3 {{ $isCorrect ? 'bg-success text-white' : 'bg-white text-secondary shadow-sm' }}">
                                                {{ strtoupper($opt) }}
                                            </div>
                                            <div
                                                class="option-text small {{ $isCorrect ? 'fw-bold text-success' : 'text-secondary' }} flex-grow-1" dir="auto">
                                                {{ $q->{'opsi_' . $opt} }}
                                            </div>
                                            @if ($isCorrect)
                                                <i class="bi bi-check-circle-fill ms-2 text-success"></i>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @else
                            <div class="p-3 bg-light rounded-3 border mt-3">
                                <span class="badge bg-info text-white mb-2"><i class="bi bi-pencil-square me-1"></i> Soal Essay</span>
                                <p class="mb-0 small fw-bold text-muted">Pedoman Jawaban / Kunci:</p>
                                <p class="mb-0 mt-1 small text-dark">{{ $q->jawaban_benar ?? 'Tidak ada pedoman.' }}</p>
                            </div>
                            @endif

                        </div>
                    </div>
                @empty
                    {{-- State Kosong --}}
                    <div class="text-center py-5">
                        <div class="p-5 bg-white rounded-4 shadow-sm border border-light">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                style="width: 70px; height: 70px;">
                                <i class="bi bi-folder-x fs-2"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-2">Belum ada butir soal.</h5>
                            <p class="text-secondary small mb-2">Gunakan tombol menu di kanan atas untuk menambahkan soal ke dalam ujian ini.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>


    {{-- ========================================================== --}}
    {{-- MODAL IMPORT SOAL EXCEL --}}
    {{-- ========================================================== --}}
    <div class="modal fade" id="modalImportSoal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('admin.exams.questions.import', $exam->id) }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                @csrf
                <div class="modal-header bg-white border-bottom pt-4 px-4 pb-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 p-3 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="bi bi-file-earmark-spreadsheet fs-4 text-success"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark" style="letter-spacing: 0.5px;">IMPORT SOAL EXCEL</h5>
                            <p class="text-muted small mb-0">Upload file Excel sesuai format template.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4" style="background-color: #f8fafc;">
                    <div class="alert alert-info border-0 shadow-sm rounded-3 small mb-4 d-flex">
                        <i class="bi bi-info-circle-fill me-2 fs-5 mt-1"></i>
                        <div>
                            <strong>Panduan Import:</strong><br>
                            Pastikan format kolom Excel Anda terdiri dari: Teks Soal, Opsi A-E, dan Jawaban Benar (a/b/c/d/e).
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-2">Upload File Excel (.xlsx, .xls)</label>
                        <input type="file" name="file_excel" class="form-control bg-white border custom-file-input py-2 shadow-none" accept=".xlsx, .xls, .csv" required>
                    </div>
                </div>

                <div class="modal-footer bg-white border-top p-3 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-medium text-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-5 py-2 fw-bold shadow-sm border-0 transition-3d d-flex align-items-center">
                        <i class="bi bi-cloud-arrow-up-fill me-2"></i> Mulai Import
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ========================================================== --}}
    {{-- MODAL IMPORT WORD (KHUSUS NASKAH DOCX) --}}
    {{-- ========================================================== --}}
    <div class="modal fade" id="modalImportWord" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('admin.exams.questions.import-word', $exam->id) }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                @csrf
                <div class="modal-header bg-white border-bottom pt-4 px-4 pb-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="bi bi-file-earmark-word fs-4 text-primary"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark" style="letter-spacing: 0.5px;">IMPORT NASKAH WORD</h5>
                            <p class="text-muted small mb-0">Upload file naskah soal berformat .docx</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4" style="background-color: #f8fafc;">
                    <div class="alert alert-primary border-0 shadow-sm rounded-3 small mb-4 d-flex">
                        <i class="bi bi-info-circle-fill me-2 fs-5 mt-1"></i>
                        <div>
                            <strong>Peringatan Sistem:</strong><br>
                            Sistem hanya dapat membaca naskah Word yang menggunakan <b>Format Template Baku</b>. Anda wajib mengunduh dan menyalin soal Anda ke dalam template tersebut sebelum mengunggahnya.
                        </div>
                    </div>

                    <div class="mb-4 text-center">
                        <a href="{{ asset('templates/template_soal.docx') }}" class="btn btn-outline-primary rounded-pill px-4 fw-medium bg-white" download>
                            <i class="bi bi-download me-2"></i> Download Template Word
                        </a>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-2">Upload File Word (.docx)</label>
                        <input type="file" name="file_word" class="form-control bg-white border custom-file-input py-2 shadow-none" accept=".docx" required>
                    </div>
                </div>

                <div class="modal-footer bg-white border-top p-3 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-medium text-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow-sm border-0 transition-3d d-flex align-items-center">
                        <i class="bi bi-cloud-arrow-up-fill me-2"></i> Ekstrak & Import
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- ========================================================== --}}
    {{-- MODAL TAMBAH SOAL --}}
    {{-- ========================================================== --}}
    <div class="modal fade" id="modalTambahSoal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">

            <form action="{{ route('admin.exams.questions.store', $exam->id) }}" method="POST"
                enctype="multipart/form-data" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                @csrf

                <div class="modal-header bg-white border-bottom pt-4 px-4 px-md-5 pb-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3 d-flex align-items-center justify-content-center"
                            style="width: 45px; height: 45px;">
                            <i class="bi bi-patch-question-fill fs-4 text-primary"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark" style="letter-spacing: 0.5px;">BUAT BUTIR SOAL BARU</h5>
                            <p class="text-muted small mb-0">Input pertanyaan dan pilihan jawaban secara teliti.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body p-4 p-md-5" style="background-color: #f8fafc;">
                    <div class="row g-4">
                        <div class="col-lg-7">
                            <div class="card border-0 shadow-sm rounded-4">
                                <div class="card-body p-4">
                                    
                                    {{-- DROPDOWN JENIS SOAL --}}
                                    <div class="mb-4">
                                        <label class="form-label small fw-bold text-muted text-uppercase mb-2">
                                            <i class="bi bi-ui-checks-grid me-1"></i> Jenis Soal
                                        </label>
                                        <select name="jenis_soal" class="form-select bg-light border-0 rounded-3 py-2 shadow-none" required>
                                            <option value="pilihan_ganda" selected>Pilihan Ganda</option>
                                            <option value="benar_salah">Benar / Salah</option>
                                            <option value="essay">Essay / Uraian</option>
                                        </select>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label small fw-bold text-muted text-uppercase mb-2">
                                            <i class="bi bi-chat-left-text me-1"></i> Teks Pertanyaan
                                        </label>
                                        <textarea name="question_text" class="form-control bg-light border-0 rounded-4 p-3 shadow-none custom-textarea"
                                            rows="8" placeholder="Tuliskan butir soal, pernyataan, atau instruksi essay di sini..." required></textarea>
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label small fw-bold text-muted text-uppercase mb-2">
                                            <i class="bi bi-image me-1"></i> Lampiran Gambar (Opsional)
                                        </label>
                                        <input type="file" name="gambar"
                                            class="form-control bg-light border-0 shadow-none custom-file-input rounded-3 py-2">
                                        <small class="text-muted mt-2 d-block"><i class="bi bi-info-circle me-1"></i>
                                            Format: JPG/PNG. Maks: 2MB.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-5">
                            <div class="card border-0 shadow-sm rounded-4">
                                <div class="card-body p-4">
                                    
                                    {{-- BAGIAN PILIHAN JAWABAN (PG & Benar/Salah) --}}
                                    <div class="section_pilihan_jawaban">
                                        <label class="form-label small fw-bold text-muted text-uppercase mb-3">
                                            <i class="bi bi-ui-radios me-1"></i> Pilihan Jawaban
                                        </label>

                                        @foreach (['a', 'b', 'c', 'd', 'e'] as $opt)
                                            <div class="mb-3 option-group-{{ $opt }}">
                                                <div class="input-group custom-input-group edit-focus border rounded-3 overflow-hidden shadow-none">
                                                    <div class="input-group-text bg-white border-0 px-3">
                                                        <input class="form-check-input mt-0 custom-radio cursor-pointer"
                                                            type="radio" name="jawaban_benar" value="{{ $opt }}"
                                                            title="Jadikan Kunci Jawaban">
                                                    </div>
                                                    <span class="input-group-text bg-light border-0 fw-bold text-primary pe-1">{{ strtoupper($opt) }}.</span>
                                                    <input type="text" name="opsi_{{ $opt }}"
                                                        class="form-control bg-light border-0 shadow-none py-2"
                                                        placeholder="Teks opsi {{ $opt }}...">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    {{-- BAGIAN KUNCI JAWABAN ESSAY (BARU) --}}
                                    <div class="section_kunci_essay" style="display: none;">
                                        <label class="form-label small fw-bold text-muted text-uppercase mb-3">
                                            <i class="bi bi-key-fill me-1"></i> Pedoman Jawaban Essay
                                        </label>
                                        <textarea class="form-control bg-light border-0 rounded-4 p-3 shadow-none input_kunci_essay" 
                                            rows="12" placeholder="Tuliskan kunci jawaban atau pedoman penilaian untuk essay ini..."></textarea>
                                        <small class="text-muted d-block mt-2">Siswa akan menjawab soal ini melalui kolom teks panjang saat ujian.</small>
                                    </div>

                                    <div class="p-3 rounded-3 mt-4 info-box"
                                        style="background-color: #fffbeb; border: 1px solid #fef08a;">
                                        <p class="mb-0 small text-warning-emphasis"><i
                                                class="bi bi-info-circle-fill me-1 text-warning"></i>
                                            <span class="info_text">Pilih satu kunci jawaban yang benar.</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-white border-top p-4 px-md-5">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-medium text-secondary"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit"
                        class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow-sm border-0 transition-3d d-flex align-items-center">
                        <i class="bi bi-floppy me-2"></i> Simpan Soal
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ========================================================== --}}
    {{-- MODAL EDIT SOAL --}}
    {{-- ========================================================== --}}
    <div class="modal fade" id="modalEditSoal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">

            <form id="formEditSoal" action="" method="POST" enctype="multipart/form-data"
                class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                @csrf
                @method('PUT')

                <div class="modal-header bg-white border-bottom pt-4 px-4 px-md-5 pb-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 p-3 rounded-circle me-3 d-flex align-items-center justify-content-center"
                            style="width: 45px; height: 45px;">
                            <i class="bi bi-pencil-square fs-4 text-warning"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark" style="letter-spacing: 0.5px;">EDIT BUTIR SOAL</h5>
                            <p class="text-muted small mb-0">Perbarui teks pertanyaan atau opsi jawaban.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body p-4 p-md-5" style="background-color: #f8fafc;">
                    <div class="row g-4">
                        <div class="col-lg-7">
                            <div class="card border-0 shadow-sm rounded-4">
                                <div class="card-body p-4">

                                    {{-- DROPDOWN JENIS SOAL --}}
                                    <div class="mb-4">
                                        <label class="form-label small fw-bold text-muted text-uppercase mb-2">
                                            <i class="bi bi-ui-checks-grid me-1"></i> Jenis Soal
                                        </label>
                                        <select name="jenis_soal" id="edit_jenis_soal" class="form-select bg-light border-0 rounded-3 py-2 shadow-none" required>
                                            <option value="pilihan_ganda">Pilihan Ganda</option>
                                            <option value="benar_salah">Benar / Salah</option>
                                            <option value="essay">Essay / Uraian</option>
                                        </select>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label small fw-bold text-muted text-uppercase mb-2">
                                            <i class="bi bi-chat-left-text me-1"></i> Teks Pertanyaan
                                        </label>
                                        <textarea name="question_text" id="edit_question_text"
                                            class="form-control bg-light border-0 rounded-4 p-3 shadow-none custom-textarea edit-mode-focus" rows="8"
                                            required></textarea>
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label small fw-bold text-muted text-uppercase mb-2">
                                            <i class="bi bi-image me-1"></i> Ganti Gambar (Opsional)
                                        </label>
                                        <input type="file" name="gambar"
                                            class="form-control bg-light border-0 shadow-none custom-file-input rounded-3 py-2">
                                        <small class="text-muted mt-2 d-block"><i class="bi bi-info-circle me-1"></i>
                                            Kosongkan jika tidak ingin mengganti gambar lama.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-5">
                            <div class="card border-0 shadow-sm rounded-4">
                                <div class="card-body p-4">
                                    
                                    {{-- BAGIAN PILIHAN JAWABAN (PG & Benar/Salah) --}}
                                    <div class="section_pilihan_jawaban">
                                        <label class="form-label small fw-bold text-muted text-uppercase mb-3">
                                            <i class="bi bi-ui-radios me-1"></i> Pilihan Jawaban
                                        </label>

                                        @foreach (['a', 'b', 'c', 'd', 'e'] as $opt)
                                            <div class="mb-3 option-group-{{ $opt }}">
                                                <div class="input-group custom-input-group edit-focus-warning border rounded-3 overflow-hidden shadow-none">
                                                    <div class="input-group-text bg-white border-0 px-3">
                                                        <input
                                                            class="form-check-input mt-0 custom-radio cursor-pointer edit-radio-warning"
                                                            type="radio" name="jawaban_benar"
                                                            id="edit_kunci_{{ $opt }}" value="{{ $opt }}">
                                                    </div>
                                                    <span
                                                        class="input-group-text bg-light border-0 fw-bold text-dark pe-1">{{ strtoupper($opt) }}.</span>
                                                    <input type="text" name="opsi_{{ $opt }}"
                                                        id="edit_opsi_{{ $opt }}"
                                                        class="form-control bg-light border-0 shadow-none py-2">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    {{-- BAGIAN KUNCI JAWABAN ESSAY (BARU) --}}
                                    <div class="section_kunci_essay" style="display: none;">
                                        <label class="form-label small fw-bold text-muted text-uppercase mb-3">
                                            <i class="bi bi-key-fill me-1"></i> Pedoman Jawaban Essay
                                        </label>
                                        <textarea class="form-control bg-light border-0 rounded-4 p-3 shadow-none input_kunci_essay" 
                                            rows="12" placeholder="Tuliskan kunci jawaban atau pedoman penilaian..."></textarea>
                                    </div>

                                    <div class="p-3 rounded-3 mt-4 info-box"
                                        style="background-color: #fffbeb; border: 1px solid #fef08a;">
                                        <p class="mb-0 small text-warning-emphasis"><i
                                                class="bi bi-info-circle-fill me-1 text-warning"></i>
                                            <span class="info_text">Pastikan semua data terisi dengan benar.</span>
                                        </p>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-white border-top p-4 px-md-5">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-medium text-secondary"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit"
                        class="btn btn-warning rounded-pill px-5 py-2 fw-bold shadow-sm border-0 transition-3d text-white d-flex align-items-center">
                        <i class="bi bi-floppy me-2"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- STYLES & SCRIPTS TETAP SAMA --}}
    <style>
        .bg-light { background-color: #f8fafc !important; }
        .btn-white { background: #fff; transition: all 0.2s; }
        .btn-white:hover { background: #f1f5f9; }
        .custom-input-group { border-radius: 12px; overflow: hidden; border: 2px solid #f1f5f9; background-color: #f8fafc; transition: all 0.3s ease; }
        .custom-input-group.edit-focus:focus-within { border-color: #cbd5e1; box-shadow: 0 0 0 4px rgba(203, 213, 225, 0.2); }
        .custom-input-group.edit-focus-warning:focus-within { border-color: #ffc107; box-shadow: 0 0 0 4px rgba(255, 193, 7, 0.15); }
        .custom-input-group input { color: #334155; }
        .form-control:focus { background-color: #f8fafc !important; box-shadow: none !important; }
        .custom-textarea { border: 2px solid #f1f5f9 !important; transition: all 0.3s ease; }
        .custom-textarea:focus { border-color: #cbd5e1 !important; box-shadow: 0 0 0 4px rgba(203, 213, 225, 0.2) !important; background-color: #fff !important; }
        .edit-mode-focus:focus { border-color: #ffc107 !important; box-shadow: 0 0 0 4px rgba(255, 193, 7, 0.15) !important; }
        .custom-file-input { border: 2px dashed #cbd5e1 !important; cursor: pointer; }
        .custom-file-input:focus { border-color: #0d6efd !important; }
        .question-card { transition: all 0.3s ease; border: 1px solid #f1f5f9 !important; }
        .question-card:hover { transform: translateY(-3px); box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05) !important; border-color: #e2e8f0 !important; }
        .option-item { transition: all 0.2s ease; min-height: 60px; }
        .option-badge { width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border-radius: 10px; font-weight: 700; font-size: 0.9rem; }
        .option-correct { background-color: #ecfdf5; border-color: #10b981 !important; }
        .transition-3d { transition: all 0.2s ease; }
        .transition-3d:hover { transform: translateY(-2px); }
        .cursor-pointer { cursor: pointer; }
        .custom-radio { width: 1.25em; height: 1.25em; }
        .edit-radio-warning:checked { background-color: #ffc107; border-color: #ffc107; }
    </style>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // 1. FUNGSI UTAMA UNTUK TOGGLE TAMPILAN BERDASARKAN JENIS SOAL
            function toggleJenisSoal(modalObj) {
                const jenis = modalObj.find('select[name="jenis_soal"]').val();
                const sectionPG = modalObj.find('.section_pilihan_jawaban');
                const sectionEssay = modalObj.find('.section_kunci_essay');
                const infoText = modalObj.find('.info_text');
                const inputEssay = modalObj.find('.input_kunci_essay');
                const radioKunci = modalObj.find('input[type="radio"]');

                if (jenis === 'essay') {
                    // Tampilkan area essay, sembunyikan PG
                    sectionPG.hide();
                    sectionEssay.show();
                    
                    // TUKAR ATRIBUT NAME: Agar textarea yang mengirim data ke 'jawaban_benar'
                    inputEssay.attr('name', 'jawaban_benar');
                    radioKunci.attr('name', 'jawaban_benar_unused'); 
                    
                    infoText.text("Mode Essay: Masukkan pedoman jawaban pada kotak di atas.");
                } else {
                    // Tampilkan PG, sembunyikan essay
                    sectionPG.show();
                    sectionEssay.hide();
                    
                    // TUKAR BALIK: Agar radio button yang mengirim data ke 'jawaban_benar'
                    inputEssay.attr('name', 'jawaban_benar_unused');
                    radioKunci.attr('name', 'jawaban_benar');
                    
                    if(jenis === 'benar_salah') {
                        infoText.text("Mode Benar/Salah: Gunakan Opsi A (Benar) dan B (Salah).");
                        modalObj.find('.option-group-c, .option-group-d, .option-group-e').hide();
                    } else {
                        infoText.text("Mode PG: Isi semua opsi dan pilih satu kunci jawaban.");
                        modalObj.find('.option-group-c, .option-group-d, .option-group-e').show();
                    }
                }
            }

            // 2. EVENT LISTENER: Saat dropdown jenis soal diubah manual
            $('select[name="jenis_soal"]').on('change', function() {
                toggleJenisSoal($(this).closest('.modal'));
            });

            // 3. EVENT LISTENER: Saat tombol EDIT diklik
            $('.btn-edit-soal').on('click', function() {
                const id = $(this).data('id');
                const jenis = $(this).data('jenis');
                const text = $(this).data('text');
                const kunci = $(this).data('kunci');
                const modal = $('#modalEditSoal');
                
                // Isi data dasar
                modal.find('#edit_jenis_soal').val(jenis);
                modal.find('#edit_question_text').val(text);
                modal.find('#edit_opsi_a').val($(this).data('a'));
                modal.find('#edit_opsi_b').val($(this).data('b'));
                modal.find('#edit_opsi_c').val($(this).data('c'));
                modal.find('#edit_opsi_d').val($(this).data('d'));
                modal.find('#edit_opsi_e').val($(this).data('e'));

                // Reset semua pilihan radio
                modal.find('input[type="radio"]').prop('checked', false);

                // Jalankan fungsi toggle agar tampilan sesuai jenis soal
                toggleJenisSoal(modal);

                // Jika essay, isi textarea kunci. Jika PG, centang radio.
                if (jenis === 'essay') {
                    modal.find('.input_kunci_essay').val(kunci);
                } else if (kunci) {
                    modal.find(`#edit_kunci_${kunci}`).prop('checked', true);
                }

                // Update Action Form (Admin Route)
                let updateUrl = "{{ route('admin.exams.questions.update', ['id' => $exam->id, 'question_id' => ':qid']) }}";
                modal.find('#formEditSoal').attr('action', updateUrl.replace(':qid', id));
                
                modal.modal('show');
            });
        });

        // 4. FUNGSI DELETE (Admin Route)
        window.confirmDeleteQuestion = function(questionId) {
            let deleteUrl = "{{ route('admin.exams.questions.remove', ['id' => $exam->id, 'question_id' => ':id']) }}";
            deleteUrl = deleteUrl.replace(':id', questionId);

            Swal.fire({
                title: 'Hapus Soal?',
                text: "Data akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: { popup: 'rounded-4' }
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = document.createElement('form');
                    form.action = deleteUrl;
                    form.method = 'POST';
                    form.innerHTML = `
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="DELETE">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        };
    </script>
@endsection
