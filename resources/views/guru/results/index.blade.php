@extends('layouts.admin')

@section('title', 'Daftar Hasil Ujian')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">Hasil Ujian Siswa</h4>
    <p class="text-muted small">Pilih ujian untuk melihat rekap nilai lengkap.</p>
</div>

<div class="row g-3">
    @forelse($exams as $exam)
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="badge bg-primary-subtle text-primary">{{ $exam->subject->name }}</span>
                    <small class="text-muted">{{ $exam->questions_count }} Soal</small>
                </div>
                <h6 class="fw-bold">{{ $exam->title }}</h6>
                <p class="text-muted small mb-3"><i class="bi bi-people me-1"></i> Kelas: {{ $exam->kelas->nama_kelas }}</p>
                
                <div class="d-grid">
                    {{-- TOMBOL INI YANG MENGARAH KE ROUTE SHOW --}}
                    <a href="{{ route('guru.results.show', $exam->id) }}" class="btn btn-primary rounded-pill btn-sm">
                        <i class="bi bi-eye me-1"></i> Lihat Rekap Nilai
                    </a>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5">
        <p class="text-muted">Belum ada data ujian.</p>
    </div>
    @endforelse
</div>
@endsection