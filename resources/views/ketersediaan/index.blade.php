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
                        <div class="mb-3">
                            <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal"
                                placeholder="Pilih tanggal" required>
                        </div>
                        <div class="mb-3">
                            <label for="waktu_mulai" class="form-label">Waktu Mulai <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="waktu_mulai" name="waktu_mulai"
                                placeholder="Pilih waktu" required>
                        </div>                        
                        <div class="mb-3">
                            <label for="waktu_selesai" class="form-label">Waktu Selesai <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="waktu_selesai" name="waktu_selesai"
                                placeholder="Pilih waktu" required>
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
