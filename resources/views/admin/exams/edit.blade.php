@extends('layouts.admin')

@section('title', 'Edit Ujian - ' . $exam->title)

@section('content')
<div class="container-fluid py-2">
    
    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div class="d-flex align-items-center">
            <a href="{{ route('admin.exams.index') }}" class="btn btn-white border shadow-sm rounded-circle d-flex align-items-center justify-content-center me-3 transition-3d text-secondary" style="width: 48px; height: 48px;">
                <i class="bi bi-arrow-left fs-5"></i>
            </a>
            <div>
                <h4 class="fw-bold mb-1 text-dark" style="letter-spacing: 0.5px;">EDIT JADWAL UJIAN</h4>
                <p class="text-muted small mb-0">Perbarui informasi pelaksanaan ujian untuk <b>{{ $exam->title }}</b>.</p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                {{-- PENTING: Pakai Method PUT untuk Update --}}
                <form action="{{ route('admin.exams.update', $exam->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body p-4 p-md-5">
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">Judul Ujian</label>
                            <input type="text" name="title" class="form-control form-control-lg rounded-3 fs-6 custom-minimal-input" value="{{ old('title', $exam->title) }}" required>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Guru Pengampu</label>
                                <select name="guru_id" class="form-select form-select-lg rounded-3 fs-6 custom-minimal-input" required>
                                    @foreach ($gurus as $guru)
                                        <option value="{{ $guru->id }}" {{ $exam->guru_id == $guru->id ? 'selected' : '' }}>
                                            {{ $guru->user->name ?? $guru->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Target Kelas</label>
                                <select name="kelas_id" class="form-select form-select-lg rounded-3 fs-6 custom-minimal-input" required>
                                    @foreach ($kelases as $kelas)
                                        <option value="{{ $kelas->id }}" {{ $exam->kelas_id == $kelas->id ? 'selected' : '' }}>
                                            {{ $kelas->nama_kelas }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Durasi (Menit)</label>
                                <div class="input-group">
                                    <input type="number" name="duration" class="form-control form-control-lg rounded-start-3 fs-6 custom-minimal-input" value="{{ old('duration', $exam->duration) }}" required>
                                    <span class="input-group-text text-muted rounded-end-3" style="background-color: #f8fafc;">Menit</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-white border-top p-4 d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.exams.index') }}" class="btn btn-light rounded-pill px-4 fw-medium text-secondary transition-3d">Batal</a>
                        <button type="submit" class="btn btn-warning rounded-pill px-5 py-2 fw-bold shadow-sm d-flex align-items-center transition-3d text-white">
                            <i class="bi bi-floppy me-2 fs-5"></i> Perbarui Ujian
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Kolom Kanan (Opsional: Info/Guide) --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 bg-light p-4">
                <h6 class="fw-bold"><i class="bi bi-info-circle me-2 text-primary"></i>Catatan Edit</h6>
                <ul class="small text-muted ps-3 mt-3">
                    <li class="mb-2">Perubahan judul akan langsung terlihat di dashboard siswa.</li>
                    <li class="mb-2">Jika mengubah durasi saat ujian berlangsung, siswa mungkin perlu refresh halaman.</li>
                    <li>Pastikan guru yang dipilih sudah memiliki bank soal yang lengkap.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
    .custom-minimal-input { background-color: #f8fafc; border: 1px solid #e2e8f0; }
    .custom-minimal-input:focus { background-color: #fff; border-color: #ffc107; box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.1); }
    .transition-3d:hover { transform: translateY(-2px); transition: 0.2s; }
</style>
@endsection