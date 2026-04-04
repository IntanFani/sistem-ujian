@extends('layouts.admin')

@section('title', 'Buat Ujian Baru')

@section('content')
<div class="container-fluid py-1">
    <div class="mb-4">
        <a href="{{ route('guru.exams.index') }}" class="btn btn-light btn-sm rounded-pill px-3 shadow-sm border text-secondary fw-medium">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                    <div class="d-flex align-items-center">
                        <div class="bg-soft-primary p-3 rounded-4 me-3" style="background: #eef2ff; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-calendar-plus text-primary fs-4"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark text-uppercase" style="letter-spacing: 1px;">Buat Jadwal Ujian</h5>
                            <p class="text-muted small mb-0">Isi formulir di bawah untuk menjadwalkan ujian baru bagi siswa.</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('guru.exams.store') }}" method="POST">
                    @csrf
                    <div class="card-body p-4 p-md-5">
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-2">Judul Ujian</label>
                                <div class="input-group custom-input-group">
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-card-heading text-muted"></i></span>
                                    <input type="text" name="title" class="form-control bg-light border-0 py-3 shadow-none" placeholder="Misal: Ujian Akhir Semester Ganjil" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-2">Durasi Pengerjaan</label>
                                <div class="input-group custom-input-group">
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-clock-history text-muted"></i></span>
                                    <input type="number" name="duration" class="form-control bg-light border-0 py-3 shadow-none" placeholder="90" required>
                                    <span class="input-group-text bg-light border-0 fw-bold text-muted">Menit</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-2">Target Kelas</label>
                                <div class="input-group custom-input-group">
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-people text-muted"></i></span>
                                    <select name="kelas_id" class="form-select bg-light border-0 py-3 shadow-none fw-medium" required>
                                        <option value="" disabled selected>-- Pilih Kelas --</option>
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}">{{ $class->nama_kelas }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-2">Mata Pelajaran</label>
                                <div class="input-group custom-input-group">
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-journal-check text-muted"></i></span>
                                    <select name="subject_id" class="form-select bg-light border-0 py-3 shadow-none fw-medium" required>
                                        <option value="" disabled selected>-- Pilih Mata Pelajaran --</option>
                                        @foreach ($subjects as $subject)
                                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="p-4 rounded-4 d-flex align-items-center justify-content-between shadow-sm border" style="background: #fafafa; border-color: #eee !important;">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-white shadow-sm p-3 rounded-3 me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-shuffle text-primary fs-4"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-0 small">Aktifkan Acak Soal</h6>
                                            <p class="text-muted mb-0 small">Urutan soal akan diacak secara otomatis untuk setiap siswa.</p>
                                        </div>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input custom-switch-lg" type="checkbox" role="switch" id="random" name="random_question" value="1" checked>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-light border-0 p-4 px-md-5 pb-5 d-flex justify-content-end gap-3">
                        <a href="{{ route('guru.exams.index') }}" class="btn btn-white border rounded-pill px-5 py-2 fw-medium text-secondary shadow-sm">Batal</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow-sm border-0 transition-3d text-white">
                            <i class="bi bi-send-check-fill me-2"></i> Simpan ujian
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    /* Styling tetap sama agar sinkron dengan halaman Edit */
    .bg-light { background-color: #f8fafc !important; }
    .custom-input-group { border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; }
    .custom-input-group:focus-within { border-color: #4f46e5; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); }
    .form-control:focus, .form-select:focus { background-color: #fff !important; box-shadow: none !important; }
    .custom-switch-lg { width: 3.5rem !important; height: 1.7rem !important; cursor: pointer; }
    .transition-3d:hover { transform: translateY(-3px); filter: brightness(1.05); }
</style>
@endsection