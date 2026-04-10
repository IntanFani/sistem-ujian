@extends('layouts.admin')

@section('title', 'Buat Ujian Baru')

@section('content')
<div class="container-fluid py-2">
    
    {{-- Header & Navigasi --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div class="d-flex align-items-center">
            <a href="{{ route('admin.exams.index') }}" class="btn btn-white border shadow-sm rounded-circle d-flex align-items-center justify-content-center me-3 transition-3d text-secondary" style="width: 48px; height: 48px;" title="Kembali ke Daftar Ujian">
                <i class="bi bi-arrow-left fs-5"></i>
            </a>
            <div>
                <h4 class="fw-bold mb-1 text-dark" style="letter-spacing: 0.5px;">BUAT UJIAN BARU</h4>
                <p class="text-muted small mb-0">Tambahkan jadwal pelaksanaan ujian sebagai Administrator.</p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Kolom Kiri: Form Utama (Desain Minimalis Berwarna) --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                
                {{-- PASTIKAN ACTION-NYA MENGARAH KE ADMIN.EXAMS.STORE --}}
                <form action="{{ route('admin.exams.store') }}" method="POST">
                    @csrf
                    <div class="card-body p-4 p-md-5">

                        {{-- Section 1: Informasi Dasar dengan Aksen Biru --}}
                        <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                            <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-info-circle-fill fs-5"></i>
                            </div>
                            <h6 class="fw-bold mb-0 text-uppercase" style="letter-spacing: 1px; color: #475569;">Informasi Dasar</h6>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">Judul Ujian</label>
                            <input type="text" name="title" class="form-control form-control-lg rounded-3 fs-6 custom-minimal-input" placeholder="Misal: Ujian Akhir Semester Ganjil" required value="{{ old('title') }}">
                        </div>

                        <div class="row g-4 mb-4">
                            {{-- Kolom Khusus Admin: Pilih Guru --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Guru Pengampu</label>
                                <select name="guru_id" class="form-select form-select-lg rounded-3 fs-6 custom-minimal-input cursor-pointer" required>
                                    <option value="" disabled selected>-- Pilih Guru & Mapel --</option>
                                    @foreach ($gurus as $guru)
                                        <option value="{{ $guru->id }}" {{ old('guru_id') == $guru->id ? 'selected' : '' }}>
                                            {{ $guru->nama }} ({{ $guru->subject->name ?? 'Tanpa Mapel' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Target Kelas</label>
                                <select name="kelas_id" class="form-select form-select-lg rounded-3 fs-6 custom-minimal-input cursor-pointer" required>
                                    <option value="" disabled selected>-- Pilih Kelas --</option>
                                    @foreach ($kelases as $kelas)
                                        <option value="{{ $kelas->id }}" {{ old('kelas_id') == $kelas->id ? 'selected' : '' }}>{{ $kelas->nama_kelas }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Section 2: Pengaturan Pelaksanaan dengan Aksen Kuning --}}
                        <div class="d-flex align-items-center mb-4 mt-5 pb-3 border-bottom">
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

                        {{-- Box Acak Soal Berwarna --}}
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

                    {{-- Card Footer & Submit (Pakai icon bi-floppy sesuai standar baru) --}}
                    <div class="card-footer bg-white border-top p-4 d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.exams.index') }}" class="btn btn-light rounded-pill px-4 fw-medium text-secondary transition-3d">Batal</a>
                        <button type="submit" class="btn btn-success rounded-pill px-5 py-2 fw-bold shadow-sm d-flex align-items-center transition-3d text-white">
                            <i class="bi bi-floppy me-2 fs-5"></i> Simpan Ujian
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Kolom Kanan: Panduan --}}
        <div class="col-lg-4 d-none d-lg-block">
            <div class="card border-0 shadow-sm rounded-4 h-100 position-sticky" style="top: 2rem; background: linear-gradient(145deg, #f8fafc 0%, #f1f5f9 100%);">
                <div class="card-body p-4 p-xl-5 text-center d-flex flex-column justify-content-center">
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px;">
                        <i class="bi bi-shield-lock-fill fs-1"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-3">Mode Administrator</h5>
                    <p class="text-muted small mb-4 text-start">
                        Anda sedang membuat ujian menggunakan hak akses Admin. <br><br>
                        Pastikan Anda memilih <b>Guru Pengampu</b> yang tepat, karena soal-soal ujian nantinya akan diambil dari Bank Soal milik guru tersebut.
                    </p>
                    <div class="p-3 bg-white rounded-3 border text-start">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <span class="small fw-medium text-dark">Pilih Guru & Mapel</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <span class="small fw-medium text-dark">Tentukan Target Kelas</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <span class="small fw-medium text-dark">Atur Durasi Waktu</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .transition-3d { transition: all 0.2s ease; }
    .transition-3d:hover { transform: translateY(-2px); }
    
    /* Style Input Minimalis */
    .custom-minimal-input {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #1e293b;
        transition: all 0.3s ease;
    }
    .custom-minimal-input:focus {
        background-color: #fff;
        border-color: #10b981; /* Warna hijau success untuk admin */
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }
    
    /* Switch Berwarna */
    .custom-colored-switch:checked {
        background-color: #10b981 !important;
        border-color: #10b981 !important;
    }
    .custom-colored-switch:focus {
        box-shadow: none;
        border-color: #e2e8f0;
    }
    .cursor-pointer { cursor: pointer; }
</style>
@endsection