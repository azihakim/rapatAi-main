@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detail Tindak Lanjut Rapat</h1>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">{{ $tindak_lanjut_rapat->rapat->judul ?? '-' }}</h5>
            <p class="card-text"><strong>Deskripsi:</strong> {{ $tindak_lanjut_rapat->deskripsi }}</p>
            <p class="card-text"><strong>Penanggung Jawab:</strong> {{ $tindak_lanjut_rapat->user->name ?? '-' }}</p>
            <p class="card-text"><strong>Batas Waktu:</strong> {{ $tindak_lanjut_rapat->batas_waktu }}</p>
            <p class="card-text"><strong>Status:</strong> {{ ucfirst($tindak_lanjut_rapat->status) }}</p>
            <p class="card-text"><strong>Progress:</strong> {{ $tindak_lanjut_rapat->progress }}%</p>
            <a href="{{ route('tindak-lanjut-rapat.edit', $tindak_lanjut_rapat) }}" class="btn btn-warning">Edit</a>
            <a href="{{ route('tindak-lanjut-rapat.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
</div>
@endsection
