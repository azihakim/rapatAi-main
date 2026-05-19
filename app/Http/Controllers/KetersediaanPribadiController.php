<?php

namespace App\Http\Controllers;

use App\Models\KetersediaanPribadi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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
     return date('H:i', strtotime($row->waktu_mulai));
    })
    ->addColumn('waktu_selesai', function ($row) {
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
  $validated = $this->validateAvailabilityRequest($request);

  DB::transaction(function () use ($validated) {
   foreach ($validated['items'] as $item) {
    foreach ($item['slots'] as $slot) {
     KetersediaanPribadi::create([
      'user_id' => Auth::user()->id,
      'tanggal' => $item['tanggal'],
      'waktu_mulai' => $slot['waktu_mulai'],
      'waktu_selesai' => $slot['waktu_selesai'],
     ]);
    }
   }
  });

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
  }

  return response()->json([
   'status' => false,
   'message' => 'Data tidak ditemukan.'
  ], 404);
 }

 public function update(Request $request, $id)
 {
  $validated = $this->validateAvailabilityRequest($request);
  $items = array_values($validated['items']);
  $firstItem = $items[0] ?? null;
  $firstSlot = $firstItem['slots'][0] ?? null;

  if (!$firstItem || !$firstSlot) {
   return response()->json([
    'status' => false,
    'message' => 'Data jadwal tidak valid.'
   ], 422);
  }

  $personal = KetersediaanPribadi::find($id);
  if (!$personal) {
   return response()->json([
    'status' => false,
    'message' => 'Data tidak ditemukan.'
   ], 404);
  }

  $personal->update([
   'tanggal' => $firstItem['tanggal'],
   'waktu_mulai' => $firstSlot['waktu_mulai'],
   'waktu_selesai' => $firstSlot['waktu_selesai'],
  ]);

  return response()->json([
   'status' => true,
   'message' => 'Ketersediaan pribadi berhasil diperbarui.'
  ], 200);
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
  }

  return response()->json([
   'status' => false,
   'message' => 'Data tidak ditemukan.'
  ], 404);
 }

 private function validateAvailabilityRequest(Request $request): array
 {
  $validator = Validator::make($request->all(), [
   'items' => ['required', 'array', 'min:1'],
   'items.*.tanggal' => ['required', 'date'],
   'items.*.slots' => ['required', 'array', 'min:1'],
   'items.*.slots.*.waktu_mulai' => ['required', 'date_format:H:i'],
   'items.*.slots.*.waktu_selesai' => ['required', 'date_format:H:i'],
  ]);

  $validator->after(function ($validator) use ($request) {
   foreach ($request->input('items', []) as $dateIndex => $item) {
    foreach (($item['slots'] ?? []) as $slotIndex => $slot) {
     $start = $slot['waktu_mulai'] ?? null;
     $end = $slot['waktu_selesai'] ?? null;

     if (!$start || !$end) {
      continue;
     }

     if (strtotime($end) <= strtotime($start)) {
      $validator->errors()->add(
       "items.$dateIndex.slots.$slotIndex.waktu_selesai",
       'Waktu selesai harus lebih besar dari waktu mulai.'
      );
     }
    }
   }
  });

  return $validator->validate();
 }
}
