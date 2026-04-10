@extends('layouts.admin')

@section('title', 'Data Siswa')

@section('content')
    <div class="container-fluid py-2">

        {{-- Header & Tombol Aksi --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div class="d-flex align-items-center">
                <div class="bg-success bg-opacity-10 p-3 rounded-circle me-3 d-flex align-items-center justify-content-center"
                    style="width: 48px; height: 48px;">
                    <i class="bi bi-people-fill fs-4 text-success"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-dark" style="letter-spacing: 0.5px;">MANAJEMEN DATA SISWA</h4>
                    <p class="text-muted small mb-0">Kelola data siswa dan otomatisasi akun login ujian CBT.</p>
                </div>
            </div>

            <div class="d-flex gap-2">
                {{-- Tombol Import Excel --}}
                <button type="button"
                    class="btn btn-excel-outline fw-bold rounded-pill px-4 py-2 shadow-sm transition-3d d-flex align-items-center"
                    data-bs-toggle="modal" data-bs-target="#modalImportExcel">
                    <i class="bi bi-file-earmark-excel-fill me-2"></i> Import Excel
                </button>

                {{-- Tombol Tambah Siswa --}}
                <button
                    class="btn btn-success rounded-pill px-4 py-2 shadow-sm fw-bold border-0 transition-3d d-flex align-items-center"
                    data-bs-toggle="modal" data-bs-target="#addSiswaModal">
                    <i class="bi bi-person-plus-fill me-2"></i> Tambah Siswa
                </button>
            </div>
        </div>

        {{-- Toolbar Filter --}}
        <div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-white">
            <form action="{{ route('admin.siswas.index') }}" method="GET" id="filterForm" class="m-0">
                <div class="row g-2 align-items-center">
                    <div class="col-auto">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-0 me-2"><i
                                class="bi bi-funnel-fill text-primary me-1"></i> Filter Kelas:</label>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <select name="kelas"
                            class="form-select bg-light border-0 shadow-none cursor-pointer rounded-pill px-3"
                            onchange="document.getElementById('filterForm').submit()">
                            <option value="">-- Semua Kelas --</option>
                            @foreach ($kelases as $k)
                                <option value="{{ $k->id }}" {{ request('kelas') == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if (request('kelas'))
                        <div class="col-auto">
                            <a href="{{ route('admin.siswas.index') }}"
                                class="btn btn-sm btn-light rounded-pill text-danger px-3 shadow-sm transition-3d">
                                <i class="bi bi-x-circle me-1"></i> Reset
                            </a>
                        </div>
                    @endif
                </div>
            </form>
        </div>

        {{-- Tabel Data --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 custom-table">
                    <thead class="bg-light text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">
                        <tr>
                            <th class="ps-4 py-3 border-0" style="width: 5%;">No</th>
                            <th class="py-3 border-0" style="width: 25%;">Nama Lengkap</th>
                            <th class="py-3 border-0" style="width: 15%;">NISN</th>
                            <th class="py-3 border-0" style="width: 15%;">Kelas</th>
                            <th class="py-3 border-0" style="width: 20%;">Akun Login</th>
                            <th class="pe-4 py-3 border-0 text-end" style="width: 20%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswas as $index => $s)
                            <tr class="transition-3d-row">
                                <td class="ps-4 text-muted fw-medium">{{ $index + 1 }}</td>
                                <td>
                                    <div class="fw-bold text-dark fs-6">{{ $s->nama }}</div>
                                </td>
                                <td>
                                    <span class="text-dark font-monospace">{{ $s->nisn }}</span>
                                </td>
                                <td>
                                    <span
                                        class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill fw-medium">
                                        <i class="bi bi-building me-1"></i> {{ $s->kelas->nama_kelas ?? 'Belum Set' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="small bg-light p-2 rounded-3 border">
                                        <div class="text-primary fw-bold"><i class="bi bi-person me-1"></i> U:
                                            {{ $s->nisn }}</div>
                                        {{-- Pastikan kamu punya kolom 'password_text' di tabel siswas untuk menyimpan raw password --}}
                                        <div class="text-danger fw-bold mt-1"><i class="bi bi-key me-1"></i> P:
                                            {{ $s->password_text ?? 'Otomatis' }}</div>
                                    </div>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button
                                            class="btn btn-sm btn-outline-warning rounded-3 transition-3d d-flex align-items-center"
                                            data-bs-toggle="modal" data-bs-target="#editSiswaModal"
                                            data-id="{{ $s->id }}" data-nama="{{ $s->nama }}"
                                            data-nisn="{{ $s->nisn }}" data-kelas-id="{{ $s->kelas_id }}"
                                            title="Edit Siswa">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        <button
                                            class="btn btn-sm btn-outline-danger rounded-3 transition-3d d-flex align-items-center"
                                            onclick="hapusSiswa({{ $s->id }}, '{{ addslashes($s->nama) }}')"
                                            title="Hapus Siswa">
                                            <i class="bi bi-trash3"></i>
                                        </button>

                                        <form id="delete-form-{{ $s->id }}"
                                            action="{{ route('admin.siswas.destroy', $s->id) }}" method="POST"
                                            class="d-none">
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
                                        <div class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                            style="width: 60px; height: 60px;">
                                            <i class="bi bi-people fs-2"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1">Belum Ada Data Siswa</h6>
                                        <p class="text-muted small mb-0">Silakan tambahkan siswa secara manual atau import
                                            via Excel.</p>
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
    {{-- MODAL TAMBAH SISWA --}}
    {{-- ========================================================== --}}
    <div class="modal fade" id="addSiswaModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-white border-bottom pt-4 px-4 pb-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 p-2 rounded-circle me-3 d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px;">
                            <i class="bi bi-person-plus-fill fs-5 text-success"></i>
                        </div>
                        <h5 class="modal-title fw-bold text-dark mb-0" style="letter-spacing: 0.5px;">TAMBAH SISWA BARU
                        </h5>
                    </div>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.siswas.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4" style="background-color: #f8fafc;">

                        {{-- Alert Info Auto-Generate --}}
                        <div
                            class="alert alert-info py-2 px-3 small border-0 rounded-3 mb-4 d-flex align-items-center shadow-sm">
                            <i class="bi bi-info-circle-fill fs-5 me-2 text-info"></i>
                            <span>Akun login (Username & Password) akan digenerate secara otomatis oleh sistem menggunakan
                                NISN.</span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-2">Nama Lengkap</label>
                            <input type="text" name="nama"
                                class="form-control bg-white border custom-input py-2 shadow-none"
                                placeholder="Masukkan nama siswa" required>
                        </div>
                        <div class="row g-3 mb-2">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-2">NISN</label>
                                <input type="text" name="nisn"
                                    class="form-control bg-white border custom-input py-2 shadow-none"
                                    placeholder="Nomor NISN (Sebagai Username)" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-2">Pilih Kelas</label>
                                <select name="kelas_id"
                                    class="form-select bg-white border custom-input py-2 shadow-none cursor-pointer"
                                    required>
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach ($kelases as $kelas)
                                        <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12 mt-3">
                                <label class="form-label small fw-bold text-primary text-uppercase mb-2"><i
                                        class="bi bi-envelope-at me-1"></i> Email Siswa</label>
                                <input type="email" name="email"
                                    class="form-control bg-white border custom-input py-2 shadow-none"
                                    placeholder="siswa@cbt.com" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-white border-top p-3 px-4">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-medium text-secondary"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit"
                            class="btn btn-success rounded-pill px-5 fw-bold shadow-sm d-flex align-items-center">
                            <i class="bi bi-floppy me-2"></i> Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ========================================================== --}}
    {{-- MODAL EDIT SISWA --}}
    {{-- ========================================================== --}}
    <div class="modal fade" id="editSiswaModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-white border-bottom pt-4 px-4 pb-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 p-2 rounded-circle me-3 d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px;">
                            <i class="bi bi-pencil-square fs-5 text-warning"></i>
                        </div>
                        <h5 class="modal-title fw-bold text-dark mb-0" style="letter-spacing: 0.5px;">EDIT DATA SISWA</h5>
                    </div>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="editSiswaForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4" style="background-color: #f8fafc;">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-2">Nama Lengkap</label>
                            <input type="text" name="nama" id="edit_nama"
                                class="form-control bg-white border custom-input py-2 shadow-none focus-warning" required>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-2">NISN</label>
                                <input type="text" name="nisn" id="edit_nisn"
                                    class="form-control bg-white border custom-input py-2 shadow-none focus-warning"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-2">Pilih Kelas</label>
                                <select name="kelas_id" id="edit_kelas_id"
                                    class="form-select bg-white border custom-input py-2 shadow-none cursor-pointer focus-warning"
                                    required>
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach ($kelases as $kelas)
                                        <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12 mt-3">
                                <label class="form-label small fw-bold text-primary text-uppercase mb-2"><i
                                        class="bi bi-envelope-at me-1"></i> Email Siswa</label>
                                <input type="email" name="email" id="edit_email"
                                    class="form-control bg-white border custom-input py-2 shadow-none focus-warning"
                                    required>
                            </div>
                        </div>
                        <div class="row g-3 mb-2">
                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-danger text-uppercase mb-2"><i
                                        class="bi bi-shield-lock me-1"></i> Reset Password (Opsional)</label>
                                <input type="password" name="password"
                                    class="form-control bg-white border custom-input py-2 shadow-none focus-warning"
                                    placeholder="Isi untuk membuat password baru">
                                <small class="text-muted mt-1 d-block" style="font-size: 0.75rem;"><i
                                        class="bi bi-info-circle me-1"></i>Kosongkan jika tidak ingin merubah password saat
                                    ini.</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-white border-top p-3 px-4">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-medium text-secondary"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit"
                            class="btn btn-warning rounded-pill px-5 fw-bold text-white shadow-sm d-flex align-items-center">
                            <i class="bi bi-floppy me-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ========================================================== --}}
    {{-- MODAL IMPORT EXCEL --}}
    {{-- ========================================================== --}}
    <div class="modal fade" id="modalImportExcel" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

                <div class="modal-header bg-white border-bottom pt-4 px-4 pb-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 p-2 rounded-circle me-3 d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px;">
                            <i class="bi bi-file-earmark-excel-fill fs-5" style="color: #107c41;"></i>
                        </div>
                        <h5 class="modal-title fw-bold text-dark mb-0" style="letter-spacing: 0.5px;">IMPORT DATA SISWA
                        </h5>
                    </div>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                {{-- Pastikan ada enctype="multipart/form-data" untuk upload file --}}
                <form action="{{ route('admin.siswas.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4" style="background-color: #f8fafc;">

                        {{-- Alert Info --}}
                        <div
                            class="alert alert-info py-3 px-4 small border-0 rounded-4 mb-4 d-flex align-items-start shadow-sm">
                            <i class="bi bi-info-circle-fill fs-5 me-3 text-info"></i>
                            <div>
                                Pastikan format Excel Anda memiliki baris judul kolom: <br>
                                <b class="text-dark">nama, nisn, kelas, email</b> pada baris pertama.<br>
                                <div class="mt-1 text-muted" style="font-size: 0.75rem;">
                                    * Password akan digenerate otomatis oleh sistem.
                                </div>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-2">Upload File Excel
                                (.xlsx)</label>
                            <input type="file" name="file_excel"
                                class="form-control bg-white border custom-input py-2 shadow-none"
                                accept=".xlsx, .xls, .csv" required>
                        </div>
                    </div>

                    <div class="modal-footer bg-white border-top p-3 px-4">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-medium text-secondary"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit"
                            class="btn text-white rounded-pill px-5 fw-bold shadow-sm d-flex align-items-center transition-3d"
                            style="background-color: #107c41;">
                            <i class="bi bi-cloud-arrow-up-fill me-2"></i> Mulai Import
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    {{-- CSS Tambahan --}}
    <style>
        .bg-light {
            background-color: #f8fafc !important;
        }

        .transition-3d {
            transition: all 0.2s ease;
        }

        .transition-3d:hover {
            transform: translateY(-2px);
        }

        .custom-table th {
            border-bottom: 2px solid #e2e8f0 !important;
        }

        .custom-table td {
            border-bottom: 1px solid #f1f5f9;
            padding-top: 1rem;
            padding-bottom: 1rem;
        }

        .transition-3d-row {
            transition: background-color 0.2s ease;
        }

        .transition-3d-row:hover {
            background-color: #f8fafc !important;
        }

        .custom-input {
            border-color: #e2e8f0;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .custom-input:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15);
        }

        .custom-input.focus-warning:focus {
            border-color: #ffc107;
            box-shadow: 0 0 0 4px rgba(255, 193, 7, 0.15);
        }

        .cursor-pointer {
            cursor: pointer;
        }

        .btn-excel-outline {
            background-color: #ffffff;
            color: #107c41;
            border: 2px solid #107c41;
        }

        .btn-excel-outline:hover {
            background-color: #107c41;
            color: #ffffff;
            border-color: #107c41;
            box-shadow: 0 8px 15px rgba(16, 124, 65, 0.2) !important;
        }
    </style>
@endsection

@section('scripts')
    <script>
        // Script Modal Edit
        const editSiswaModal = document.getElementById('editSiswaModal');
        if (editSiswaModal) {
            editSiswaModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const nama = button.getAttribute('data-nama');
                const nisn = button.getAttribute('data-nisn');
                const kelasId = button.getAttribute('data-kelas-id');

                editSiswaModal.querySelector('#edit_nama').value = nama;
                editSiswaModal.querySelector('#edit_nisn').value = nisn;
                editSiswaModal.querySelector('#edit_kelas_id').value = kelasId;
                editSiswaModal.querySelector('#editSiswaForm').setAttribute('action', '/admin/siswas/' + id);
            });
        }

        // Fungsi Hapus
        function hapusSiswa(id, nama) {
            Swal.fire({
                title: 'Hapus Siswa?',
                text: "Akun login " + nama + " juga akan terhapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-4'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>
@endsection
