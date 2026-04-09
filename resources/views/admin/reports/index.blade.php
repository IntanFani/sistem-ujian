@extends('layouts.admin')

@section('title', 'Rekapitulasi Nilai')

@section('content')
<div class="container-fluid py-2">

    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div class="d-flex align-items-center">
            <div class="bg-success bg-opacity-10 p-3 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                <i class="bi bi-file-earmark-bar-graph-fill fs-4 text-success"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-1 text-dark" style="letter-spacing: 0.5px;">REKAPITULASI NILAI</h4>
                <p class="text-muted small mb-0">Pilih jadwal ujian untuk melihat dan mencetak hasil nilai peserta.</p>
            </div>
        </div>
    </div>

    {{-- Tabel Data --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 custom-table">
                <thead class="bg-light text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-4 py-3 border-0" style="width: 5%">No</th>
                        <th class="py-3 border-0" style="width: 25%">Mata Pelajaran & Guru</th>
                        <th class="py-3 border-0" style="width: 30%">Judul Ujian</th>
                        <th class="py-3 border-0 text-center" style="width: 15%">Kelas</th>
                        <th class="pe-4 py-3 border-0 text-center" style="width: 25%">Aksi</th>
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
                                    <i class="bi bi-clock me-1 text-success"></i> {{ $exam->duration }} Menit
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 rounded-pill fw-medium">
                                    <i class="bi bi-building me-1"></i> {{ $exam->kelas->nama_kelas ?? '-' }}
                                </span>
                            </td>
                            <td class="text-center pe-4">
                                <a href="{{ route('admin.reports.show', $exam->id) }}" class="btn btn-sm btn-primary rounded-pill px-4 py-2 shadow-sm transition-3d fw-bold d-inline-flex align-items-center">
                                    <i class="bi bi-card-checklist me-2"></i> Lihat Nilai
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="p-4">
                                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                        <i class="bi bi-folder-x fs-2"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">Belum Ada Ujian Tersedia</h6>
                                    <p class="text-muted small mb-0">Guru belum membuat ujian atau belum ada data yang bisa direkap.</p>
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
    .transition-3d:hover { transform: translateY(-2px); box-shadow: 0 5px 10px rgba(13, 110, 253, 0.15) !important; }
    .custom-table th { border-bottom: 2px solid #e2e8f0 !important; }
    .custom-table td { border-bottom: 1px solid #f1f5f9; padding-top: 1rem; padding-bottom: 1rem; }
    .transition-3d-row { transition: background-color 0.2s ease; }
    .transition-3d-row:hover { background-color: #f8fafc !important; }
</style>
@endsection