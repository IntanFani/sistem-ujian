@extends('layouts.admin')

@section('title', 'Manajemen Ujian')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">Manajemen Pelaksanaan Ujian</h4>
            <p class="text-muted small mb-0">Kontrol status ujian dan token untuk peserta CBT MTs Al Huda.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="px-4 py-3 text-muted small" style="width: 5%">NO</th>
                    <th class="py-3 text-muted small" style="width: 22%">MATA PELAJARAN / GURU</th>
                    <th class="py-3 text-muted small" style="width: 20%">JUDUL UJIAN</th>
                    <th class="py-3 text-center text-muted small" style="width: 10%">KELAS</th>
                    <th class="py-3 text-center text-muted small" style="width: 13%">TOKEN</th>
                    <th class="py-3 text-center text-muted small" style="width: 12%">STATUS</th>
                    <th class="py-3 text-center px-4 text-muted small" style="width: 18%">AKSI</th>
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
                        <td class="text-center">
                            @if($exam->token)
                                <span class="badge bg-dark bg-opacity-10 text-dark px-3 py-2 rounded-2 fw-bold" style="letter-spacing: 2px; font-size: 1rem;">
                                    {{ $exam->token }}
                                </span>
                            @else
                                <span class="badge bg-light text-muted px-3 py-2 border">Belum Ada</span>
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
                        <td class="text-center px-4">
                            <div class="d-flex justify-content-center gap-2">
                                {{-- Tombol Monitor --}}
                                <a href="{{ route('admin.exams.monitor', $exam->id) }}" class="btn btn-sm btn-info text-white rounded-3" title="Monitoring Ujian">
                                    <i class="bi bi-display"></i> 
                                </a>

                                {{-- Tombol Generate Token --}}
                                <form action="{{ route('admin.exams.generate-token', $exam->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary rounded-3" title="Generate Token Baru">
                                        <i class="bi bi-arrow-repeat"></i> 
                                    </button>
                                </form>

                                {{-- Tombol Buka/Tutup Ujian --}}
                                <form action="{{ route('admin.exams.toggle-status', $exam->id) }}" method="POST">
                                    @csrf
                                    @if($exam->status == 'aktif')
                                        <button type="submit" class="btn btn-sm btn-danger rounded-3" title="Tutup Ujian">
                                            <i class="bi bi-power"></i> 
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-sm btn-success rounded-3" title="Buka Ujian">
                                            <i class="bi bi-power"></i> 
                                        </button>
                                    @endif
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        {{-- colspan diubah jadi 7 karena ada penambahan kolom KELAS --}}
                        <td colspan="7" class="text-center py-5">
                            <i class="bi bi-folder-x fs-1 text-muted opacity-50 mb-3 d-block"></i>
                            <h6 class="fw-bold text-dark">Belum Ada Ujian</h6>
                            <p class="text-muted small">Guru belum membuat jadwal ujian apapun.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
@endsection
