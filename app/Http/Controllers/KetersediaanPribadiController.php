<?php

namespace App\Http\Controllers;

use App\Models\KetersediaanPribadi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KetersediaanPribadiController extends Controller
{
    public function index()
    {
        if (Auth::user()->role_id == 1) {
            $availablePersonal = KetersediaanPribadi::with('user')->get();
        } else {
            $availablePersonal = KetersediaanPribadi::with('user')->where('user_id', Auth::user()->id)->get();
        }

        if (Request()->ajax()) {
            return datatables()->of($availablePersonal)
                ->addIndexColumn()
                ->addColumn('nama', function ($row) {
                    return $row->user->name;
                })
                ->addColumn('tanggal', function ($row) {
                    Carbon::setLocale('id');
                    return Carbon::parse($row->tanggal)->translatedFormat('d F Y');
                })
                ->addColumn('waktu_mulai', function ($row) {
                    if ($row->full_day) {
                        return 'Seharian';
                    }
                    return date('H:i', strtotime($row->waktu_mulai));
                })
                ->addColumn('waktu_selesai', function ($row) {
                    if ($row->full_day) {
                        return 'Seharian';
                    }
                    return date('H:i', strtotime($row->waktu_selesai));
                })
                ->addColumn('action', function ($row) {
                    $btn = '<button type="button" class="btn btn-primary btn-sm editKetersediaanPribadi" data-id="' . $row->id . '">Edit</button>';
                    $btn .= ' <button type="button" class="deleteKetersediaanPribadi btn btn-danger btn-sm" data-id="' . $row->id . '">Delete</button>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->toJson();
        }

        return view('ketersediaan.index');
    }

    public function store(Request $request)
    {
        $fullDay = $request->boolean('full_day');
        $rules = [
            'tanggal' => 'required|date',
        ];
        if (!$fullDay) {
            $rules['waktu_mulai'] = 'required|date_format:H:i';
            $rules['waktu_selesai'] = 'required|date_format:H:i|after:waktu_mulai';
        }
        $request->validate($rules);

        $waktuMulai = $fullDay ? '00:00' : $request->waktu_mulai;
        $waktuSelesai = $fullDay ? '23:59' : $request->waktu_selesai;

        KetersediaanPribadi::create([
            'user_id' => Auth::user()->id,
            'tanggal' => $request->tanggal,
            'waktu_mulai' => $waktuMulai,
            'waktu_selesai' => $waktuSelesai,
            'full_day' => $fullDay,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Ketersediaan pribadi berhasil ditambahkan.'
        ], 200);
    }

    public function show($id)
    {
        $personal = KetersediaanPribadi::find($id);
        if ($personal) {
            return response()->json([
                'status' => true,
                'data' => $personal
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $fullDay = $request->boolean('full_day');
        $rules = [
            'tanggal' => 'required|date',
        ];
        if (!$fullDay) {
            $rules['waktu_mulai'] = 'required|date_format:H:i';
            $rules['waktu_selesai'] = 'required|date_format:H:i|after:waktu_mulai';
        }
        $request->validate($rules);

        $waktuMulai = $fullDay ? '00:00' : $request->waktu_mulai;
        $waktuSelesai = $fullDay ? '23:59' : $request->waktu_selesai;

        $personal = KetersediaanPribadi::find($id);
        if ($personal) {
            $personal->update([
                'tanggal' => $request->tanggal,
                'waktu_mulai' => $waktuMulai,
                'waktu_selesai' => $waktuSelesai,
                'full_day' => $fullDay,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Ketersediaan pribadi berhasil diperbarui.'
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }
    }

    public function destroy($id)
    {
        $personal = KetersediaanPribadi::find($id);
        if ($personal) {
            $personal->delete();
            return response()->json([
                'status' => true,
                'message' => 'Ketersediaan pribadi berhasil dihapus.'
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }
    }
}
