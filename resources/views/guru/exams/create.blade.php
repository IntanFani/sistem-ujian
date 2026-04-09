@extends('layouts.admin')

@section('title', 'Buat Ujian Baru')

@section('content')
<div class="container-fluid py-2">
    
    {{-- Header dengan Tombol Kembali --}}
    <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
        <a href="{{ route('guru.exams.index') }}" class="btn btn-white border shadow-sm rounded-circle d-flex align-items-center justify-content-center me-3 transition-3d text-secondary" style="width: 45px; height: 45px;" title="Kembali">
            <i class="bi bi-arrow-left fs-5"></i>
        </a>
        <div>
            <h4 class="fw-bold mb-1 text-dark" style="letter-spacing: 0.5px;">BUAT JADWAL UJIAN</h4>
            <p class="text-muted small mb-0">Atur informasi dan pelaksanaan ujian baru untuk siswa.</p>
        </div>
    </div>

    <div class="row g-4">
        {{-- Kolom Kiri: Form Utama (Desain Minimalis Berwarna) --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <form action="{{ route('guru.exams.store') }}" method="POST">
                    @csrf
                    <div class="card-body p-4 p-md-5">

                        {{-- Section 1: Informasi Dasar dengan Aksen Biru --}}
                        <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                            <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-info-circle-fill fs-5"></i>
                            </div>
                            <h6 class="fw-bold mb-0 text-uppercase" style="letter-spacing: 1px; color: #475569;">Informasi Dasar</h6>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-12">
                                <label class="form-label fw-semibold text-dark">Judul Ujian</label>
                                <input type="text" name="title" class="form-control form-control-lg rounded-3 fs-6 custom-minimal-input" placeholder="Misal: Ujian Akhir Semester Ganjil" required value="{{ old('title') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Mata Pelajaran</label>
                                <select name="subject_id" class="form-select form-select-lg rounded-3 fs-6 custom-minimal-input cursor-pointer" required>
                                    <option value="" disabled selected>-- Pilih Mata Pelajaran --</option>
                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Target Kelas</label>
                                <select name="kelas_id" class="form-select form-select-lg rounded-3 fs-6 custom-minimal-input cursor-pointer" required>
                                    <option value="" disabled selected>-- Pilih Kelas --</option>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}" {{ old('kelas_id') == $class->id ? 'selected' : '' }}>{{ $class->nama_kelas }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Section 2: Pengaturan Pelaksanaan dengan Aksen Kuning --}}
                        <div class="d-flex align-items-center mb-4 mt-2 pb-3 border-bottom">
                            <div class="bg-warning bg-opacity-10 text-warning p-2 rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-sliders fs-5"></i>
                            </div>
                            <h6 class="fw-bold mb-0 text-uppercase" style="letter-spacing: 1px; color: #475569;">Pengaturan Pelaksanaan</h6>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Durasi Pengerjaan</label>
                                <div class="input-group">
                                    <input type="number" name="duration" class="form-control form-control-lg rounded-start-3 fs-6 custom-minimal-input border-end-0" placeholder="90" required min="10" value="{{ old('duration', 90) }}">
                                    <span class="input-group-text text-muted fw-medium rounded-end-3" style="background-color: #f8fafc; border-color: #e2e8f0;">Menit</span>
                                </div>
                            </div>
                        </div>

                        {{-- Box Acak Soal Berwarna Tanpa Ikon Besar --}}
                        <div class="d-flex justify-content-between align-items-center p-3 rounded-3" style="background-color: #f1f5f9; border: 1px solid #e2e8f0;">
                            <div>
                                <h6 class="fw-semibold text-dark mb-1">Acak Urutan Soal</h6>
                                <small class="text-muted">Setiap siswa akan mendapatkan urutan nomor soal yang berbeda.</small>
                            </div>
                            <div class="form-check form-switch fs-4 mb-0 pe-2">
                                <input class="form-check-input cursor-pointer custom-colored-switch" type="checkbox" role="switch" id="random" name="random_question" value="1" checked>
                            </div>
                        </div>

                    </div>

                    {{-- Card Footer & Submit --}}
                    <div class="card-footer bg-white border-top p-4 d-flex justify-content-end gap-2">
                        <a href="{{ route('guru.exams.index') }}" class="btn btn-light rounded-pill px-4 fw-medium text-secondary transition-3d">Batal</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow-sm d-flex align-items-center transition-3d">
                            <i class="bi bi-floppy me-2"></i> Simpan Ujian
                        </button>
                    </div>
                </form>
                
                {{-- Tambahkan CSS khusus untuk input minimalis di bawah ini --}}
                <style>
                    /* Style Input Tanpa Garis Samping, Hanya Background Lembut */
                    .custom-minimal-input {
                        background-color: #f8fafc;
                        border: 1px solid #e2e8f0;
                        color: #1e293b;
                        transition: all 0.3s ease;
                    }

                    /* Efek Focus: Berubah Warna Halus (Biru Primary) */
                    .custom-minimal-input:focus {
                        background-color: #fff;
                        border-color: #3b82f6; /* Warna biru primary lembut */
                        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); /* Shadow tipis */
                    }
                    
                    /* CSS Custom Switch Berwarna */
                    .custom-colored-switch:checked {
                        background-color: #10b981 !important; /* Hijau Emerald */
                        border-color: #10b981 !important;
                    }
                    
                    /* Hilangkan bayangan bawaan Bootstrap saat focus switch */
                    .custom-colored-switch:focus {
                        box-shadow: none;
                        border-color: #e2e8f0;
                    }
                </style>
            </div>
        </div>

        {{-- Kolom Kanan: Sidebar Tips --}}
        <div class="col-lg-4 d-none d-lg-block">
            <div class="card border-0 shadow-sm rounded-4 h-100 position-sticky" style="top: 2rem; background: linear-gradient(145deg, #f8fafc 0%, #eef2ff 100%);">
                <div class="card-body p-4 p-xl-5">
                    <div class="mb-4 text-center">
                        <div class="d-inline-block bg-white text-primary p-3 rounded-circle shadow-sm mb-3">
                            <i class="bi bi-lightbulb-fill fs-2"></i>
                        </div>
                        <h5 class="fw-bold text-dark">Tips Pembuatan Ujian</h5>
                    </div>
                    
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex">
                            <i class="bi bi-check-circle-fill text-primary mt-1 me-3 fs-5"></i>
                            <p class="text-muted small mb-0"><strong class="text-dark">Judul Spesifik:</strong> Gunakan judul yang jelas (Misal: PAS Matematika Kelas 7).</p>
                        </div>
                        <div class="d-flex">
                            <i class="bi bi-check-circle-fill text-primary mt-1 me-3 fs-5"></i>
                            <p class="text-muted small mb-0"><strong class="text-dark">Durasi Pengerjaan:</strong> Waktu akan otomatis menghitung mundur setelah siswa menekan tombol mulai.</p>
                        </div>
                        <div class="d-flex">
                            <i class="bi bi-check-circle-fill text-primary mt-1 me-3 fs-5"></i>
                            <p class="text-muted small mb-0"><strong class="text-dark">Acak Soal:</strong> Sangat direkomendasikan agar urutan nomor soal berbeda antar siswa.</p>
                        </div>
                        <div class="d-flex">
                            <i class="bi bi-check-circle-fill text-primary mt-1 me-3 fs-5"></i>
                            <p class="text-muted small mb-0"><strong class="text-dark">Token:</strong> Sistem akan membuat Token unik (6 Digit) secara otomatis.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-light { background-color: #f8fafc !important; }
    .custom-input-group { border-radius: 12px; overflow: hidden; border: 2px solid #f1f5f9; background-color: #f8fafc; transition: all 0.3s ease; }
    .custom-input-group:focus-within { border-color: #cbd5e1; box-shadow: 0 0 0 4px rgba(203, 213, 225, 0.2); }
    .custom-input-group input, .custom-input-group select { color: #334155; }
    .form-control:focus, .form-select:focus { background-color: #f8fafc !important; box-shadow: none !important; }
    .custom-switch-lg { width: 3.5rem !important; height: 1.75rem !important; cursor: pointer; }
    .transition-3d { transition: all 0.2s ease; }
    .transition-3d:hover { transform: translateY(-2px); }
    .cursor-pointer { cursor: pointer; }
    
    /* Memperbaiki posisi checkbox switch bawaan bootstrap agar pas di tengah */
    .form-check-input.custom-switch-lg {
        margin-top: 0;
        float: none;
    }
</style>
@endsection