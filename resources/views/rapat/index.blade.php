@extends('layouts.app')
@section('title', 'Penjadwalan Rapat')
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
                                    <label for="judul" class="form-label">Judul <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="judul" name="judul"
                                        placeholder="Masukkan judul rapat" required>
                                </div>
                                <div class="mb-3">
                                    <label for="hal" class="form-label">Hal <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="hal" name="hal"
                                        placeholder="Masukkan Hal" required>
                                </div>
                                <div class="mb-3">
                                    <label for="tanggal" class="form-label">Tanggal <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="tanggal" name="tanggal"
                                        placeholder="Pilih tanggal rapat" required>
                                </div>
                                <div class="mb-3">
                                    <label for="waktu" class="form-label">Waktu <span
                                            class="text-danger">*</span></label>
                                    <input type="time" class="form-control" id="waktu" name="waktu"
                                        placeholder="Pilih waktu rapat" required>
                                </div>
                                <div class="mb-3">
                                    <label for="lokasi" class="form-label">Lokasi <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="lokasi" name="lokasi"
                                        placeholder="Masukkan lokasi rapat" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="jenis_rapat" class="form-label">Jenis Rapat <span
                                            class="text-danger">*</span></label>
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
                                    <label for="penandatangan_id" class="form-label">Penandatangan<span
                                            class="text-danger">*</span></label>
                                    <select class="form-control" id="penandatangan_id" name="penandatangan_id" required>
                                        <option value="" disabled selected>Pilih penandatangan</option>
                                        @foreach ($pendatanganRapat as $pimpinan)
                                            <option value="{{ $pimpinan->id }}">{{ $pimpinan->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Jadwal Ketersediaan Peserta Terpilih</label>
                                    <div id="jadwalPesertaTerpilih" class="border rounded p-2"
                                        style="min-height:60px; background:#f8f9fa;">
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
                                            <tr data-jabatan="{{ $peserta->jabatan }}"
                                                data-komisi="{{ $peserta->komisi }}">
                                                <td>
                                                    <input type="checkbox" name="peserta[]" class="checkboxPeserta"
                                                        value="{{ $peserta->id }}" id="peserta_{{ $peserta->id }}" />
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
                            <input type="number" class="form-control" id="durasi" name="durasi"
                                placeholder="Contoh: 2 jam" required>
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

@endsection
@push('scripts')
    <script src="{{ asset('js/rapat.js') }}?v={{ time() }}"></script>
@endpush
