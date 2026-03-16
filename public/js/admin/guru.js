document.addEventListener('DOMContentLoaded', function () {
    const editGuruModal = document.getElementById('editGuruModal');

    if (editGuruModal) {
        editGuruModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            
            const id = button.getAttribute('data-id');
            const nip = button.getAttribute('data-nip');
            const nama = button.getAttribute('data-nama');
            const email = button.getAttribute('data-email');
            const subjectId = button.getAttribute('data-subject');

            // Mengisi input di modal
            editGuruModal.querySelector('#edit_nip').value = nip;
            editGuruModal.querySelector('#edit_nama').value = nama;
            editGuruModal.querySelector('#edit_email').value = email;
            editGuruModal.querySelector('#edit_subject_id').value = subjectId;
            
            // Mengubah action form
            editGuruModal.querySelector('#editGuruForm').setAttribute('action', '/admin/gurus/' + id);
        });
    }
});

// Fungsi Hapus dengan SweetAlert2
function hapusGuru(id) {
    Swal.fire({
        title: 'Hapus Data Guru?',
        text: "Akun login guru ini juga akan terhapus!",
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

function resetPassword(id, nama) {
    Swal.fire({
        title: 'Reset Password?',
        text: "Password " + nama + " akan dikembalikan menjadi NIP-nya.",
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#0dcaf0',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Reset!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Kita buat form dinamis untuk mengirim request PUT
            let form = document.createElement('form');
            form.action = '/admin/gurus/' + id + '/reset-password';
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                <input type="hidden" name="_method" value="PUT">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
}