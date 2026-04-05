@extends('layouts.siswa')

@section('title', 'Riwayat Ujian')

@section('content')
    {{-- Header Minimalist --}}
    <div class="mb-4 pb-3 border-bottom">
        <h4 class="fw-bold mb-1 text-success">Riwayat Ujian</h4>
        <p class="text-muted small mb-0">Catatan hasil ujian yang telah kamu selesaikan.</p>
    </div>

    {{-- Grid Minimalist Card --}}
    <div class="row g-4">
        @forelse($sessions as $session)
            <div class="col-md-6 col-lg-4">
                <div class="card border border-light shadow-sm rounded-4 h-100 minimalist-card">
                    <div class="card-body p-4 d-flex flex-column">
                        {{-- Bagian Atas: Mapel, Judul, Tanggal --}}
                        <div class="mb-4">
                            <span class="badge bg-transparent border border-secondary-subtle text-secondary px-2 py-1 rounded-2 fw-medium mb-3">
                                {{ $session->exam->subject->name ?? 'Mata Pelajaran' }}
                            </span>
                            <h5 class="fw-bold text-dark mb-2" style="letter-spacing: -0.5px;">
                                {{ $session->exam->title ?? 'Judul Ujian' }}
                            </h5>
                            <div class="text-muted small d-flex align-items-center">
                                <i class="bi bi-calendar2-check me-2 text-success opacity-75"></i>
                                {{ \Carbon\Carbon::parse($session->completed_at)->translatedFormat('d F Y • H:i') }}
                            </div>
                        </div>
                        
                        {{-- Garis Pemisah Tipis --}}
                        <hr class="border-secondary-subtle opacity-50 my-auto">

                        {{-- Bagian Bawah: Nilai --}}
                        <div class="d-flex justify-content-between align-items-end mt-4">
                            <span class="text-muted" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 600;">
                                Nilai Akhir
                            </span>
                            <span class="fw-bold text-success" style="font-size: 1.5rem; line-height: 0.8; letter-spacing: -1px;">
                                {{ $session->score ?? '0' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="py-5 mx-auto" style="max-width: 400px;">
                    <i class="bi bi-inbox text-muted opacity-25" style="font-size: 4rem;"></i>
                    <h5 class="fw-bold text-dark mt-3">Belum Ada Riwayat</h5>
                    <p class="text-muted small">Kamu belum menyelesaikan ujian apa pun.</p>
                </div>
            </div>
        @endforelse
    </div>

    <style>
        .minimalist-card {
            transition: all 0.3s ease;
            background-color: #ffffff;
        }
        .minimalist-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.04) !important;
            border-color: #e2e8f0 !important;
        }
    </style>
@endsection