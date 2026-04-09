@extends('layouts.admin')

@section('title', 'Manajemen Pelaksanaan Ujian')

@section('content')
<div class="container-fluid py-2">

    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div class="d-flex align-items-center">
            <div class="bg-success bg-opacity-10 p-3 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                <i class="bi bi-pc-display-horizontal fs-4 text-success"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-1 text-dark" style="letter-spacing: 0.5px;">PELAKSANAAN UJIAN</h4>
                <p class="text-muted small mb-0">Kontrol status ujian, monitoring, dan generate token CBT.</p>
            </div>
        </div>
        {{-- Di halaman admin biasanya tidak ada tombol "Buat Ujian" karena itu tugas Guru --}}
    </div>

    {{-- Tabel Data --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 custom-table">
                <thead class="bg-light text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-4 py-3 border-0" style="width: 5%">No</th>
                        <th class="py-3 border-0" style="width: 22%">Mata Pelajaran & Guru</th>
                        <th class="py-3 border-0" style="width: 20%">Judul Ujian</th>
                        <th class="py-3 border-0 text-center" style="width: 10%">Kelas</th>
                        <th class="py-3 border-0 text-center" style="width: 13%">Token</th>
                        <th class="py-3 border-0 text-center" style="width: 12%">Status</th>
                        <th class="pe-4 py-3 border-0 text-center" style="width: 18%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exams as $index => $exam)
                        <tr class="transition-3d-row">
                            <td class="ps-4 text-muted fw-medium">{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-bold text-dark fs-6">{{ $exam->subject->name ?? 'Mata Pelajaran' }}</div>
                                <div class="text-muted small mt-1 d-flex align-items-center">
                                    <i class="bi bi-person-badge me-1 text-primary"></i> {{ $exam->guru->user->name ?? 'Nama Guru' }}
                                </div>
                            </td>
                            <td>
                                <div class="fw-medium text-dark">{{ $exam->title }}</div>
                                <div class="text-muted small mt-1">
                                    <i class="bi bi-stopwatch me-1 text-success"></i> {{ $exam->duration }} Menit
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 rounded-pill fw-medium">
                                    <i class="bi bi-building me-1"></i> {{ $exam->kelas->nama_kelas ?? '-' }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($exam->token)
                                    <span class="badge rounded-pill fw-bold font-monospace bg-warning bg-opacity-10 text-warning border border-warning px-3 py-2 fs-6 shadow-sm" style="letter-spacing: 2px;">
                                        {{ $exam->token }}
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted px-3 py-2 border rounded-pill">Belum Ada</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($exam->status == 'aktif')
                                    <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-medium">
                                        <i class="bi bi-unlock-fill me-1"></i> Aktif
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill fw-medium">
                                        <i class="bi bi-lock-fill me-1"></i> Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center gap-2">
                                    {{-- Tombol Monitor --}}
                                    <a href="{{ route('admin.exams.monitor', $exam->id) }}" class="btn btn-sm btn-info text-white rounded-3 transition-3d d-flex align-items-center" title="Monitoring Ujian">
                                        <i class="bi bi-display"></i> 
                                    </a>

                                    {{-- Tombol Generate Token --}}
                                    <form action="{{ route('admin.exams.generate-token', $exam->id) }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary rounded-3 transition-3d d-flex align-items-center" title="Generate Token Baru">
                                            <i class="bi bi-arrow-repeat"></i> 
                                        </button>
                                    </form>

                                    {{-- Tombol Buka/Tutup Ujian --}}
                                    <form action="{{ route('admin.exams.toggle-status', $exam->id) }}" method="POST" class="m-0">
                                        @csrf
                                        @if($exam->status == 'aktif')
                                            <button type="submit" class="btn btn-sm btn-danger rounded-3 transition-3d d-flex align-items-center" title="Tutup Ujian">
                                                <i class="bi bi-power"></i> 
                                            </button>
                                        @else
                                            <button type="submit" class="btn btn-sm btn-success rounded-3 transition-3d d-flex align-items-center" title="Buka Ujian">
                                                <i class="bi bi-power"></i> 
                                            </button>
                                        @endif
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="p-4">
                                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                        <i class="bi bi-calendar-x fs-2"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">Belum Ada Ujian</h6>
                                    <p class="text-muted small mb-0">Guru belum membuat jadwal ujian apapun di sistem.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- CSS Tambahan --}}
<style>
    .bg-light { background-color: #f8fafc !important; }
    .transition-3d { transition: all 0.2s ease; }
    .transition-3d:hover { transform: translateY(-2px); }
    .custom-table th { border-bottom: 2px solid #e2e8f0 !important; }
    .custom-table td { border-bottom: 1px solid #f1f5f9; padding-top: 1rem; padding-bottom: 1rem; }
    .transition-3d-row { transition: background-color 0.2s ease; }
    .transition-3d-row:hover { background-color: #f8fafc !important; }
</style>
@endsection