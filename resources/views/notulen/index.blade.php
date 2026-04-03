@extends('layouts.app')
@section('title', 'Notulensi Rapat')
@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-end">
            <button type="button" class="btn btn-primary addNotulen">Tambah</button>
        </div>
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

    <div class="modal fade" id="modalNotulen" tabindex="-1" aria-labelledby="modalNotulenLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNotulenLabel">Tambah Notulen</h5>
                </div>
                <div class="modal-body">
                    <form id="formNotulen">
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
                            <label for="file_path" class="form-label">Upload Audio <span class="text-secondary">(Optional)</span></label>
                            <input type="file" class="form-control" id="file_path" name="file_path"
                                accept="audio/mp3,video/mp4">
                            <small id="name_filePath" class="form-text text-muted"></small>
                            <button type="button" class="btn btn-success mt-2 d-none" id="btnGenerateSTT">Generate Speech to Text</button>
                        </div>
                        <div class="mb-3">
                            <label for="isi" class="form-label">Isi <span class="text-danger">*</span></label>
                            <textarea name="isi" class="form-control" id="isi" cols="30" rows="10"></textarea>
                        </div>                                                                   
                    </form>                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="btnSaveNotulen" onclick="save()">Simpan</button>
                </div>
            </div>
        </div>
    </div>
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
@endpush
