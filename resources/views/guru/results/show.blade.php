@extends('layouts.admin')

@section('title', 'Rekap Nilai - ' . $exam->title)

@section('content')
    <div class="container-fluid py-2">

        {{-- Header Fleksibel --}}
        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-3 border-bottom gap-3">

            {{-- Kiri: Tombol Back & Judul --}}
            <div class="d-flex align-items-center">
                <a href="{{ route('guru.results.index') }}"
                    class="btn btn-white border shadow-sm rounded-circle d-flex align-items-center justify-content-center me-3 transition-3d text-secondary"
                    style="width: 45px; height: 45px;" title="Kembali">
                    <i class="bi bi-arrow-left fs-5"></i>
                </a>
                <div>
                    <h4 class="fw-bold mb-1 text-dark" style="letter-spacing: 0.5px;">REKAP NILAI SISWA</h4>
                    <div class="d-flex align-items-center text-muted small mt-1 flex-wrap gap-2">
                        <span
                            class="badge bg-primary bg-opacity-10 text-primary px-2 py-1 rounded-2 fw-medium">{{ $exam->title }}</span>
                        <span><i class="bi bi-book me-1"></i> {{ $exam->subject->name ?? '-' }}</span>
                        <span class="d-none d-md-inline">|</span>
                        <span><i class="bi bi-building me-1"></i> Kelas {{ $exam->kelas->nama_kelas ?? '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- Kanan: Tombol Aksi --}}
            <div class="d-flex align-items-center gap-2">
                {{-- Tombol Reset Semua --}}
                <form action="{{ route('guru.exams.reset-all', $exam->id) }}" method="POST" id="formResetAll"
                    class="m-0">
                    @csrf
                    @method('DELETE')
                    <button type="button"
                        class="btn btn-danger btn-sm rounded-pill px-3 py-1 shadow-sm fw-medium transition-3d d-flex align-items-center"
                        onclick="confirmResetAll()">
                        <i class="bi bi-trash3-fill me-1"></i> Reset Semua
                    </button>
                </form>

                {{-- Tombol Excel --}}
                <a href="{{ route('guru.exams.export-excel', $exam->id) }}"
                    class="btn btn-success btn-sm rounded-pill px-3 py-1 shadow-sm fw-medium transition-3d d-flex align-items-center">
                    <i class="bi bi-file-earmark-excel-fill me-1"></i> Download Excel
                </a>

                {{-- Tombol Analisis Soal --}}
                <a href="{{ route('guru.results.analysis', $exam->id) }}"
                    class="btn btn-warning btn-sm text-white rounded-pill px-3 py-1 shadow-sm fw-medium transition-3d d-flex align-items-center">
                    <i class="bi bi-pie-chart-fill me-1"></i> Analisis Soal
                </a>
            </div>
        </div>

        {{-- Tabel Nilai --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small fw-bold text-uppercase">
                        <tr>
                            <th class="ps-4 py-3" style="width: 5%">NO</th>
                            <th class="py-3" style="width: 25%">NAMA SISWA</th>
                            <th class="text-center py-3" style="width: 10%">NISN</th>
                            <th class="text-center py-3" style="width: 15%">STATUS</th>
                            <th class="text-center py-3" style="width: 15%">WAKTU</th>
                            <th class="text-center py-3" style="width: 10%">NILAI</th>
                            <th class="text-center pe-4 py-3" style="width: 20%">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($results as $index => $res)
                            <tr>
                                <td class="ps-4 text-muted">{{ $index + 1 }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $res->user->siswa->nama ?? 'Nama Tidak Ditemukan' }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="text-muted font-monospace small bg-light px-2 py-1 rounded border">{{ $res->user->siswa->nisn ?? '-' }}</span>
                                </td>
                                <td class="text-center">
                                    @if ($res->completed_at)
                                        <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-medium">
                                            <i class="bi bi-check-circle-fill me-1"></i> Selesai
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning px-3 py-2 rounded-pill fw-medium">
                                            <i class="bi bi-clock-history me-1"></i> Mengerjakan
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($res->completed_at)
                                        @php
                                            $awal = \Carbon\Carbon::parse($res->started_at);
                                            $akhir = \Carbon\Carbon::parse($res->completed_at);

                                            // Cari selisih dalam detik, bagi 60 untuk desimal, lalu format 2 angka di belakang koma
                                            $durasiDetik = $awal->diffInSeconds($akhir);
                                            $durasiMenit = $durasiDetik / 60;
                                            $durasiFormat = number_format($durasiMenit, 2, ',', '.');
                                        @endphp
                                        <div class="text-muted small">
                                            <i class="bi bi-stopwatch me-1 text-secondary"></i> {{ $durasiFormat }} Menit
                                        </div>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    {{-- Warna diubah jadi hitam standar dan ukuran dinormalkan --}}
                                    <span class="fw-bold text-dark">
                                        {{ $res->score ?? 0 }}
                                    </span>
                                </td>
                                <td class="text-center pe-4">
                                    <form action="{{ route('guru.exams.reset-session', $res->id) }}" method="POST"
                                        id="formResetIndividu-{{ $res->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                            class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1 shadow-sm transition-3d"
                                            onclick="confirmResetIndividu({{ $res->id }}, '{{ $res->user->siswa->nama ?? 'Siswa' }}')"
                                            title="Reset Jawaban Siswa">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            {{-- State Kosong --}}
                            <tr>
                                {{-- Colspan diubah jadi 7 karena ada penambahan kolom --}}
                                <td colspan="7" class="text-center py-5">
                                    <div class="p-4">
                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                            style="width: 70px; height: 70px;">
                                            <i class="bi bi-inbox fs-1"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">Belum Ada Data Ujian</h5>
                                        <p class="text-muted small mb-0">Hasil nilai akan otomatis muncul di sini setelah
                                            siswa mulai mengerjakan.</p>
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
        .bg-light {
            background-color: #f8fafc !important;
        }

        .btn-white {
            background: #fff;
            transition: all 0.2s;
        }

        .btn-white:hover {
            background: #f1f5f9;
        }

        .transition-3d {
            transition: all 0.2s ease;
        }

        .transition-3d:hover {
            transform: translateY(-2px);
            filter: brightness(1.05);
        }
    </style>
@endsection

@section('scripts')
    <script>
        // Konfirmasi Reset Semua Siswa
        function confirmResetAll() {
            Swal.fire({
                title: 'PERINGATAN KERAS!',
                text: "Anda akan menghapus SELURUH jawaban siswa untuk ujian ini. Semua siswa harus mengulang dari awal. Lanjutkan?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Reset Semua!',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-4'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('formResetAll').submit();
                }
            });
        }

        // Konfirmasi Reset Individu
        function confirmResetIndividu(id, nama) {
            Swal.fire({
                title: 'Reset Nilai?',
                text: `Semua jawaban milik ${nama} akan dihapus permanen dan siswa harus mengulang dari awal.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Reset!',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-4'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('formResetIndividu-' + id).submit();
                }
            });
        }
    </script>
@endsection
