@extends('layouts.admin')

@section('title', 'Kelola Soal - ' . $exam->title)

@section('content')
    <div class="container-fluid py-1">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <div class="mb-1">
                    <a href="{{ route('guru.exams.index') }}"
                        class="btn btn-light btn-sm rounded-pill px-3 shadow-sm border text-secondary fw-medium">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                </div>
                <h3 class="fw-bold text-dark mb-1 my-4">Kelola Soal</h3>
                <p class="text-muted mb-0">
                    <span class="badge bg-soft-primary text-primary px-3 rounded-pill">{{ $exam->title }}</span>
                    <span class="mx-1 text-secondary">|</span>
                    <span class="small fw-medium"><i class="bi bi-door-open me-1"></i> Kelas
                        {{ $exam->kelas->nama_kelas ?? '-' }}</span>
                </p>
            </div>
            <button class="btn btn-primary rounded-3 px-4 py-2 shadow-sm fw-bold transition-3d border-0"
                data-bs-toggle="modal" data-bs-target="#modalTambahSoal">
                <i class="bi bi-plus-lg me-2"></i>Tambah Soal
            </button>
        </div>

        <div class="row mb-4">
            <div class="col-auto">
                <div class="small text-muted">Total Soal: <span
                        class="fw-bold text-dark">{{ $exam->questions->count() }}</span></div>
            </div>
        </div>

        <div class="row">
            @forelse($exam->questions as $index => $q)
                <div class="col-12 mb-4">
                    <div class="card border-0 shadow-sm rounded-4 question-card h-100">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge rounded-pill bg-primary px-3 py-2">Pertanyaan #{{ $index + 1 }}</span>
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm rounded-circle shadow-none border-0" type="button"
                                        data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm rounded-3">
                                        <li>
                                            <a class="dropdown-item py-2 btn-edit-soal" href="javascript:void(0)"
                                                data-id="{{ $q->id }}" data-text="{{ $q->question_text }}"
                                                data-a="{{ $q->opsi_a }}" data-b="{{ $q->opsi_b }}"
                                                data-c="{{ $q->opsi_c }}" data-d="{{ $q->opsi_d }}"
                                                data-e="{{ $q->opsi_e }}" data-kunci="{{ $q->jawaban_benar }}">
                                                <i class="bi bi-pencil me-2 text-warning"></i> Edit Soal
                                            </a>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <button class="dropdown-item py-2 text-danger"
                                                onclick="confirmDeleteQuestion({{ $q->id }})">
                                                <i class="bi bi-trash3 me-2"></i> Hapus Soal
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="question-text text-dark fs-5 mb-4 px-1" style="line-height: 1.6;">
                                {!! $q->question_text !!}
                            </div>

                            @if ($q->gambar)
                                <div class="mb-4">
                                    <img src="{{ asset('storage/' . $q->gambar) }}"
                                        class="rounded-4 img-fluid border shadow-sm" style="max-height: 250px;">
                                </div>
                            @endif

                            <div class="row g-3">
                                @foreach (['a', 'b', 'c', 'd', 'e'] as $opt)
                                    @php $isCorrect = ($q->jawaban_benar == $opt); @endphp
                                    <div class="col-md-6">
                                        <div
                                            class="option-item p-3 rounded-3 border d-flex align-items-center {{ $isCorrect ? 'option-correct' : 'bg-light border-light' }}">
                                            <div
                                                class="option-badge me-3 {{ $isCorrect ? 'bg-success text-white' : 'bg-white text-dark shadow-sm' }}">
                                                {{ strtoupper($opt) }}
                                            </div>
                                            <div
                                                class="option-text {{ $isCorrect ? 'fw-bold text-success' : 'text-secondary' }}">
                                                {{ $q->{'opsi_' . $opt} }}
                                            </div>
                                            @if ($isCorrect)
                                                <i class="bi bi-check-circle-fill ms-auto text-success fs-5"></i>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="p-5">
                        <img src="https://illustrations.popsy.co/gray/opening-tabs.svg" style="width: 200px;"
                            class="mb-4 opacity-50">
                        <h5 class="text-muted">Belum ada soal untuk ujian ini.</h5>
                        <p class="text-secondary small">Klik tombol "Tambah Soal" untuk mulai menginput.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <div class="modal fade" id="modalTambahSoal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-white border-0 pt-4 px-4 pb-0">
                    <div class="d-flex align-items-center">
                        <div class="bg-soft-primary p-3 rounded-4 me-3" style="background: #eef2ff;">
                            <i class="bi bi-patch-question fs-4 text-primary"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold">Buat Butir Soal Baru</h5>
                            <p class="text-muted small mb-0">Input pertanyaan dan pilihan jawaban secara teliti.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <form action="{{ route('guru.exams.questions.store', $exam->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4 p-md-5">
                        <div class="row g-4">
                            <div class="col-lg-7">
                                <div class="mb-4">
                                    <label
                                        class="form-label small fw-bold text-muted text-uppercase mb-2">Pertanyaan</label>
                                    <textarea name="question_text" class="form-control bg-light border-0 rounded-4 p-3 shadow-none focus-primary"
                                        rows="10" placeholder="Tuliskan butir soal di sini..." required></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted text-uppercase mb-2">Lampiran Gambar
                                        (Opsional)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i
                                                class="bi bi-image text-muted"></i></span>
                                        <input type="file" name="gambar"
                                            class="form-control bg-light border-0 shadow-none focus-primary">
                                    </div>
                                    <small class="text-muted mt-2 d-block">*Format: jpg, png, jpeg. Maks 2MB.</small>
                                </div>
                            </div>

                            <div class="col-lg-5">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-3">Pilihan Jawaban
                                    (Pilih Kunci)</label>

                                @foreach (['a', 'b', 'c', 'd', 'e'] as $opt)
                                    <div class="mb-3">
                                        <div class="input-group custom-option-group">
                                            <div class="input-group-text bg-white border-end-0 border-primary-subtle">
                                                <input class="form-check-input mt-0 custom-radio" type="radio"
                                                    name="jawaban_benar" value="{{ $opt }}" required>
                                            </div>
                                            <span
                                                class="input-group-text bg-light border-start-0 border-end-0 fw-bold text-primary">{{ strtoupper($opt) }}</span>
                                            <input type="text" name="opsi_{{ $opt }}"
                                                class="form-control bg-white border-start-0 shadow-none"
                                                placeholder="Isi pilihan {{ $opt }}..." required>
                                        </div>
                                    </div>
                                @endforeach

                                <div class="p-3 rounded-4 bg-light mt-4">
                                    <p class="mb-0 small text-muted"><i class="bi bi-info-circle me-1"></i> Pilih salah
                                        satu Radio Button sebagai kunci jawaban.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 p-4 pt-0 gap-2">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-medium"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit"
                            class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm border-0 transition-3d">
                            <i class="bi bi-save2-fill me-2"></i> Simpan Soal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditSoal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-white border-0 pt-4 px-4 pb-0">
                    <h5 class="modal-title fw-bold">Edit Butir Soal</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>

                <form id="formEditSoal" action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4 p-md-5">
                        <div class="row g-4">
                            <div class="col-lg-7">
                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Pertanyaan</label>
                                    <textarea name="question_text" id="edit_question_text"
                                        class="form-control bg-light border-0 rounded-4 p-3 shadow-none focus-primary" rows="10" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Ganti Gambar
                                        (Opsional)</label>
                                    <input type="file" name="gambar"
                                        class="form-control bg-light border-0 shadow-none">
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-3">Pilihan
                                    Jawaban</label>
                                @foreach (['a', 'b', 'c', 'd', 'e'] as $opt)
                                    <div class="mb-3">
                                        <div class="input-group border rounded-3 overflow-hidden">
                                            <div class="input-group-text bg-white border-0">
                                                <input class="form-check-input mt-0" type="radio" name="jawaban_benar"
                                                    id="edit_kunci_{{ $opt }}" value="{{ $opt }}"
                                                    required>
                                            </div>
                                            <span
                                                class="input-group-text bg-light border-0 fw-bold text-primary">{{ strtoupper($opt) }}</span>
                                            <input type="text" name="opsi_{{ $opt }}"
                                                id="edit_opsi_{{ $opt }}"
                                                class="form-control border-0 shadow-none" required>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0 gap-2">
                        <button type="button" class="btn btn-light rounded-pill px-4"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit"
                            class="btn btn-warning text-white rounded-pill px-4 py-2 fw-bold shadow-sm border-0 transition-3d">
                            <i class="bi bi-save2-fill me-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Tambahkan Style CSS di bawah ini --}}
    <style>
        .bg-soft-primary {
            background-color: #eef2ff;
        }

        .question-card {
            transition: all 0.3s ease;
            border: 1px solid transparent !important;
        }

        .question-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08) !important;
            border-color: #e0e7ff !important;
        }

        .option-item {
            transition: all 0.2s ease;
            min-height: 60px;
        }

        .option-badge {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.85rem;
        }

        .option-correct {
            background-color: #ecfdf5;
            border-color: #10b981 !important;
        }

        .transition-3d:hover {
            transform: translateY(-2px);
            filter: brightness(1.1);
        }
    </style>
@endsection

@section('scripts')
<script>
    // 1. Fungsi Hapus (Global Scope agar bisa dipanggil onclick)
    window.confirmDeleteQuestion = function(questionId) {
        let deleteUrl = "{{ route('guru.exams.questions.remove', ['question_id' => ':id']) }}";
        deleteUrl = deleteUrl.replace(':id', questionId);

        Swal.fire({
            title: 'Hapus Soal ini?',
            text: "Data tidak bisa dikembalikan!",
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

    $(document).ready(function() {
        // 2. Logika Edit: Isi data ke modal saat tombol edit diklik
        $('.btn-edit-soal').on('click', function() {
            // Ambil data-attributes dari tombol yang diklik
            const id = $(this).data('id');
            const text = $(this).data('text');
            const kunci = $(this).data('kunci');

            // Isi value ke form modal edit
            $('#edit_question_text').val(text);
            $('#edit_opsi_a').val($(this).data('a'));
            $('#edit_opsi_b').val($(this).data('b'));
            $('#edit_opsi_c').val($(this).data('c'));
            $('#edit_opsi_d').val($(this).data('d'));
            $('#edit_opsi_e').val($(this).data('e'));

            // Set radio button kunci jawaban
            $(`#edit_kunci_${kunci}`).prop('checked', true);

            // Update URL action form sesuai ID soal
            let updateUrl = "{{ route('guru.exams.questions.update', ['id' => $exam->id, 'question_id' => ':qid']) }}";
            $('#formEditSoal').attr('action', updateUrl.replace(':qid', id));

            // Tampilkan modal
            $('#modalEditSoal').modal('show');
        });
    });
</script>
@endsection
