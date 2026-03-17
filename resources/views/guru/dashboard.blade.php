@extends('layouts.admin') {{-- Pakai layout yang sama tapi nanti sidebar-nya kita bedakan --}}

@section('title', 'Dashboard Guru')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-success text-white">
            <h4 class="fw-bold mb-1">Selamat Datang, {{ $guru->nama }}! 👋</h4>
            <p class="mb-0 opacity-75">Panel Guru MTs Al Huda Pamegatan</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3">
            <div class="d-flex align-items-center">
                <div class="bg-primary-subtle text-primary rounded-4 p-3 me-3">
                    <i class="bi bi-journal-text fs-3"></i>
                </div>
                <div>
                    <p class="text-muted small mb-0">Total Soal Anda</p>
                    <h3 class="fw-bold mb-0">{{ $countSoal }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3">
            <div class="d-flex align-items-center">
                <div class="bg-warning-subtle text-warning rounded-4 p-3 me-3">
                    <i class="bi bi-calendar-event fs-3"></i>
                </div>
                <div>
                    <p class="text-muted small mb-0">Ujian Aktif</p>
                    <h3 class="fw-bold mb-0">{{ $countUjian }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <div class="alert alert-light border-0 shadow-sm rounded-4">
        <i class="bi bi-info-circle me-2 text-success"></i>
        Gunakan menu di samping untuk mulai menyusun soal atau melihat hasil ujian siswa Anda.
    </div>
</div>
@endsection