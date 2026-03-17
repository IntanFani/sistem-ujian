@extends('layouts.admin')

@section('title', 'Kelola Soal Ujian')

@section('content')
<div class="mb-4">
    <a href="{{ route('guru.exams.index') }}" class="btn btn-light btn-sm rounded-pill mb-3">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Jadwal
    </a>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-1">{{ $exam->title }}</h4>
            <p class="text-muted small">
                <span class="badge bg-primary-subtle text-primary me-2">{{ $exam->subject->name }}</span>
                <i class="bi bi-people me-1"></i> Kelas: {{ $exam->kelas->nama_kelas }}
            </p>
        </div>
        <div class="text-end">
            <small class="text-muted d-block">Jumlah Soal Saat Ini</small>
            <span class="badge bg-dark rounded-pill fs-6 px-3">{{ $exam->questions->count() }} Soal</span>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h6 class="fw-bold mb-0"><i class="bi bi-database me-2"></i> Bank Soal Tersedia</h6>
                <small class="text-muted">Pilih soal yang ingin dimasukkan ke ujian ini.</small>
            </div>
            <div class="card-body px-4 pb-4">
                <form action="{{ route('guru.exams.questions.store', $exam->id) }}" method="POST">
                    @csrf
                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                        <table class="table table-hover align-middle">
                            <thead class="sticky-top bg-white">
                                <tr>
                                    <th width="40">#</th>
                                    <th>Pertanyaan</th>
                                    <th width="80" class="text-center small text-muted text-uppercase">Kunci</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bankSoal as $soal)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="question_ids[]" value="{{ $soal->id }}" class="form-check-input border-success">
                                    </td>
                                    <td>
                                        <div class="small text-dark fw-medium">{{ Str::limit($soal->question_text, 100) }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-warning text-dark px-2">{{ strtoupper($soal->jawaban_benar) }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5 text-muted small">
                                        <i class="bi bi-info-circle d-block fs-4 mb-2"></i>
                                        Tidak ada soal tersedia untuk mapel ini di bank soal Anda.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($bankSoal->count() > 0)
                    <div class="mt-4 d-grid">
                        <button type="submit" class="btn btn-success rounded-pill fw-bold">
                            Tambahkan Soal Terpilih <i class="bi bi-plus-circle ms-1"></i>
                        </button>
                    </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 bg-light">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h6 class="fw-bold mb-0"><i class="bi bi-list-check me-2"></i> Daftar Soal Ujian</h6>
                <small class="text-muted">Urutan soal akan diacak otomatis saat ujian.</small>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="list-group list-group-flush rounded-3 overflow-hidden shadow-sm border">
                    @forelse($exam->questions as $index => $q)
                    <div class="list-group-item border-0 d-flex justify-content-between align-items-center py-3">
                        <div class="me-3">
                            <span class="text-muted small d-block mb-1">Soal #{{ $index + 1 }}</span>
                            <div class="small fw-medium text-dark line-clamp-2">{{ Str::limit($q->question_text, 60) }}</div>
                        </div>
                        <form action="{{ route('guru.exams.questions.remove', $exam->id) }}" method="POST">
                            @csrf @method('DELETE')
                            <input type="hidden" name="question_id" value="{{ $q->id }}">
                            <button type="submit" class="btn btn-sm btn-outline-danger border-0 rounded-circle">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </form>
                    </div>
                    @empty
                    <div class="list-group-item border-0 text-center py-5 text-muted small bg-white">
                        Ujian ini belum memiliki soal.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;  
        overflow: hidden;
    }
</style>
@endsection