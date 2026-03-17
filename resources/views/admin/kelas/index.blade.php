@extends('layouts.admin')

@section('title', 'Data Kelas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0 text-success">Manajemen Data Kelas</h4>
        <p class="text-muted small">Kelola tingkatan kelas untuk pengelompokan siswa</p>
    </div>
    <button class="btn btn-success rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addKelasModal">
        <i class="bi bi-plus-lg me-1"></i> Tambah Kelas
    </button>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="px-4 py-3 text-muted small" style="width: 100px;">NO</th>
                    <th class="py-3 text-muted small">NAMA KELAS</th>
                    <th class="py-3 text-muted small">WALI KELAS</th> 
                    <th class="py-3 text-end px-4 text-muted small">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kelas as $index => $kelas)
                    <tr>
                        <td class="px-4">{{ $index + 1 }}</td>
                        <td class="fw-bold">{{ $kelas->nama_kelas }}</td>
                        <td>
                            {{ $kelas->waliKelas->nama ?? 'Belum Ditentukan' }} 
                            <small class="text-muted d-block" style="font-size: 10px;">{{ $kelas->waliKelas->nip ?? '-' }}</small>
                        </td>
                        <td class="text-end px-4">
                            <button class="btn btn-sm btn-outline-warning border-0" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editKelasModal"
                                data-id="{{ $kelas->id }}"
                                data-nama="{{ $kelas->nama_kelas }}"
                                data-guru-id="{{ $kelas->guru_id }}">
                                <i class="bi bi-pencil-square"></i>
                            </button>

                        <button class="btn btn-sm btn-outline-danger border-0" onclick="hapusKelas({{ $kelas->id }})">
                            <i class="bi bi-trash"></i>
                        </button>

                        <form id="delete-form-{{ $kelas->id }}" action="{{ route('admin.kelas.destroy', $kelas->id) }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center py-5 text-muted">
                        <i class="bi bi-door-closed fs-1 d-block mb-3"></i>
                        Belum ada data kelas.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="addKelasModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Tambah Kelas Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.kelas.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Kelas</label>
                        <input type="text" name="nama_kelas" class="form-control rounded-3" placeholder="Contoh: VII-A atau 9-B" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Wali Kelas</label>
                        <select name="guru_id" id="edit_guru_id" class="form-select rounded-3">
                            <option value="">-- Pilih Guru (Opsional) --</option>
                            @foreach($gurus as $guru)
                                <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">Simpan Kelas</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editKelasModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Edit Nama Kelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editKelasForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Kelas</label>
                        <input type="text" name="nama_kelas" id="edit_nama_kelas" class="form-control rounded-3" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Wali Kelas</label>
                        <select name="guru_id" id="edit_guru_id" class="form-select rounded-3">
                            <option value="">-- Pilih Guru (Opsional) --</option>
                            @foreach($gurus as $guru)
                                <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 text-white">Update Kelas</button>
                </div>
            </form>
        </div>
    </div>
</div>

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
                const guruId = button.getAttribute('data-guru-id'); // Ambil guru_id

                editKelasModal.querySelector('#edit_nama_kelas').value = nama;
                editKelasModal.querySelector('#edit_guru_id').value = guruId; // Set value dropdown
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
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>
@endsection