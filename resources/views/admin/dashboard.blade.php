@extends('layouts.admin')

@section('title', 'Dashboard Utama')

@section('content')
<div class="row g-4">
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card card-stats p-3">
            <div class="d-flex align-items-center">
                <div class="p-3 bg-success bg-opacity-10 text-success rounded-3 me-3">
                    <i class="bi bi-book fs-4"></i>
                </div>
                <div>
                    <p class="text-muted mb-0 small">Mata Pelajaran</p>
                    <h5 class="fw-bold mb-0">12</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-lg-3">
        <div class="card card-stats p-3">
            <div class="d-flex align-items-center">
                <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-3 me-3">
                    <i class="bi bi-people fs-4"></i>
                </div>
                <div>
                    <p class="text-muted mb-0 small">Total Siswa</p>
                    <h5 class="fw-bold mb-0">120</h5>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <div class="card border-0 shadow-sm p-4 rounded-4">
        <h5 class="fw-bold">Selamat Datang, Fani!</h5>
        <p class="text-muted">Ini adalah pusat kendali sistem CBT MTs Al Huda Pamegatan. Silakan gunakan navigasi di sebelah kiri untuk mengelola data ujian.</p>
    </div>
</div>
@endsection