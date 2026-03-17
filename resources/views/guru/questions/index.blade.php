@extends('layouts.admin')

@section('title', 'Bank Soal Saya')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Bank Soal Saya</h4>
        <p class="text-muted small">Kelola koleksi soal ujian Anda di sini.</p>
    </div>
    <button class="btn btn-success rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addSoalModal">
        <i class="bi bi-plus-lg me-1"></i> Tambah Soal
    </button>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form action="{{ route('guru.questions.index') }}" method="GET" class="row g-2">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control rounded-3" placeholder="Cari soal..." value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <select name="subject_id" class="form-select rounded-3">
                    <option value="">-- Semua Mata Pelajaran --</option>
                    @foreach($subjects as $s)
                        <option value="{{ $s->id }}" {{ request('subject_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-success w-100 rounded-3"><i class="bi bi-filter"></i></button>
                <a href="{{ route('guru.questions.index') }}" class="btn btn-light w-100 rounded-3"><i class="bi bi-arrow-clockwise"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Soal</th>
                        <th>Mapel</th>
                        <th>Kunci</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($questions as $q)
                    <tr>
                        <td class="ps-4">
                            <div class="small fw-bold text-truncate" style="max-width: 300px;">{{ $q->question_text }}</div>
                        </td>
                        <td><span class="badge bg-primary-subtle text-primary px-3 rounded-pill">{{ $q->subject->name }}</span></td>
                        <td><span class="badge bg-warning text-dark fw-bold px-3 rounded-pill">{{ strtoupper($q->jawaban_benar) }}</span></td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-light text-warning" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editSoalModal"
                                    data-id="{{ $q->id }}"
                                    data-subject-id="{{ $q->subject_id }}"
                                    data-text="{{ $q->question_text }}"
                                    data-a="{{ $q->opsi_a }}" data-b="{{ $q->opsi_b }}" data-c="{{ $q->opsi_c }}" data-d="{{ $q->opsi_d }}" data-e="{{ $q->opsi_e }}"
                                    data-kunci="{{ $q->jawaban_benar }}">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            
                            <button class="btn btn-sm btn-light text-danger" onclick="hapusSoal('{{ $q->id }}')">
                                <i class="bi bi-trash"></i>
                            </button>

                            <form id="delete-form-{{ $q->id }}" action="{{ route('guru.questions.destroy', $q->id) }}" method="POST" class="d-none">
                                @csrf @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">Belum ada soal yang dibuat.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-3">
            {{ $questions->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<div class="modal fade" id="addSoalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <form action="{{ route('guru.questions.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0">
                    <h5 class="fw-bold">Tambah Soal Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Mata Pelajaran</label>
                        <select name="subject_id" class="form-select" required>
                            @foreach($subjects as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Pertanyaan</label>
                        <textarea name="question_text" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="row g-3">
                        @foreach(['a', 'b', 'c', 'd', 'e'] as $opt)
                        <div class="col-md-6">
                            <input type="text" name="opsi_{{ $opt }}" class="form-control" placeholder="Opsi {{ strtoupper($opt) }}" required>
                        </div>
                        @endforeach
                        <div class="col-md-6">
                            <select name="jawaban_benar" class="form-select bg-warning-subtle fw-bold" required>
                                <option value="">Pilih Kunci Jawaban</option>
                                @foreach(['a', 'b', 'c', 'd', 'e'] as $ans)
                                    <option value="{{ $ans }}">OPSI {{ strtoupper($ans) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-success rounded-pill px-4">Simpan Soal</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Script logic Edit dan SweetAlert hapus sama seperti di Admin, cuma arahkan form action ke guru.questions.update --}}
@endsection

@section('scripts')
<script>
    function hapusSoal(id) {
        Swal.fire({
            title: 'Hapus Soal?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) document.getElementById('delete-form-' + id).submit();
        });
    }

    // Logic Modal Edit (Tinggal sesuaikan ID form-nya saja)
    const editSoalModal = document.getElementById('editSoalModal');
    // ... (sama seperti logic JavaScript admin sebelumnya, tapi action form-nya diarahkan ke /guru/questions/id)
</script>
@endsection