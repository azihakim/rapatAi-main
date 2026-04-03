@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Tindak Lanjut Rapat</h1>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <a href="{{ route('tindak-lanjut-rapat.create') }}" class="btn btn-primary mb-3">Tambah Tindak Lanjut</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Rapat</th>
                <th>Deskripsi</th>
                <th>Penanggung Jawab</th>
                <th>Batas Waktu</th>
                <th>Status</th>
                <th>Progress</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tindakLanjut as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->rapat->judul ?? '-' }}</td>
                <td>{{ $item->deskripsi }}</td>
                <td>{{ $item->user->name ?? '-' }}</td>
                <td>{{ $item->batas_waktu }}</td>
                <td>{{ ucfirst($item->status) }}</td>
                <td>{{ $item->progress }}%</td>
                <td>
                    <a href="{{ route('tindak-lanjut-rapat.show', $item) }}" class="btn btn-info btn-sm">Detail</a>
                    <a href="{{ route('tindak-lanjut-rapat.edit', $item) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('tindak-lanjut-rapat.destroy', $item) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus data?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $tindakLanjut->links() }}
</div>
@endsection
