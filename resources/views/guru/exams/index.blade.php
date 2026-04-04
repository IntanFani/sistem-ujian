@extends('layouts.admin')

@section('title', 'Manajemen Ujian')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1 text-dark">MANAJEMEN UJIAN</h4>
                <p class="text-muted small mb-0">Halaman pengelolaan jadwal ujian siswa MTs Al Huda.</p>
            </div>
            <a href="{{ route('guru.exams.create') }}"
                class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold border-0 transition-3d">
                <i class="bi bi-plus-lg me-2"></i>Buat Ujian Baru
            </a>
        </div>

        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                    <div class="d-flex align-items-center">
                        <div class="p-3 rounded-4 me-3" style="background: #eef2ff;">
                            <i class="bi bi-file-earmark-text text-primary fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 small fw-bold text-uppercase">Total Ujian</h6>
                            <h4 class="fw-bold mb-0">{{ $exams->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small fw-bold">
                            <tr>
                                <th class="ps-4 py-3 border-0">JUDUL UJIAN</th>
                                <th class="py-3 border-0 text-center">TOKEN</th>
                                <th class="py-3 border-0">DURASI</th>
                                <th class="text-end pe-4 py-3 border-0">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($exams as $exam)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark">{{ $exam->title }}</div>
                                        <small class="text-muted">
                                            <i class="bi bi-people me-1 text-primary"></i>
                                            {{ $exam->kelas->nama_kelas ?? 'Tanpa Kelas' }}
                                            <span class="mx-2">•</span>
                                            <i class="bi bi-collection-play me-1"></i> {{ $exam->questions->count() }} Soal
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="badge rounded-pill fw-bold font-monospace bg-light text-dark border px-3 py-2">
                                            {{ $exam->token }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="text-muted small">
                                            <i class="bi bi-clock me-1 text-primary"></i> {{ $exam->duration }} Menit
                                        </div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('guru.exams.questions', $exam->id) }}"
                                                class="btn btn-sm btn-outline-primary rounded-3" title="Kelola Soal">
                                                <i class="bi bi-list-task"></i>
                                            </a>
                                            <a href="{{ route('guru.exams.edit', $exam->id) }}"
                                                class="btn btn-sm btn-outline-warning rounded-3">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-3"
                                                onclick="alertHapusUjian({{ $exam->id }})">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </div>
                                        <form id="delete-exam-{{ $exam->id }}"
                                            action="{{ route('guru.exams.destroy', $exam->id) }}" method="POST"
                                            class="d-none">
                                            @csrf @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <p class="text-muted mb-0">Belum ada data ujian.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        window.alertHapusUjian = function(id) {
            Swal.fire({
                title: 'Hapus Ujian?',
                text: "Data soal dan nilai akan ikut terhapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-exam-' + id).submit();
                }
            });
        };
    </script>
@endsection
