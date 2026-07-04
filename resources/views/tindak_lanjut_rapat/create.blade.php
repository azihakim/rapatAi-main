@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Tambah Tindak Lanjut Rapat</h1>
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <form action="{{ route('tindak-lanjut-rapat.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label>Rapat</label>
            <select name="rapat_id" class="form-control" required>
                <option value="">Pilih Rapat</option>
                @foreach($rapats as $rapat)
                    <option value="{{ $rapat->id }}">{{ $rapat->judul }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Deskripsi</label>
            <textarea name="deskripsi" class="form-control" required>{{ old('deskripsi') }}</textarea>
        </div>
        <div class="mb-3">
            <label>Penanggung Jawab</label>
            <select name="user_id" class="form-control" required>
                <option value="">Pilih User</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Batas Waktu</label>
            <input type="date" name="batas_waktu" class="form-control" value="{{ old('batas_waktu') }}" required>
        </div>
        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control" required>
                <option value="pending">Pending</option>
                <option value="proses">Proses</option>
                <option value="selesai">Selesai</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Progress (%)</label>
            <input type="number" name="progress" class="form-control" min="0" max="100" value="{{ old('progress', 0) }}" required>
        </div>
        <div class="mb-3">
            <label>Bukti Progress (PDF, Maks. 10MB)</label>
            <input type="file" name="bukti_progres" class="form-control" accept=".pdf">
            <small class="form-text text-muted">Upload bukti progres dari anggota dewan berupa file PDF.</small>
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('tindak-lanjut-rapat.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
