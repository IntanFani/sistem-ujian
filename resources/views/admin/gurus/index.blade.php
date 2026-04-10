@extends('layouts.admin')

@section('title', 'Data Guru')

@section('content')
<div class="container-fluid py-2">

    {{-- Alert Error Validation --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4 p-4" role="alert">
            <div class="d-flex align-items-center">
                <div class="bg-white text-danger rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 40px; height: 40px; flex-shrink: 0;">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                </div>
                <div>
                    <strong class="d-block mb-1">Ups! Ada data yang kurang tepat:</strong>
                    <ul class="mb-0 small text-danger-emphasis ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close mt-2 me-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Header & Tombol Tambah --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div class="d-flex align-items-center">
            <div class="bg-success bg-opacity-10 p-3 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                <i class="bi bi-person-workspace fs-4 text-success"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-1 text-dark" style="letter-spacing: 0.5px;">MANAJEMEN DATA GURU</h4>
                <p class="text-muted small mb-0">Kelola data pengampu mata pelajaran dan akses akun.</p>
            </div>
        </div>
        <button class="btn btn-success rounded-pill px-4 py-2 shadow-sm fw-bold border-0 transition-3d d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addGuruModal">
            <i class="bi bi-person-plus-fill me-2"></i> Tambah Guru
        </button>
    </div>

    {{-- Tabel Data --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 custom-table">
                <thead class="bg-light text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-4 py-3 border-0" style="width: 5%">No</th>
                        <th class="py-3 border-0" style="width: 15%">NIP</th>
                        <th class="py-3 border-0" style="width: 25%">Nama Guru</th>
                        <th class="py-3 border-0" style="width: 20%">Mata Pelajaran</th>
                        <th class="py-3 border-0" style="width: 15%">Email</th>
                        <th class="pe-4 py-3 border-0 text-end" style="width: 20%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gurus as $index => $guru)
                    <tr class="transition-3d-row">
                        <td class="ps-4 text-muted fw-medium">{{ $index + 1 }}</td>
                        <td class="text-dark">{{ $guru->nip }}</td>
                        <td>
                            <div class="fw-bold text-dark fs-6">{{ $guru->nama }}</div>
                        </td>
                        <td>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill fw-medium">
                                <i class="bi bi-book me-1"></i> {{ $guru->subject->name ?? 'Belum Set' }}
                            </span>
                        </td>
                        <td>
                            <div class="text-muted small"> {{ $guru->user->email }}</div>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">
                                {{-- Tombol Edit --}}
                                <button class="btn btn-sm btn-outline-warning rounded-3 transition-3d d-flex align-items-center" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editGuruModal"
                                    data-id="{{ $guru->id }}"
                                    data-nip="{{ $guru->nip }}"
                                    data-nama="{{ $guru->nama }}"
                                    data-email="{{ $guru->user->email }}"
                                    data-subject="{{ $guru->subject_id }}"
                                    title="Edit Guru">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                {{-- Tombol Reset Password --}}
                                <button class="btn btn-sm btn-outline-info rounded-3 transition-3d d-flex align-items-center" 
                                    onclick="resetPassword({{ $guru->id }}, '{{ $guru->nama }}')" 
                                    title="Reset Password ke Default (NIP)">
                                    <i class="bi bi-key-fill"></i>
                                </button>

                                {{-- Tombol Hapus --}}
                                <button class="btn btn-sm btn-outline-danger rounded-3 transition-3d d-flex align-items-center" 
                                    onclick="hapusGuru({{ $guru->id }})"
                                    title="Hapus Guru">
                                    <i class="bi bi-trash3"></i>
                                </button>

                                {{-- Form Hapus --}}
                                <form id="delete-form-{{ $guru->id }}" action="{{ route('admin.gurus.destroy', $guru->id) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="p-4">
                                <div class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <i class="bi bi-person-x fs-2"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">Belum Ada Data Guru</h6>
                                <p class="text-muted small mb-0">Silakan tambahkan data guru pengajar melalui tombol di kanan atas.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- ========================================================== --}}
{{-- MODAL TAMBAH GURU --}}
{{-- ========================================================== --}}
<div class="modal fade" id="addGuruModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            
            <div class="modal-header bg-white border-bottom pt-4 px-4 pb-3">
                <div class="d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 p-2 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-person-plus-fill fs-5 text-success"></i>
                    </div>
                    <h5 class="modal-title fw-bold text-dark mb-0" style="letter-spacing: 0.5px;">TAMBAH DATA GURU</h5>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('admin.gurus.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4" style="background-color: #f8fafc;">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-2">NIP</label>
                            <input type="text" name="nip" class="form-control bg-white border custom-input py-2 shadow-none" placeholder="Masukkan NIP" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-2">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control bg-white border custom-input py-2 shadow-none" placeholder="Misal: Budi Santoso, S.Pd." required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-2">Pilih Mata Pelajaran</label>
                            <select name="subject_id" class="form-select bg-white border custom-input py-2 shadow-none cursor-pointer" required>
                                <option value="" selected disabled>-- Pilih Mapel yang Diampu --</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- Perubahan Layout Email & Password --}}
                        <div class="col-md-12 mt-4">
                            <label class="form-label small fw-bold text-primary text-uppercase mb-2"><i class="bi bi-envelope-at me-1"></i> Email Login</label>
                            <input type="email" name="email" class="form-control bg-white border custom-input py-2 shadow-none" placeholder="guru@mtsalhuda.com" required>
                        </div>
                        <div class="col-md-6 mt-3">
                            <label class="form-label small fw-bold text-primary text-uppercase mb-2"><i class="bi bi-lock me-1"></i> Password</label>
                            <input type="password" name="password" class="form-control bg-white border custom-input py-2 shadow-none" placeholder="Min. 6 Karakter">
                            <div class="form-text text-muted mt-2" style="font-size: 0.75rem;">
                                <i class="bi bi-info-circle me-1"></i> Kosongkan jika ingin menggunakan <b>NIP</b>.
                            </div>
                        </div>
                        <div class="col-md-6 mt-3">
                            <label class="form-label small fw-bold text-primary text-uppercase mb-2"><i class="bi bi-shield-check me-1"></i> Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control bg-white border custom-input py-2 shadow-none" placeholder="Ulangi Password">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top p-3 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-medium text-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-5 fw-bold shadow-sm d-flex align-items-center">
                        <i class="bi bi-floppy me-2"></i> Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ========================================================== --}}
{{-- MODAL EDIT GURU --}}
{{-- ========================================================== --}}
<div class="modal fade" id="editGuruModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            
            <div class="modal-header bg-white border-bottom pt-4 px-4 pb-3">
                <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 p-2 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-pencil-square fs-5 text-warning"></i>
                    </div>
                    <h5 class="modal-title fw-bold text-dark mb-0" style="letter-spacing: 0.5px;">EDIT DATA GURU</h5>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="editGuruForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4" style="background-color: #f8fafc;">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-2">NIP</label>
                            <input type="text" name="nip" id="edit_nip" class="form-control bg-white border custom-input py-2 shadow-none focus-warning" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-2">Nama Lengkap</label>
                            <input type="text" name="nama" id="edit_nama" class="form-control bg-white border custom-input py-2 shadow-none focus-warning" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-2">Pilih Mata Pelajaran</label>
                            <select name="subject_id" id="edit_subject_id" class="form-select bg-white border custom-input py-2 shadow-none cursor-pointer focus-warning" required>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- Perubahan Layout Email & Password --}}
                        <div class="col-md-12 mt-4">
                            <label class="form-label small fw-bold text-primary text-uppercase mb-2"><i class="bi bi-envelope-at me-1"></i> Email Login</label>
                            <input type="email" name="email" id="edit_email" class="form-control bg-white border custom-input py-2 shadow-none focus-warning" required>
                        </div>
                        <div class="col-md-6 mt-3">
                            <label class="form-label small fw-bold text-danger text-uppercase mb-2"><i class="bi bi-shield-lock me-1"></i> Ganti Password</label>
                            <input type="password" name="password" class="form-control bg-white border custom-input py-2 shadow-none focus-warning" placeholder="Isi jika ingin reset password">
                            <div class="form-text text-muted mt-2" style="font-size: 0.75rem;">
                                <i class="bi bi-info-circle me-1"></i> Kosongkan jika tidak mengubah password.
                            </div>
                        </div>
                        <div class="col-md-6 mt-3">
                            <label class="form-label small fw-bold text-danger text-uppercase mb-2"><i class="bi bi-shield-check me-1"></i> Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control bg-white border custom-input py-2 shadow-none focus-warning" placeholder="Ulangi Password Baru">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top p-3 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-medium text-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-5 fw-bold text-white shadow-sm d-flex align-items-center">
                        <i class="bi bi-floppy me-2"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
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
    
    /* Styling Input Modal */
    .custom-input { border-color: #e2e8f0; border-radius: 10px; transition: all 0.3s ease; }
    .custom-input:focus { border-color: #10b981; box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15); }
    .custom-input.focus-warning:focus { border-color: #ffc107; box-shadow: 0 0 0 4px rgba(255, 193, 7, 0.15); }
    .cursor-pointer { cursor: pointer; }
</style>
@endsection

@section('scripts')
    {{-- Memanggil JS bawaan Fani --}}
    <script src="{{ asset('js/admin/guru.js') }}"></script>
@endsection