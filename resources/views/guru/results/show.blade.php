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
            <div class="me-3 d-flex justify-content-end">
                <form action="{{ route('guru.exams.reset-all', $exam->id) }}" method="POST"
                    onsubmit="return confirm('PERINGATAN KERAS! Anda akan menghapus SELURUH jawaban siswa untuk ujian ini. Semua siswa harus mengulang dari awal. Lanjutkan?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="me-3 btn btn-danger rounded-pill shadow-sm">
                        <i class="bi bi-trash3-fill me-1"></i> Reset Semua Sesi Ujian
                    </button>
                </form>
                <a href="{{ route('guru.exams.export-excel', $exam->id) }}" class="btn btn-outline-success rounded-pill px-4">
                    <i class="bi bi-file-earmark-excel me-1"></i> Download Excel
                </a>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-5">
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
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($results as $index => $res)
                            <tr>
                                <td class="ps-4 text-muted small">{{ $index + 1 }}</td>
                                <td>
                                    <div class="fw-bold text-dark">
                                        {{ $res->user->siswa->nama ?? 'Nama Tidak Ditemukan' }}
                                    </div>
                                </td>
                                <td class="text-center small text-muted">
                                    {{ $res->user->siswa->nisn ?? '-' }}
                                </td>
                                <td class="text-center">
                                    @if ($res->completed_at)
                                        {{-- Jika sudah ada jam selesainya --}}
                                        <span class="badge bg-success rounded-pill px-3 shadow-sm">
                                            <i class="bi bi-check-circle me-1"></i> Selesai
                                        </span>
                                        <div class="small text-muted mt-1" style="font-size: 0.7rem;">
                                            @if ($res->completed_at)
                                                @php
                                                    $awal = \Carbon\Carbon::parse($res->started_at);
                                                    $akhir = \Carbon\Carbon::parse($res->completed_at);
                                                    $durasi = $awal->diffInMinutes($akhir);
                                                @endphp
                                                <small class="text-muted">{{ $durasi }} Menit</small>
                                            @endif
                                        </div>
                                    @else
                                        {{-- Jika belum klik selesai --}}
                                        <span class="badge bg-warning text-dark rounded-pill px-3 shadow-sm">
                                            <i class="bi bi-clock-history me-1"></i> Mengerjakan
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="fs-5 fw-bold {{ $res->score >= 75 ? 'text-success' : 'text-danger' }}">
                                        {{ $res->score ?? 0 }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('guru.exams.reset-session', $res->id) }}" method="POST"
                                        onsubmit="return confirm('Apakah Anda yakin? Semua jawaban siswa ini akan DIHAPUS PERMANEN dan siswa harus mengulang dari awal.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill shadow-sm">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">Belum ada data nilai untuk ujian ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
