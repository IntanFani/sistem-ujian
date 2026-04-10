@extends('layouts.admin')

@section('title', 'Manajemen Pelaksanaan Ujian')

@section('content')
<div class="container-fluid py-2">

    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div class="d-flex align-items-center">
            <div class="bg-success bg-opacity-10 p-3 rounded-circle me-3 d-flex align-items-center justify-content-center"
                style="width: 48px; height: 48px;">
                <i class="bi bi-pc-display-horizontal fs-4 text-success"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-1 text-dark" style="letter-spacing: 0.5px;">PELAKSANAAN UJIAN</h4>
                <p class="text-muted small mb-0">Kontrol status ujian, monitoring, dan kelola data CBT.</p>
            </div>
        </div>
        <a href="{{ route('admin.exams.create') }}"
            class="btn btn-success rounded-3 shadow-sm d-flex align-items-center px-3 py-2 transition-3d">
            <i class="bi bi-plus-lg me-2"></i>
            <span class="fw-bold small">BUAT UJIAN BARU</span>
        </a>
    </div>

    {{-- Tabel Data --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 custom-table">
                <thead class="bg-light text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-4 py-3 border-0" style="width: 5%">No</th>
                        <th class="py-3 border-0" style="width: 22%">Mata Pelajaran & Guru</th>
                        <th class="py-3 border-0" style="width: 18%">Judul Ujian</th>
                        <th class="py-3 border-0 text-center" style="width: 8%">Kelas</th>
                        <th class="py-3 border-0 text-center" style="width: 12%">Token</th>
                        <th class="py-3 border-0 text-center" style="width: 10%">Status</th>
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
                                    <i class="bi bi-person-badge me-1 text-primary"></i>
                                    {{ $exam->guru->user->name ?? 'Nama Guru' }}
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
                                @if ($exam->token)
                                    <span class="badge rounded-pill fw-bold font-monospace bg-warning bg-opacity-10 text-warning border border-warning px-3 py-2 fs-6 shadow-sm" style="letter-spacing: 2px;">
                                        {{ $exam->token }}
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted px-3 py-2 border rounded-pill">Belum Ada</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($exam->status == 'aktif')
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
                                    {{-- Tombol Kelola Soal --}}
                                    <a href="{{ route('admin.exams.questions', $exam->id) }}" class="btn btn-sm btn-primary rounded-3 transition-3d" title="Kelola Soal">
                                        <i class="bi bi-list-check"></i>
                                    </a>

                                    {{-- Tombol Monitoring --}}
                                    <a href="{{ route('admin.exams.monitor', $exam->id) }}" class="btn btn-sm btn-info text-white rounded-3 transition-3d" title="Monitoring Ujian">
                                        <i class="bi bi-display"></i>
                                    </a>

                                    {{-- Tombol On/Off --}}
                                    <form action="{{ route('admin.exams.toggle-status', $exam->id) }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $exam->status == 'aktif' ? 'btn-danger' : 'btn-success' }} rounded-3 transition-3d" title="{{ $exam->status == 'aktif' ? 'Tutup Ujian' : 'Buka Ujian' }}">
                                            <i class="bi bi-power"></i>
                                        </button>
                                    </form>

                                    {{-- Dropdown Aksi Lainnya --}}
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border rounded-3 transition-3d" type="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 small">
                                            <li>
                                                <a class="dropdown-item py-2" href="{{ route('admin.exams.edit', $exam->id) }}">
                                                    <i class="bi bi-pencil-square me-2 text-warning"></i> Edit Detail
                                                </a>
                                            </li>
                                            <li>
                                                <form action="{{ route('admin.exams.generate-token', $exam->id) }}" method="POST" class="m-0">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item py-2">
                                                        <i class="bi bi-arrow-repeat me-2 text-primary"></i> Perbarui Token
                                                    </button>
                                                </form>
                                            </li>
                                            <li><hr class="dropdown-divider opacity-50"></li>
                                            <li>
                                                <button type="button" class="dropdown-item py-2 text-danger" onclick="deleteExam({{ $exam->id }})">
                                                    <i class="bi bi-trash3 me-2"></i> Hapus Ujian
                                                </button>
                                                <form id="delete-form-{{ $exam->id }}" action="{{ route('admin.exams.destroy', $exam->id) }}" method="POST" style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">Belum ada data ujian.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .bg-light { background-color: #f8fafc !important; }
    .transition-3d { transition: all 0.2s ease; }
    .transition-3d:hover { transform: translateY(-2px); }
    .custom-table th { border-bottom: 2px solid #e2e8f0 !important; }
    .custom-table td { border-bottom: 1px solid #f1f5f9; padding-top: 1rem; padding-bottom: 1rem; }
    .transition-3d-row:hover { background-color: #f8fafc !important; }
</style>
@endsection

@section('scripts')
<script>
    function deleteExam(id) {
        Swal.fire({
            title: 'Hapus Ujian?',
            text: "Seluruh data soal, sesi, dan nilai akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: { popup: 'rounded-4' }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }

    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            timer: 2500,
            showConfirmButton: false,
            customClass: { popup: 'rounded-4' }
        });
    @endif
</script>
@endsection