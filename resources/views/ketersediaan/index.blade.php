@extends('layouts.app')
@section('title', 'Kegiatan Anggota')
@push('styles')
	<style>
		/* ===== Google Calendar-like Premium Styling ===== */
		.calendar-wrapper {
			background: #fff;
			border-radius: 12px;
			box-shadow: 0 4px 24px rgba(0,0,0,.08);
			overflow: hidden;
		}

		/* --- Calendar Header Bar --- */
		.calendar-toolbar {
			background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
			padding: 18px 24px;
			display: flex;
			justify-content: space-between;
			align-items: center;
			flex-wrap: wrap;
			gap: 12px;
		}
		.calendar-toolbar .toolbar-left {
			display: flex;
			align-items: center;
			gap: 12px;
		}
		.calendar-toolbar .toolbar-left .cal-title {
			color: #fff;
			font-size: 1.35rem;
			font-weight: 700;
			margin: 0;
			letter-spacing: .3px;
		}
		.calendar-toolbar .toolbar-left .cal-subtitle {
			color: rgba(255,255,255,.7);
			font-size: .82rem;
			margin: 0;
		}

		.calendar-toolbar .toolbar-right {
			display: flex;
			align-items: center;
			gap: 8px;
			flex-wrap: wrap;
		}

		/* Navigation buttons */
		.cal-nav-btn {
			background: rgba(255,255,255,.15);
			border: 1px solid rgba(255,255,255,.25);
			color: #fff;
			border-radius: 8px;
			width: 36px;
			height: 36px;
			display: inline-flex;
			align-items: center;
			justify-content: center;
			cursor: pointer;
			transition: all .2s ease;
			font-size: .85rem;
		}
		.cal-nav-btn:hover {
			background: rgba(255,255,255,.3);
			transform: scale(1.05);
		}
		.cal-today-btn {
			background: rgba(255,255,255,.2);
			border: 1px solid rgba(255,255,255,.35);
			color: #fff;
			border-radius: 8px;
			padding: 6px 16px;
			font-size: .82rem;
			font-weight: 600;
			cursor: pointer;
			transition: all .2s ease;
		}
		.cal-today-btn:hover {
			background: #fff;
			color: #4e73df;
		}

		/* Month/Year display */
		.cal-month-display {
			color: #fff;
			font-size: 1.1rem;
			font-weight: 600;
			min-width: 180px;
			text-align: center;
		}

		/* View toggle buttons */
		.view-toggle {
			display: flex;
			background: rgba(255,255,255,.15);
			border-radius: 8px;
			overflow: hidden;
		}
		.view-toggle .vtoggle-btn {
			background: transparent;
			border: none;
			color: rgba(255,255,255,.7);
			padding: 6px 14px;
			font-size: .8rem;
			font-weight: 600;
			cursor: pointer;
			transition: all .2s ease;
		}
		.view-toggle .vtoggle-btn.active {
			background: #fff;
			color: #4e73df;
			border-radius: 6px;
		}
		.view-toggle .vtoggle-btn:hover:not(.active) {
			color: #fff;
			background: rgba(255,255,255,.1);
		}

		/* Add event button */
		.cal-add-btn {
			background: #fff;
			color: #4e73df;
			border: none;
			border-radius: 10px;
			padding: 8px 18px;
			font-weight: 700;
			font-size: .85rem;
			cursor: pointer;
			transition: all .25s ease;
			box-shadow: 0 2px 8px rgba(0,0,0,.15);
			display: inline-flex;
			align-items: center;
			gap: 6px;
		}
		.cal-add-btn:hover {
			transform: translateY(-1px);
			box-shadow: 0 4px 16px rgba(0,0,0,.2);
			background: #f0f4ff;
		}

		/* --- FullCalendar Overrides --- */
		.calendar-body {
			padding: 8px 16px 16px;
		}

		/* Hide default FC toolbar */
		.fc .fc-toolbar { display: none !important; }

		/* Day headers */
		.fc .fc-col-header-cell {
			background: #f8f9fc;
			border-color: #e3e6f0;
			padding: 10px 0;
		}
		.fc .fc-col-header-cell-cushion {
			color: #5a5c69;
			font-weight: 600;
			font-size: .82rem;
			text-transform: uppercase;
			letter-spacing: .5px;
			text-decoration: none;
		}

		/* Day cells */
		.fc .fc-daygrid-day {
			transition: background .15s ease;
			cursor: pointer;
			min-height: 100px;
		}
		.fc .fc-daygrid-day:hover {
			background: #f0f4ff;
		}
		.fc .fc-daygrid-day-number {
			color: #5a5c69;
			font-weight: 500;
			font-size: .85rem;
			padding: 6px 10px;
			text-decoration: none;
		}
		.fc .fc-day-today {
			background: #eef2ff !important;
		}
		.fc .fc-day-today .fc-daygrid-day-number {
			background: #4e73df;
			color: #fff;
			border-radius: 50%;
			width: 28px;
			height: 28px;
			display: inline-flex;
			align-items: center;
			justify-content: center;
			font-weight: 700;
		}

		/* Events */
		.fc .fc-event {
			border: none !important;
			border-radius: 6px;
			padding: 2px 8px;
			font-size: .76rem;
			font-weight: 600;
			cursor: pointer;
			transition: all .15s ease;
			margin: 1px 3px;
			box-shadow: 0 1px 3px rgba(0,0,0,.1);
		}
		.fc .fc-event:hover {
			transform: translateY(-1px);
			box-shadow: 0 3px 8px rgba(0,0,0,.18);
			filter: brightness(1.05);
		}
		.fc .fc-daygrid-event-dot { display: none; }

		/* More events link */
		.fc .fc-daygrid-more-link {
			color: #4e73df;
			font-weight: 600;
			font-size: .75rem;
		}

		/* Borders */
		.fc td, .fc th {
			border-color: #e3e6f0;
		}

		/* Week/Day time grid */
		.fc .fc-timegrid-slot {
			height: 48px;
		}
		.fc .fc-timegrid-slot-label-cushion {
			font-size: .75rem;
			color: #858796;
		}

		/* --- Legend --- */
		.calendar-legend {
			padding: 12px 20px;
			background: #f8f9fc;
			border-top: 1px solid #e3e6f0;
			display: flex;
			align-items: center;
			gap: 18px;
			flex-wrap: wrap;
		}
		.legend-title {
			font-size: .78rem;
			font-weight: 700;
			color: #5a5c69;
			text-transform: uppercase;
			letter-spacing: .5px;
		}
		.legend-item {
			display: flex;
			align-items: center;
			gap: 6px;
			font-size: .78rem;
			color: #6e707e;
		}
		.legend-dot {
			width: 10px;
			height: 10px;
			border-radius: 3px;
		}

		/* --- Modal Enhancements --- */
		#modalPersonal .modal-header {
			background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
			color: #fff;
			border-radius: 0;
		}
		#modalPersonal .modal-title {
			font-weight: 700;
		}
		#modalPersonal .modal-header .close {
			color: #fff;
			opacity: .8;
		}

		/* Detail Event Modal */
		#modalEventDetail .modal-header {
			border-bottom: none;
			padding-bottom: 0;
		}
		.event-detail-header {
			display: flex;
			align-items: center;
			gap: 12px;
		}
		.event-detail-dot {
			width: 14px;
			height: 14px;
			border-radius: 4px;
			flex-shrink: 0;
		}
		.event-detail-title {
			font-size: 1.2rem;
			font-weight: 700;
			color: #2d3436;
		}
		.event-detail-body .detail-row {
			display: flex;
			align-items: flex-start;
			gap: 12px;
			padding: 10px 0;
			border-bottom: 1px solid #f1f3f5;
		}
		.event-detail-body .detail-row:last-child {
			border-bottom: none;
		}
		.event-detail-body .detail-icon {
			width: 20px;
			color: #858796;
			text-align: center;
			flex-shrink: 0;
			margin-top: 2px;
		}
		.event-detail-body .detail-content {
			flex: 1;
		}
		.event-detail-body .detail-label {
			font-size: .72rem;
			color: #858796;
			text-transform: uppercase;
			letter-spacing: .5px;
			font-weight: 600;
		}
		.event-detail-body .detail-value {
			font-size: .92rem;
			color: #2d3436;
			font-weight: 500;
		}

		/* Responsive */
		@media (max-width: 768px) {
			.calendar-toolbar {
				padding: 14px 16px;
			}
			.cal-month-display {
				min-width: auto;
				font-size: .95rem;
			}
			.calendar-body {
				padding: 4px 8px 8px;
			}
			.fc .fc-daygrid-day {
				min-height: 60px;
			}
			.view-toggle .vtoggle-btn {
				padding: 5px 10px;
				font-size: .72rem;
			}
		}

		/* Animation */
		@keyframes fadeInUp {
			from { opacity: 0; transform: translateY(12px); }
			to { opacity: 1; transform: translateY(0); }
		}
		.calendar-wrapper {
			animation: fadeInUp .4s ease-out;
		}
	</style>
@endpush
@section('content')
	<div class="calendar-wrapper">
		{{-- Calendar Toolbar --}}
		<div class="calendar-toolbar">
			<div class="toolbar-left">
				<div>
					<h4 class="cal-title"><i class="fas fa-calendar-alt mr-2"></i>Kegiatan Anggota</h4>
					<p class="cal-subtitle">Kelola jadwal kegiatan dalam tampilan kalender</p>
				</div>
			</div>
			<div class="toolbar-right">
				<button type="button" class="cal-nav-btn" id="calPrev" title="Bulan Sebelumnya">
					<i class="fas fa-chevron-left"></i>
				</button>
				<button type="button" class="cal-today-btn" id="calToday">Hari Ini</button>
				<div class="cal-month-display" id="calMonthDisplay"></div>
				<button type="button" class="cal-nav-btn" id="calNext" title="Bulan Berikutnya">
					<i class="fas fa-chevron-right"></i>
				</button>

				<div class="view-toggle">
					<button type="button" class="vtoggle-btn active" data-view="dayGridMonth">Bulan</button>
					<button type="button" class="vtoggle-btn" data-view="timeGridWeek">Minggu</button>
					<button type="button" class="vtoggle-btn" data-view="timeGridDay">Hari</button>
				</div>

				<button type="button" class="cal-add-btn addPersonal">
					<i class="fas fa-plus"></i> Tambah Kegiatan
				</button>
			</div>
		</div>

		{{-- Calendar Body --}}
		<div class="calendar-body">
			<div id="calendarKegiatan"></div>
		</div>

		{{-- Legend --}}
		<div class="calendar-legend">
			<span class="legend-title">Keterangan:</span>
			<span class="legend-item"><span class="legend-dot" style="background:#4e73df"></span> Pagi</span>
			<span class="legend-item"><span class="legend-dot" style="background:#1cc88a"></span> Siang</span>
			<span class="legend-item"><span class="legend-dot" style="background:#f6c23e"></span> Sore</span>
			<span class="legend-item"><span class="legend-dot" style="background:#6f42c1"></span> Seharian</span>
		</div>
	</div>

	{{-- Add / Edit Modal (preserved from original) --}}
	<div class="modal fade" id="modalPersonal" tabindex="-1" aria-labelledby="modalPersonalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalPersonalLabel">Tambah Ketersediaan</h5>
					<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
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
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
					<button type="button" class="btn btn-primary" id="btnSaveRapat" onclick="save()">
						<i class="fas fa-save mr-1"></i> Simpan
					</button>
				</div>
			</div>
		</div>
	</div>

	{{-- Event Detail Modal --}}
	<div class="modal fade" id="modalEventDetail" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<div class="event-detail-header">
						<span class="event-detail-dot" id="detailDot"></span>
						<span class="event-detail-title" id="detailEventTitle"></span>
					</div>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body event-detail-body">
					<div class="detail-row">
						<div class="detail-icon"><i class="fas fa-user"></i></div>
						<div class="detail-content">
							<div class="detail-label">Nama Anggota</div>
							<div class="detail-value" id="detailNama">-</div>
						</div>
					</div>
					<div class="detail-row">
						<div class="detail-icon"><i class="fas fa-calendar-day"></i></div>
						<div class="detail-content">
							<div class="detail-label">Tanggal</div>
							<div class="detail-value" id="detailTanggal">-</div>
						</div>
					</div>
					<div class="detail-row">
						<div class="detail-icon"><i class="fas fa-clock"></i></div>
						<div class="detail-content">
							<div class="detail-label">Waktu</div>
							<div class="detail-value" id="detailWaktu">-</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary btn-sm" id="btnEditEvent">
						<i class="fas fa-edit mr-1"></i> Edit
					</button>
					<button type="button" class="btn btn-danger btn-sm" id="btnDeleteEvent">
						<i class="fas fa-trash mr-1"></i> Hapus
					</button>
					<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
				</div>
			</div>
		</div>
	</div>
@endsection
@push('scripts')
	<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
	<script src="{{ asset('js/personal.js') }}?v={{ time() }}"></script>
@endpush
