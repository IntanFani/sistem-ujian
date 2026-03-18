@extends('layouts.admin')

@section('title', 'Monitoring Ujian')

@section('content')
<div class="mb-4">
    <a href="{{ route('guru.exams.index') }}" class="btn btn-light btn-sm rounded-pill mb-3">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-1">Monitoring: {{ $exam->title }}</h4>
            <p class="text-muted small">Token: <span class="badge bg-dark">{{ $exam->token }}</span> | Kelas: {{ $exam->kelas->nama_kelas }}</p>
        </div>
        <div class="text-end">
            <button onclick="location.reload()" class="btn btn-outline-primary rounded-pill">
                <i class="bi bi-arrow-clockwise me-1"></i> Refresh Status
            </button>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 bg-primary text-white">
            <div class="card-body">
                <small>Total Siswa</small>
                <h3 class="fw-bold mb-0">
                    @if($exam->kelas && $exam->kelas->siswas)
                        {{ $exam->kelas->siswas->count() }}
                    </h3><h3>
                    @else
                        0
                    @endif
                </h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 bg-warning text-white">
            <div class="card-body">
                <small>Sedang Mengerjakan</small>
                <h3 class="fw-bold mb-0">{{ $statusSiswa->where('status', 'mengerjakan')->count() }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 bg-success text-white">
            <div class="card-body">
                <small>Sudah Selesai</small>
                <h3 class="fw-bold mb-0">{{ $statusSiswa->where('status', 'selesai')->count() }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">NISN / Nama Siswa</th>
                        <th class="text-center">Mulai Jam</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Nilai Sementara</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($statusSiswa as $session)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold">{{ $session->siswa->nama }}</div>
                            <small class="text-muted">{{ $session->siswa->nisn }}</small>
                        </td>
                        <td class="text-center small">{{ date('H:i:s', strtotime($session->start_time)) }}</td>
                        <td class="text-center">
                            @if($session->status == 'mengerjakan')
                                <span class="badge bg-warning rounded-pill">Mengerjakan</span>
                            @else
                                <span class="badge bg-success rounded-pill">Selesai</span>
                            @endif
                        </td>
                        <td class="text-center fw-bold">{{ $session->score ?? '-' }}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-light text-danger rounded-pill px-3">
                                <i class="bi bi-arrow-repeat me-1"></i> Reset
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">Belum ada siswa yang login ke ujian ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection