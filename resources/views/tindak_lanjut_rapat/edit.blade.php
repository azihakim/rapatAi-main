@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Tindak Lanjut Rapat</h1>
    <form action="{{ route('tindak-lanjut-rapat.update', $tindak_lanjut_rapat) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label>Rapat</label>
            <select name="rapat_id" class="form-control" required>
                <option value="">Pilih Rapat</option>
                @foreach($rapats as $rapat)
                    <option value="{{ $rapat->id }}" {{ $tindak_lanjut_rapat->rapat_id == $rapat->id ? 'selected' : '' }}>{{ $rapat->judul }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Deskripsi</label>
            <textarea name="deskripsi" class="form-control" required>{{ old('deskripsi', $tindak_lanjut_rapat->deskripsi) }}</textarea>
        </div>
        <div class="mb-3">
            <label>Penanggung Jawab</label>
            <select name="user_id" class="form-control" required>
                <option value="">Pilih User</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ $tindak_lanjut_rapat->user_id == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Batas Waktu</label>
            <input type="date" name="batas_waktu" class="form-control" value="{{ old('batas_waktu', $tindak_lanjut_rapat->batas_waktu) }}" required>
        </div>
        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control" required>
                <option value="pending" {{ $tindak_lanjut_rapat->status == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="proses" {{ $tindak_lanjut_rapat->status == 'proses' ? 'selected' : '' }}>Proses</option>
                <option value="selesai" {{ $tindak_lanjut_rapat->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Progress (%)</label>
            <input type="number" name="progress" class="form-control" min="0" max="100" value="{{ old('progress', $tindak_lanjut_rapat->progress) }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('tindak-lanjut-rapat.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
