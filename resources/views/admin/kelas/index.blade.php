@extends('layouts.admin')

@section('title', 'Data Kelas')

@section('content')
<div class="container-fluid py-2">

    {{-- Header & Tombol Tambah --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div class="d-flex align-items-center">
            <div class="bg-success bg-opacity-10 p-3 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                <i class="bi bi-door-open-fill fs-4 text-success"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-1 text-dark" style="letter-spacing: 0.5px;">MANAJEMEN DATA KELAS</h4>
                <p class="text-muted small mb-0">Kelola tingkatan kelas untuk pengelompokan siswa.</p>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-warning text-dark fw-bold rounded-pill px-4 py-2 shadow-sm border-0 transition-3d d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalNaikKelas">
                <i class="bi bi-arrow-up-circle me-2"></i> Kenaikan Kelas
            </button>

            <button class="btn btn-success rounded-pill px-4 py-2 shadow-sm fw-bold border-0 transition-3d d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addKelasModal">
                <i class="bi bi-plus-circle-fill me-2"></i> Tambah Kelas
            </button>
        </div>
    </div>

    {{-- Tabel Data --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 custom-table">
                <thead class="bg-light text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-4 py-3 border-0" style="width: 5%;">No</th>
                        <th class="py-3 border-0" style="width: 25%;">Nama Kelas</th>
                        <th class="py-3 border-0" style="width: 30%;">Wali Kelas</th> 
                        <th class="py-3 border-0 text-center" style="width: 20%;">Jumlah Siswa</th>
                        <th class="pe-4 py-3 border-0 text-end" style="width: 20%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kelas as $index => $item)
                        <tr class="transition-3d-row">
                            <td class="ps-4 text-muted fw-medium">{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-bold text-dark fs-6">{{ $item->nama_kelas }}</div>
                            </td>
                            <td>
                                @if($item->waliKelas)
                                    <div class="fw-medium text-dark">{{ $item->waliKelas->nama }}</div>
                                    <div class="text-muted small mt-1"><i class="bi bi-person-badge me-1"></i> {{ $item->waliKelas->nip }}</div>
                                @else
                                    <span class="badge bg-light text-muted border px-2 py-1 fw-normal">Belum Ditentukan</span>
                                @endif
                            </td>
                            
                            {{-- KOLOM BARU: Jumlah Siswa Dinamis --}}
                            <td class="text-center">
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 rounded-pill fw-medium">
                                    <i class="bi bi-people-fill me-1"></i> 
                                    {{-- Asumsi relasi di model Kelas bernama 'siswa' --}}
                                    {{ $item->siswa->count() ?? 0 }} Siswa
                                </span>
                            </td>

                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    {{-- Tombol Edit --}}
                                    <button class="btn btn-sm btn-outline-warning rounded-3 transition-3d d-flex align-items-center" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editKelasModal"
                                        data-id="{{ $item->id }}"
                                        data-nama="{{ $item->nama_kelas }}"
                                        data-guru-id="{{ $item->guru_id }}"
                                        title="Edit Kelas">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    {{-- Tombol Hapus --}}
                                    <button class="btn btn-sm btn-outline-danger rounded-3 transition-3d d-flex align-items-center" 
                                        onclick="hapusKelas({{ $item->id }})"
                                        title="Hapus Kelas">
                                        <i class="bi bi-trash3"></i>
                                    </button>

                                    {{-- Form Hapus --}}
                                    <form id="delete-form-{{ $item->id }}" action="{{ route('admin.kelas.destroy', $item->id) }}" method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="p-4">
                                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                        <i class="bi bi-door-closed fs-2"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">Belum Ada Data Kelas</h6>
                                    <p class="text-muted small mb-0">Silakan tambahkan ruangan/kelas baru melalui tombol di kanan atas.</p>
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
{{-- MODAL TAMBAH KELAS --}}
{{-- ========================================================== --}}
<div class="modal fade" id="addKelasModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            
            <div class="modal-header bg-white border-bottom pt-4 px-4 pb-3">
                <div class="d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 p-2 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-door-open-fill fs-5 text-success"></i>
                    </div>
                    <h5 class="modal-title fw-bold text-dark mb-0" style="letter-spacing: 0.5px;">TAMBAH KELAS BARU</h5>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('admin.kelas.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4" style="background-color: #f8fafc;">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-2">Nama Kelas</label>
                        <input type="text" name="nama_kelas" class="form-control bg-white border custom-input py-2 shadow-none" placeholder="Contoh: VII-A atau 9-B" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-2">Wali Kelas (Opsional)</label>
                        <select name="guru_id" class="form-select bg-white border custom-input py-2 shadow-none cursor-pointer">
                            <option value="">-- Pilih Guru --</option>
                            @foreach($gurus as $guru)
                                <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top p-3 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-medium text-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm d-flex align-items-center">
                        <i class="bi bi-floppy me-2"></i> Simpan Kelas
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

{{-- ========================================================== --}}
{{-- MODAL EDIT KELAS --}}
{{-- ========================================================== --}}
<div class="modal fade" id="editKelasModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            
            <div class="modal-header bg-white border-bottom pt-4 px-4 pb-3">
                <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 p-2 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-pencil-square fs-5 text-warning"></i>
                    </div>
                    <h5 class="modal-title fw-bold text-dark mb-0" style="letter-spacing: 0.5px;">EDIT DATA KELAS</h5>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="editKelasForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4" style="background-color: #f8fafc;">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-2">Nama Kelas</label>
                        <input type="text" name="nama_kelas" id="edit_nama_kelas" class="form-control bg-white border custom-input py-2 shadow-none focus-warning" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-2">Wali Kelas</label>
                        <select name="guru_id" id="edit_guru_id" class="form-select bg-white border custom-input py-2 shadow-none cursor-pointer focus-warning">
                            <option value="">-- Pilih Guru (Opsional) --</option>
                            @foreach($gurus as $guru)
                                <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                            @endforeach
                        </select>
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

{{-- ========================================================== --}}
{{-- MODAL KENAIKAN KELAS MASSAL --}}
{{-- ========================================================== --}}
<div class="modal fade" id="modalNaikKelas" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-white border-bottom pt-4 px-4 pb-3">
                <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 p-2 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-arrow-up-circle-fill fs-5 text-warning"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" style="letter-spacing: 0.5px;">KENAIKAN KELAS MASSAL</h5>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('admin.siswas.naik-kelas') }}" method="POST">
                @csrf
                <div class="modal-body p-4" style="background-color: #f8fafc;">
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-2">Dari: Kelas Asal</label>
                        <select class="form-select bg-white border custom-input py-2 shadow-none cursor-pointer" name="kelas_asal" required>
                            <option value="">-- Pilih Kelas Saat Ini --</option>
                            @foreach ($kelas as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted mt-2 d-block" style="font-size: 0.75rem;"><i class="bi bi-info-circle me-1"></i>Semua siswa di kelas ini akan dipindahkan.</small>
                    </div>

                    <div class="mb-4 text-center">
                        <div class="d-inline-flex align-items-center justify-content-center bg-white shadow-sm rounded-circle" style="width: 40px; height: 40px; border: 1px solid #e2e8f0;">
                            <i class="bi bi-arrow-down text-primary fs-5"></i>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-2">Menuju: Kelas Tujuan</label>
                        <select class="form-select bg-white border custom-input py-2 shadow-none cursor-pointer focus-warning" name="kelas_tujuan" required>
                            <option value="">-- Pilih Kelas Tujuan Baru --</option>
                            @foreach ($kelas as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top p-3 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-medium text-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-dark fw-bold rounded-pill px-5 py-2 shadow-sm border-0 d-flex align-items-center">
                        <i class="bi bi-send-check-fill me-2"></i> Proses Pindah
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
<script>
    // Script untuk mengisi data di Modal Edit
    const editKelasModal = document.getElementById('editKelasModal');
    if (editKelasModal) {
        editKelasModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const nama = button.getAttribute('data-nama');
            const guruId = button.getAttribute('data-guru-id');

            editKelasModal.querySelector('#edit_nama_kelas').value = nama;
            editKelasModal.querySelector('#edit_guru_id').value = guruId || ""; 
            editKelasModal.querySelector('#editKelasForm').setAttribute('action', '/admin/kelas/' + id);
        });
    }

    // Fungsi Hapus dengan SweetAlert2
    function hapusKelas(id) {
        Swal.fire({
            title: 'Hapus Kelas?',
            text: "Data siswa di dalam kelas ini juga akan terpengaruh!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-4' }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>
@endsection