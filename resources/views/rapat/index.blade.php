@extends('layouts.app')
@section('title', 'Penjadwalan Rapat')
@push('styles')
	<style>
		.modal-xl {
			max-width: 90%;
		}

		.surat-container {
			background: #fff;
			padding: 32px;
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

		.logo-container img {
			width: 140px;
			height: 140px;
			object-fit: contain;
		}

		.kop-text h3,
		.kop-text h4,
		.kop-text p {
			margin: 0;
		}

		.kop-text h3 {
			font-size: 22px;
			font-weight: 700;
			text-transform: uppercase;
		}

		.kop-text h4 {
			font-size: 18px;
			font-weight: 700;
		}

		.kop-text p {
			font-size: 14px;
		}

		.tanggal-surat {
			text-align: right;
			margin-bottom: 20px;
			font-size: 16px;
		}

		.detail-surat table,
		.agenda-table {
			width: 100%;
		}

		.detail-surat td,
		.agenda-table td {
			padding: 6px 0;
			vertical-align: top;
		}

		.detail-surat .label,
		.agenda-table .label-agenda {
			width: 150px;
			font-weight: 700;
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
			border-collapse: collapse;
			margin: 20px 0;
		}

		.agenda-table td {
			border: 1px solid #ddd;
			padding: 8px 12px;
		}

		.agenda-table .label-agenda {
			background-color: #f8f9fa;
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
	</style>
@endpush
@section('content')
	<div class="card">
		<div class="card-header d-flex justify-content-end">
			<button type="button" class="btn btn-primary addRapat">Tambah</button>
		</div>
		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-bordered" id="tableRapat" width="100%" cellspacing="0">
					<thead>
						<tr>
							<th>No</th>
							<th>Nomor Surat</th>
							<th>Hari / Tanggal</th>
							<th>Waktu / Pukul</th>
							<th>Tempat</th>
							<th>Kegiatan / Acara</th>
							<th>Status</th>
							<th>Aksi</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalRapat" tabindex="-1" aria-labelledby="modalRapatLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalRapatLabel">Tambah Rapat</h5>
				</div>
				<div class="modal-body">
					<form id="formTambahRapat">
						@csrf
						<input type="text" name="id" id="id" hidden>
						<div class="row">
							<div class="col-md-6">
								<div class="mb-3">
									<label for="judul" class="form-label">Judul <span class="text-danger">*</span></label>
									<input type="text" class="form-control" id="judul" name="judul" placeholder="Masukkan judul rapat"
										required>
								</div>
								<div class="mb-3">
									<label for="hal" class="form-label">Hal <span class="text-danger">*</span></label>
									<input type="text" class="form-control" id="hal" name="hal" placeholder="Masukkan Hal" required>
								</div>
								<div class="mb-3">
									<label for="tanggal" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
									<input type="date" class="form-control" id="tanggal" name="tanggal" placeholder="Pilih tanggal mulai rapat"
										required>
								</div>
								<div class="mb-3">
									<label for="tanggal_selesai" class="form-label">Tanggal Selesai <small class="text-muted">(Kosongkan jika rapat 1 hari)</small></label>
									<input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" placeholder="Pilih tanggal selesai rapat">
								</div>
								<div class="mb-3">
									<label for="jam_mulai" class="form-label">Jam Mulai <span class="text-danger">*</span></label>
									<input type="time" class="form-control" id="jam_mulai" name="jam_mulai" placeholder="Pilih jam mulai rapat"
										required>
								</div>
								<div class="mb-3">
									<label for="jam_selesai" class="form-label">Jam Selesai <span class="text-danger">*</span></label>
									<input type="time" class="form-control" id="jam_selesai" name="jam_selesai"
										placeholder="Pilih jam selesai rapat" required>
								</div>
								<div class="mb-3">
									<label for="lokasi" class="form-label">Lokasi <span class="text-danger">*</span></label>
									<input type="text" class="form-control" id="lokasi" name="lokasi" placeholder="Masukkan lokasi rapat"
										required>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label for="jenis_rapat" class="form-label">Jenis Rapat <span class="text-danger">*</span></label>
									<select class="form-control" id="jenis_rapat" name="jenis_rapat" required>
										<option value="" disabled selected>Pilih jenis rapat</option>
										<option value="Rapat Paripurna">Rapat Paripurna</option>
										<option value="Rapat Komisi">Rapat Komisi</option>
										<option value="Rapat Badan">Rapat Badan</option>
										<option value="Lain-lainnya">Lain-lainnya</option>
									</select>
								</div>
								<div class="mb-3">
									<label for="sifat" class="form-label">Sifat<span class="text-danger">*</span></label>
									<select class="form-control" id="sifat" name="sifat" required>
										<option value="" disabled selected>Pilih sifat rapat</option>
										<option value="Biasa">Biasa</option>
										<option value="Undangan">Undangan</option>
										<option value="Penting">Penting</option>
									</select>
								</div>
								<div class="mb-3">
									<label for="penandatangan_id" class="form-label">Penandatangan<span class="text-danger">*</span></label>
									<select class="form-control" id="penandatangan_id" name="penandatangan_id" required>
										<option value="" disabled selected>Pilih penandatangan</option>
										@foreach ($pendatanganRapat as $pimpinan)
											<option value="{{ $pimpinan->id }}">{{ $pimpinan->name }}</option>
										@endforeach
									</select>
								</div>
								<div class="mb-3">
									<label class="form-label">Jadwal Ketersediaan Peserta Terpilih</label>
									<div id="jadwalPesertaTerpilih" class="border rounded p-2" style="min-height:60px; background:#f8f9fa;">
										<em>Pilih peserta dan tanggal rapat untuk melihat jadwal ketersediaan mereka.</em>
									</div>
								</div>
								<div class="mb-3">
									<label for="deskripsi" class="form-label">Deskripsi</label>
									<textarea name="deskripsi" class="form-control" id="deskripsi" cols="30" rows="8"
									 placeholder="Masukkan deskripsi rapat"></textarea>
								</div>
							</div>
						</div>
						<div class="mb-3">
							<label class="form-label">Filter Peserta Rapat <span class="text-danger">*</span></label>
							<div class="row mb-2">
								<div class="col-md-6">
									<select class="form-control" id="filterJabatan">
										<option value="">Pilih Jabatan</option>
										@foreach ($pesertaRapat->pluck('jabatan')->unique() as $jabatan)
											@if ($jabatan)
												<option value="{{ $jabatan }}">{{ $jabatan }}</option>
											@endif
										@endforeach
									</select>
								</div>
								<div class="col-md-6">
									<select class="form-control" id="filterKomisi">
										<option value="">Pilih Komisi</option>
										@foreach ($pesertaRapat->pluck('komisi')->unique() as $komisi)
											@if ($komisi)
												<option value="{{ $komisi }}">{{ $komisi }}</option>
											@endif
										@endforeach
									</select>
								</div>
							</div>
							<div class="table-responsive">
								<table class="table table-bordered" id="tablePesertaRapat">
									<thead>
										<tr>
											<th><input type="checkbox" id="checkAllPeserta" /></th>
											<th>Nama</th>
											<th>Jabatan</th>
											<th>Komisi</th>
										</tr>
									</thead>
									<tbody>
										@foreach ($pesertaRapat as $peserta)
											<tr data-jabatan="{{ $peserta->jabatan }}" data-komisi="{{ $peserta->komisi }}">
												<td>
													<input type="checkbox" name="peserta[]" class="checkboxPeserta" value="{{ $peserta->id }}"
														id="peserta_{{ $peserta->id }}" />
												</td>
												<td>{{ $peserta->name }}</td>
												<td>{{ $peserta->jabatan }}</td>
												<td>{{ $peserta->komisi }}</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-success" id="btnRekomendasi" data-bs-toggle="modal"
						data-bs-target="#modalRekomendasi">
						Generate Rekomendasi Jadwal
					</button>

					<button type="button" class="btn btn-primary" id="btnSaveRapat" onclick="save()">Simpan</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="modalRekomendasi" tabindex="-1" aria-labelledby="modalRekomendasiLabel"
		aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Generate Rekomendasi Jadwal</h5>
				</div>
				<div class="modal-body">
					<form id="formRekomendasi">
						@csrf
						<div class="mb-3">
							<label for="durasi" class="form-label">Durasi Rapat (misal: 2 jam)</label>
							<input type="number" class="form-control" id="durasi" name="durasi" placeholder="Contoh: 2 jam"
								required>
						</div>
						<div class="mb-3">
							<label for="hasilRekomendasi" class="form-label">Hasil Rekomendasi</label>
							<textarea id="hasilRekomendasi" class="form-control" rows="6" disabled></textarea>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" id="btnGenerateRekomendasi">Generate</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="suratModal" tabindex="-1" role="dialog" aria-labelledby="suratModalLabel"
		aria-hidden="true">
		<div class="modal-dialog modal-xl" role="document">
			<div class="modal-content">
				<div class="modal-header bg-primary text-white">
					<h5 class="modal-title" id="suratModalLabel">
						<i class="fas fa-file-alt mr-2"></i>
						Surat Undangan Rapat
					</h5>
					<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body p-0">
					<div class="surat-container">
						<div class="kop-surat">
							<div class="logo-container">
								<div>
									<img src="{{ asset('img/Logo_DPRD_Sumatera_Selatan.png') }}" alt="Logo DPRD">
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

						<div class="tanggal-surat">
							<p id="tanggal-surat">Palembang, -</p>
						</div>

						<div class="detail-surat">
							<table>
								<tr>
									<td class="label">Nomor</td>
									<td class="colon">:</td>
									<td id="nomor-surat">-</td>
								</tr>
								<tr>
									<td class="label">Sifat</td>
									<td class="colon">:</td>
									<td id="sifat-surat">-</td>
								</tr>
								<tr>
									<td class="label">Lampiran</td>
									<td class="colon">:</td>
									<td>-</td>
								</tr>
								<tr>
									<td class="label">Hal</td>
									<td class="colon">:</td>
									<td id="perihal-surat"><strong>-</strong></td>
								</tr>
							</table>
						</div>

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
									<td id="acara-rapat">-</td>
								</tr>
								<tr>
									<td class="label-agenda">Agenda</td>
									<td id="agenda-rapat">-</td>
								</tr>
								<tr>
									<td class="label-agenda">Hari/Tanggal</td>
									<td id="tanggal-rapat">-</td>
								</tr>
								<tr>
									<td class="label-agenda">Waktu</td>
									<td id="waktu-rapat">-</td>
								</tr>
								<tr>
									<td class="label-agenda">Tempat</td>
									<td id="tempat-rapat">-</td>
								</tr>
							</table>

							<p>Mengingat pentingnya acara tersebut, diharapkan Bapak/Ibu dapat hadir tepat waktu. Atas
								perhatian dan kehadiran Bapak/Ibu, kami ucapkan terima kasih.</p>
						</div>

						<div class="ttd-section">
							<div class="ttd-box">
								<p>Ketua DPRD Provinsi Sumatera Selatan</p>
								<div class="ttd-space"></div>
								<p><strong><u id="nama-ttd">-</u></strong><br>
									NIP. 196812251994031008</p>
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
							<button type="button" class="btn btn-danger mr-2" onclick="rejectUndangan()">
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
		let currentRapatId = null;

		$(document).on('click', '.approveDetail', function() {
			const id = $(this).data('id');
			currentRapatId = id;
			showSuratUndangan(id);
		});

		function showSuratUndangan(id) {
			$.ajax({
				type: 'GET',
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
					if (!response.status) {
						Swal.fire({
							title: 'Error!',
							text: response.message || 'Gagal mengambil data',
							icon: 'error'
						});
						return;
					}

					const data = response.data;
					$('#nomor-surat').text(data.nomor || '-');
					$('#sifat-surat').text(data.sifat || '-');
					$('#tanggal-surat').text(data.tanggal || '-');
					$('#perihal-surat').html('<strong>' + (data.perihal || '-') + '</strong>');
					$('#acara-rapat').text(data.acara || '-');
					$('#agenda-rapat').text(data.agenda || '-');
					$('#tanggal-rapat').text(data.tanggalRapat || '-');
					$('#waktu-rapat').text(data.waktu || '-');
					$('#tempat-rapat').text(data.tempat || '-');
					$('#nama-ttd').text(data.penandatangan || '-');

					const $modalFooter = $('#suratModal .modal-footer');
					$modalFooter.find('.approval-status-alert').remove();
					$('#suratModal .btn-success, #suratModal .btn-danger').show();

					if (data.status == 2) {
						$('#suratModal .btn-success, #suratModal .btn-danger').hide();
						$modalFooter.prepend(
							'<div class="alert alert-success mb-0 approval-status-alert">Rapat ini sudah disetujui</div>'
							);
					} else if (data.status == 3) {
						$('#suratModal .btn-success, #suratModal .btn-danger').hide();
						$modalFooter.prepend(
							'<div class="alert alert-danger mb-0 approval-status-alert">Rapat ini sudah ditolak</div>'
							);
					}

					$('#suratModal').modal('show');
				},
				error: function(xhr) {
					Swal.close();
					Swal.fire({
						title: 'Error!',
						text: xhr.responseJSON?.message || 'Terjadi kesalahan saat mengambil data.',
						icon: 'error'
					});
				}
			});
		}

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
					updateApprovalStatus(currentRapatId, 2);
				}
			});
		}

		function rejectUndangan() {
			if (!currentRapatId) return;

			Swal.fire({
				title: 'Tolak Undangan',
				text: 'Apakah Anda yakin ingin menolak undangan rapat ini?',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#dc3545',
				cancelButtonColor: '#6c757d',
				confirmButtonText: 'Tolak',
				cancelButtonText: 'Batal'
			}).then((result) => {
				if (result.isConfirmed) {
					updateApprovalStatus(currentRapatId, 3);
				}
			});
		}

		function updateApprovalStatus(id, status) {
			$.ajax({
				type: 'POST',
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
						rapatTable.ajax.reload(null, false);
						Swal.fire({
							title: 'Berhasil!',
							text: status == 2 ? 'Undangan rapat berhasil disetujui' :
								'Undangan rapat berhasil ditolak',
							icon: status == 2 ? 'success' : 'info',
							timer: 2000
						});
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
					Swal.fire({
						title: 'Error!',
						text: xhr.responseJSON?.message || 'Terjadi kesalahan saat memperbarui status.',
						icon: 'error'
					});
				}
			});
		}

		function printSurat() {
			window.print();
		}

		$('#suratModal').on('hidden.bs.modal', function() {
			currentRapatId = null;
			$('#suratModal .approval-status-alert').remove();
			$('#suratModal .btn-success, #suratModal .btn-danger').show();
		});
	</script>
@endpush
