@extends('layouts.admin')

@section('title', 'Rekapitulasi Nilai')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold text-dark mb-1">Rekapitulasi Nilai Ujian</h4>
    <p class="text-muted small mb-0">Pilih ujian untuk melihat dan mencetak hasil nilai siswa.</p>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="px-4 py-3 text-muted small" style="width: 5%">NO</th>
                    <th class="py-3 text-muted small" style="width: 25%">MATA PELAJARAN / GURU</th>
                    <th class="py-3 text-muted small" style="width: 25%">JUDUL UJIAN</th>
                    <th class="py-3 text-center text-muted small" style="width: 15%">KELAS</th>
                    <th class="py-3 text-center px-4 text-muted small" style="width: 20%">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($exams as $index => $exam)
                    <tr>
                        <td class="px-4 text-muted fw-medium">{{ $index + 1 }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $exam->subject->name ?? 'Mata Pelajaran' }}</div>
                            <div class="text-muted small"><i class="bi bi-person me-1"></i> {{ $exam->guru->user->name ?? 'Nama Guru' }}</div>
                        </td>
                        <td>
                            <div class="fw-medium text-dark">{{ $exam->title }}</div>
                            <div class="text-muted small mt-1">
                                <i class="bi bi-clock me-1"></i> {{ $exam->duration }} Menit
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-3 py-2 rounded-2">
                                {{ $exam->kelas->nama_kelas ?? '-' }}
                            </span>
                        </td>
                        <td class="text-center px-4">
                            <a href="{{ route('admin.reports.show', $exam->id) }}" class="btn btn-sm btn-primary rounded-3">
                                <i class="bi bi-list-check me-1"></i> Lihat Nilai
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="bi bi-folder-x fs-1 text-muted opacity-50 mb-3 d-block"></i>
                            <h6 class="fw-bold text-dark">Belum Ada Ujian</h6>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection