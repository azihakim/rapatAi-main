@extends('layouts.app')
@section('title', 'Pengajuan Logistik')
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

    <div class="modal fade" id="modalLogistik" tabindex="-1" aria-labelledby="modalLogistikLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLogistikLabel">Tambah Pengajuan Logistik</h5>
                </div>
                <div class="modal-body">
                    <form id="formLogistik">
                        @csrf
                        <input type="text" name="id" id="id" hidden>
                        <div class="mb-3">
                            <label for="rapat" class="form-label">Pilih Rapat <span class="text-danger">*</span></label>
                            <select class="form-control" id="rapat" name="rapat_id" required>
                                <option value="" disabled selected>Pilih rapat</option>
                                @foreach ($rapat as $item)
                                    <option value="{{ $item->id }}"
                                        title="{{ $item->judul }} - {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('l, d F Y') }}">
                                        {{ Str::limit($item->judul, 60) }} -
                                        {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="jenis_pengajuan" class="form-label">Jenis Pengajuan <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="jenis_pengajuan" name="jenis_pengajuan"
                                placeholder="jenis pengajuan" required>
                        </div>
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan <span class="text-danger">*</span></label>
                            <textarea name="keterangan" class="form-control" id="keterangan" cols="10" rows="5"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Detail Logistik</label>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="tableDetailLogistik">
                                    <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th>Jumlah</th>
                                            <th>Satuan</th>
                                            <th>Keterangan</th>
                                            <th>
                                                <button type="button" class="btn btn-sm btn-success" id="addDetailRow">
                                                    +
                                                </button>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <input type="text" name="detail[0][item]" class="form-control" required>
                                            </td>
                                            <td>
                                                <input type="number" name="detail[0][jumlah]" class="form-control" min="1" required>
                                            </td>
                                            <td>
                                                <input type="text" name="detail[0][satuan]" class="form-control" required>
                                            </td>
                                            <td>
                                                <input type="text" name="detail[0][keterangan]" class="form-control">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-danger removeDetailRow">
                                                    -
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </form>
                    <div class="mb-3" id="pengajuanSebelumnyaSection" style="display:none;">
                        <label class="form-label">Pengajuan Logistik Sebelumnya untuk Rapat Ini:</label>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="tablePengajuanSebelumnya">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Jenis Pengajuan</th>
                                        <th>Keterangan</th>
                                        <th>Status</th>
                                        <th>Detail</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="btnSaveLogistik" onclick="save()">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    {{-- modal detail --}}
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('js/logistik.js') }}?v={{ time() }}"></script>
@endpush
