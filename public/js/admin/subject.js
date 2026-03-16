// public/js/admin/subject.js

document.addEventListener('DOMContentLoaded', function () {
    const editModal = document.getElementById('editModal');

    if (editModal) {
        editModal.addEventListener('show.bs.modal', function (event) {
            // 1. Tangkap tombol yang diklik
            const button = event.relatedTarget;

            // 2. Ambil data dari atribut data-id dan data-name
            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');

            // 3. Cari elemen input dan form di dalam modal
            const inputName = editModal.querySelector('#edit_name');
            const form = editModal.querySelector('#editForm');

            // 4. Isi data ke dalam input
            if (inputName) {
                inputName.val = name; // Native JS
                $('#edit_name').val(name); // Pakai jQuery biar lebih pasti masuk
            }

            // 5. Ubah action form agar mengarah ke rute update (URL: /admin/subjects/ID)
            if (form) {
                form.setAttribute('action', '/admin/subjects/' + id);
            }

            console.log('Data berhasil dimuat untuk ID:', id);
        });
    }
});


 // Fungsi Konfirmasi Hapus dengan SweetAlert2

function hapusData(id) {
    Swal.fire({
        title: 'Apakah kamu yakin?',
        text: "Data mata pelajaran ini akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
}