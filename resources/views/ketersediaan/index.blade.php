@extends('layouts.app')
@section('title', 'Ketersediaan Pribadi')
@section('content')
	<div class="card">
		<div class="card-header d-flex justify-content-end">
			<button type="button" class="btn btn-primary addPersonal">Tambah</button>
		</div>
		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-bordered" id="tablePersonal" width="100%" cellspacing="0">
					<thead>
						<tr>
							<th>No</th>
							<th>Nama</th>
							<th>Hari / Tanggal</th>
							<th>Waktu Mulai</th>
							<th>Waktu Selesai</th>
							<th>Aksi</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalPersonal" tabindex="-1" aria-labelledby="modalPersonalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalPersonalLabel">Tambah Ketersediaan</h5>
				</div>
				<div class="modal-body">
					<form id="formPersonal">
						@csrf
						<input type="text" name="id" id="id" hidden>
						<div class="alert alert-info py-2 mb-3">
							Tambahkan satu atau beberapa tanggal. Setiap tanggal bisa memiliki lebih dari satu rentang jam.
							Jika seharian dicentang, jam akan otomatis menjadi 00:00 sampai 23:59.
						</div>
						<div id="personalRows"></div>
						<div class="d-flex justify-content-end mt-3">
							<button type="button" class="btn btn-outline-primary btn-sm addTanggalRow">Tambah Tanggal</button>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" id="btnSaveRapat" onclick="save()">Simpan</button>
				</div>
			</div>
		</div>
	</div>
@endsection
@push('scripts')
	<script src="{{ asset('js/personal.js') }}?v={{ time() }}"></script>
@endpush
