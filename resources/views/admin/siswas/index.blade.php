@extends('layouts.admin')

@section('title', 'Data Siswa')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0 text-success">Manajemen Data Siswa</h4>
        <p class="text-muted small">Kelola data siswa dan akun login ujian</p>
    </div>
    <button class="btn btn-success rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addSiswaModal">
        <i class="bi bi-plus-lg me-1"></i> Tambah Siswa
    </button>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="px-4 py-3 text-muted small">NO</th>
                    <th class="py-3 text-muted small">NAMA LENGKAP</th>
                    <th class="py-3 text-muted small">NISN</th>
                    <th class="py-3 text-muted small">KELAS</th>
                    <th class="py-3 text-muted small">EMAIL</th>
                    <th class="py-3 text-end px-4 text-muted small">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($siswas as $index => $s)
                <tr>
                    <td class="px-4 text-muted">{{ $index + 1 }}</td>
                    <td>
                        <div class="fw-bold text-dark">{{ $s->nama }}</div>
                    </td>
                    <td><span class="badge bg-light text-dark border">{{ $s->nisn }}</span></td>
                    <td>
                        <span class="badge bg-success-subtle text-success px-3">{{ $s->kelas->nama_kelas ?? 'N/A' }}</span>
                    </td>
                    <td class="small">{{ $s->user->email }}</td>
                    <td class="text-end px-4">
                        <button class="btn btn-sm btn-outline-warning border-0" 
                            data-bs-toggle="modal" 
                            data-bs-target="#editSiswaModal"
                            data-id="{{ $s->id }}"
                            data-nama="{{ $s->nama }}"
                            data-nisn="{{ $s->nisn }}"
                            data-email="{{ $s->user->email }}"
                            data-kelas-id="{{ $s->kelas_id }}">
                            <i class="bi bi-pencil-square"></i>
                        </button>

                        <button class="btn btn-sm btn-outline-danger border-0" onclick="hapusSiswa({{ $s->id }}, '{{ $s->nama }}')">
                            <i class="bi bi-trash"></i>
                        </button>

                        <form id="delete-form-{{ $s->id }}" action="{{ route('admin.siswas.destroy', $s->id) }}" method="POST" style="display: none;">
                            @csrf @method('DELETE')
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-people fs-1 d-block mb-3 text-light"></i>
                        Belum ada data siswa.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="addSiswaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Tambah Siswa Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.siswas.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control rounded-3" placeholder="Masukkan nama siswa" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">NISN</label>
                            <input type="text" name="nisn" class="form-control rounded-3" placeholder="Nomor NISN" required>
                            <small class="text-muted" style="font-size: 10px;">Akan menjadi password default.</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Pilih Kelas</label>
                            <select name="kelas_id" class="form-select rounded-3" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($kelases as $kelas)
                                    <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Email</label>
                        <input type="email" name="email" class="form-control rounded-3" placeholder="contoh@siswa.com" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">Simpan Siswa</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL EDIT SISWA --}}
<div class="modal fade" id="editSiswaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Edit Data Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            {{-- Tambahkan ID pada form agar bisa diakses JS --}}
            <form id="editSiswaForm" method="POST">
                @csrf
                @method('PUT') {{-- WAJIB UNTUK UPDATE --}}
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Lengkap</label>
                        {{-- Tambahkan ID pada input --}}
                        <input type="text" name="nama" id="edit_nama" class="form-control rounded-3" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">NISN</label>
                            <input type="text" name="nisn" id="edit_nisn" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Pilih Kelas</label>
                            <select name="kelas_id" id="edit_kelas_id" class="form-select rounded-3" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($kelases as $kelas)
                                    <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Email</label>
                        <input type="email" name="email" id="edit_email" class="form-control rounded-3" required>
                    </div>
                    <div class="alert alert-info py-2 border-0 small">
                        <i class="bi bi-info-circle me-1"></i> Kosongkan password jika tidak ingin diubah.
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Password Baru (Opsional)</label>
                        <input type="password" name="password" class="form-control rounded-3" placeholder="Minimal 6 karakter">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 text-white">Update Siswa</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Script untuk mengisi data di Modal Edit
    const editSiswaModal = document.getElementById('editSiswaModal');
    if (editSiswaModal) {
        editSiswaModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            
            // Ambil data dari atribut data-* di tombol edit
            const id = button.getAttribute('data-id');
            const nama = button.getAttribute('data-nama');
            const nisn = button.getAttribute('data-nisn');
            const email = button.getAttribute('data-email');
            const kelasId = button.getAttribute('data-kelas-id');

            // Masukkan data ke dalam input modal
            editSiswaModal.querySelector('#edit_nama').value = nama;
            editSiswaModal.querySelector('#edit_nisn').value = nisn;
            editSiswaModal.querySelector('#edit_email').value = email;
            editSiswaModal.querySelector('#edit_kelas_id').value = kelasId;
            
            // Atur URL action form ke route update
            editSiswaModal.querySelector('#editSiswaForm').setAttribute('action', '/admin/siswas/' + id);
        });
    }

    function hapusSiswa(id, nama) {
        Swal.fire({
            title: 'Hapus Siswa?',
            text: "Akun login " + nama + " juga akan dihapus permanen!",
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