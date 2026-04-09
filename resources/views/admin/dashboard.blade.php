@extends('layouts.admin')

@section('title', 'Dashboard Utama')

@section('content')
<div class="container-fluid py-2">

    {{-- 1. Welcome Banner Premium (Admin Version - Hijau Zamrud) --}}
    <div class="card border-0 rounded-4 mb-4 overflow-hidden position-relative shadow-sm" style="background: linear-gradient(135deg, #064e3b 0%, #059669 100%);">
        {{-- Efek Dekorasi Lingkaran di Background --}}
        <div class="position-absolute top-0 end-0 opacity-25" style="transform: translate(10%, -30%);">
            <i class="bi bi-circle-fill text-white" style="font-size: 15rem;"></i>
        </div>
        <div class="position-absolute bottom-0 end-0 opacity-25" style="transform: translate(-50%, 50%);">
            <i class="bi bi-circle-fill text-white" style="font-size: 10rem;"></i>
        </div>
        
        <div class="card-body p-4 p-md-5 position-relative z-1 text-white">
            <h3 class="fw-bold mb-2">Selamat Datang, Administrator 👋</h3>
            <p class="mb-0 fs-6 opacity-75">Panel Pusat Manajemen CBT MTs Al Huda</p>
        </div>
    </div>

    {{-- 2. Row 4 Widget Statistik Master --}}
    <div class="row g-3 mb-4">
        {{-- Widget 1: Mapel --}}
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 widget-hover">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 rounded-4 me-3 text-success d-flex align-items-center justify-content-center" style="width: 46px; height: 46px;">
                        <i class="bi bi-book fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0" style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;">Mata Pelajaran</p>
                        <h4 class="fw-bold mb-0 text-dark">{{ $countSubject ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- Widget 2: Total Guru --}}
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 widget-hover">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 rounded-4 me-3 text-warning d-flex align-items-center justify-content-center" style="width: 46px; height: 46px;">
                        <i class="bi bi-person-badge fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0" style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;">Total Guru</p>
                        <h4 class="fw-bold mb-0 text-dark">{{ $countGuru ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- Widget 3: Total Siswa --}}
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 widget-hover">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 rounded-4 me-3 text-primary d-flex align-items-center justify-content-center" style="width: 46px; height: 46px;">
                        <i class="bi bi-people fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0" style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;">Total Siswa</p>
                        <h4 class="fw-bold mb-0 text-dark">{{ $countSiswa ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- Widget 4: Total Kelas --}}
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 widget-hover">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="bg-danger bg-opacity-10 rounded-4 me-3 text-danger d-flex align-items-center justify-content-center" style="width: 46px; height: 46px;">
                        <i class="bi bi-door-open fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0" style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;">Total Kelas</p>
                        <h4 class="fw-bold mb-0 text-dark">{{ $countKelas ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. Row Konten Bawah (Jalan Pintas Admin) --}}
    <div class="row g-4">
        
        {{-- Kolom Kiri: Menu Pintasan --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-grid-fill text-primary opacity-75 me-2"></i>Akses Cepat Pengelolaan</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        
                        {{-- Pintasan 1: Kelola Siswa --}}
                        <div class="col-md-6">
                            <a href="{{ route('admin.siswas.index') }}" class="btn btn-outline-primary p-3 rounded-4 text-start d-flex align-items-center transition-3d text-decoration-none bg-white w-100 h-100" style="border-color: #e2e8f0;">
                                <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-3 me-3">
                                    <i class="bi bi-person-plus-fill fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark">Kelola Data Siswa</h6>
                                    <small class="text-muted">Tambah, edit, atau hapus akun siswa.</small>
                                </div>
                            </a>
                        </div>

                        {{-- Pintasan 2: Kelola Guru --}}
                        <div class="col-md-6">
                            <a href="{{ route('admin.gurus.index') }}" class="btn btn-outline-warning p-3 rounded-4 text-start d-flex align-items-center transition-3d text-decoration-none bg-white w-100 h-100" style="border-color: #e2e8f0;">
                                <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-3 me-3">
                                    <i class="bi bi-person-workspace fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark">Kelola Data Guru</h6>
                                    <small class="text-muted">Atur akses dan akun pengajar.</small>
                                </div>
                            </a>
                        </div>

                        {{-- Pintasan 3: Kelola Kelas --}}
                        <div class="col-md-6">
                            <a href="{{ route('admin.kelas.index') }}" class="btn btn-outline-danger p-3 rounded-4 text-start d-flex align-items-center transition-3d text-decoration-none bg-white w-100 h-100" style="border-color: #e2e8f0;">
                                <div class="bg-danger bg-opacity-10 text-danger p-3 rounded-3 me-3">
                                    <i class="bi bi-door-open-fill fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark">Manajemen Kelas</h6>
                                    <small class="text-muted">Daftar kelas dan ruangan ujian.</small>
                                </div>
                            </a>
                        </div>

                        {{-- Pintasan 4: Kelola Mapel --}}
                        <div class="col-md-6">
                            <a href="{{ route('admin.subjects.index') }}" class="btn btn-outline-success p-3 rounded-4 text-start d-flex align-items-center transition-3d text-decoration-none bg-white w-100 h-100" style="border-color: #e2e8f0;">
                                <div class="bg-success bg-opacity-10 text-success p-3 rounded-3 me-3">
                                    <i class="bi bi-journal-bookmark-fill fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark">Mata Pelajaran</h6>
                                    <small class="text-muted">Atur daftar mapel yang diujikan.</small>
                                </div>
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Panduan / Bantuan --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="background-color: #f8fafc;">
                <div class="card-body p-4 text-center d-flex flex-column justify-content-center align-items-center">
                    <img src="https://illustrations.popsy.co/gray/freelancer.svg" alt="Admin Task" style="width: 180px; opacity: 0.8;" class="mb-3">
                    <h6 class="fw-bold text-dark">Pusat Kendali Sistem</h6>
                    <p class="text-muted small mb-0">Sebagai Administrator, pastikan data Master (Guru, Siswa, Kelas, Mapel) sudah lengkap sebelum jadwal ujian dibuat oleh Guru.</p>
                </div>
            </div>
        </div>

    </div>

</div>

{{-- CSS Tambahan --}}
<style>
    .widget-hover { transition: all 0.3s ease; border: 1px solid transparent !important; }
    .widget-hover:hover { 
        transform: translateY(-5px); 
        box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important; 
        border-color: #f1f5f9 !important;
    }
    .transition-3d { transition: all 0.2s ease; }
    .transition-3d:hover { 
        transform: translateY(-3px);
        box-shadow: 0 10px 15px rgba(0,0,0,0.05) !important;
        border-color: #cbd5e1 !important;
    }
</style>
@endsection