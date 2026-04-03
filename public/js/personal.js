var personalTable;
var save_method;
let myData = {};
const urls = 'ketersediaan-pribadi'

jQuery(function () {
    myData._token = $('meta[name="csrf-token"]').attr('content')
})

$(document).ready(function () {
    initTablePersonal();
});

$(document).on('click', '.addPersonal', function () {
    $('#modalPersonal').modal('show');
    $('.modal-title').text('Tambah Data Jadwal Pribadi');
    save_method = 'add';
});

$(document).on('click', '.editKetersediaanPribadi', function () {
    var id = $(this).data('id');
    showSelectedData(id);
});

$('#modalPersonal').on('hidden.bs.modal', function () {
    $('#formPersonal')[0].reset();
    $('#id').val('');
    $('.modal-title').text('');
    save_method = '';

    var form = $('#formPersonal');
    form.validate().resetForm();
    form.find('.form-control').removeClass('is-invalid');
    form.find('.form-control').removeClass('is-valid');
});

async function initTablePersonal() {
    personalTable = $('#tablePersonal').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: urls,
            type: 'GET',
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'nama', name: 'nama' },
            { data: 'tanggal', name: 'tanggal' },
            { data: 'waktu_mulai', name: 'waktu_mulai' },
            { data: 'waktu_selesai', name: 'waktu_selesai' },            
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
        order: [[1, 'asc']],
        responsive: true,
        autoWidth: false,
    });
}

function save() {
    var url, method, formData;
    method = "POST";
    url = urls;
    formData = new FormData($('#formPersonal')[0]);

    if (save_method == 'edit') {
        url = urls + '/' + $('#id').val();
        formData.append('_method', 'PUT');
    }

    $.ajax({
        type: method,
        url: url,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            $('#modalPersonal').modal('hide');
            if (response.status) {
                personalTable.ajax.reload();
                toastr.success(response.message);
            }
        },
        error: function (xhr, status, error) {
            Swal.fire({
                title: 'Error!',
                text: xhr.responseJSON.message || 'Terjadi kesalahan saat menyimpan data.',
                icon: 'error'
            });
        }
    });
}

function showSelectedData(id) {
    $.ajax({
        type: "GET",
        url: urls + '/' + id,
        beforeSend: function () {
            Swal.fire({
                title: 'Loading...',
                text: 'Mengambil data Pribadi...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
        },
        success: function (response) {
            Swal.close();
            const data = response.data || {};            
            $('#id').val(data.id || '');
            $('#tanggal').val(data.tanggal || '');
            $('#waktu_mulai').val(data.waktu_mulai || '');            
            $('#waktu_selesai').val(data.waktu_selesai || '');
                    
            save_method = 'edit';
            $('#modalPersonal').modal('show');
            $('.modal-title').text('Edit Data Jadwal Pribadi');
        },
        error: function (xhr) {
            Swal.close();
            let message = 'Terjadi kesalahan saat mengambil data.';
            if (xhr.responseJSON?.message) message = xhr.responseJSON.message;
            Swal.fire({ title: 'Error!', text: message, icon: 'error' });
        }
    });
}

$(document).on('click', '.deleteKetersediaanPribadi', function () {
    var id = $(this).data('id');

    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: "Apakah Anda yakin ingin menghapus data ini?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `${urls}/${id}/delete`,
                method: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: myData._token
                },
                success: function (res) {
                    if (res.status) {
                        personalTable.ajax.reload();
                        toastr.success(res.message);
                    }
                },
                error: function (xhr) {
                    Swal.fire({
                        title: 'Error!',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan saat menghapus data.',
                        icon: 'error'
                    });
                }
            });

        }
    });
});