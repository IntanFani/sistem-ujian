@extends('layouts.admin')

@section('title', 'Dashboard Guru')

@section('content')
<div class="container-fluid py-2">

    {{-- 1. Welcome Banner Premium --}}
    <div class="card border-0 rounded-4 mb-4 overflow-hidden position-relative shadow-sm" style="background: linear-gradient(135deg, #198754 0%, #20c997 100%);">
        {{-- Efek Dekorasi Lingkaran di Background --}}
        <div class="position-absolute top-0 end-0 opacity-25" style="transform: translate(10%, -30%);">
            <i class="bi bi-circle-fill text-white" style="font-size: 15rem;"></i>
        </div>
        <div class="position-absolute bottom-0 end-0 opacity-25" style="transform: translate(-50%, 50%);">
            <i class="bi bi-circle-fill text-white" style="font-size: 10rem;"></i>
        </div>
        
        <div class="card-body p-4 p-md-5 position-relative z-1 text-white">
            <h3 class="fw-bold mb-2">Selamat Datang, {{ Auth::user()->name ?? 'Guru' }}! 👋</h3>
            <p class="mb-0 fs-6 opacity-75">Panel Guru CBT MTs Al Huda Pamegatan</p>
        </div>
    </div>

    {{-- 2. Row 4 Widget Statistik (Versi Compact) --}}
    <div class="row g-3 mb-4">
        {{-- Widget 1: Total Ujian --}}
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 widget-hover">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 rounded-4 me-3 text-primary d-flex align-items-center justify-content-center" style="width: 46px; height: 46px;">
                        <i class="bi bi-file-earmark-text fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0" style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;">Total Ujian</p>
                        <h4 class="fw-bold mb-0 text-dark">{{ $total_ujian ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- Widget 2: Ujian Aktif --}}
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 widget-hover">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 rounded-4 me-3 text-success d-flex align-items-center justify-content-center" style="width: 46px; height: 46px;">
                        <i class="bi bi-broadcast fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0" style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;">Ujian Aktif</p>
                        <h4 class="fw-bold mb-0 text-dark">{{ $ujian_aktif ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- Widget 3: Total Soal --}}
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 widget-hover">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 rounded-4 me-3 text-warning d-flex align-items-center justify-content-center" style="width: 46px; height: 46px;">
                        <i class="bi bi-list-task fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0" style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;">Soal Anda</p>
                        <h4 class="fw-bold mb-0 text-dark">{{ $total_soal ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- Widget 4: Rekap Nilai --}}
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 widget-hover">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="bg-info bg-opacity-10 rounded-4 me-3 text-info d-flex align-items-center justify-content-center" style="width: 46px; height: 46px;">
                        <i class="bi bi-graph-up-arrow fs-5"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0" style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;">Data Nilai</p>
                        <h4 class="fw-bold mb-0 text-dark">{{ $total_hasil ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. Row Konten Bawah (Tabel & Pintasan) --}}
    <div class="row g-4">
        
        {{-- Bagian Kiri: Tabel Info Ujian (Ambil porsi 8 kolom di PC) --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history text-primary me-2"></i>Aktivitas Ujian Anda</h6>
                    <a href="{{ route('guru.exams.index') }}" class="btn btn-sm btn-light text-primary fw-medium rounded-pill px-3">Lihat Semua</a>
                </div>
                <div class="card-body p-4">
                    {{-- Kita looping data $recent_exams dari controller --}}
                    @forelse($recent_exams ?? [] as $exam)
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between p-3 mb-3 bg-light rounded-4 border transition-3d">
                            <div class="d-flex align-items-center mb-2 mb-md-0">
                                <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3 text-primary d-none d-sm-block">
                                    <i class="bi bi-journal-text fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-1">{{ $exam->title }}</h6>
                                    <div class="text-muted small">
                                        <i class="bi bi-book me-1"></i> {{ $exam->subject->name ?? 'Mapel' }}
                                        <span class="mx-1">•</span>
                                        <i class="bi bi-people me-1"></i> Kelas {{ $exam->kelas->nama_kelas ?? '-' }}
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="text-end d-none d-md-block">
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">Status</small>
                                    @if($exam->status == 'aktif')
                                        <span class="fw-bold text-success small">Aktif</span>
                                    @else
                                        <span class="fw-bold text-danger small">Nonaktif</span>
                                    @endif
                                </div>
                                <a href="{{ route('guru.exams.questions', $exam->id) }}" class="btn btn-white border shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;" title="Kelola Ujian">
                                    <i class="bi bi-chevron-right text-primary"></i>
                                </a>
                            </div>
                        </div>
                    @empty
                        {{-- Kalau belum ada ujian sama sekali, baru tampilkan ilustrasi --}}
                        <div class="p-4 bg-light rounded-4 border text-center">
                            <img src="https://illustrations.popsy.co/gray/student-going-to-school.svg" alt="Ilustrasi" style="width: 150px; opacity: 0.6;" class="mb-3">
                            <h6 class="fw-bold text-dark">Belum Ada Ujian Terjadwal</h6>
                            <p class="text-muted small mb-0">Ujian yang sedang aktif atau baru saja dibuat akan otomatis muncul di sini.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Bagian Kanan: Menu Pintasan (Ambil porsi 4 kolom di PC) --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-grid-fill text-primary opacity-75 me-2"></i>Akses Cepat</h6>
                </div>
                <div class="card-body p-4">
                    <div class="d-grid gap-3">
                        <a href="{{ route('guru.exams.create') }}" class="p-3 rounded-4 text-start d-flex align-items-center transition-3d text-decoration-none bg-white">
                            <div class="bg-success bg-opacity-10 text-success p-2 rounded-3 me-3">
                                <i class="bi bi-plus-lg fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">Buat Ujian Baru</h6>
                                <small class="text-muted">Jadwalkan sesi ujian siswa.</small>
                            </div>
                        </a>

                        <a href="{{ route('guru.exams.index') }}" class="p-3 rounded-4 text-start d-flex align-items-center transition-3d text-decoration-none bg-white">
                            <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3 me-3">
                                <i class="bi bi-card-list fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">Kelola Ujian & Soal</h6>
                                <small class="text-muted">Atur jadwal dan input soal ujian.</small>
                            </div>
                        </a>

                        <a href="{{ route('guru.results.index') }}" class="p-3 rounded-4 text-start d-flex align-items-center transition-3d text-decoration-none bg-white">
                            <div class="bg-info bg-opacity-10 text-info p-2 rounded-3 me-3">
                                <i class="bi bi-clipboard-data fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">Lihat Hasil Ujian</h6>
                                <small class="text-muted">Cek rekap nilai kelas.</small>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    .widget-hover { transition: all 0.3s ease; border: 1px solid transparent !important; }
    .widget-hover:hover { 
        transform: translateY(-5px); 
        box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important; 
        border-color: #f1f5f9 !important;
    }
    .transition-3d { transition: all 0.2s ease; border: 1px solid #e2e8f0; color: #334155; }
    .transition-3d:hover { 
        transform: translateX(5px); 
        background-color: #f8fafc;
        border-color: #cbd5e1;
    }
</style>
@endsection