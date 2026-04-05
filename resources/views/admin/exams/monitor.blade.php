@extends('layouts.admin')

@section('title', 'Monitoring Ujian')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('admin.exams.index') }}" class="btn btn-sm btn-light border mb-2 rounded-3 text-muted">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <h4 class="fw-bold text-dark mb-1">Live Monitoring: {{ $exam->title }}</h4>
            <p class="text-muted small mb-0">Mata Pelajaran: <span class="fw-bold">{{ $exam->subject->name ?? '-' }}</span> |
                Token: <span class="badge bg-dark bg-opacity-10 text-dark">{{ $exam->token ?? 'Belum Ada' }}</span></p>
        </div>
        <div class="d-flex gap-2">
            {{-- Tombol Reset Masal --}}
            @if ($sessions->count() > 0)
                <form action="{{ route('admin.exams.reset-all-sessions', $exam->id) }}" method="POST"
                    onsubmit="return confirm('BAHAYA: Yakin ingin mereset SEMUA peserta di ujian ini? Seluruh jawaban mereka yang sudah masuk akan terhapus!');">
                    @csrf
                    <button type="submit" class="btn btn-danger rounded-3 shadow-sm">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset Semua
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3 text-muted small" style="width: 5%">NO</th>
                        <th class="py-3 text-muted small" style="width: 25%">NAMA SISWA</th>
                        <th class="py-3 text-center text-muted small" style="width: 20%">WAKTU MULAI</th>
                        <th class="py-3 text-center text-muted small" style="width: 15%">NILAI</th>
                        <th class="py-3 text-center text-muted small" style="width: 15%">STATUS</th>
                        <th class="py-3 text-center px-4 text-muted small" style="width: 20%">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sessions as $index => $session)
                        <tr>
                            <td class="px-4 text-muted fw-medium">{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $session->user->name ?? 'Nama Siswa' }}</div>
                                <div class="text-muted small">Kelas: {{ $session->user->siswa->kelas->nama_kelas ?? '-' }}
                                </div>
                            </td>
                            <td class="text-center text-muted small">
                                {{ \Carbon\Carbon::parse($session->created_at)->format('H:i:s') }} WIB
                            </td>
                            <td class="text-center">
                                @if ($session->completed_at)
                                    <span class="fw-bold text-success fs-5">{{ $session->score ?? '0' }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($session->completed_at)
                                    <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-medium">
                                        <i class="bi bi-check2-circle me-1"></i> Selesai
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning px-3 py-2 rounded-pill fw-medium">
                                        <i class="bi bi-hourglass-split me-1"></i> Mengerjakan
                                    </span>
                                @endif
                            </td>
                            <td class="text-center px-4">
                                <form action="{{ route('admin.exams.reset-session', $session->id) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin mereset sesi siswa ini? Jawaban yang sudah tersimpan mungkin akan hilang dan siswa harus mengulang dari awal.');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-3"
                                        title="Reset Sesi Ujian">
                                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-people text-muted opacity-50 mb-3 d-block" style="font-size: 2.5rem;"></i>
                                <h6 class="fw-bold text-dark">Belum Ada Peserta</h6>
                                <p class="text-muted small">Belum ada siswa yang mulai mengerjakan ujian ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script>
        // Halaman akan otomatis me-refresh dirinya sendiri setiap 30 detik (30000 milidetik)
        setInterval(function() {
            window.location.reload();
        }, 30000);
    </script>
@endsection
