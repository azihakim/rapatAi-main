// Tampilkan pengajuan logistik sebelumnya saat memilih rapat
$(document).on('change', '#rapat', function () {
    var rapatId = $(this).val();
    if (!rapatId) {
        $('#pengajuanSebelumnyaSection').hide();
        $('#tablePengajuanSebelumnya tbody').empty();
        return;
    }
    $.ajax({
        url: '/logistik/pengajuan-sebelumnya/' + rapatId,
        type: 'GET',
        success: function (res) {
            if (res.status && res.data.length > 0) {
                var rows = '';
                res.data.forEach(function (item, idx) {
                    rows += `<tr>
                        <td>${idx + 1}</td>
                        <td>${item.jenis_pengajuan}</td>
                        <td>${item.keterangan || '-'}</td>
                        <td>${item.status}</td>
                        <td>
                            <button type="button" class="btn btn-info btn-sm detailLogistik" data-id="${item.id}">Detail</button>
                        </td>
                    </tr>`;
                });
                $('#tablePengajuanSebelumnya tbody').html(rows);
                $('#pengajuanSebelumnyaSection').show();
            } else {
                $('#tablePengajuanSebelumnya tbody').html('<tr><td colspan="5" class="text-center">Belum ada pengajuan logistik untuk rapat ini.</td></tr>');
                $('#pengajuanSebelumnyaSection').show();
            }
        },
        error: function () {
            $('#pengajuanSebelumnyaSection').hide();
            $('#tablePengajuanSebelumnya tbody').empty();
        }
    });
});
var logistikTable;
var save_method;
let myData = {};
const urls = '/logistik';
var currentRapatId = null;

jQuery(function () {
    myData._token = $('meta[name="csrf-token"]').attr('content')
})

$(document).ready(function () {
    initTableLogistik();
});

$(document).on('click', '.addLogistik', function () {
    $('#modalLogistik').modal('show');
    $('.modal-title').text('Tambah Data Pengajuan Logistik');
    save_method = 'add';
});

$(document).on('click', '.editLogistik', function () {
    var id = $(this).data('id');
    showSelectedData(id);
});

$(document).on('click', '.detailLogistik', function () {
    var id = $(this).data('id');
    currentRapatId = id;
    showSelectedDataDetail(id);
});

$('#modalLogistik').on('hidden.bs.modal', function () {
    $('#formLogistik')[0].reset();
    $('#id').val('');
    $('.modal-title').text('');
    const tbody = $('#tableDetailLogistik tbody');
    tbody.empty();
    const row = `
                                    <tr>
                                        <td><input type="text" name="detail[0][item]" class="form-control" required></td>
                                        <td><input type="number" name="detail[0][jumlah]" class="form-control" min="1" required></td>
                                        <td><input type="text" name="detail[0][satuan]" class="form-control" required></td>
                                        <td><input type="text" name="detail[0][keterangan]" class="form-control"></td>
                                        <td><button type="button" class="btn btn-sm btn-danger removeDetailRow">-</button></td>
                                    </tr>
                                `;
    tbody.append(row);
    detailIndex = 1;

    save_method = '';

    var form = $('#formLogistik');
    form.validate().resetForm();
    form.find('.form-control').removeClass('is-invalid');
    form.find('.form-control').removeClass('is-valid');
});

async function initTableLogistik() {
    logistikTable = $('#tableLogistik').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: urls,
            type: 'GET',
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'kegiatan', name: 'kegiatan' },
            { data: 'tanggal', name: 'tanggal' },
            { data: 'diajukan', name: 'diajukan' },
            { data: 'status', name: 'status' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
        order: [[1, 'asc']],
        responsive: true,
        autoWidth: false,
    });
}

let detailIndex = 1;
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('addDetailRow').addEventListener('click', function () {
        const tbody = document.querySelector('#tableDetailLogistik tbody');
        const row = document.createElement('tr');
        row.innerHTML = `
                                    <td><input type="text" name="detail[${detailIndex}][item]" class="form-control" required></td>
                                    <td><input type="number" name="detail[${detailIndex}][jumlah]" class="form-control" min="1" required></td>
                                    <td><input type="text" name="detail[${detailIndex}][satuan]" class="form-control" required></td>
                                    <td><input type="text" name="detail[${detailIndex}][keterangan]" class="form-control"></td>
                                    <td><button type="button" class="btn btn-sm btn-danger removeDetailRow">-</button></td>
                                `;
        tbody.appendChild(row);
        detailIndex++;
    });

    document.querySelector('#tableDetailLogistik tbody').addEventListener('click', function (e) {
        if (e.target.classList.contains('removeDetailRow')) {
            const rows = document.querySelectorAll('#tableDetailLogistik tbody tr');
            if (rows.length > 1) {
                e.target.closest('tr').remove();
            }
        }
    });
});

function save() {
    var url, method, formData;
    method = "POST";
    url = urls;
    formData = new FormData($('#formLogistik')[0]);

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
            $('#modalLogistik').modal('hide');
            if (response.status) {
                logistikTable.ajax.reload();
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
                text: 'Mengambil data Logistik...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
        },
        success: function (response) {
            if (response.status) {
                Swal.close();
                const data = response.data || {};
                $('#id').val(data.id || '');
                $('#rapat').val(data.rapat_id || '');
                $('#jenis_pengajuan').val(data.jenis_pengajuan || '');
                $('#keterangan').val(data.keterangan || '');
                const tbody = $('#tableDetailLogistik tbody');
                tbody.empty();
                if (data.details && data.details.length > 0) {
                    detailIndex = 0;
                    data.details.forEach(detail => {
                        const row = `
                                                <tr>
                                                    <td><input type="text" name="detail[${detailIndex}][item]" class="form-control" value="${detail.item || ''}" required></td>
                                                    <td><input type="number" name="detail[${detailIndex}][jumlah]" class="form-control" min="1" value="${detail.jumlah || ''}" required></td>
                                                    <td><input type="text" name="detail[${detailIndex}][satuan]" class="form-control" value="${detail.satuan || ''}" required></td>
                                                    <td><input type="text" name="detail[${detailIndex}][keterangan]" class="form-control" value="${detail.keterangan || ''}"></td>
                                                    <td><button type="button" class="btn btn-sm btn-danger removeDetailRow">-</button></td>
                                                </tr>
                                            `;
                        tbody.append(row);
                        detailIndex++;
                    });
                } else {
                    const row = `
                                            <tr>
                                                <td><input type="text" name="detail[0][item]" class="form-control" required></td>
                                                <td><input type="number" name="detail[0][jumlah]" class="form-control" min="1" required></td>
                                                <td><input type="text" name="detail[0][satuan]" class="form-control" required></td>
                                                <td><input type="text" name="detail[0][keterangan]" class="form-control"></td>
                                                <td><button type="button" class="btn btn-sm btn-danger removeDetailRow">-</button></td>
                                            </tr>   
                                        `;
                    tbody.append(row);
                    detailIndex = 1;
                }
                save_method = 'edit';
                $('#modalLogistik').modal('show');
                $('.modal-title').text('Edit Data Pengajuan Logistik');
            }
        },
        error: function (xhr) {
            Swal.close();
            let message = 'Terjadi kesalahan saat mengambil data.';
            if (xhr.responseJSON?.message) message = xhr.responseJSON.message;
            Swal.fire({ title: 'Error!', text: message, icon: 'error' });
        }
    });
}

function showSelectedDataDetail(id) {
    $.ajax({
        type: "GET",
        url: urls + '/' + id,
        beforeSend: function () {
            Swal.fire({
                title: 'Loading...',
                text: 'Mengambil data Detail...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
        },
        success: function (response) {
            if (response.status) {
                Swal.close();
                const data = response.data || {};

                // Isi field header
                $('#detailJudul').text(data.rapat?.judul || '-');
                $('#detailHariTanggal').text(data.rapat?.tanggal || '-');
                $('#detailJenisPengajuan').text(data.jenis_pengajuan || '-');
                $('#detailPengaju').text(data.user?.name || '-');
                $('#detailKeterangan').text(data.keterangan || '-');
                $('#detailStatus').text(data.status || '-');
                $('#detailTanggal').text(data.rapat?.tanggal || '-');
                $('#detailTanggalPengajuan').text(data.created_at || '-');
                $('#detailNamaPengaju').text(data.user?.name || '-');

                // Isi tabel detail logistik
                const tbody = $('#detailLogistikTable tbody');
                tbody.empty();

                if (data.details && data.details.length > 0) {
                    data.details.forEach((detail, index) => {
                        const row = `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${detail.item || '-'}</td>
                                <td>${detail.jumlah || '-'}</td>
                                <td>${detail.satuan || '-'}</td>
                                <td>${detail.keterangan || '-'}</td>
                            </tr>
                        `;
                        tbody.append(row);
                    });
                } else {
                    tbody.append(`
                        <tr>
                            <td colspan="5" class="text-center text-muted">Tidak ada detail logistik</td>
                        </tr>
                    `);
                }

                // Tampilkan modal
                $('#modalDetailLogistik').modal('show');
            }
        },
        error: function (xhr) {
            Swal.close();
            let message = 'Terjadi kesalahan saat mengambil data.';
            if (xhr.responseJSON?.message) message = xhr.responseJSON.message;
            Swal.fire({ title: 'Error!', text: message, icon: 'error' });
        }
    });
}


$(document).on('click', '.deleteLogistik', function () {
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
                        logistikTable.ajax.reload();
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