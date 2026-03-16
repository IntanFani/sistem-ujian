@extends('layouts.admin')

@section('title', 'Data Guru')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0 text-success">Manajemen Data Guru</h4>
        <p class="text-muted small">Kelola data pengampu mata pelajaran</p>
    </div>
    <button class="btn btn-success rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addGuruModal">
        <i class="bi bi-person-plus me-1"></i> Tambah Guru
    </button>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="px-4 py-3 text-muted small">NO</th>
                    <th class="py-3 text-muted small">NIP</th>
                    <th class="py-3 text-muted small">NAMA GURU</th>
                    <th class="py-3 text-muted small">MATA PELAJARAN</th>
                    <th class="py-3 text-muted small">EMAIL</th>
                    <th class="py-3 text-end px-4 text-muted small">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($gurus as $index => $guru)
                <tr>
                    <td class="px-4 text-muted">{{ $index + 1 }}</td>
                    <td><span class="badge bg-light text-dark fw-normal">{{ $guru->nip }}</span></td>
                    <td class="fw-semibold">{{ $guru->nama }}</td>
                    <td>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 rounded-pill">
                            {{ $guru->subject->name ?? 'Belum Set' }}
                        </span>
                    </td>
                    <td class="text-muted">{{ $guru->user->email }}</td>
                    <td class="text-end px-4">
                        <button class="btn btn-sm btn-outline-warning border-0 me-1" 
                            data-bs-toggle="modal" 
                            data-bs-target="#editGuruModal"
                            data-id="{{ $guru->id }}"
                            data-nip="{{ $guru->nip }}"
                            data-nama="{{ $guru->nama }}"
                            data-email="{{ $guru->user->email }}"
                            data-subject="{{ $guru->subject_id }}">
                            <i class="bi bi-pencil-square"></i>
                        </button>

                        <button class="btn btn-sm btn-outline-info border-0 me-1" 
                            onclick="resetPassword({{ $guru->id }}, '{{ $guru->nama }}')" 
                            title="Reset Password ke NIP">
                            <i class="bi bi-key-fill"></i>
                        </button>

                        <button class="btn btn-sm btn-outline-danger border-0" onclick="hapusGuru({{ $guru->id }})">
                            <i class="bi bi-trash"></i>
                        </button>

                        <form id="delete-form-{{ $guru->id }}" action="{{ route('admin.gurus.destroy', $guru->id) }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">Belum ada data guru.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="addGuruModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Tambah Data Guru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.gurus.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">NIP</label>
                        <input type="text" name="nip" class="form-control rounded-3" placeholder="Masukkan NIP " required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control rounded-3" placeholder="Masukkan Nama Lengkap" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Pilih Mata Pelajaran</label>
                        <select name="subject_id" class="form-select rounded-3" required>
                            <option value="" selected disabled>-- Pilih Mapel --</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-primary">Email Login</label>
                        <input type="email" name="email" class="form-control rounded-3" placeholder="Masukkan Email " required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-primary">Password</label>
                        <input type="password" name="password" class="form-control rounded-3" placeholder="Min. 6 Karakter">
                        <div class="form-text text-muted" style="font-size: 0.75rem;">
                            <i class="bi bi-info-circle me-1"></i> Kosongkan jika ingin menggunakan <b>NIP</b> sebagai password default.
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editGuruModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Edit Data Guru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editGuruForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">NIP</label>
                        <input type="text" name="nip" id="edit_nip" class="form-control rounded-3" placeholder="Masukkan NIP" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Lengkap</label>
                        <input type="text" name="nama" id="edit_nama" class="form-control rounded-3" placeholder="Masukkan Nama Lengkap"required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Pilih Mata Pelajaran</label>
                        <select name="subject_id" id="edit_subject_id" class="form-select rounded-3" required>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-primary">Email Login</label>
                        <input type="email" name="email" id="edit_email" class="form-control rounded-3" placeholder="Masukkan Email " required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-danger">Ganti Password (Opsional)</label>
                        <input type="password" name="password" class="form-control rounded-3" placeholder="Isi jika ingin reset password">
                        <small class="text-muted">Kosongkan jika tidak ingin mengubah password guru.</small>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 text-white">Update Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('js/admin/guru.js') }}"></script>
@endsection