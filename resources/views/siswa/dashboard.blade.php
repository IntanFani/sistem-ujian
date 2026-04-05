@extends('layouts.siswa')

@section('title', 'Beranda')

@section('content')
    {{-- Notifikasi Error --}}
    @if (session('error'))
        <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill fs-5 me-3 text-danger"></i> 
            <div>{{ session('error') }}</div>
        </div>
    @endif

    {{-- Header Section (Disesuaikan dengan UI Admin) --}}
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <div>
            <h4 class="fw-bold mb-1 text-success">
                <i class="bi bi-journal-text me-2"></i>Daftar Ujian Tersedia
            </h4>
            <p class="text-muted small mb-0">Halo <span class="fw-bold text-dark">{{ Auth::user()->name }}</span>, silakan pilih dan kerjakan ujian hari ini.</p>
        </div>
        
        {{-- Jika nanti butuh tombol aksi di kanan (misal: Refresh), taruh di sini --}}
        <div>
            <button class="btn btn-light rounded-pill px-3 shadow-sm border text-muted small transition-hover" onclick="window.location.reload();">
                <i class="bi bi-arrow-clockwise me-1"></i> Segarkan
            </button>
        </div>
    </div>

    {{-- Grid Ujian --}}
    <div class="row g-4">
        @forelse($exams as $e)
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 transition-hover overflow-hidden">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-medium">
                                {{ $e->subject->name }}
                            </span>
                        </div>
                        
                        <h5 class="fw-bold text-dark mb-2">{{ $e->title }}</h5>
                        
                        <div class="d-flex align-items-center small text-muted mb-4 bg-light p-2 rounded-3">
                            <i class="bi bi-alarm text-warning me-2 fs-5"></i> 
                            <span class="fw-medium">Durasi: {{ $e->duration }} Menit</span>
                        </div>

                        <div class="d-grid mt-auto">
                            @php $session = $e->exam_sessions->first(); @endphp

                            {{-- Cek: Kalau completed_at TIDAK NULL, berarti sudah selesai --}}
                            @if ($session && $session->completed_at)
                                <button class="btn btn-light text-muted border rounded-pill fw-bold py-2 shadow-none" disabled>
                                    <i class="bi bi-check-circle-fill text-success me-1"></i> Selesai Dikerjakan
                                </button>

                            {{-- Cek: Kalau ada session tapi completed_at masih NULL, berarti lagi ngerjain --}}
                            @elseif($session && !$session->completed_at)
                                <a href="{{ route('siswa.exams.show', $e->id) }}"
                                    class="btn btn-warning text-dark rounded-pill fw-bold py-2 shadow-sm transition-hover">
                                    Lanjutkan Ujian <i class="bi bi-play-circle-fill ms-1"></i>
                                </a>
                            @else
                                <button class="btn btn-success rounded-pill fw-bold py-2 shadow-sm transition-hover"
                                    onclick="persiapanUjian('{{ $e->id }}', '{{ $e->title }}')">
                                    Kerjakan Sekarang <i class="bi bi-arrow-right-circle-fill ms-1"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="card border-0 shadow-sm p-5 rounded-4 bg-white mx-auto" style="max-width: 500px;">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-4" style="width: 100px; height: 100px;">
                        <i class="bi bi-inbox fs-1 text-muted"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Belum Ada Ujian Tersedia</h5>
                    <p class="text-muted mb-0 small">Tunggu informasi dari Bapak/Ibu guru atau coba segarkan halaman ini.</p>
                </div>
            </div>
        @endforelse
    </div>

    {{-- MODAL TOKEN --}}
    <div class="modal fade" id="tokenModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-success bg-opacity-10 border-0 pt-4 px-4 pb-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-white p-2 rounded-3 shadow-sm me-3">
                            <i class="bi bi-shield-lock-fill fs-4 text-success"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark" id="examTitleLabel">Konfirmasi Ujian</h5>
                            <p class="text-muted small mb-0">Masukkan token untuk memulai</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <form id="formToken" method="POST">
                    @csrf
                    <div class="modal-body p-4 text-center">
                        <div class="alert alert-warning border-0 rounded-4 small mb-4 text-start">
                            <i class="bi bi-info-circle-fill me-1"></i> Waktu akan langsung berjalan setelah klik <strong>Mulai Sekarang</strong>.
                        </div>

                        <label class="form-label fw-bold text-muted text-uppercase small mb-2">Token Ujian</label>
                        <input type="text" name="token" id="inputToken"
                            class="form-control form-control-lg text-center fw-bold text-uppercase border-2 rounded-3 shadow-none"
                            placeholder="------" maxlength="6" required
                            style="letter-spacing: 8px; font-size: 1.8rem; border-color: #e2e8f0; background-color: #f8fafc;">
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0 justify-content-center gap-2">
                        <button type="button" class="btn btn-light border rounded-pill px-4 fw-medium shadow-sm"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success rounded-pill px-5 fw-bold shadow-sm transition-hover">
                            <i class="bi bi-play-fill me-1"></i> Mulai Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .transition-hover {
            transition: all 0.3s ease;
        }
        .transition-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(16, 185, 129, 0.15) !important;
        }
        #inputToken:focus {
            border-color: #10b981 !important; /* Warna hijau success */
            background-color: #fff !important;
        }
    </style>
@endsection

@section('scripts')
    <script>
        function persiapanUjian(id, title) {
            // Judul disesuaikan agar rapi di UI baru
            document.getElementById('examTitleLabel').innerText = title;

            // Set Action Form
            const form = document.getElementById('formToken');
            form.action = `/siswa/exams/${id}/start`;

            // Munculkan Modal
            const el = document.getElementById('tokenModal');
            const myModal = new bootstrap.Modal(el);
            myModal.show();

            // Autofocus ke input token
            el.addEventListener('shown.bs.modal', function() {
                document.getElementById('inputToken').focus();
            });
        }
    </script>
@endsection