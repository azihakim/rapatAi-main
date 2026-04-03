var notulenTable;
var save_method;
let myData = {};
const urls = '/notulensi'

jQuery(function () {
    myData._token = $('meta[name="csrf-token"]').attr('content')
})

$(document).ready(function () {
    initTableNotulen();

    $('#file_path').on('change', function () {
        if (this.files.length > 0) {
            $('#btnGenerateSTT').removeClass('d-none');
            $('#name_filePath').text(this.files[0].name);
        } else {
            $('#btnGenerateSTT').addClass('d-none');
            $('#name_filePath').text('');
        }
    });

    // Klik Generate
    $('#btnGenerateSTT').on('click', function () {
        let formData = new FormData();
        formData.append('file_path', $('#file_path')[0].files[0]);
        formData.append('continue_text', $('#isi').val());
        formData.append('_token', $('input[name=_token]').val());

        // Tampilkan loading
        $('#btnGenerateSTT')
            .prop('disabled', true)
            .text('Mengonversi...')
            .append('<span class="spinner-border spinner-border-sm ms-2"></span>');

        $.ajax({
            url: '/notulensi/speech-to-text',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                $('#btnGenerateSTT')
                    .prop('disabled', false)
                    .text('Generate Speech to Text');

                if (res.status && res.transcription) {
                    typeWriterEffect(res.transcription, '#isi');
                } else {
                    Swal.fire('Gagal', res.message || 'Transkripsi gagal', 'error');
                }
            },
            error: function (xhr) {
                $('#btnGenerateSTT')
                    .prop('disabled', false)
                    .text('Generate Speech to Text');

                Swal.fire('Error', xhr.responseJSON?.message || 'Terjadi kesalahan.', 'error');
            }
        });
    });

    // Fungsi typing effect ke textarea
    function typeWriterEffect(text, targetSelector, speed = 30) {
        let i = 0;
        const textarea = $(targetSelector);
        textarea.val(''); // kosongkan dulu

        function typing() {
            if (i < text.length) {
                textarea.val(textarea.val() + text.charAt(i));
                i++;
                setTimeout(typing, speed);
            }
        }
        typing();
    }
});

$(document).on('click', '.addNotulen', function () {
    $('#modalNotulen').modal('show');
    $('.modal-title').text('Tambah Data Notulen');
    save_method = 'add';
});

$(document).on('click', '.editNotulen', function () {
    var id = $(this).data('id');
    showSelectedData(id);
});

$('#modalNotulen').on('hidden.bs.modal', function () {
    $('#formNotulen')[0].reset();
    $('#id').val('');
    $('.modal-title').text('');
    save_method = '';

    var form = $('#formNotulen');
    // form.validate().resetForm();
    form.find('.form-control').removeClass('is-invalid');
    form.find('.form-control').removeClass('is-valid');
});

document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('file_path');
    const btnGenerate = document.getElementById('btnGenerateSTT');
    fileInput.addEventListener('change', function () {
        if (fileInput.files && fileInput.files.length > 0) {
            btnGenerate.classList.remove('d-none');
        } else {
            btnGenerate.classList.add('d-none');
        }
    });
});

async function initTableNotulen() {
    notulenTable = $('#tableNotulen').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: urls,
            type: 'GET',
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'rapat', name: 'rapat' },
            { data: 'tanggal', name: 'tanggal' },
            { data: 'isi', name: 'isi' },
            { data: 'status', name: 'status' },
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
    formData = new FormData($('#formNotulen')[0]);

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
            $('#modalNotulen').modal('hide');
            if (response.status) {
                notulenTable.ajax.reload();
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
                text: 'Mengambil data Notulen...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
        },
        success: function (response) {
            Swal.close();
            const data = response.data || {};
            $('#id').val(data.id || '');
            $('#isi').val(data.isi || '');
            $('#rapat').val(data.rapat_id || '');
            $('#name_filePath').text(data.file_path || '');
            save_method = 'edit';
            $('#modalNotulen').modal('show');
            $('.modal-title').text('Edit Data Notulen');
        },
        error: function (xhr) {
            Swal.close();
            let message = 'Terjadi kesalahan saat mengambil data.';
            if (xhr.responseJSON?.message) message = xhr.responseJSON.message;
            Swal.fire({ title: 'Error!', text: message, icon: 'error' });
        }
    });
}

$(document).on('click', '.deleteNotulen', function () {
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
                        notulenTable.ajax.reload();
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


$(document).on('click', '.detailNotulen', function () {
    var id = $(this).data('id');
    currentRapatId = id;
    showSelectedDataNotulen(id);
});

function showSelectedDataNotulen(id) {
    $.ajax({
        type: "GET",
        url: '/approval/notulen/' + id,
        beforeSend: function () {
            Swal.fire({
                title: 'Loading...',
                text: 'Mengambil data Notulen...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
        },
        success: function (response) {
            Swal.close();
            const data = response.data || {};
            $('#detailKegiatan').text(data.rapat.judul || '-');
            $('#detailTanggal').text(data.rapat.tanggal || '-');
            $('#detailIsiNotulen').text(data.isi || '-');
            $('#modalDetailNotulen').modal('show');

            if (data.status == 'disetujui') {
                $('#modalDetailNotulen .btn-success, #modalDetailNotulen .btn-danger').hide();
            } else {
                $('#modalDetailNotulen .btn-success, #modalDetailNotulen .btn-danger').show();
            }


        },
        error: function (xhr) {
            Swal.close();
            let message = 'Terjadi kesalahan saat mengambil data.';
            if (xhr.responseJSON?.message) message = xhr.responseJSON.message;
            Swal.fire({
                title: 'Error!',
                text: message,
                icon: 'error'
            });
        }
    });
}

