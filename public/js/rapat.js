
var rapatTable;
var save_method;
let myData = {};
const urls = '/rapat'

jQuery(function () {
    myData._token = $('meta[name="csrf-token"]').attr('content')
})

$(document).on('hidden.bs.modal', '.modal', function () {
    if ($('.modal.show').length > 0) {
        $('body').addClass('modal-open');
    }
});

function fetchJadwalPeserta() {
    let pesertaIds = [];
    $('.checkboxPeserta:checked').each(function () {
        pesertaIds.push($(this).val());
    });
    let tanggal = $('#tanggal').val();
    if (pesertaIds.length === 0 || !tanggal) {
        $('#jadwalPesertaTerpilih').html('<em>Pilih peserta dan tanggal rapat untuk melihat jadwal ketersediaan mereka.</em>');
        return;
    }
    $('#jadwalPesertaTerpilih').html('<span class="text-muted">⏳ Mengambil jadwal...</span>');
    $.ajax({
        url: '/rapat/jadwal-peserta',
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            peserta: pesertaIds,
            tanggal: tanggal
        },
        success: function(res) {
            let html = '';
            if (res.data && res.data.length > 0) {
                res.data.forEach(function(p) {
                    html += `<b>${p.nama}</b><ul>`;
                    if (p.jadwal.length > 0) {
                        p.jadwal.forEach(function(j) {
                            html += `<li>${j.tanggal} (${j.mulai} - ${j.selesai})</li>`;
                        });
                    } else {
                        html += '<li><em>Tidak ada jadwal tersedia di tanggal ini</em></li>';
                    }
                    html += '</ul>';
                });
            } else {
                html = '<em>Tidak ada peserta terpilih atau jadwal tidak ditemukan.</em>';
            }
            $('#jadwalPesertaTerpilih').html(html);
        },
        error: function() {
            $('#jadwalPesertaTerpilih').html('<span class="text-danger">Gagal mengambil jadwal peserta.</span>');
        }
    });
}

// Trigger fetch jadwal saat peserta atau tanggal berubah
$(document).on('change', '.checkboxPeserta, #tanggal', fetchJadwalPeserta);


$(document).ready(function () {
    initTableRapat();

    $('#tablePesertaRapat').DataTable({
        searching: true,
        columnDefs: [
            { orderable: false, targets: 0 }
        ],
        lengthChange: false
    });

    // Filter peserta berdasarkan jabatan dan komisi
    $('#filterJabatan, #filterKomisi').on('change', function () {
        var jabatan = $('#filterJabatan').val();
        var komisi = $('#filterKomisi').val();
        $('#tablePesertaRapat tbody tr').each(function () {
            var show = true;
            if (jabatan && $(this).data('jabatan') !== jabatan) show = false;
            if (komisi && $(this).data('komisi') !== komisi) show = false;
            $(this).toggle(show);
        });
    });
});

$(document).on('click', '.addRapat', function () {
    $('#modalRapat').modal('show');
    $('.modal-title').text('Tambah Data Rapat');
    save_method = 'add';
});

$(document).on('click', '.editRapat', function () {
    var id = $(this).data('id');
    showSelectedData(id);
});

$('#modalRapat').on('hidden.bs.modal', function () {
    $('#formTambahRapat')[0].reset();
    $('#id').val('');
    $('.modal-title').text('');
    save_method = '';

    var form = $('#formTambahRapat');
    form.validate().resetForm();
    form.find('.form-control').removeClass('is-invalid');
    form.find('.form-control').removeClass('is-valid');
});

document.addEventListener('DOMContentLoaded', function () {
    const checkAll = document.getElementById('checkAllPeserta');
    const checkboxes = document.querySelectorAll('.checkboxPeserta');

    checkAll?.addEventListener('change', function () {
        checkboxes.forEach(cb => cb.checked = checkAll.checked);
    });

    checkboxes.forEach(cb => {
        cb.addEventListener('change', function () {
            if (!cb.checked) {
                checkAll.checked = false;
            } else if ([...checkboxes].every(c => c.checked)) {
                checkAll.checked = true;
            }
        });
    });
});

async function initTableRapat() {
    rapatTable = $('#tableRapat').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: urls,
            type: 'GET',
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'nomor', name: 'nomor' },
            { data: 'tanggal_rapat', name: 'tanggal_rapat' },
            { data: 'waktu_rapat', name: 'waktu_rapat' },
            { data: 'lokasi', name: 'lokasi' },
            { data: 'judul', name: 'judul' },
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
    formData = new FormData($('#formTambahRapat')[0]);

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
            $('#modalRapat').modal('hide');
            if (response.status) {
                rapatTable.ajax.reload();
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
                text: 'Mengambil data Rapat...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
        },
        success: function (response) {
            Swal.close();
            const data = response.data || {};
            $('#id').val(data.id || '');
            $('#tanggal').val(data.tanggal || '');
            $('#waktu').val(data.waktu || '');
            $('#lokasi').val(data.lokasi || '');
            $('#judul').val(data.judul || '');
            $('#deskripsi').val(data.deskripsi || '');
            $('#jenis_rapat').val(data.jenis_rapat || '');
            $('#hal').val(data.hal || '');
            $('#pendatangan').val(data.pendatangan_id || '');
            $('#sifat').val(data.sifat || '');

            $('#peserta_' + data.peserta_rapat?.map(p => p.user.id).join(', #peserta_') || '').prop('checked', true);

            save_method = 'edit';
            $('#modalRapat').modal('show');
            $('.modal-title').text('Edit Data Rapat');
        },
        error: function (xhr) {
            Swal.close();
            let message = 'Terjadi kesalahan saat mengambil data.';
            if (xhr.responseJSON?.message) message = xhr.responseJSON.message;
            Swal.fire({ title: 'Error!', text: message, icon: 'error' });
        }
    });
}

$(document).on('click', '.deleteRapat', function () {
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
                        rapatTable.ajax.reload();
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

$(document).on('click', '#btnRekomendasi', function () {
    $('#modalRekomendasi').modal('show');
    $('.modal-title').text('Generate Rekomendasi Jadwal Rapat');
});

$('#modalRekomendasi').on('hidden.bs.modal', function () {
    $('#durasi').val('');
    $('#hasilRekomendasi').val('');
    $('.modal-title').text('');
});

$('#btnGenerateRekomendasi').on('click', function () {
    let durasi = $('#durasi').val();

    if (!durasi) {
        alert('Durasi rapat wajib diisi');
        return;
    }

    // Kumpulkan data peserta terpilih
    let pesertaIds = [];
    $('.checkboxPeserta:checked').each(function () {
        pesertaIds.push($(this).val());
    });

    if (pesertaIds.length === 0) {
        alert('Pilih minimal satu peserta rapat');
        return;
    }

    // Set loading state
    $('#hasilRekomendasi').val("⏳ Sedang mencari rekomendasi jadwal...");
    $('#btnGenerateRekomendasi').prop('disabled', true).text('Loading...');

    $.ajax({
        url: '/rekomendasi-jadwal',
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            peserta: pesertaIds,
            duration: durasi,
            tanggal : $('#tanggal').val()
        },
        success: function (res) {
            // Hapus isi textarea
            $('#hasilRekomendasi').val('');

            // Animasi typing
            let text = JSON.stringify(res.recommendation, null, 2);
            let i = 0;
            function typeWriter() {
                if (i < text.length) {
                    $('#hasilRekomendasi').val(function (_, val) {
                        return val + text.charAt(i);
                    });
                    i++;
                    setTimeout(typeWriter, 15); // kecepatan ketikan
                }
            }
            typeWriter();
        },
        error: function () {
            $('#hasilRekomendasi').val('❌ Gagal generate rekomendasi');
        },
        complete: function () {
            // Reset tombol
            $('#btnGenerateRekomendasi').prop('disabled', false).text('Generate');
        }
    });
});

