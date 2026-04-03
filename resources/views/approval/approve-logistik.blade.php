@extends('layouts.app')
@section('title', 'Approval Logistik')
@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-end">
            <button type="button" class="btn btn-primary addLogistik">Tambah</button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="tableLogistik" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kegiatan / Acara</th>
                            <th>Hari / Tanggal</th>
                            <th>Di ajukan Oleh</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalDetailLogistik" tabindex="-1" aria-labelledby="modalDetailLogistikLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDetailLogistikLabel">Detail Pengajuan Logistik</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="detailContent">
                        <div class="border p-4 rounded" style="background: #f9f9f9;">
                            <div class="text-center mb-4">
                                <h4 class="fw-bold mb-1">PENGAJUAN LOGISTIK</h4>
                                <small class="text-muted" id="detailTanggal"></small>
                            </div>
                            <div class="mb-3">
                                <table class="table table-borderless">
                                    <tr>
                                        <th style="width: 200px;">Kegiatan / Acara</th>
                                        <td id="detailJudul"></td>
                                    </tr>
                                    <tr>
                                        <th>Hari / Tanggal</th>
                                        <td id="detailHariTanggal"></td>
                                    </tr>
                                    <tr>
                                        <th>Jenis Pengajuan</th>
                                        <td id="detailJenisPengajuan"></td>
                                    </tr>
                                    <tr>
                                        <th>Di Ajukan Oleh</th>
                                        <td id="detailPengaju"></td>
                                    </tr>
                                    <tr>
                                        <th>Keterangan</th>
                                        <td id="detailKeterangan"></td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td id="detailStatus"></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="mb-2">
                                <h6 class="fw-bold">Detail Logistik</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="detailLogistikTable">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Item</th>
                                                <th>Jumlah</th>
                                                <th>Satuan</th>
                                                <th>Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Data logistik akan dimuat di sini -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="mt-4 text-end">
                                <span id="detailTanggalPengajuan"></span><br>
                                <span id="detailNamaPengaju"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
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
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('js/logistik.js') }}?v={{ time() }}"></script>
    <script>
        function approveUndangan() {
            if (!currentRapatId) return;
            Swal.fire({
                title: 'Konfirmasi Approval',
                text: 'Apakah Anda yakin ingin menyetujui logistik ini?',
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
                text: 'Apakah Anda yakin ingin menolak logistik ini?',
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
                url: "{{ url('/approval/update-status-logistik/') }}/" + id,
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
                        $('#modalDetailLogistik').modal('hide');

                        const statusText = status == 'disetujui' ? 'disetujui' : 'ditolak';
                        const icon = status == 'disetujui' ? 'success' : 'info';

                        Swal.fire({
                            title: 'Berhasil!',
                            text: `Logistik berhasil ${statusText}`,
                            icon: icon,
                            timer: 2000
                        });

                        // Reload DataTable
                        logistikTable.ajax.reload(null, false);
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
