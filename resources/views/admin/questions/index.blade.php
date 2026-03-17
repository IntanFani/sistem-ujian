@extends('layouts.admin')

@section('title', 'Bank Soal')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0 text-success">Bank Soal Utama</h4>
        <p class="text-muted small">Kelola koleksi pertanyaan untuk semua mata pelajaran</p>
    </div>
    <button class="btn btn-success rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addSoalModal">
        <i class="bi bi-plus-lg me-1"></i> Tambah Soal
    </button>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form action="{{ route('admin.questions.index') }}" method="GET" class="row g-2">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control rounded-3" placeholder="Cari soal..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="subject_id" class="form-select rounded-3">
                    <option value="">-- Semua Mapel --</option>
                    @foreach($subjects as $s)
                        <option value="{{ $s->id }}" {{ request('subject_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="guru_id" class="form-select rounded-3">
                    <option value="">-- Semua Guru --</option>
                    @foreach($gurus as $g)
                        <option value="{{ $g->id }}" {{ request('guru_id') == $g->id ? 'selected' : '' }}>{{ $g->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-success w-100 rounded-3">
                    <i class="bi bi-filter"></i>
                </button>
                <a href="{{ route('admin.questions.index') }}" class="btn btn-light w-100 rounded-3">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="px-4 py-3 text-muted small">NO</th>
                    <th class="py-3 text-muted small">PERTANYAAN</th>
                    <th class="py-3 text-muted small">MAPEL</th>
                    <th class="py-3 text-muted small">GURU</th>
                    <th class="py-3 text-muted small">KUNCI</th>
                    <th class="py-3 text-end px-4 text-muted small">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($questions as $index => $q)
                <tr>
                    <td class="px-4 text-muted">{{ $index + 1 }}</td>
                    <td style="max-width: 300px;">
                        <div class="text-truncate fw-bold text-dark">{!! Str::limit(strip_tags($q->question_text), 50) !!}</div>
                        @if($q->gambar)
                            <small class="text-success"><i class="bi bi-image me-1"></i> Ada Gambar</small>
                        @endif
                    </td>
                    <td><span class="badge bg-success-subtle text-success">{{ $q->subject->name ?? 'N/A' }}</span></td>
                    <td class="small">{{ $q->guru->nama ?? 'N/A' }}</td>
                    <td><span class="badge bg-primary px-3 text-uppercase">{{ $q->jawaban_benar }}</span></td>
                    <td class="text-end px-4">
                        <button class="btn btn-sm btn-outline-warning border-0" 
                            data-bs-toggle="modal" 
                            data-bs-target="#editSoalModal"
                            data-id="{{ $q->id }}"
                            data-subject-id="{{ $q->subject_id }}"
                            data-guru-id="{{ $q->guru_id }}"
                            data-text="{{ $q->question_text }}"
                            data-a="{{ $q->opsi_a }}"
                            data-b="{{ $q->opsi_b }}"
                            data-c="{{ $q->opsi_c }}"
                            data-d="{{ $q->opsi_d }}"
                            data-e="{{ $q->opsi_e }}"
                            data-kunci="{{ $q->jawaban_benar }}">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger border-0" onclick="hapusSoal({{ $q->id }})">
                            <i class="bi bi-trash"></i>
                        </button>
                        <form id="delete-form-{{ $q->id }}" action="{{ route('admin.questions.destroy', $q->id) }}" method="POST" style="display: none;">
                            @csrf @method('DELETE')
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">Belum ada soal tersedia.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-between align-items-center mt-4 px-4 pb-3">
    <div class="text-muted small">
        Menampilkan {{ $questions->firstItem() }} sampai {{ $questions->lastItem() }} 
        dari {{ $questions->total() }} soal
    </div>
    <div>
        {{-- Agar filter search/mapel tetap ikut ke halaman selanjutnya --}}
        {{ $questions->appends(request()->query())->links() }}
    </div>
</div>
</div>

<div class="modal fade" id="addSoalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Tambah Soal Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.questions.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Mata Pelajaran</label>
                            <select name="subject_id" class="form-select rounded-3" required>
                                <option value="">-- Pilih Mapel --</option>
                                @foreach($subjects as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Guru Pembuat</label>
                            <select name="guru_id" class="form-select rounded-3" required>
                                <option value="">-- Pilih Guru --</option>
                                @foreach($gurus as $g)
                                    <option value="{{ $g->id }}">{{ $g->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Pertanyaan</label>
                        <textarea name="question_text" class="form-control rounded-3" rows="3" required placeholder="Tuliskan isi soal..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Gambar Pendukung (Opsional)</label>
                        <input type="file" name="gambar" class="form-control rounded-3">
                    </div>
                    
                    <hr class="my-4 text-muted">
                    <label class="form-label d-block fw-bold text-success mb-3">Pilihan Jawaban</label>
                    
                    <div class="row g-3">
                        @foreach(['a', 'b', 'c', 'd', 'e'] as $opt)
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold text-uppercase border-end-0">{{ $opt }}</span>
                                <input type="text" name="opsi_{{ $opt }}" class="form-control border-start-0" placeholder="Jawaban {{ strtoupper($opt) }}" required>
                            </div>
                        </div>
                        @endforeach
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Kunci Jawaban</label>
                            <select name="jawaban_benar" class="form-select bg-success-subtle fw-bold" required>
                                <option value="">-- Pilih Kunci --</option>
                                @foreach(['a', 'b', 'c', 'd', 'e'] as $ans)
                                    <option value="{{ $ans }}">OPSI {{ strtoupper($ans) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm">Simpan Ke Bank Soal</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL EDIT SOAL --}}
<div class="modal fade" id="editSoalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Edit Data Soal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editSoalForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Mata Pelajaran</label>
                            <select name="subject_id" id="edit_subject_id" class="form-select rounded-3" required>
                                @foreach($subjects as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Guru Pembuat</label>
                            <select name="guru_id" id="edit_guru_id" class="form-select rounded-3" required>
                                @foreach($gurus as $g)
                                    <option value="{{ $g->id }}">{{ $g->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Pertanyaan</label>
                        <textarea name="question_text" id="edit_question_text" class="form-control rounded-3" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Ganti Gambar (Opsional)</label>
                        <input type="file" name="gambar" class="form-control rounded-3">
                        <small class="text-muted">Biarkan kosong jika tidak ingin mengubah gambar.</small>
                    </div>
                    
                    <hr class="my-4 text-muted">
                    <div class="row g-3">
                        @foreach(['a', 'b', 'c', 'd', 'e'] as $opt)
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold">{{ strtoupper($opt) }}</span>
                                <input type="text" name="opsi_{{ $opt }}" id="edit_opsi_{{ $opt }}" class="form-control" required>
                            </div>
                        </div>
                        @endforeach
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Kunci Jawaban</label>
                            <select name="jawaban_benar" id="edit_jawaban_benar" class="form-select bg-warning-subtle fw-bold" required>
                                @foreach(['a', 'b', 'c', 'd', 'e'] as $ans)
                                    <option value="{{ $ans }}">OPSI {{ strtoupper($ans) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 text-white shadow-sm">Update Soal</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // FUNGSI HAPUS
    function hapusSoal(id) {
        Swal.fire({
            title: 'Hapus Soal?',
            text: "Soal ini akan dihapus permanen dari bank soal!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }

    // LOGIC MODAL EDIT
    const editSoalModal = document.getElementById('editSoalModal');
    if (editSoalModal) {
        editSoalModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            
            // Ambil data-attributes dari tombol
            const id = button.getAttribute('data-id');
            const subjectId = button.getAttribute('data-subject-id');
            const guruId = button.getAttribute('data-guru-id');
            const text = button.getAttribute('data-text');
            const kunci = button.getAttribute('data-kunci');

            // Isi input modal
            editSoalModal.querySelector('#edit_subject_id').value = subjectId;
            editSoalModal.querySelector('#edit_guru_id').value = guruId;
            editSoalModal.querySelector('#edit_question_text').value = text;
            editSoalModal.querySelector('#edit_jawaban_benar').value = kunci;

            // Isi opsi A sampai E
            ['a', 'b', 'c', 'd', 'e'].forEach(opt => {
                const val = button.getAttribute('data-' + opt);
                editSoalModal.querySelector('#edit_opsi_' + opt).value = val;
            });

            // Set Action Form
            editSoalModal.querySelector('#editSoalForm').setAttribute('action', '/admin/questions/' + id);
        });
    }
</script>
@endsection
