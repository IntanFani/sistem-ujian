@extends('layouts.admin')

@section('title', 'Analisis Soal - ' . $exam->title)

@section('content')
<div class="container-fluid py-2">

    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-3 border-bottom gap-3">
        <div class="d-flex align-items-center">
            <a href="{{ route('guru.results.show', $exam->id) }}" class="btn btn-white border shadow-sm rounded-circle d-flex align-items-center justify-content-center me-3 transition-3d text-secondary" style="width: 45px; height: 45px;" title="Kembali ke Rekap Nilai">
                <i class="bi bi-arrow-left fs-5"></i>
            </a>
            <div>
                <h4 class="fw-bold mb-1 text-dark" style="letter-spacing: 0.5px;">ANALISIS BUTIR SOAL</h4>
                <div class="d-flex align-items-center text-muted small mt-1 flex-wrap gap-2">
                    <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1 rounded-2 fw-medium">{{ $exam->title }}</span>
                    <span><i class="bi bi-people me-1"></i> {{ $total_peserta }} Siswa Mengerjakan</span>
                </div>
            </div>
        </div>
        
        <button class="btn btn-outline-primary rounded-pill px-4 py-2 shadow-sm fw-bold d-none d-md-block" onclick="window.print()">
            <i class="bi bi-printer-fill me-2"></i> Cetak Analisis
        </button>
    </div>

    {{-- Daftar Analisis per Soal --}}
    <div class="row g-4">
        @forelse($questions as $index => $q)
            @php
                // Hitung total yang menjawab soal ini
                $total_dijawab = $q->answers_count;
                // Hitung persentase benar
                $persen_benar = $total_dijawab > 0 ? round(($q->benar_count / $total_dijawab) * 100) : 0;
                
                // Tentukan warna badge tingkat kesulitan
                if($persen_benar >= 70) {
                    $badge_color = 'success'; $tingkat = 'Mudah';
                } elseif($persen_benar >= 40) {
                    $badge_color = 'warning'; $tingkat = 'Sedang';
                } else {
                    $badge_color = 'danger'; $tingkat = 'Sulit';
                }
            @endphp

            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-4 p-md-5">
                        <div class="row align-items-center">
                            
                            {{-- Teks Pertanyaan (Kiri) --}}
                            <div class="col-lg-7 mb-4 mb-lg-0 border-end-lg pe-lg-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-bold">SOAL #{{ $index + 1 }}</span>
                                    <span class="badge bg-{{ $badge_color }}-subtle text-{{ $badge_color }} px-3 py-2 rounded-pill fw-bold">
                                        Kategori: {{ $tingkat }}
                                    </span>
                                </div>
                                <div class="text-dark fs-6 mb-3" style="line-height: 1.6;">
                                    {!! $q->question_text !!}
                                </div>
                                <div class="p-3 bg-success bg-opacity-10 border border-success border-opacity-25 rounded-3 d-inline-block">
                                    <span class="text-success small fw-bold text-uppercase d-block mb-1">Kunci Jawaban:</span>
                                    <span class="fw-bold fs-5 text-dark">{{ strtoupper($q->jawaban_benar) }}. {{ $q->{'opsi_' . $q->jawaban_benar} }}</span>
                                </div>
                            </div>

                            {{-- Statistik Jawaban (Kanan) --}}
                            <div class="col-lg-5 ps-lg-4">
                                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-bar-chart-fill text-primary me-2"></i>Distribusi Jawaban Siswa</h6>
                                
                                {{-- Bar Persentase Benar Keseluruhan --}}
                                <div class="mb-4 pb-3 border-bottom">
                                    <div class="d-flex justify-content-between small mb-1">
                                        <span class="fw-medium text-muted">Menjawab Benar</span>
                                        <span class="fw-bold text-{{ $badge_color }}">{{ $persen_benar }}% ({{ $q->benar_count }} Siswa)</span>
                                    </div>
                                    <div class="progress" style="height: 10px; border-radius: 10px;">
                                        <div class="progress-bar bg-{{ $badge_color }}" role="progressbar" style="width: {{ $persen_benar }}%;"></div>
                                    </div>
                                </div>

                                {{-- Rincian Pilihan A, B, C, D, E --}}
                                <div class="d-flex flex-column gap-2">
                                    @foreach(['a', 'b', 'c', 'd', 'e'] as $opt)
                                        @php
                                            $dipilih = $q->{'jawab_'.$opt.'_count'} ?? 0;
                                            $persen_opsi = $total_dijawab > 0 ? round(($dipilih / $total_dijawab) * 100) : 0;
                                            $is_kunci = $q->jawaban_benar == $opt;
                                        @endphp
                                        <div class="d-flex align-items-center">
                                            <div class="fw-bold text-muted me-3" style="width: 20px;">{{ strtoupper($opt) }}</div>
                                            <div class="progress flex-grow-1 bg-light me-3" style="height: 25px; border-radius: 8px;">
                                                <div class="progress-bar {{ $is_kunci ? 'bg-success' : 'bg-secondary opacity-50' }}" 
                                                     role="progressbar" 
                                                     style="width: {{ $persen_opsi }}%; text-align: left; padding-left: 10px;">
                                                    @if($persen_opsi > 5) <span class="fw-bold">{{ $persen_opsi }}%</span> @endif
                                                </div>
                                            </div>
                                            <div class="small fw-medium text-muted" style="width: 60px; text-align: right;">{{ $dipilih }} Siswa</div>
                                        </div>
                                    @endforeach
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 bg-white rounded-4 shadow-sm">
                <h5 class="text-muted">Belum ada data analisis.</h5>
                <p class="small text-secondary">Pastikan ujian sudah dikerjakan oleh siswa.</p>
            </div>
        @endforelse
    </div>
</div>

<style>
    @media (min-width: 992px) {
        .border-end-lg { border-right: 1px dashed #cbd5e1; }
    }
    @media print {
        #sidebar, #navbar, .btn { display: none !important; }
        #content { margin-left: 0 !important; width: 100% !important; padding: 0 !important; }
        .card { box-shadow: none !important; border: 1px solid #000 !important; page-break-inside: avoid; }
    }
</style>
@endsection