@extends('layouts.siswa')

@section('title', 'Beranda')

@section('content')
<div class="row mb-4">
    @if(session('error'))
        <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4">
            <i class="bi bi-exclamation-circle-fill me-2"></i> {{ session('error') }}
        </div>
    @endif
    <div class="col-12">
        <h3 class="fw-bold text-dark">Ujian Tersedia</h3>
        <p class="text-muted small">Halo {{ Auth::user()->name }}, silakan pilih ujian hari ini.</p>
    </div>
</div>

<div class="row g-4">
    @forelse($exams as $e)
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 transition-hover overflow-hidden">
            <div class="card-body p-4">
                <span class="badge bg-success-subtle text-success mb-3 px-3 rounded-pill">{{ $e->subject->name }}</span>
                <h5 class="fw-bold mb-2">{{ $e->title }}</h5>
                <div class="d-flex align-items-center small text-muted mb-4">
                    <i class="bi bi-alarm me-2"></i> {{ $e->duration }} Menit
                    <span class="mx-2">•</span>
                    <i class="bi bi-card-text me-2"></i> {{ $e->questions_count ?? 0 }} Soal
                </div>
                
                <div class="d-grid">
                    <button class="btn btn-success rounded-pill fw-bold py-2 shadow-sm" 
                        onclick="persiapanUjian('{{ $e->id }}', '{{ $e->title }}')">
                        Kerjakan Sekarang <i class="bi bi-chevron-right ms-1"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5">
        <div class="card border-0 shadow-sm p-5 rounded-4 bg-white">
            <img src="https://illustrations.popsy.co/teal/work-from-home.svg" width="180" class="mb-3 mx-auto">
            <h5 class="fw-bold">Belum Ada Ujian</h5>
            <p class="text-muted mb-0">Tunggu info dari Bapak/Ibu guru ya!</p>
        </div>
    </div>
    @endforelse
</div>

<div class="modal fade" id="tokenModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form id="formToken" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="fw-bold" id="examTitleLabel">Ujian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="alert alert-warning border-0 rounded-4 small mb-4">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> 
                        Waktu akan langsung berjalan setelah kamu klik <strong>"Mulai"</strong>.
                    </div>
                    
                    <div class="text-center mb-3">
                        <label class="form-label fw-bold text-dark mb-3">Masukkan Token Ujian</label>
                        <input type="text" name="token" id="inputToken"
                               class="form-control form-control-lg text-center fw-bold text-uppercase border-2" 
                               placeholder="------" maxlength="6" required 
                               style="letter-spacing: 8px; font-size: 1.8rem; border-color: #e2e8f0;">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 justify-content-center gap-2">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-medium" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-5 fw-bold shadow">Mulai Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .transition-hover:hover { transform: translateY(-8px); transition: 0.3s; box-shadow: 0 1rem 3rem rgba(16, 185, 129, 0.1) !important; }
    #inputToken:focus { border-color: var(--primary); box-shadow: none; }
</style>
@endsection

@section('scripts')
<script>
    function persiapanUjian(id, title) {
        // Set Judul
        document.getElementById('examTitleLabel').innerText = "Konfirmasi: " + title;
        
        // Set Action Form (Update rute sesuai kebutuhanmu)
        const form = document.getElementById('formToken');
        form.action = `/siswa/exams/${id}/start`; 
        
        // Munculkan Modal
        const el = document.getElementById('tokenModal');
        const myModal = new bootstrap.Modal(el);
        myModal.show();
        
        // Autofocus ke input token
        el.addEventListener('shown.bs.modal', function () {
            document.getElementById('inputToken').focus();
        });
    }
</script>
@endsection