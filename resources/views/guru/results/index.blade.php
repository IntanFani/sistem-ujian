@extends('layouts.admin')

@section('title', 'Daftar Hasil Ujian')

@section('content')
<div class="container-fluid py-2">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
        <div class="d-flex align-items-center">
            <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                <i class="bi bi-clipboard-data-fill fs-4 text-primary"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-1 text-dark" style="letter-spacing: 0.5px;">HASIL UJIAN SISWA</h4>
                <p class="text-muted small mb-0">Pilih ujian untuk melihat rekap nilai dan kemajuan siswa.</p>
            </div>
        </div>
        
        {{-- Widget Mini Total Ujian --}}
        <div class="d-none d-md-flex align-items-center bg-white border rounded-pill px-4 py-2 shadow-sm">
            <span class="text-muted small fw-medium me-2">Total Ujian:</span>
            <span class="fw-bold text-primary fs-5">{{ $exams->count() }}</span>
        </div>
    </div>

    {{-- Grid Kartu Ujian --}}
    <div class="row g-4">
        @forelse($exams as $exam)
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 exam-card">
                    <div class="card-body p-4 d-flex flex-column">
                        
                        {{-- Badge Mapel & Jumlah Soal --}}
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold" style="letter-spacing: 0.5px;">
                                <i class="bi bi-journal-bookmark-fill me-1"></i> {{ $exam->subject->name ?? 'Mapel' }}
                            </span>
                            <span class="badge bg-light text-secondary border px-2 py-1 rounded-3">
                                <i class="bi bi-file-earmark-text me-1"></i> {{ $exam->questions_count ?? 0 }} Soal
                            </span>
                        </div>

                        {{-- Judul Ujian --}}
                        <h5 class="fw-bold text-dark mb-1 line-clamp-2">{{ $exam->title }}</h5>
                        
                        {{-- Info Kelas di bagian bawah card (didorong pakai mt-auto) --}}
                        <div class="mt-auto pt-3">
                            <div class="d-flex align-items-center text-muted small mb-4 bg-light p-2 rounded-3 border">
                                <i class="bi bi-building ms-1 me-2 text-primary"></i> 
                                <span class="fw-medium">Kelas {{ $exam->kelas->nama_kelas ?? '-' }}</span>
                            </div>
                            
                            {{-- Tombol Lihat Rekap --}}
                            <a href="{{ route('guru.results.show', $exam->id) }}" class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow-sm transition-3d d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-graph-up-arrow"></i> Lihat Rekap Nilai
                            </a>
                        </div>
                        
                    </div>
                </div>
            </div>
        @empty
            {{-- State Kosong Premium --}}
            <div class="col-12 text-center py-5 mt-3">
                <div class="p-5 bg-white rounded-4 shadow-sm border border-light mx-auto" style="max-width: 600px;">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                        <i class="bi bi-clipboard-x fs-1"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Belum Ada Hasil Ujian</h5>
                    <p class="text-secondary small mb-0">Belum ada ujian yang tersedia untuk dilihat rekap nilainya. Silakan buat ujian terlebih dahulu di menu Manajemen Ujian.</p>
                </div>
            </div>
        @endforelse
    </div>

</div>

{{-- CSS Tambahan --}}
<style>
    /* Styling Kartu */
    .exam-card { 
        transition: all 0.3s ease; 
        border: 1px solid #f8fafc !important; 
        background-color: #ffffff;
    }
    .exam-card:hover { 
        transform: translateY(-5px); 
        box-shadow: 0 12px 25px rgba(0,0,0,0.06) !important; 
        border-color: #e2e8f0 !important; 
    }

    /* Potong teks jika judul terlalu panjang (Max 2 baris) */
    .line-clamp-2 { 
        display: -webkit-box; 
        -webkit-line-clamp: 2; 
        -webkit-box-orient: vertical; 
        overflow: hidden; 
    }

    /* Animasi Tombol */
    .transition-3d { transition: all 0.2s ease; }
    .transition-3d:hover { 
        transform: translateY(-2px); 
        box-shadow: 0 8px 15px rgba(13, 110, 253, 0.2) !important; 
    }
</style>
@endsection