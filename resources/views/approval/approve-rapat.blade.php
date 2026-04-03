@extends('layouts.app')
@section('title', 'Approval Rapat')
@push('styles')
    <style>
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .btn-detail {
            padding: 0.25rem 0.75rem;
            font-size: 0.875rem;
        }

        /* Modal Surat Styles */
        .modal-xl {
            max-width: 90%;
        }

        .surat-container {
            background: white;
            padding: 40px;
            font-family: 'Times New Roman', serif;
            line-height: 1.6;
            color: #333;
        }

        .kop-surat {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
        }

        .logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(45deg, #007bff, #0056b3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: bold;
        }

        .kop-text h3 {
            margin: 0;
            font-size: 22px;
            font-weight: bold;
            color: #2c3e50;
            text-transform: uppercase;
        }

        .kop-text h4 {
            margin: 5px 0;
            font-size: 18px;
            font-weight: bold;
            color: #34495e;
        }

        .kop-text p {
            margin: 2px 0;
            font-size: 14px;
            color: #555;
        }

        .surat-header {
            margin-bottom: 25px;
        }

        .tanggal-surat {
            text-align: right;
            margin-bottom: 20px;
            font-size: 16px;
        }

        .detail-surat table {
            width: 100%;
            margin-bottom: 25px;
        }

        .detail-surat td {
            padding: 3px 0;
            vertical-align: top;
        }

        .detail-surat .label {
            width: 120px;
            font-weight: bold;
        }

        .detail-surat .colon {
            width: 20px;
            text-align: center;
        }

        .isi-surat {
            text-align: justify;
            margin-bottom: 30px;
            font-size: 16px;
        }

        .agenda-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .agenda-table td {
            padding: 8px 12px;
            border: 1px solid #ddd;
            vertical-align: top;
        }

        .agenda-table .label-agenda {
            background-color: #f8f9fa;
            font-weight: bold;
            width: 150px;
        }

        .penutup {
            margin-top: 40px;
        }

        .ttd-section {
            display: flex;
            justify-content: flex-end;
            margin-top: 40px;
        }

        .ttd-box {
            text-align: center;
            min-width: 250px;
        }

        .ttd-space {
            height: 80px;
            margin: 20px 0;
        }

        @media print {

            .modal-header,
            .modal-footer {
                display: none !important;
            }

            .modal-body {
                padding: 0 !important;
            }
        }

        @media (max-width: 768px) {
            .surat-container {
                padding: 20px;
                font-size: 14px;
            }

            .logo {
                width: 60px;
                height: 60px;
                font-size: 18px;
            }

            .kop-text h3 {
                font-size: 18px;
            }

            .kop-text h4 {
                font-size: 16px;
            }

            .modal-xl {
                max-width: 95%;
            }
        }

        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
    </style>
@endpush
@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-clipboard-list me-2"></i>
                Daftar Undangan Rapat untuk Approval
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="tableRapat" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Nomor Surat</th>
                            <th width="20%">Hari / Tanggal</th>
                            <th width="15%">Waktu / Pukul</th>
                            <th width="15%">Tempat</th>
                            <th width="20%">Jenis Rapat</th>
                            <th width="15%">Status</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <div class="modal fade" id="suratModal" tabindex="-1" role="dialog" aria-labelledby="suratModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="suratModalLabel">
                        <i class="fas fa-file-alt me-2"></i>
                        Surat Undangan Rapat
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div class="surat-container">
                        <!-- Kop Surat -->
                        <div class="kop-surat">
                            <div class="logo-container">
                                <div class="">
                                    <img src="{{ asset('img/Logo_DPRD_Sumatera_Selatan.png') }}" alt="Logo DPRD"
                                        style="width: 160px; height: 160px;">
                                </div>
                                <div class="kop-text">
                                    <h3>Dewan Perwakilan Rakyat Daerah</h3>
                                    <h4>Provinsi Sumatera Selatan</h4>
                                    <p>Jl. Kapten A. Rivai No. 2, Palembang 30129</p>
                                    <p>Telp: (0711) 354654 | Email: dprd@sumselprov.go.id</p>
                                    <p>Website: www.dprd.sumselprov.go.id</p>
                                </div>
                            </div>
                        </div>

                        <!-- Tanggal Surat -->
                        <div class="tanggal-surat">
                            <p id="tanggal-surat">Palembang, 30 April 2025</p>
                        </div>

                        <!-- Detail Surat -->
                        <div class="detail-surat">
                            <table>
                                <tr>
                                    <td class="label">Nomor</td>
                                    <td class="colon">:</td>
                                    <td id="nomor-surat">000.1.5/01642/DPRD-SS/2025</td>
                                </tr>
                                <tr>
                                    <td class="label">Sifat</td>
                                    <td class="colon">:</td>
                                    <td id="sifat-surat">Penting</td>
                                </tr>
                                <tr>
                                    <td class="label">Lampiran</td>
                                    <td class="colon">:</td>
                                    <td>-</td>
                                </tr>
                                <tr>
                                    <td class="label">Hal</td>
                                    <td class="colon">:</td>
                                    <td id="perihal-surat"><strong>Undangan Rapat</strong></td>
                                </tr>
                            </table>
                        </div>

                        <!-- Isi Surat -->
                        <div class="isi-surat">
                            <p>Kepada Yth.<br>
                                Seluruh Anggota DPRD Provinsi Sumatera Selatan<br>
                                di Tempat</p>

                            <p>Dengan hormat,</p>

                            <p>Sehubungan dengan adanya agenda penting yang perlu dibahas bersama, maka dengan ini kami
                                mengundang Bapak/Ibu untuk menghadiri:</p>

                            <table class="agenda-table">
                                <tr>
                                    <td class="label-agenda">Acara</td>
                                    <td id="acara-rapat">Rapat Badan Musyawarah DPRD Provinsi Sumatera Selatan</td>
                                </tr>
                                <tr>
                                    <td class="label-agenda">Agenda</td>
                                    <td id="agenda-rapat">Membahas jadwal kegiatan dan program kerja semester II tahun 2025
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label-agenda">Hari/Tanggal</td>
                                    <td id="tanggal-rapat">Minggu, 05 Mei 2025</td>
                                </tr>
                                <tr>
                                    <td class="label-agenda">Waktu</td>
                                    <td id="waktu-rapat">09.00 WIB s.d Selesai</td>
                                </tr>
                                <tr>
                                    <td class="label-agenda">Tempat</td>
                                    <td id="tempat-rapat">Ruang Rapat DPRD Provinsi Sumatera Selatan</td>
                                </tr>
                            </table>

                            <p>Mengingat pentingnya acara tersebut, diharapkan Bapak/Ibu dapat hadir tepat waktu. Atas
                                perhatian dan kehadiran Bapak/Ibu, kami ucapkan terima kasih.</p>
                        </div>

                        <!-- Penutup -->
                        <div class="penutup">
                            <div class="ttd-section">
                                <div class="ttd-box">
                                    <p>Ketua DPRD Provinsi Sumatera Selatan</p>
                                    <div class="ttd-space"></div>
                                    <p><strong><u id="nama-ttd">Andie Dinialdie, SE, MM</u></strong><br>
                                        NIP. 196812251994031008</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex justify-content-between w-100">
                        <div>
                            <button type="button" class="btn btn-secondary" onclick="printSurat()">
                                <i class="fas fa-print"></i> Print
                            </button>
                        </div>
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
    <script src="{{ asset('js/rapat.js') }}?v={{ time() }}"></script>
    <script>
        var currentRapatId = null;
        $(document).on('click', '.approveDetail', function() {
            var id = $(this).data('id');
            currentRapatId = id;
            showSelectedData(id);
        });

        function showSelectedData(id) {
            $.ajax({
                type: "GET",
                url: "{{ url('/approval/surat-undangan-rapat') }}/" + id,
                beforeSend: function() {
                    Swal.fire({
                        title: 'Loading...',
                        text: 'Mengambil data surat undangan...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                },
                success: function(response) {
                    Swal.close();
                    if (response.status) {
                        const data = response.data;

                        // Update konten modal dengan data dari server
                        $('#nomor-surat').text(data.nomor);
                        $('#sifat-surat').text(data.sifat);
                        $('#tanggal-surat').text(data.tanggal);
                        $('#perihal-surat').html('<strong>' + data.perihal + '</strong>');
                        $('#acara-rapat').text(data.acara);
                        $('#agenda-rapat').text(data.agenda);
                        $('#tanggal-rapat').text(data.tanggalRapat);
                        $('#waktu-rapat').text(data.waktu);
                        $('#tempat-rapat').text(data.tempat);
                        $('#nama-ttd').text(data.penandatangan);

                        // Show atau hide tombol berdasarkan status
                        if (data.status == 2) {
                            $('.btn-success, .btn-danger').hide();
                            $('.modal-footer').prepend(
                                '<div class="alert alert-success mb-0">Rapat ini sudah disetujui</div>');
                        } else if (data.status == 3) {
                            $('.btn-success, .btn-danger').hide();
                            $('.modal-footer').prepend(
                                '<div class="alert alert-danger mb-0">Rapat ini sudah ditolak</div>');
                        } else {
                            $('.btn-success, .btn-danger').show();
                            $('.modal-footer .alert').remove();
                        }

                        $('#suratModal').modal('show');
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message || 'Gagal mengambil data',
                            icon: 'error'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    let message = 'Terjadi kesalahan saat mengambil data.';
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

        // Fungsi approve undangan
        function approveUndangan() {        
            if (!currentRapatId) return;    
            Swal.fire({
                title: 'Konfirmasi Approval',
                text: 'Apakah Anda yakin ingin menyetujui undangan rapat ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Setujui',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    updateStatus(currentRapatId, 2);
                }
            });
        }

        // Fungsi reject undangan
        function rejectUndangan() {
            if (!currentRapatId) return;

            Swal.fire({
                title: 'Tolak Undangan',
                text: 'Apakah Anda yakin ingin menolak undangan rapat ini?',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Tolak',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const reason = result.value || null;
                    updateStatus(currentRapatId, 3);
                }
            });
        }

        // Fungsi update status
        function updateStatus(id, status) {
            $.ajax({
                type: "POST",
                url: "{{ url('/approval/update-status') }}/" + id,
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
                        $('#suratModal').modal('hide');

                        const statusText = status == 2 ? 'disetujui' : 'ditolak';
                        const icon = status == 2 ? 'success' : 'info';

                        Swal.fire({
                            title: 'Berhasil!',
                            text: `Undangan rapat berhasil ${statusText}`,
                            icon: icon,
                            timer: 2000
                        });

                        // Reload DataTable
                        rapatTable.ajax.reload(null, false);
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

        // Fungsi print surat
        function printSurat() {
            window.print();
        }

        // Reset modal ketika ditutup
        $('#suratModal').on('hidden.bs.modal', function() {
            currentRapatId = null;
            $('.modal-footer .alert').remove();
            $('.btn-success, .btn-danger').show();
        });

        // Remove tambah button karena ini halaman approval
        $('.addRapat').hide();
    </script>
@endpush
