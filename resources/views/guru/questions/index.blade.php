@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold">Kelola Soal Ujian</h3>
            <p class="text-muted">{{ $exam->title }} | {{ $exam->subject->name }} ({{ $exam->kelas->name }})</p>
        </div>
        <a href="{{ route('guru.exams.index') }}" class="btn btn-secondary rounded-pill">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalTambahSoal">
                <i class="bi bi-plus-lg"></i> Tambah Soal Baru
            </button>
        </div>
    </div>

    @foreach($questions as $index => $q)
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <h5 class="fw-bold">Soal No. {{ $index + 1 }}</h5>
                <form action="{{ route('guru.questions.destroy', $q->id) }}" method="POST">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger border-0"><i class="bi bi-trash"></i></button>
                </form>
            </div>
            <p>{!! $q->question_text !!}</p>
            <div class="row g-2">
                <div class="col-md-6"><span class="badge bg-light text-dark border">A</span> {{ $q->opsi_a }}</div>
                <div class="col-md-6"><span class="badge bg-light text-dark border">B</span> {{ $q->opsi_b }}</div>
                <div class="col-md-6"><span class="badge bg-light text-dark border">C</span> {{ $q->opsi_c }}</div>
                <div class="col-md-6"><span class="badge bg-light text-dark border">D</span> {{ $q->opsi_d }}</div>
                <div class="col-md-6"><span class="badge bg-light text-dark border">E</span> {{ $q->opsi_e }}</div>
            </div>
            <div class="mt-3">
                <span class="badge bg-success">Kunci: {{ strtoupper($q->jawaban_benar) }}</span>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Modal Tambah Soal (Singkat saja buat demo) --}}
@include('guru.questions.modal_create')

@endsection