@extends('layouts.admin')

@section('title', 'Kelola Soal - ' . $exam->title)

@section('content')
    <div class="container-fluid py-2">

        {{-- Header --}}
        <div
            class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 pb-3 border-bottom gap-3">
            {{-- Sisi Kiri: Info Ujian --}}
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

            {{-- Sisi Kanan: Statistik & Aksi --}}
            <div class="d-flex align-items-center gap-2">
                {{-- Statistik Total Soal (Desktop Only) --}}
                <div class="d-none d-md-flex flex-column align-items-end me-3">
                    <span class="text-muted small fw-medium">Total Soal</span>
                    <span class="fw-bold text-dark fs-5">{{ $exam->questions->count() }}</span>
                </div>

                {{-- Tombol Import Excel --}}
                <button
                    class="btn btn-outline-success rounded-pill px-4 py-2 shadow-sm fw-bold transition-3d d-flex align-items-center"
                    data-bs-toggle="modal" data-bs-target="#modalImportExcel">
                    <i class="bi bi-file-earmark-excel-fill me-2"></i> Import
                </button>

                {{-- Tombol Tambah Manual --}}
                <button
                    class="btn btn-success rounded-pill px-4 py-2 shadow-sm fw-bold transition-3d border-0 d-flex align-items-center"
                    data-bs-toggle="modal" data-bs-target="#modalTambahSoal">
                    <i class="bi bi-plus-circle-fill me-2"></i> Tambah Soal
                </button>
            </div>
        </div>

        {{-- MODAL IMPORT EXCEL (Diletakkan terpisah agar rapi) --}}
        <div class="modal fade" id="modalImportExcel" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form action="{{ route('admin.exams.questions.import', $exam->id) }}" method="POST"
                    enctype="multipart/form-data" class="modal-content border-0 shadow rounded-4">
                    @csrf
                    <div class="modal-header border-bottom p-4">
                        <h5 class="fw-bold mb-0">IMPORT BANK SOAL</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted text-uppercase">File Excel (.xlsx)</label>
                            <input type="file" name="file_excel" class="form-control rounded-3" required>
                            <div class="form-text mt-2 text-muted small">Pastikan file menggunakan format .xlsx atau .xls
                            </div>
                        </div>

                        <div class="p-3 rounded-3 bg-light border">
                            <small class="text-muted d-block mb-2 font-italic"><i class="bi bi-info-circle me-1"></i> Format
                                Kolom:</small>
                            <code class="small fw-bold text-success">pertanyaan | opsi_a | opsi_b | opsi_c | opsi_d | opsi_e
                                | kunci</code>
                        </div>
                    </div>
                    <div class="modal-footer bg-light p-4 px-5">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-medium"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success rounded-pill px-5 fw-bold shadow-sm">Mulai
                            Import</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Daftar Soal --}}
        <div class="row">
            <div class="col-12">
                @forelse($exam->questions as $index => $q)
                    <div class="card border-0 shadow-sm rounded-4 question-card mb-4">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                <span class="badge rounded-pill bg-light text-dark border px-3 py-2 fw-bold">PERTANYAAN
                                    #{{ $index + 1 }}</span>

                                <div class="d-flex gap-2">
                                    <button type="button"
                                        class="btn btn-sm btn-outline-warning rounded-3 fw-medium btn-edit-soal transition-3d"
                                        data-id="{{ $q->id }}" data-text="{{ $q->question_text }}"
                                        data-a="{{ $q->opsi_a }}" data-b="{{ $q->opsi_b }}"
                                        data-c="{{ $q->opsi_c }}" data-d="{{ $q->opsi_d }}"
                                        data-e="{{ $q->opsi_e }}" data-kunci="{{ $q->jawaban_benar }}">
                                        <i class="bi bi-pencil-square me-1"></i> Edit
                                    </button>

                                    <button type="button"
                                        class="btn btn-sm btn-outline-danger rounded-3 fw-medium transition-3d"
                                        onclick="confirmDeleteQuestion({{ $q->id }})">
                                        <i class="bi bi-trash3-fill me-1"></i> Hapus
                                    </button>
                                </div>
                            </div>

                            <div class="question-text text-dark mb-3">
                                {!! $q->question_text !!}
                            </div>

                            @if ($q->gambar)
                                <div class="mb-3">
                                    <img src="{{ asset('storage/' . $q->gambar) }}" class="rounded-3 img-fluid border"
                                        style="max-height: 200px;">
                                </div>
                            @endif

                            <div class="row g-2 mt-1">
                                @foreach (['a', 'b', 'c', 'd', 'e'] as $opt)
                                    @php $isCorrect = ($q->jawaban_benar == $opt); @endphp
                                    <div class="col-md-6">
                                        <div
                                            class="option-item py-2 px-3 rounded-3 border d-flex align-items-center {{ $isCorrect ? 'option-correct shadow-sm' : 'bg-light border-light' }}">
                                            <div
                                                class="option-badge me-3 {{ $isCorrect ? 'bg-success text-white' : 'bg-white text-secondary shadow-sm' }}">
                                                {{ strtoupper($opt) }}
                                            </div>
                                            <div
                                                class="option-text small {{ $isCorrect ? 'fw-bold text-success' : 'text-secondary' }} flex-grow-1">
                                                {{ $q->{'opsi_' . $opt} }}
                                            </div>
                                            @if ($isCorrect)
                                                <i class="bi bi-check-circle-fill ms-2 text-success"></i>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 bg-white rounded-4 shadow-sm border">
                        <i class="bi bi-folder-x fs-1 text-muted"></i>
                        <h5 class="mt-3 text-secondary">Belum ada butir soal.</h5>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- MODAL TAMBAH SOAL --}}
    <div class="modal fade" id="modalTambahSoal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <form action="{{ route('admin.exams.questions.store', $exam->id) }}" method="POST"
                enctype="multipart/form-data" class="modal-content border-0 shadow rounded-4">
                @csrf
                <div class="modal-header border-bottom p-4">
                    <h5 class="fw-bold mb-0">TAMBAH BUTIR SOAL</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <div class="row g-4">
                        <div class="col-lg-7">
                            <label class="form-label fw-bold">Pertanyaan</label>
                            <textarea name="question_text" class="form-control rounded-3" rows="10" required></textarea>
                            <label class="form-label fw-bold mt-3">Gambar (Opsional)</label>
                            <input type="file" name="gambar" class="form-control">
                        </div>
                        <div class="col-lg-5">
                            <label class="form-label fw-bold">Opsi Jawaban & Kunci</label>
                            @foreach (['a', 'b', 'c', 'd', 'e'] as $opt)
                                <div class="input-group mb-2">
                                    <div class="input-group-text bg-white">
                                        <input type="radio" name="jawaban_benar" value="{{ $opt }}" required>
                                    </div>
                                    <span class="input-group-text">{{ strtoupper($opt) }}</span>
                                    <input type="text" name="opsi_{{ $opt }}" class="form-control"
                                        placeholder="Teks opsi..." required>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5">Simpan Soal</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL EDIT SOAL --}}
    <div class="modal fade" id="modalEditSoal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <form id="formEditSoal" action="" method="POST" enctype="multipart/form-data"
                class="modal-content border-0 shadow rounded-4">
                @csrf @method('PUT')
                <div class="modal-header border-bottom p-4">
                    <h5 class="fw-bold mb-0">EDIT BUTIR SOAL</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <div class="row g-4">
                        <div class="col-lg-7">
                            <label class="form-label fw-bold">Pertanyaan</label>
                            <textarea name="question_text" id="edit_question_text" class="form-control rounded-3" rows="10" required></textarea>
                            <label class="form-label fw-bold mt-3">Ganti Gambar</label>
                            <input type="file" name="gambar" class="form-control">
                        </div>
                        <div class="col-lg-5">
                            <label class="form-label fw-bold">Opsi Jawaban & Kunci</label>
                            @foreach (['a', 'b', 'c', 'd', 'e'] as $opt)
                                <div class="input-group mb-2">
                                    <div class="input-group-text bg-white">
                                        <input type="radio" name="jawaban_benar" id="edit_kunci_{{ $opt }}"
                                            value="{{ $opt }}" required>
                                    </div>
                                    <span class="input-group-text">{{ strtoupper($opt) }}</span>
                                    <input type="text" name="opsi_{{ $opt }}"
                                        id="edit_opsi_{{ $opt }}" class="form-control" required>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-5 text-white">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .option-correct {
            background-color: #ecfdf5;
            border-color: #10b981 !important;
        }

        .option-badge {
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-weight: bold;
        }

        .transition-3d:hover {
            transform: translateY(-2px);
            transition: 0.2s;
        }
    </style>
@endsection

@section('scripts')
    <script>
        window.confirmDeleteQuestion = function(questionId) {
            let deleteUrl =
                "{{ route('admin.exams.questions.destroy', ['id' => $exam->id, 'question_id' => ':id']) }}";
            deleteUrl = deleteUrl.replace(':id', questionId);

            Swal.fire({
                title: 'Hapus Soal?',
                text: "Data akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = document.createElement('form');
                    form.action = deleteUrl;
                    form.method = 'POST';
                    form.innerHTML = `@csrf @method('DELETE')`;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        };

        $(document).ready(function() {
            $('.btn-edit-soal').on('click', function() {
                const id = $(this).data('id');
                $('#edit_question_text').val($(this).data('text'));
                $('#edit_opsi_a').val($(this).data('a'));
                $('#edit_opsi_b').val($(this).data('b'));
                $('#edit_opsi_c').val($(this).data('c'));
                $('#edit_opsi_d').val($(this).data('d'));
                $('#edit_opsi_e').val($(this).data('e'));
                $(`#edit_kunci_${$(this).data('kunci')}`).prop('checked', true);

                let updateUrl =
                    "{{ route('admin.exams.questions.update', ['id' => $exam->id, 'question_id' => ':qid']) }}";
                $('#formEditSoal').attr('action', updateUrl.replace(':qid', id));
                $('#modalEditSoal').modal('show');
            });
        });
    </script>
@endsection
