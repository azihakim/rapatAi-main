<?php

namespace App\Http\Controllers;

use App\Models\Rapat;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $rapats = Rapat::where('status', 2)->with('pesertaRapat.user')->get();
                
        $calendarEvents = $rapats->map(function ($rapat) {    
            $startDateTime = $rapat->tanggal . 'T' . $rapat->waktu;                
            $colors = [
                'rapat_paripurna' => ['backgroundColor' => '#007bff', 'borderColor' => '#007bff'],
                'rapat_komisi' => ['backgroundColor' => '#28a745', 'borderColor' => '#28a745'],
                'rapat_badan' => ['backgroundColor' => '#17a2b8', 'borderColor' => '#17a2b8'],
                'lain_lain' => ['backgroundColor' => '#ffc107', 'borderColor' => '#ffc107'],
            ];
            
            $color = $colors[$rapat->jenis_rapat] ?? $colors['rapat_paripurna'];
            
            return [
                'id' => $rapat->id,
                'title' => $rapat->judul,
                'start' => $startDateTime,
                'end' => $startDateTime,
                'backgroundColor' => $color['backgroundColor'],
                'borderColor' => $color['borderColor'],
                'location' => $rapat->lokasi,
                'description' => $rapat->deskripsi,
                'category' => $this->mapJenisRapat($rapat->jenis_rapat),
                'status' => $rapat->status,
                'extendedProps' => [
                    'location' => $rapat->lokasi,
                    'description' => $rapat->deskripsi,
                    'category' => $this->mapJenisRapat($rapat->jenis_rapat),
                    'status' => $rapat->status,
                    'peserta_count' => $rapat->pesertaRapat->count(),
                    'peserta' => $rapat->pesertaRapat->map(function($peserta) {
                        return [
                            'name' => $peserta->user->name ?? 'Nama tidak tersedia',
                            'jabatan' => $peserta->user->email ?? 'Email tidak tersedia'
                        ];
                    })
                ]
            ];
        });

        return view('dashboard.index', compact('rapats', 'calendarEvents'));
    }
    
    private function mapJenisRapat($jenisRapat)
    {
        $mapping = [
            'rapat_paripurna' => 'paripurna',
            'rapat_komisi' => 'komisi',
            'rapat_badan' => 'badan',
            'lain_lain' => 'lain_lain',
        ];
        
        return $mapping[$jenisRapat] ?? 'paripurna';
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal' => 'required|date',
            'waktu' => 'required',
            'lokasi' => 'nullable|string|max:255',
            'jenis_rapat' => 'required|in:rapat_paripurna,rapat_komisi,rapat_badan,lain_lain',
        ]);

        $rapat = Rapat::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Rapat berhasil dibuat',
            'data' => $rapat
        ]);
    }
    
    public function update(Request $request, $id)
    {
        $rapat = Rapat::findOrFail($id);
        
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal' => 'required|date',
            'waktu' => 'required',
            'lokasi' => 'nullable|string|max:255',
            'jenis_rapat' => 'required|in:rapat_paripurna,rapat_komisi,rapat_badan,lain_lain',
        ]);

        $rapat->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Rapat berhasil diupdate',
            'data' => $rapat
        ]);
    }
    
    public function destroy($id)
    {
        $rapat = Rapat::findOrFail($id);
        $rapat->delete();

        return response()->json([
            'success' => true,
            'message' => 'Rapat berhasil dihapus'
        ]);
    }
}
