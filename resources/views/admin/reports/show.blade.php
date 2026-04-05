@extends('layouts.admin')

@section('title', 'Detail Nilai: ' . $exam->title)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-light border mb-2 rounded-3 text-muted">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h4 class="fw-bold text-dark mb-1">Laporan Nilai Ujian</h4>
    </div>
    <div>
        {{-- Tombol Export Excel --}}
        <a href="{{ route('admin.reports.export-excel', $exam->id) }}" class="btn btn-success rounded-3 shadow-sm">
            <i class="bi bi-file-earmark-excel me-1"></i> Download Excel
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-body p-4 p-md-5">
        
        {{-- Info Ujian --}}
        <div class="row mb-4 border-bottom pb-4">
            <div class="col-md-6 mb-3 mb-md-0">
                <table class="table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted" width="120">Mata Pelajaran</td>
                        <td width="20">:</td>
                        <td class="fw-bold">{{ $exam->subject->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Guru Pengampu</td>
                        <td>:</td>
                        <td class="fw-bold">{{ $exam->guru->user->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Kelas</td>
                        <td>:</td>
                        <td class="fw-bold">{{ $exam->kelas->nama_kelas ?? '-' }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted" width="120">Judul Ujian</td>
                        <td width="20">:</td>
                        <td class="fw-bold">{{ $exam->title }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Durasi Ujian</td>
                        <td>:</td>
                        <td class="fw-bold">{{ $exam->duration }} Menit</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Total Selesai</td>
                        <td>:</td>
                        <td class="fw-bold">{{ $sessions->count() }} Siswa</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Tabel Daftar Nilai (Web View) --}}
        <div class="table-responsive rounded-3 border">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3 text-muted small fw-semibold" style="width: 5%">NO</th>
                        <th class="py-3 text-muted small fw-semibold" style="width: 35%">NAMA SISWA</th>
                        <th class="py-3 text-center text-muted small fw-semibold" style="width: 20%">WAKTU MULAI</th>
                        <th class="py-3 text-center text-muted small fw-semibold" style="width: 20%">WAKTU SELESAI</th>
                        <th class="py-3 text-center text-muted small fw-semibold" style="width: 20%">NILAI AKHIR</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($sessions as $index => $session)
                        <tr>
                            <td class="px-4 text-muted">{{ $index + 1 }}</td>
                            <td class="fw-medium text-dark">{{ $session->user->name ?? 'Nama Siswa' }}</td>
                            <td class="text-center text-muted small">
                                {{ \Carbon\Carbon::parse($session->started_at)->format('H:i:s') }} WIB
                            </td>
                            <td class="text-center text-muted small">
                                {{ \Carbon\Carbon::parse($session->completed_at)->format('H:i:s') }} WIB
                            </td>
                            <td class="text-center fw-medium text-dark">
                                {{ $session->score }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
                                Belum ada siswa yang menyelesaikan ujian ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection