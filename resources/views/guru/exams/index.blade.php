@extends('layouts.admin')

@section('title', 'Manajemen Ujian')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Manajemen Ujian</h4>
        <p class="text-muted small">Atur jadwal dan sesi ujian siswa.</p>
    </div>
    <button class="btn btn-success rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addExamModal">
        <i class="bi bi-calendar-plus me-1"></i> Buat Ujian Baru
    </button>
</div>

<div class="row g-3">
    @forelse($exams as $exam)
    <div class="col-md-6 col-xl-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3">{{ $exam->subject->name }}</span>
                    <div class="text-end">
                        <small class="text-muted d-block">Token</small>
                        <span class="fw-bold text-success fs-5">{{ $exam->token }}</span>
                    </div>
                </div>
                
                <h5 class="fw-bold mb-1">{{ $exam->title }}</h5>
                <p class="text-muted small mb-3"><i class="bi bi-door-open me-1"></i> Kelas: {{ $exam->kelas->nama_kelas }}</p>
                
                <div class="p-3 bg-light rounded-3 mb-3">
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Durasi:</span>
                        <span class="fw-bold">{{ $exam->duration }} Menit</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Mulai:</span>
                        <span class="fw-bold">{{ date('d M Y, H:i', strtotime($exam->start_time)) }}</span>
                    </div>
                    <div class="d-flex justify-content-between small">
                        <span class="text-muted">Selesai:</span>
                        <span class="fw-bold">{{ date('d M Y, H:i', strtotime($exam->end_time)) }}</span>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <a href="{{ route('guru.exams.questions', $exam->id) }}" class="btn btn-outline-success btn-sm rounded-pill">
                        <i class="bi bi-list-check me-1"></i> Kelola Soal ({{ $exam->questions_count ?? $exam->questions->count() }})
                    </a>
                    <button class="btn btn-link btn-sm text-danger text-decoration-none" onclick="hapusUjian('{{ $exam->id }}')">
                        <i class="bi bi-trash me-1"></i> Hapus Jadwal
                    </button>

                    <form id="delete-form-{{ $exam->id }}" action="{{ route('guru.exams.destroy', $exam->id) }}" method="POST" class="d-none">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5">
        <img src="{{ asset('images/empty-exam.svg') }}" style="width: 150px;" class="mb-3 opacity-50">
        <p class="text-muted">Belum ada jadwal ujian yang dibuat.</p>
    </div>
    @endforelse
</div>

<div class="modal fade" id="addExamModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <form action="{{ route('guru.exams.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="fw-bold">Buat Jadwal Ujian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Judul Ujian</label>
                        <input type="text" name="title" class="form-control" placeholder="Contoh: PTS Ganjil 2026" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Mata Pelajaran</label>
                            <select name="subject_id" class="form-select" required>
                                @foreach($subjects as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Target Kelas</label>
                            <select name="kelas_id" class="form-select" required>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Durasi (Menit)</label>
                        <input type="number" name="duration" class="form-control" placeholder="60" required>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label small fw-bold">Waktu Mulai</label>
                            <input type="datetime-local" name="start_time" class="form-control" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label small fw-bold">Waktu Selesai</label>
                            <input type="datetime-local" name="end_time" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-success w-100 rounded-pill py-2 fw-bold">Simpan Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function hapusUjian(id) {
        Swal.fire({
            title: 'Hapus Jadwal Ujian?',
            text: "Data ujian yang dihapus tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit form hapus berdasarkan ID
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }
</script>
@endsection