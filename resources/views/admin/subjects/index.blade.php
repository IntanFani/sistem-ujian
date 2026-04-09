@extends('layouts.admin')

@section('title', 'Data Mata Pelajaran')

@section('content')
<div class="container-fluid py-2">
    
    {{-- Header & Tombol Tambah --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div class="d-flex align-items-center">
            <div class="bg-success bg-opacity-10 p-3 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                <i class="bi bi-journal-bookmark-fill fs-4 text-success"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-1 text-dark" style="letter-spacing: 0.5px;">DATA MATA PELAJARAN</h4>
                <p class="text-muted small mb-0">Kelola daftar mata pelajaran yang akan diujikan.</p>
            </div>
        </div>
        <button class="btn btn-success rounded-pill px-4 py-2 shadow-sm fw-bold border-0 transition-3d d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="bi bi-plus-circle-fill me-2"></i> Tambah Mapel
        </button>
    </div>

    {{-- Tabel Data --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 custom-table">
                <thead class="bg-light text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-4 py-3 border-0" style="width: 10%;">No</th>
                        <th class="py-3 border-0" style="width: 70%;">Nama Mata Pelajaran</th>
                        <th class="pe-4 py-3 border-0 text-end" style="width: 20%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subjects as $index => $subject)
                    <tr class="transition-3d-row">
                        <td class="ps-4 text-muted fw-medium">{{ $index + 1 }}</td>
                        <td>
                            <div class="fw-bold text-dark fs-6">{{ $subject->name }}</div>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">
                                {{-- Tombol Edit --}}
                                <button class="btn btn-sm btn-outline-warning rounded-3 transition-3d d-flex align-items-center" 
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal" 
                                    data-id="{{ $subject->id }}" 
                                    data-name="{{ $subject->name }}"
                                    title="Edit Mapel">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                {{-- Tombol Hapus --}}
                                <button class="btn btn-sm btn-outline-danger rounded-3 transition-3d d-flex align-items-center" 
                                    onclick="hapusData({{ $subject->id }})"
                                    title="Hapus Mapel">
                                    <i class="bi bi-trash3"></i>
                                </button>

                                {{-- Form Hapus (Sesuai JS Bawaan Fani) --}}
                                <form id="delete-form-{{ $subject->id }}" action="{{ route('admin.subjects.destroy', $subject->id) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center py-5">
                            <div class="p-4">
                                <div class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <i class="bi bi-journal-x fs-2"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">Belum Ada Data Mata Pelajaran</h6>
                                <p class="text-muted small mb-0">Silakan tambahkan mata pelajaran baru melalui tombol di kanan atas.</p>
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
{{-- MODAL TAMBAH MAPEL --}}
{{-- ========================================================== --}}
<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            
            <div class="modal-header bg-white border-bottom pt-4 px-4 pb-3">
                <div class="d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 p-2 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-journal-plus fs-5 text-success"></i>
                    </div>
                    <h5 class="modal-title fw-bold text-dark mb-0" style="letter-spacing: 0.5px;">TAMBAH MAPEL</h5>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('admin.subjects.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4" style="background-color: #f8fafc;">
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-2">Nama Mata Pelajaran</label>
                        <input type="text" name="name" class="form-control bg-white border custom-input py-2 shadow-none" placeholder="Contoh: Matematika" required>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top p-3 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-medium text-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm d-flex align-items-center">
                        <i class="bi bi-floppy me-2"></i> Simpan
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

{{-- ========================================================== --}}
{{-- MODAL EDIT MAPEL --}}
{{-- ========================================================== --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            
            <div class="modal-header bg-white border-bottom pt-4 px-4 pb-3">
                <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 p-2 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-pencil-square fs-5 text-warning"></i>
                    </div>
                    <h5 class="modal-title fw-bold text-dark mb-0" style="letter-spacing: 0.5px;">EDIT MAPEL</h5>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4" style="background-color: #f8fafc;">
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-2">Nama Mata Pelajaran</label>
                        <input type="text" name="name" id="edit_name" class="form-control bg-white border custom-input py-2 shadow-none focus-warning" required>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top p-3 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-medium text-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold text-white shadow-sm d-flex align-items-center">
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
</style>
@endsection

@section('scripts')
    {{-- Memanggil JS bawaan Fani --}}
    <script src="{{ asset('js/admin/subject.js') }}"></script>
@endsection