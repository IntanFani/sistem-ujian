@extends('layouts.admin')

@section('title', 'Edit Ujian - ' . $exam->title)

@section('content')
<div class="container-fluid py-2">
    
    {{-- Header dengan Tombol Kembali --}}
    <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
        <a href="{{ route('guru.exams.index') }}" class="btn btn-white border shadow-sm rounded-circle d-flex align-items-center justify-content-center me-3 transition-3d text-secondary" style="width: 45px; height: 45px;" title="Kembali">
            <i class="bi bi-arrow-left fs-5"></i>
        </a>
        <div>
            <h4 class="fw-bold mb-1 text-dark" style="letter-spacing: 0.5px;">EDIT JADWAL UJIAN</h4>
            <p class="text-muted small mb-0">Ubah detail pelaksanaan dan pengaturan sesi ujian siswa.</p>
        </div>
    </div>

    <div class="row">
        {{-- Form Utama dibuat Full Width (col-12) --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <form action="{{ route('guru.exams.update', $exam->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body p-4 p-md-5">

                        {{-- Section 1: Informasi Dasar --}}
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-info-circle-fill fs-5"></i>
                            </div>
                            <h6 class="fw-bold mb-0 text-uppercase" style="letter-spacing: 1px;">Informasi Dasar</h6>
                        </div>

                        <div class="row g-4 mb-5 pb-3 border-bottom">
                            <div class="col-12">
                                <label class="form-label fw-bold small text-muted text-uppercase mb-2">Judul Ujian</label>
                                <div class="input-group custom-input-group edit-focus">
                                    <span class="input-group-text bg-light border-0 px-3"><i class="bi bi-card-heading text-muted fs-5"></i></span>
                                    <input type="text" name="title" class="form-control bg-light border-0 py-3 shadow-none fw-medium" placeholder="Misal: Penilaian Tengah Semester" required value="{{ old('title', $exam->title) }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted text-uppercase mb-2">Mata Pelajaran</label>
                                <div class="input-group custom-input-group edit-focus">
                                    <span class="input-group-text bg-light border-0 px-3"><i class="bi bi-journal-check text-muted fs-5"></i></span>
                                    <select name="subject_id" class="form-select bg-light border-0 py-3 shadow-none fw-medium cursor-pointer" required>
                                        <option value="" disabled>-- Pilih Mata Pelajaran --</option>
                                        @foreach ($subjects as $subject)
                                            <option value="{{ $subject->id }}" {{ old('subject_id', $exam->subject_id) == $subject->id ? 'selected' : '' }}>
                                                {{ $subject->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted text-uppercase mb-2">Target Kelas</label>
                                <div class="input-group custom-input-group edit-focus">
                                    <span class="input-group-text bg-light border-0 px-3"><i class="bi bi-people text-muted fs-5"></i></span>
                                    <select name="kelas_id" class="form-select bg-light border-0 py-3 shadow-none fw-medium cursor-pointer" required>
                                        <option value="" disabled>-- Pilih Kelas --</option>
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}" {{ old('kelas_id', $exam->kelas_id) == $class->id ? 'selected' : '' }}>
                                                {{ $class->nama_kelas }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Section 2: Pengaturan Pelaksanaan --}}
                        <div class="d-flex align-items-center mb-4 mt-2">
                            <div class="bg-warning bg-opacity-10 text-warning p-2 rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-sliders fs-5"></i>
                            </div>
                            <h6 class="fw-bold mb-0 text-uppercase" style="letter-spacing: 1px;">Pengaturan Pelaksanaan</h6>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted text-uppercase mb-2">Durasi Pengerjaan</label>
                                <div class="input-group custom-input-group edit-focus">
                                    <span class="input-group-text bg-light border-0 px-3"><i class="bi bi-clock-history text-muted fs-5"></i></span>
                                    <input type="number" name="duration" class="form-control bg-light border-0 py-3 shadow-none fw-medium" placeholder="90" required min="10" value="{{ old('duration', $exam->duration) }}">
                                    <span class="input-group-text bg-light border-0 fw-bold text-muted pe-4">Menit</span>
                                </div>
                            </div>
                        </div>

                        {{-- Box Acak Soal --}}
                        <div class="p-4 rounded-4 d-flex align-items-center justify-content-between border mt-2" style="background-color: #fafafa; border-color: #f1f5f9 !important;">
                            <div class="d-flex align-items-center">
                                <div class="bg-white shadow-sm rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="bi bi-shuffle text-warning fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark">Aktifkan Acak Soal</h6>
                                    <p class="text-muted small mb-0">Urutan soal akan diacak secara otomatis untuk meminimalisir kecurangan.</p>
                                </div>
                            </div>
                            <div class="form-check form-switch ms-3 m-0 p-0">
                                <input class="form-check-input custom-switch-lg m-0" type="checkbox" role="switch" id="random" name="random_question" value="1" {{ old('random_question', $exam->random_question) ? 'checked' : '' }}>
                            </div>
                        </div>

                    </div>

                    {{-- Card Footer & Submit --}}
                    <div class="card-footer bg-white border-top p-4 px-md-5 d-flex justify-content-end gap-3">
                        <a href="{{ route('guru.exams.index') }}" class="btn btn-light border rounded-pill px-4 py-2 fw-medium text-secondary shadow-sm transition-3d">Batal</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow-sm border-0 transition-3d text-white d-flex align-items-center">
                            <i class="bi bi-file-earmark-check-fill me-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-light { background-color: #f8fafc !important; }
    .custom-input-group { border-radius: 12px; overflow: hidden; border: 2px solid #f1f5f9; background-color: #f8fafc; transition: all 0.3s ease; }
    .custom-input-group.edit-focus:focus-within { border-color: #ffc107; box-shadow: 0 0 0 4px rgba(255, 193, 7, 0.15); }
    .custom-input-group input, .custom-input-group select { color: #334155; }
    .form-control:focus, .form-select:focus { background-color: #f8fafc !important; box-shadow: none !important; }
    
    .custom-switch-lg { width: 3.5rem !important; height: 1.75rem !important; cursor: pointer; }
    .transition-3d { transition: all 0.2s ease; }
    .transition-3d:hover { transform: translateY(-2px); }
    .cursor-pointer { cursor: pointer; }
    
    .form-check-input.custom-switch-lg {
        margin-top: 0;
        float: none;
    }
</style>
@endsection