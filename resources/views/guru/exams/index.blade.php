@extends('layouts.admin') 

@section('title', 'Manajemen Ujian')

@section('content')
    <div class="container-fluid">
        {{-- Header & Tombol Buat Ujian --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="bi bi-journal-text fs-4 text-primary"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-dark" style="letter-spacing: 0.5px;">MANAJEMEN UJIAN</h4>
                    <p class="text-muted small mb-0">Halaman pengelolaan jadwal ujian siswa MTs Al Huda.</p>
                </div>
            </div>
            <a href="{{ route('guru.exams.create') }}" class="btn btn-success rounded-pill px-4 shadow-sm fw-bold border-0 transition-3d">
                <i class="bi bi-file-earmark-plus me-2"></i>Buat Ujian Baru
            </a>
        </div>

        {{-- Widget Total Ujian --}}
        <!-- <div class="row mb-4">
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
        </div> -->

        {{-- Tabel Data Ujian (Desain Baru) --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small fw-bold">
                    <tr>
                        <th class="ps-4 py-3 border-0" style="width: 5%">NO</th>
                        <th class="py-3 border-0" style="width: 20%">MATA PELAJARAN</th>
                        <th class="py-3 border-0" style="width: 20%">JUDUL UJIAN</th>
                        <th class="py-3 border-0 text-center" style="width: 10%">TOKEN</th>
                        <th class="py-3 border-0 text-center" style="width: 15%">INFO</th>
                        <th class="py-3 border-0 text-center" style="width: 10%">STATUS</th>
                        <th class="text-center pe-4 py-3 border-0" style="width: 20%">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exams as $index => $exam)
                        <tr>
                            <td class="ps-4 text-muted">{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $exam->subject->name ?? 'Mapel' }}</div>
                                <div class="text-muted small mt-1">
                                    <i class="bi bi-building me-1"></i> Kelas {{ $exam->kelas->nama_kelas ?? '-' }}
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $exam->title }}</div>
                            </td>
                            <td class="text-center">
                                @if($exam->token)
                                    <span class="badge rounded-pill fw-bold font-monospace bg-light text-dark border px-3 py-2" style="letter-spacing: 1px;">
                                        {{ $exam->token }}
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted px-2 py-1 border">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="text-dark small"><i class="bi bi-clock me-1 text-primary"></i> {{ $exam->duration }} Menit</div>
                                <div class="text-muted small mt-1"><i class="bi bi-collection-play me-1"></i> {{ $exam->questions->count() ?? 0 }} Soal</div>
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
                                    {{-- Tombol Kelola Soal (Lebih Jelas) --}}
                                    <a href="{{ route('guru.exams.questions', $exam->id) }}" class="btn btn-sm btn-primary rounded-3 shadow-sm d-flex align-items-center" title="Kelola Soal">
                                        <i class="bi bi-list-task me-1"></i>
                                    </a>

                                    {{-- Tombol Edit --}}
                                    <a href="{{ route('guru.exams.edit', $exam->id) }}" class="btn btn-sm btn-outline-warning rounded-3" title="Edit Ujian">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    {{-- Tombol Hapus --}}
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-3" onclick="alertHapusUjian({{ $exam->id }})" title="Hapus Ujian">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                    
                                    {{-- Form Tersembunyi untuk Delete --}}
                                    <form id="delete-exam-{{ $exam->id }}" action="{{ route('guru.exams.destroy', $exam->id) }}" method="POST" class="d-none">
                                        @csrf 
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            {{-- Colspan diubah jadi 7 karena nambah kolom TOKEN --}}
                            <td colspan="7" class="text-center py-5">
                                <p class="text-muted mb-0">Belum ada data ujian.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        </div>
    </div>
@endsection

@section('scripts')
    {{-- Script SweetAlert2 aslimu --}}
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