@extends('layouts.admin')

@section('title', 'Dashboard Utama')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
            <div class="d-flex align-items-center">
                <div class="icon-box bg-success-subtle text-success rounded-4 p-3 me-3">
                    <i class="bi bi-book fs-3"></i>
                </div>
                <div>
                    <p class="text-muted small mb-1">Mata Pelajaran</p>
                    <h3 class="fw-bold mb-0">{{ $countSubject }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
            <div class="d-flex align-items-center">
                <div class="icon-box bg-warning-subtle text-warning rounded-4 p-3 me-3">
                    <i class="bi bi-person-badge fs-3"></i>
                </div>
                <div>
                    <p class="text-muted small mb-1">Total Guru</p>
                    <h3 class="fw-bold mb-0">{{ $countGuru }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
            <div class="d-flex align-items-center">
                <div class="icon-box bg-primary-subtle text-primary rounded-4 p-3 me-3">
                    <i class="bi bi-people fs-3"></i>
                </div>
                <div>
                    <p class="text-muted small mb-1">Total Siswa</p>
                    <h3 class="fw-bold mb-0">{{ $countSiswa }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
            <div class="d-flex align-items-center">
                <div class="icon-box bg-danger-subtle text-danger rounded-4 p-3 me-3">
                    <i class="bi bi-door-open fs-3"></i>
                </div>
                <div>
                    <p class="text-muted small mb-1">Total Kelas</p>
                    <h3 class="fw-bold mb-0">{{ $countKelas }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h5 class="fw-bold mb-3">Informasi Sistem</h5>
            <div class="alert alert-info border-0 rounded-3 mb-0">
                <i class="bi bi-info-circle me-2"></i> 
                Halo <strong>{{ Auth::user()->name }}</strong>, data statistik di atas sudah terhubung langsung dengan database. 
                Gunakan menu di samping untuk mengelola data.
            </div>
        </div>
    </div>
</div>
@endsection