<?php

namespace App\Http\Controllers;

use App\Models\Notulensi;
use App\Models\Rapat;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class NotulensiController extends Controller
{
    public function index()
    {

        // Ambil rapat yang diikuti user (sebagai peserta)
        $user = Auth::user();
        $rapatIds = [];
        if ($user) {
            $rapatIds = DB::table('peserta_rapats')->where('user_id', $user->id)->pluck('rapat_id')->toArray();
        }
        $rapat = Rapat::whereIn('id', $rapatIds)->get();

        if (Request()->ajax()) {
            $data = Notulensi::with('rapat')->whereIn('rapat_id', $rapatIds)->get();
            return datatables()->of($data)
                ->addIndexColumn()
                ->addColumn('rapat', function ($row) {
                    return \Illuminate\Support\Str::limit($row->rapat->judul, 60);
                })
                ->addColumn('tanggal', function ($row) {
                    \Carbon\Carbon::setLocale('id');
                    return \Carbon\Carbon::parse($row->rapat->tanggal)->translatedFormat('d F Y');
                })
                ->addColumn('isi', function ($row) {
                    return \Illuminate\Support\Str::limit($row->isi, 100);
                })
                ->addColumn('status', function ($row) {
                    return ucfirst($row->status);
                })
                ->addColumn('action', function ($row) {
                    $btn = '';

                    if (Auth::user()->role && Auth::user()->role->name === 'sekretariat') {
                        $btn .= ' <button type="button" class="btn btn-primary btn-sm editNotulen" data-id="' . $row->id . '">Edit</button>';
                        $btn .= ' <button type="button" class="deleteNotulen btn btn-danger btn-sm" data-id="' . $row->id . '">Delete</button>';
                    } 
                    if ($row->status == 'draft' || $row->status == 'disetujui') {
                        $btn = '<button class="btn btn-info btn-detail btn-sm detailNotulen" data-toggle="modal" data-target="#modalDetailNotulen" data-id="' . $row->id . '">
                                <i class="fas fa-eye"></i> Detail
                            </button>';
                    }

                    return $btn;
                })
                ->rawColumns(['action'])
                ->toJson();
        }

        return view('notulen.index', compact('rapat'));
    }


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'rapat_id' => 'required|exists:rapats,id',
            'isi' => 'required|string',
        ]);

        $validatedData['file_path'] = $request->file_path ?? null;
        if ($validatedData['file_path']) {
            $validatedData['file_path'] = $request->file('file_path')->store('notulensi_files', 'public');
        }
        $validatedData['status'] = 'draft';

        Notulensi::create($validatedData);

        return response()->json([
            'status' => true,
            'message' => 'Notulensi berhasil ditambahkan',
        ], 200);
    }

    public function show($id)
    {
        $notulensi = Notulensi::find($id);
        if (!$notulensi) {
            return response()->json([
                'status' => false,
                'message' => 'Notulensi tidak ditemukan',
            ], 404);
        }
        // Cek apakah user peserta rapat terkait
        $user = Auth::user();
        $isPeserta = DB::table('peserta_rapats')
            ->where('rapat_id', $notulensi->rapat_id)
            ->where('user_id', $user->id)
            ->exists();
        if (!$isPeserta) {
            return response()->json([
                'status' => false,
                'message' => 'Anda tidak berhak mengakses notulensi ini',
            ], 403);
        }
        return response()->json([
            'status' => true,
            'data' => $notulensi,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $notulensi = Notulensi::find($id);
        if (!$notulensi) {
            return response()->json([
                'status' => false,
                'message' => 'Notulensi tidak ditemukan',
            ], 404);
        }
        // Cek apakah user peserta rapat terkait
        $user = Auth::user();
        $isPeserta = DB::table('peserta_rapats')
            ->where('rapat_id', $notulensi->rapat_id)
            ->where('user_id', $user->id)
            ->exists();
        if (!$isPeserta) {
            return response()->json([
                'status' => false,
                'message' => 'Anda tidak berhak mengakses notulensi ini',
            ], 403);
        }

        $validatedData = $request->validate([
            'rapat_id' => 'required|exists:rapats,id',
            'isi' => 'required|string',
        ]);

        if ($request->hasFile('file_path')) {
            if ($notulensi->file_path) {
                Storage::disk('public')->delete($notulensi->file_path);
            }
            $validatedData['file_path'] = $request->file('file_path')->store('notulensi_files', 'public');
        }

        $notulensi->update($validatedData);

        return response()->json([
            'status' => true,
            'message' => 'Notulensi berhasil diperbarui',
        ], 200);
    }

    public function destroy($id)
    {
        $notulensi = Notulensi::find($id);
        if (!$notulensi) {
            return response()->json([
                'status' => false,
                'message' => 'Notulensi tidak ditemukan',
            ], 404);
        }
        // Cek apakah user peserta rapat terkait
        $user = Auth::user();
        $isPeserta = DB::table('peserta_rapats')
            ->where('rapat_id', $notulensi->rapat_id)
            ->where('user_id', $user->id)
            ->exists();
        if (!$isPeserta) {
            return response()->json([
                'status' => false,
                'message' => 'Anda tidak berhak mengakses notulensi ini',
            ], 403);
        }

        if ($notulensi->file_path) {
            Storage::disk('public')->delete($notulensi->file_path);
        }

        $notulensi->delete();

        return response()->json([
            'status' => true,
            'message' => 'Notulensi berhasil dihapus',
        ], 200);
    }

    public function generateAudioToText(Request $request, AIService $aiservice)
    {
        try {
            $request->validate([
                'file_path' => 'required|file|mimes:mp3,wav,aac|max:20480',
                'continue_text' => 'nullable|string'
            ]);

            $filePath = $request->file('file_path')->getPathname();
            $continueText = $request->input('continue_text');


            $result = $aiservice->speechToText($filePath, 'id', $continueText);

            return response()->json([
                'status' => true,
                'message' => 'Transkripsi berhasil',
                'transcription' => $result,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat memproses audio: ' . $e->getMessage(),
            ], 500);
        }
    }
}
