<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval Rapat - DPRD Sumsel</title>
    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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
            .modal-header, .modal-footer {
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
</head>
<body>
    

    <!-- Modal Surat Undangan -->
    <div class="modal fade" id="suratModal" tabindex="-1" role="dialog" aria-labelledby="suratModalLabel" aria-hidden="true">
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
                                <div class="logo">
                                    SS
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
                                    <td>Penting</td>
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

                            <p>Sehubungan dengan adanya agenda penting yang perlu dibahas bersama, maka dengan ini kami mengundang Bapak/Ibu untuk menghadiri:</p>

                            <table class="agenda-table">
                                <tr>
                                    <td class="label-agenda">Acara</td>
                                    <td id="acara-rapat">Rapat Badan Musyawarah DPRD Provinsi Sumatera Selatan</td>
                                </tr>
                                <tr>
                                    <td class="label-agenda">Agenda</td>
                                    <td id="agenda-rapat">Membahas jadwal kegiatan dan program kerja semester II tahun 2025</td>
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

                            <p>Mengingat pentingnya acara tersebut, diharapkan Bapak/Ibu dapat hadir tepat waktu. Atas perhatian dan kehadiran Bapak/Ibu, kami ucapkan terima kasih.</p>
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

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <!-- Bootstrap 4 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>

    <script>
        // Data dummy surat undangan
        const suratData = {
            1: {
                nomor: "000.1.5/01642/DPRD-SS/2025",
                tanggal: "Palembang, 30 April 2025",
                perihal: "Undangan Rapat",
                acara: "Rapat Badan Musyawarah DPRD Provinsi Sumatera Selatan",
                agenda: "Membahas jadwal kegiatan dan program kerja semester II tahun 2025",
                tanggalRapat: "Minggu, 05 Mei 2025",
                waktu: "09.00 WIB s.d Selesai",
                tempat: "Ruang Rapat DPRD Provinsi Sumatera Selatan",
                penandatangan: "Andie Dinialdie, SE, MM"
            },
            2: {
                nomor: "000.1.5/01643/DPRD-SS/2025",
                tanggal: "Palembang, 28 April 2025",
                perihal: "Undangan Rapat Komisi",
                acara: "Rapat Komisi A DPRD Provinsi Sumatera Selatan",
                agenda: "Pembahasan APBD Tahun 2025 dan evaluasi program pendidikan",
                tanggalRapat: "Kamis, 02 Mei 2025",
                waktu: "10.00 WIB s.d Selesai",
                tempat: "Ruang Komisi A DPRD Provinsi Sumatera Selatan",
                penandatangan: "Dr. Siti Aminah, M.Si"
            },
            3: {
                nomor: "000.1.5/01644/DPRD-SS/2025",
                tanggal: "Palembang, 25 April 2025",
                perihal: "Undangan Rapat Paripurna",
                acara: "Rapat Paripurna DPRD Provinsi Sumatera Selatan",
                agenda: "Pembahasan Peraturan Daerah tentang Retribusi Daerah",
                tanggalRapat: "Senin, 29 April 2025",
                waktu: "09.00 WIB s.d Selesai",
                tempat: "Ruang Paripurna DPRD Provinsi Sumatera Selatan",
                penandatangan: "H. Ahmad Syukri, S.H., M.H"
            },
            4: {
                nomor: "000.1.5/01645/DPRD-SS/2025",
                tanggal: "Palembang, 22 April 2025",
                perihal: "Undangan Rapat Badan Anggaran",
                acara: "Rapat Badan Anggaran DPRD Provinsi Sumatera Selatan",
                agenda: "Evaluasi APBD Semester I dan penyusunan program semester II",
                tanggalRapat: "Jumat, 26 April 2025",
                waktu: "13.30 WIB s.d Selesai",
                tempat: "Ruang Badan Anggaran DPRD Provinsi Sumatera Selatan",
                penandatangan: "Drs. Budi Santoso, M.M"
            }
        };

        let currentSuratId = null;

        // Event listener untuk tombol detail
        $('.btn-detail').on('click', function() {
            const suratId = $(this).data('id');
            currentSuratId = suratId;
            const data = suratData[suratId];
            
            if (data) {
                // Update konten modal dengan data surat
                $('#nomor-surat').text(data.nomor);
                $('#tanggal-surat').text(data.tanggal);
                $('#perihal-surat').html('<strong>' + data.perihal + '</strong>');
                $('#acara-rapat').text(data.acara);
                $('#agenda-rapat').text(data.agenda);
                $('#tanggal-rapat').text(data.tanggalRapat);
                $('#waktu-rapat').text(data.waktu);
                $('#tempat-rapat').text(data.tempat);
                $('#nama-ttd').text(data.penandatangan);
            }
        });

        // Fungsi approve undangan
        function approveUndangan() {
            if (currentSuratId) {
                // Update status di tabel
                const row = $(`button[data-id="${currentSuratId}"]`).closest('tr');
                row.find('.status-badge')
                   .removeClass('badge-warning badge-danger')
                   .addClass('badge-success')
                   .text('Approved');
                
                // Tutup modal
                $('#suratModal').modal('hide');
                
                // Tampilkan alert
                setTimeout(() => {
                    alert('✅ Undangan telah disetujui!');
                }, 300);
            }
        }

        // Fungsi reject undangan
        function rejectUndangan() {
            if (currentSuratId) {
                const reason = prompt('Masukkan alasan penolakan (opsional):');
                
                // Update status di tabel
                const row = $(`button[data-id="${currentSuratId}"]`).closest('tr');
                row.find('.status-badge')
                   .removeClass('badge-warning badge-success')
                   .addClass('badge-danger')
                   .text('Rejected');
                
                // Tutup modal
                $('#suratModal').modal('hide');
                
                // Tampilkan alert
                setTimeout(() => {
                    let message = '❌ Undangan ditolak!';
                    if (reason && reason.trim() !== '') {
                        message += `\nAlasan: ${reason}`;
                    }
                    alert(message);
                }, 300);
            }
        }

        // Fungsi print surat
        function printSurat() {
            window.print();
        }

        // Format tanggal Indonesia
        function formatTanggalIndonesia(date) {
            const bulan = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];
            
            const hari = [
                'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'
            ];
            
            const d = new Date(date);
            return `${hari[d.getDay()]}, ${d.getDate()} ${bulan[d.getMonth()]} ${d.getFullYear()}`;
        }

        // Initialize tooltips
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });

        // Add smooth animations
        $('.btn-detail').hover(
            function() {
                $(this).css('transform', 'scale(1.05)');
            },
            function() {
                $(this).css('transform', 'scale(1)');
            }
        );

        // Auto refresh tanggal di header
        function updateCurrentDate() {
            const now = new Date();
            const options = { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                locale: 'id-ID'
            };
            $('.badge-light').html(`
                <i class="fas fa-calendar"></i> 
                ${now.toLocaleDateString('id-ID', options)}
            `);
        }

        // Update date every minute
        updateCurrentDate();
        setInterval(updateCurrentDate, 60000);

        console.log('🎉 Sistem Approval Rapat DPRD Sumsel berhasil dimuat!');
        console.log('📋 Total undangan:', Object.keys(suratData).length);
    </script>
</body>
</html>