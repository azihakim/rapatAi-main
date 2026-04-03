<?php

namespace App\Http\Controllers;

use App\Models\PengajuanLogistik;
use App\Models\Rapat;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengajuanLogistikController extends Controller
{
    public function index()
    {
        $rapat = Rapat::get();
        if (Request()->ajax()) {
            $data = PengajuanLogistik::with('rapat', 'user')->get();
            return datatables()->of($data)
                ->addIndexColumn()
                ->addColumn('kegiatan', function ($row) {
                    return \Illuminate\Support\Str::limit($row->rapat->judul, 60);
                })
                ->addColumn('tanggal', function ($row) {
                    return Carbon::parse($row->rapat->tanggal)
                        ->locale('id')
                        ->translatedFormat('d F Y');
                })
                ->addColumn('diajukan', function ($row) {
                    return $row->user->name;
                })
                ->addColumn('status', function ($row) {
                    return ucfirst($row->status);
                })
                ->addColumn('action', function ($row) {
                    $user = Auth::user();
                    $btn = '';
                    if ($user && $user->role && $user->role->name === 'sekretariat') {
                        $btn = '<button type="button" class="btn btn-sm btn-info detailLogistik" data-id="' . $row->id . '">Detail</button> ';
                        $btn .= '<button type="button" class="btn btn-sm btn-primary editLogistik" data-id="' . $row->id . '">Edit</button> ';
                        $btn .= '<button type="button" class="btn btn-sm btn-danger deleteLogistik" data-id="' . $row->id . '">Delete</button>';
                    } 
                    if ($row->status === 'draft') {
                        $btn = '<button type="button" class="btn btn-sm btn-info detailLogistik" data-id="' . $row->id . '">Detail</button> ';
                    }                   
                    return $btn;
                })
                ->rawColumns(['action'])
                ->toJson();
        }

        return view('logistik.index', compact('rapat'));
    }


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'rapat_id'        => 'required|exists:rapats,id',
            'jenis_pengajuan' => 'required|string|max:255',
            'keterangan'      => 'nullable|string',
            'detail'          => 'required|array|min:1',
            'detail.*.item'   => 'required|string|max:255',
            'detail.*.jumlah' => 'required|integer|min:1',
            'detail.*.satuan' => 'required|string|max:100',
            'detail.*.keterangan' => 'nullable|string|max:255',
        ]);

        $validatedData['user_id'] = Auth::id();
        
        $logistik = PengajuanLogistik::create([
            'rapat_id'        => $validatedData['rapat_id'],
            'jenis_pengajuan' => $validatedData['jenis_pengajuan'],
            'keterangan'      => $validatedData['keterangan'] ?? null,
            'user_id'         => $validatedData['user_id'],
        ]);
        
        foreach ($validatedData['detail'] as $detail) {
            $logistik->details()->create([
                'item'       => $detail['item'],
                'jumlah'     => $detail['jumlah'],
                'satuan'     => $detail['satuan'],
                'keterangan' => $detail['keterangan'] ?? null,
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Pengajuan logistik berhasil disimpan.',
        ], 200);
    }


    public function show($id)
    {
        try {
            $logistik = PengajuanLogistik::with('details', 'rapat', 'user')->findOrFail($id);
            return response()->json([
                'status' => true,
                'data' => $logistik,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan.',
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $logistik = PengajuanLogistik::findOrFail($id);

            $validatedData = $request->validate([
                'rapat_id'        => 'required|exists:rapats,id',
                'jenis_pengajuan' => 'required|string|max:255',
                'keterangan'      => 'nullable|string',
                'detail'          => 'required|array|min:1',
                'detail.*.item'   => 'required|string|max:255',
                'detail.*.jumlah' => 'required|integer|min:1',
                'detail.*.satuan' => 'required|string|max:100',
                'detail.*.keterangan' => 'nullable|string|max:255',
            ]);

            $logistik->update([
                'rapat_id'        => $validatedData['rapat_id'],
                'jenis_pengajuan' => $validatedData['jenis_pengajuan'],
                'keterangan'      => $validatedData['keterangan'] ?? null,
            ]);

            
            $logistik->details()->delete();
            
            foreach ($validatedData['detail'] as $detail) {
                $logistik->details()->create([
                    'item'       => $detail['item'],
                    'jumlah'     => $detail['jumlah'],
                    'satuan'     => $detail['satuan'],
                    'keterangan' => $detail['keterangan'] ?? null,
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Pengajuan logistik berhasil diperbarui.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan atau terjadi kesalahan.',
            ], 404);
        }
    }

    public function destroy($id)
    {
        try {
            $logistik = PengajuanLogistik::findOrFail($id);
            $logistik->details()->delete();
            $logistik->delete();

            return response()->json([
                'status' => true,
                'message' => 'Pengajuan logistik berhasil dihapus.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan atau terjadi kesalahan.',
            ], 404);
        }
    }

    public function pengajuanSebelumnya($rapatId)
    {
        $data = PengajuanLogistik::with('details', 'user')
            ->where('rapat_id', $rapatId)
            ->orderBy('created_at', 'asc')
            ->get();
        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }
}
