<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TindakLanjutRapat;
use App\Models\Rapat;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TindakLanjutRapatController extends Controller{
    public function index()
    {
        try {
            $tindakLanjut = TindakLanjutRapat::with(['rapat', 'user'])->paginate(10);
            return view('tindak_lanjut_rapat.index', compact('tindakLanjut'));
        } catch (\Exception $e) {
            Log::error('Error index tindak lanjut rapat: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Gagal memuat daftar tindak lanjut rapat: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            $rapats = Rapat::all();
            $users = User::all();
            return view('tindak_lanjut_rapat.create', compact('rapats', 'users'));
        } catch (\Exception $e) {
            Log::error('Error create tindak lanjut rapat: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Gagal memuat form tambah tindak lanjut: ' . $e->getMessage());
        }
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
            'bukti_progres' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        try {
            if ($request->hasFile('bukti_progres')) {
                $validated['bukti_progres'] = $request->file('bukti_progres')->store('bukti_progres_files', 'public');
            }

            TindakLanjutRapat::create($validated);
            return redirect()->route('tindak-lanjut-rapat.index')->with('success', 'Tindak lanjut rapat berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Error store tindak lanjut rapat: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'request' => $request->except(['bukti_progres']),
                'trace' => $e->getTraceAsString()
            ]);

            if (isset($validated['bukti_progres']) && Storage::disk('public')->exists($validated['bukti_progres'])) {
                Storage::disk('public')->delete($validated['bukti_progres']);
            }

            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan tindak lanjut rapat: ' . $e->getMessage());
        }
    }

    public function show(TindakLanjutRapat $tindak_lanjut_rapat)
    {
        try {
            $tindak_lanjut_rapat->load(['rapat', 'user']);
            return view('tindak_lanjut_rapat.show', compact('tindak_lanjut_rapat'));
        } catch (\Exception $e) {
            Log::error('Error show tindak lanjut rapat (ID: ' . $tindak_lanjut_rapat->id . '): ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Gagal memuat detail tindak lanjut: ' . $e->getMessage());
        }
    }

    public function edit(TindakLanjutRapat $tindak_lanjut_rapat)
    {
        try {
            $rapats = Rapat::all();
            $users = User::all();
            return view('tindak_lanjut_rapat.edit', compact('tindak_lanjut_rapat', 'rapats', 'users'));
        } catch (\Exception $e) {
            Log::error('Error edit tindak lanjut rapat (ID: ' . $tindak_lanjut_rapat->id . '): ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Gagal memuat form edit tindak lanjut: ' . $e->getMessage());
        }
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
            'bukti_progres' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        try {
            $oldBukti = $tindak_lanjut_rapat->bukti_progres;
            if ($request->hasFile('bukti_progres')) {
                $validated['bukti_progres'] = $request->file('bukti_progres')->store('bukti_progres_files', 'public');
            }

            $tindak_lanjut_rapat->update($validated);

            if ($request->hasFile('bukti_progres') && $oldBukti && Storage::disk('public')->exists($oldBukti)) {
                Storage::disk('public')->delete($oldBukti);
            }

            return redirect()->route('tindak-lanjut-rapat.index')->with('success', 'Tindak lanjut rapat berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error update tindak lanjut rapat (ID: ' . $tindak_lanjut_rapat->id . '): ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'request' => $request->except(['bukti_progres']),
                'trace' => $e->getTraceAsString()
            ]);

            if (isset($validated['bukti_progres']) && $request->hasFile('bukti_progres') && Storage::disk('public')->exists($validated['bukti_progres'])) {
                Storage::disk('public')->delete($validated['bukti_progres']);
            }

            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui tindak lanjut rapat: ' . $e->getMessage());
        }
    }

    public function destroy(TindakLanjutRapat $tindak_lanjut_rapat)
    {
        try {
            $oldBukti = $tindak_lanjut_rapat->bukti_progres;

            $tindak_lanjut_rapat->delete();

            if ($oldBukti && Storage::disk('public')->exists($oldBukti)) {
                Storage::disk('public')->delete($oldBukti);
            }

            return redirect()->route('tindak-lanjut-rapat.index')->with('success', 'Tindak lanjut rapat berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error destroy tindak lanjut rapat (ID: ' . $tindak_lanjut_rapat->id . '): ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus tindak lanjut rapat: ' . $e->getMessage());
        }
    }

    public function downloadBukti(TindakLanjutRapat $tindak_lanjut_rapat)
    {
        try {
            if (!$tindak_lanjut_rapat->bukti_progres || !Storage::disk('public')->exists($tindak_lanjut_rapat->bukti_progres)) {
                return redirect()->back()->with('error', 'File bukti progres tidak ditemukan.');
            }
            return Storage::disk('public')->download($tindak_lanjut_rapat->bukti_progres);
        } catch (\Exception $e) {
            Log::error('Error download bukti progres (ID: ' . $tindak_lanjut_rapat->id . '): ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'file_path' => $tindak_lanjut_rapat->bukti_progres,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunduh file bukti progres: ' . $e->getMessage());
        }
    }
}
