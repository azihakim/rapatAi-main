<?php

namespace App\Http\Controllers;

use App\Models\KetersediaanPribadi;
use App\Models\Notification;
use App\Models\Rapat;
use App\Models\User;
use App\Services\AIService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RapatController extends Controller
{
    public function index()
    {

        $pesertaRapat = User::whereHas('role', function ($query) {
            $query->where('name', 'anggota');
        })->with('role')->get();

        $pendatanganRapat = User::with('role')->whereHas('role', function($query) {
            $query->where('name', 'pimpinan');
        })->get();

        if (Request()->ajax()) {
            $rapat = Rapat::latest()->get();
            return datatables()->of($rapat)
                ->addIndexColumn()
                ->addColumn('tanggal_rapat', function ($row) {                    
                    Carbon::setLocale('id');
                    return Carbon::parse($row->tanggal)->translatedFormat('d F Y');
                })
                ->addColumn('waktu_rapat', function ($row) {
                    return date('H:i', strtotime($row->waktu)) . ' WIB s.d Selesai';
                })
                ->addColumn('status', function ($row) {
                    if ($row->status == 1) {
                        $status = '<span class="badge badge-warning">Pengajuan</span>';
                    } else if ($row->status == 2) {
                        $status = '<span class="badge badge-success">Disetujui</span>';
                    } else if ($row->status == 3) {
                        $status = '<span class="badge badge-danger">Ditolak</span>';
                    } else {
                        $status = '<span class="badge badge-secondry">Draft</span>';
                    }
                    return $status;
                })
                ->addColumn('action', function ($row) {
                    $user = Auth::user();
                    $btn = '';
                    
                    if ($user && $user->role && $user->role->name === 'sekretariat') {
                        $btn .= '<button type="button" class="btn btn-primary btn-sm editRapat" data-id="' . $row->id . '">Edit</button>';
                        $btn .= ' <button type="button" class="deleteRapat btn btn-danger btn-sm" data-id="' . $row->id . '">Delete</button>';
                    }
                    
                    if ($user && $user->role && $user->role->name === 'pimpinan' && $row->status == 1) {
                        $btn .= '<button class="btn btn-info btn-detail btn-sm approveDetail" data-toggle="modal" data-target="#suratModal" data-id="' . $row->id . '">
                                <i class="fas fa-eye"></i> Detail
                            </button>';                
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->toJson();
        }

        return view('rapat.index', compact('pesertaRapat', 'pendatanganRapat'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'waktu' => 'required',
            'lokasi' => 'required|string|max:255',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:255',
            'peserta' => 'required|array|min:1',
            'peserta.*' => 'exists:users,id',
            'jenis_rapat' => 'required|string|max:255',
            'hal' => 'required|string|max:255',
            'penandatangan_id' => 'required|exists:users,id',
            'sifat' => 'required|string|max:255',
        ]);

        $generateNomorSurat = Rapat::max('id') + 1;
        $nomorSurat = 'SURAT/RA/01/' . $generateNomorSurat;

        $rapat = Rapat::create([
            'tanggal' => $request->tanggal,
            'waktu' => $request->waktu,
            'lokasi' => $request->lokasi,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'jenis_rapat' => $request->jenis_rapat,
            'status' => 1, // Pengajuan
            'hal' => $request->hal,
            'penandatangan_id' => $request->penandatangan_id,
            'sifat' => $request->sifat,
            'nomor' => $nomorSurat,
        ]);

        $notif = Notification::create([
            'title' => 'Pengajuan Rapat Baru',
            'message' => 'Rapat dengan judul "' . $rapat->judul . '" telah diajukan dan menunggu persetujuan.',
            'type' => 'info',
        ]);
        
        $pimpinanUsers = User::whereHas('role', function($query) {
            $query->where('name', 'pimpinan');
        })->get();

        foreach ($pimpinanUsers as $user) {
            $notif->users()->attach($user->id, [
                'is_read' => false,
                'read_at' => null,
            ]);
        }
        

        // Attach peserta rapat
        foreach ($request->peserta as $userId) {
            $rapat->pesertaRapat()->create([
                'user_id' => $userId,
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Rapat berhasil dibuat dan menunggu persetujuan.',
        ], 200);
    }

    public function show($id)
    {
        try {
            $rapat = Rapat::with('pesertaRapat.user')->findOrFail($id);
            return response()->json([
                'status' => true,
                'message' => 'Data Berhasil diambil',
                'data' => $rapat
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'waktu' => 'required',
            'lokasi' => 'required|string|max:255',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:255',
            'peserta' => 'required|array|min:1',
            'peserta.*' => 'exists:users,id',
            'jenis_rapat' => 'required|string|max:255',
            'hal' => 'required|string|max:255',
            'penandatangan_id' => 'required|exists:users,id',
            'sifat' => 'required|string|max:255',
        ]);

        try {
            $rapat = Rapat::findOrFail($id);
            $rapat->update([
                'tanggal' => $request->tanggal,
                'waktu' => $request->waktu,
                'lokasi' => $request->lokasi,
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
                'jenis_rapat' => $request->jenis_rapat,
                'hal' => $request->hal,
                'penandatangan_id' => $request->penandatangan_id,
                'sifat' => $request->sifat,
            ]);

            // Sync peserta rapat
            $rapat->pesertaRapat()->delete(); // Hapus peserta lama
            foreach ($request->peserta as $userId) {
                $rapat->pesertaRapat()->create([
                    'user_id' => $userId,
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Rapat berhasil diperbarui.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function destroy($id)
    {
        try {
            $rapat = Rapat::findOrFail($id);
            $rapat->pesertaRapat()->delete(); // Hapus peserta rapat terkait
            $rapat->delete();

            return response()->json([
                'status' => true,
                'message' => 'Rapat berhasil dihapus.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function rekomendasiJadwal(Request $request, AIService $ai)
    {
        $request->validate([
            'peserta' => 'required|array',
            'duration' => 'required|string',
            'tanggal' => 'required|date',
        ]);

        $availabilities = User::whereIn('id', $request->peserta)
            ->with(['ketersediaanPribadi' => function($q) use ($request) {
                $q->where('tanggal', $request->tanggal);
            }])
            ->get()
            ->map(function ($user) {
                return [
                    'nama' => $user->name,
                    'jadwal' => optional($user->ketersediaanPribadi)->map(function ($k) {
                        return [
                            'tanggal' => $k->tanggal,
                            'mulai' => $k->waktu_mulai,
                            'selesai' => $k->waktu_selesai
                        ];
                    })->toArray()
                ];
            })->toArray();

        $result = $ai->generateMeetingRecommendation($availabilities, $request->duration . " hour", $request->tanggal);

        return response()->json([
            'status' => 'success',
            'recommendation' => $result
        ]);
    }

    public function jadwalPeserta(Request $request)
    {
        $request->validate([
            'peserta' => 'required|array|min:1',
            'tanggal' => 'required|date',
        ]);

        $users = \App\Models\User::whereIn('id', $request->peserta)
            ->with(['ketersediaanPribadi' => function($q) use ($request) {
                $q->where('tanggal', $request->tanggal);
            }])->get();

        $result = $users->map(function($user) {
            return [
                'id' => $user->id,
                'nama' => $user->name,
                'jadwal' => $user->ketersediaanPribadi->map(function($k) {
                    return [
                        'tanggal' => $k->tanggal,
                        'mulai' => $k->waktu_mulai,
                        'selesai' => $k->waktu_selesai
                    ];
                })
            ];
        });

        return response()->json(['data' => $result]);
    }
}
