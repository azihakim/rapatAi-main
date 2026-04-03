@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detail User</h1>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">{{ $user->name }}</h5>
            <p class="card-text"><strong>Email:</strong> {{ $user->email }}</p>
            <p class="card-text"><strong>Role:</strong> {{ $user->role->name ?? '-' }}</p>
            <p class="card-text"><strong>Jabatan:</strong> {{ $user->jabatan }}</p>
            <p class="card-text"><strong>NIP:</strong> {{ $user->nip }}</p>
            <p class="card-text"><strong>Foto TTD:</strong> <br>@if($user->foto_ttd)<img src="{{ $user->foto_ttd }}" alt="TTD" style="max-width:200px;">@else - @endif</p>
            <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">Edit</a>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
</div>
@endsection
