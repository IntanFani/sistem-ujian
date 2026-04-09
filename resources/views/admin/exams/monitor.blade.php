@extends('layouts.admin')

@section('title', 'Monitoring Ujian')

@section('content')
<div class="container-fluid py-2">

    {{-- Header & Navigasi --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div class="d-flex align-items-center">
            <a href="{{ route('admin.exams.index') }}" class="btn btn-white border shadow-sm rounded-circle d-flex align-items-center justify-content-center me-3 transition-3d text-secondary" style="width: 48px; height: 48px;" title="Kembali ke Daftar Ujian">
                <i class="bi bi-arrow-left fs-5"></i>
            </a>
            <div>
                <h4 class="fw-bold mb-1 text-dark" style="letter-spacing: 0.5px;">LIVE MONITORING</h4>
                <div class="text-muted small mb-0 d-flex align-items-center flex-wrap gap-2 mt-1">
                    <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1 rounded-2 border border-primary-subtle">{{ $exam->subject->name ?? '-' }}</span>
                    <span><i class="bi bi-journal-text me-1"></i> {{ $exam->title }}</span>
                    <span class="badge bg-warning bg-opacity-10 text-warning px-2 py-1 rounded-2 border border-warning fw-monospace" style="letter-spacing: 1px;"><i class="bi bi-key-fill me-1"></i> Token: {{ $exam->token ?? 'Belum Ada' }}</span>
                </div>
            </div>
        </div>
        
        <div class="d-flex gap-2">
            {{-- Tombol Reset Masal --}}
            @if ($sessions->count() > 0)
                <form action="{{ route('admin.exams.reset-all-sessions', $exam->id) }}" method="POST" id="formResetAll" class="m-0">
                    @csrf
                    <button type="button" class="btn btn-danger rounded-pill px-4 py-2 shadow-sm fw-bold border-0 transition-3d d-flex align-items-center" onclick="confirmResetAll()">
                        <i class="bi bi-arrow-counterclockwise me-2"></i> Reset Semua Peserta
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Tabel Data --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold text-dark mb-0"><i class="bi bi-broadcast text-info me-2"></i>Status Pengerjaan Peserta</h6>
            <span class="badge bg-light text-muted border px-3 py-2 rounded-pill shadow-sm"><i class="bi bi-arrow-repeat me-1"></i> Auto-refresh aktif</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 custom-table">
                <thead class="bg-light text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-4 py-3 border-0" style="width: 5%">No</th>
                        <th class="py-3 border-0" style="width: 25%">Nama Siswa</th>
                        <th class="py-3 border-0 text-center" style="width: 20%">Waktu Mulai</th>
                        <th class="py-3 border-0 text-center" style="width: 15%">Nilai Akhir</th>
                        <th class="py-3 border-0 text-center" style="width: 15%">Status</th>
                        <th class="pe-4 py-3 border-0 text-center" style="width: 20%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sessions as $index => $session)
                        <tr class="transition-3d-row">
                            <td class="ps-4 text-muted fw-medium">{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-bold text-dark fs-6">{{ $session->user->name ?? 'Nama Siswa' }}</div>
                                <div class="text-muted small mt-1">
                                    <i class="bi bi-building me-1 text-primary"></i> Kelas {{ $session->user->siswa->kelas->nama_kelas ?? '-' }}
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border px-3 py-2 fw-medium shadow-sm font-monospace">
                                    <i class="bi bi-clock me-1 text-primary"></i> {{ \Carbon\Carbon::parse($session->created_at)->format('H:i:s') }} WIB
                                </span>
                            </td>
                            <td class="text-center">
                                @if ($session->completed_at)
                                    <span class="fw-bold text-success fs-5">{{ $session->score ?? '0' }}</span>
                                @else
                                    <span class="text-muted fw-medium">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($session->completed_at)
                                    <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-medium border border-success-subtle">
                                        <i class="bi bi-check2-circle me-1"></i> Selesai
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning px-3 py-2 rounded-pill fw-medium border border-warning-subtle">
                                        <i class="bi bi-hourglass-split me-1"></i> Mengerjakan
                                    </span>
                                @endif
                            </td>
                            <td class="text-center pe-4">
                                <form action="{{ route('admin.exams.reset-session', $session->id) }}" method="POST" id="formReset-{{ $session->id }}" class="m-0">
                                    @csrf
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-3 transition-3d d-flex align-items-center mx-auto px-3" 
                                        onclick="confirmResetIndividual({{ $session->id }}, '{{ addslashes($session->user->name) }}')"
                                        title="Reset Sesi Ujian Siswa Ini">
                                        <i class="bi bi-arrow-counterclockwise me-2"></i> Reset
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="p-4">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                        <i class="bi bi-people fs-2"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">Belum Ada Peserta Masuk</h6>
                                    <p class="text-muted small mb-0">Halaman ini akan otomatis diperbarui ketika siswa mulai mengerjakan ujian.</p>
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

@section('scripts')
    {{-- Ubah nama section jadi 'scripts' menyesuaikan template yang sebelumnya --}}
    <script>
        // Fitur Auto-Refresh setiap 30 Detik
        let refreshInterval = setInterval(function() {
            window.location.reload();
        }, 30000);

        // SweetAlert untuk Reset Masal
        function confirmResetAll() {
            // Hentikan sementara auto-refresh saat alert muncul
            clearInterval(refreshInterval);
            
            Swal.fire({
                title: 'BAHAYA: Reset Semua Peserta?',
                text: "Seluruh jawaban siswa yang sudah masuk akan terhapus dan mereka harus mengulang dari awal!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Reset Semua!',
                cancelButtonText: 'Batal',
                customClass: { popup: 'rounded-4' }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('formResetAll').submit();
                } else {
                    // Nyalakan lagi auto-refresh jika batal
                    refreshInterval = setInterval(function() { window.location.reload(); }, 30000);
                }
            });
        }

        // SweetAlert untuk Reset Individu
        function confirmResetIndividual(id, nama) {
            clearInterval(refreshInterval);
            
            Swal.fire({
                title: 'Reset Sesi Siswa?',
                text: "Yakin ingin mereset sesi milik " + nama + "? Jawabannya akan terhapus.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Reset',
                cancelButtonText: 'Batal',
                customClass: { popup: 'rounded-4' }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('formReset-' + id).submit();
                } else {
                    refreshInterval = setInterval(function() { window.location.reload(); }, 30000);
                }
            });
        }
    </script>
@endsection