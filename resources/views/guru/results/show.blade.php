@extends('layouts.admin')

@section('title', 'Hasil Nilai Siswa')

@section('content')
<div class="mb-4">
    <a href="{{ route('guru.results.index') }}" class="btn btn-light btn-sm rounded-pill mb-3">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-1">Hasil: {{ $exam->title }}</h4>
            <p class="text-muted small">{{ $exam->subject->name }} | Kelas {{ $exam->kelas->nama_kelas }}</p>
        </div>
        <button class="btn btn-outline-success rounded-pill px-4">
            <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
        </button>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">No</th>
                        <th>Nama Siswa</th>
                        <th class="text-center">NISN</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Nilai Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($results as $index => $res)
                    <tr>
                        <td class="ps-4 text-muted small">{{ $index + 1 }}</td>
                        <td><div class="fw-bold text-dark">{{ $res->siswa->nama }}</div></td>
                        <td class="text-center small text-muted">{{ $res->siswa->nisn }}</td>
                        <td class="text-center">
                            <span class="badge {{ $res->status == 'selesai' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }} rounded-pill px-3">
                                {{ ucfirst($res->status) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="fs-5 fw-bold {{ $res->score >= 75 ? 'text-success' : 'text-danger' }}">
                                {{ $res->score ?? 0 }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">Belum ada data nilai untuk ujian ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection