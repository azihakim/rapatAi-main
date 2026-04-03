@extends('layouts.app')
@section('title', 'Approval Notulen')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="tableNotulen" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kegiatan / Acara</th>
                            <th>Hari / Tanggal</th>
                            <th>Isi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Detail Notulen -->
    <div class="modal fade" id="modalDetailNotulen" tabindex="-1" role="dialog" aria-labelledby="modalDetailNotulenLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDetailNotulenLabel">Detail Notulen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="detailNotulenContent">
                        <p><strong>Kegiatan / Acara:</strong> <span id="detailKegiatan"></span></p>
                        <p><strong>Hari / Tanggal:</strong> <span id="detailTanggal"></span></p>
                        <p><strong>Isi Notulen:</strong></p>
                        <div id="detailIsiNotulen"
                            style="white-space: pre-wrap; border: 1px solid #ddd; padding: 10px; border-radius: 5px; max-height: 400px; overflow-y: auto;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div>
                        <button type="button" class="btn btn-danger me-2" onclick="rejectUndangan()">
                            <i class="fas fa-times"></i> Reject
                        </button>
                        <button type="button" class="btn btn-success" onclick="approveUndangan()">
                            <i class="fas fa-check"></i> Approve
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('js/notulen.js') }}?v={{ time() }}"></script>
    <script>
        var currentRapatId = null;
        $(document).on('click', '.detailNotulen', function() {
            var id = $(this).data('id');
            currentRapatId = id;
            showSelectedData(id);
        });

        function showSelectedData(id) {
            $.ajax({
                type: "GET",
                url: '/approval/notulen/' + id,
                beforeSend: function() {
                    Swal.fire({
                        title: 'Loading...',
                        text: 'Mengambil data Notulen...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                },
                success: function(response) {
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
                error: function(xhr) {
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

        // Fungsi approve undangan
        function approveUndangan() {
            if (!currentRapatId) return;
            Swal.fire({
                title: 'Konfirmasi Approval',
                text: 'Apakah Anda yakin ingin menyetujui notulen rapat ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Setujui',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    updateStatus(currentRapatId, 'disetujui');
                }
            });
        }

        // Fungsi reject undangan
        function rejectUndangan() {
            if (!currentRapatId) return;

            Swal.fire({
                title: 'Tolak Notulen',
                text: 'Apakah Anda yakin ingin menolak notulen rapat ini?',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Tolak',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const reason = result.value || null;
                    updateStatus(currentRapatId, 'ditolak');
                }
            });
        }

        function updateStatus(id, status) {
            $.ajax({
                type: "POST",
                url: "{{ url('/approval/update-status-notulen/') }}/" + id,
                data: {
                    _token: "{{ csrf_token() }}",
                    status: status
                },
                beforeSend: function() {
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Memperbarui status...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                },
                success: function(response) {
                    Swal.close();
                    if (response.status) {
                        $('#modalDetailNotulen').modal('hide');

                        const statusText = status == 2 ? 'disetujui' : 'ditolak';
                        const icon = status == 2 ? 'success' : 'info';

                        Swal.fire({
                            title: 'Berhasil!',
                            text: `Notulen berhasil ${statusText}`,
                            icon: icon,
                            timer: 2000
                        });

                        // Reload DataTable
                        notulenTable.ajax.reload(null, false);
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message || 'Gagal memperbarui status',
                            icon: 'error'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    let message = 'Terjadi kesalahan saat memperbarui status.';
                    if (xhr.responseJSON?.message) {
                        message = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        title: 'Error!',
                        text: message,
                        icon: 'error'
                    });
                }
            });
        }
    </script>
@endpush
