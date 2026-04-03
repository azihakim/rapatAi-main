<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TindakLanjutRapat;
use App\Models\Rapat;
use App\Models\User;

class TindakLanjutRapatController extends Controller{
    public function index()
    {
        $tindakLanjut = TindakLanjutRapat::with(['rapat', 'user'])->paginate(10);
        return view('tindak_lanjut_rapat.index', compact('tindakLanjut'));
    }

    public function create()
    {
        $rapats = Rapat::all();
        $users = User::all();
        return view('tindak_lanjut_rapat.create', compact('rapats', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'rapat_id' => 'required|exists:rapats,id',
            'deskripsi' => 'required|string',
            'user_id' => 'required|exists:users,id',
            'batas_waktu' => 'required|date',
            'status' => 'required|in:pending,proses,selesai',
            'progress' => 'required|integer|min:0|max:100',
        ]);
        TindakLanjutRapat::create($validated);
        return redirect()->route('tindak-lanjut-rapat.index')->with('success', 'Tindak lanjut rapat berhasil ditambahkan.');
    }

    public function show(TindakLanjutRapat $tindak_lanjut_rapat)
    {
        $tindak_lanjut_rapat->load(['rapat', 'user']);
        return view('tindak_lanjut_rapat.show', compact('tindak_lanjut_rapat'));
    }

    public function edit(TindakLanjutRapat $tindak_lanjut_rapat)
    {
        $rapats = Rapat::all();
        $users = User::all();
        return view('tindak_lanjut_rapat.edit', compact('tindak_lanjut_rapat', 'rapats', 'users'));
    }

    public function update(Request $request, TindakLanjutRapat $tindak_lanjut_rapat)
    {
        $validated = $request->validate([
            'rapat_id' => 'required|exists:rapats,id',
            'deskripsi' => 'required|string',
            'user_id' => 'required|exists:users,id',
            'batas_waktu' => 'required|date',
            'status' => 'required|in:pending,proses,selesai',
            'progress' => 'required|integer|min:0|max:100',
        ]);
        $tindak_lanjut_rapat->update($validated);
        return redirect()->route('tindak-lanjut-rapat.index')->with('success', 'Tindak lanjut rapat berhasil diperbarui.');
    }

    public function destroy(TindakLanjutRapat $tindak_lanjut_rapat)
    {
        $tindak_lanjut_rapat->delete();
        return redirect()->route('tindak-lanjut-rapat.index')->with('success', 'Tindak lanjut rapat berhasil dihapus.');
    }
}
