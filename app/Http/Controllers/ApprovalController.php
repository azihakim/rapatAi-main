<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Notulensi;
use App\Models\PengajuanLogistik;
use App\Models\Rapat;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApprovalController extends Controller
{
    public function approveRapatView()
    {
        return view('approval.approve-rapat');
    }

    public function viewSuratUndanganRapat($id)
    {
        try {
            $rapat = Rapat::findOrFail($id);

            // Format data untuk surat
            $suratData = [
                'id' => $rapat->id,
                'nomor' => $rapat->nomor,
                'tanggal' => $this->formatTanggalSurat($rapat->created_at),
                'perihal' => $rapat->hal,
                'acara' => $rapat->judul,
                'sifat' => $rapat->sifat,
                'nip' => User::with('role')->whereHas('role', function ($query) {
                    $query->where('name', 'pimpinan');
                })->value('nip'),
                'agenda' => $rapat->deskripsi ?? 'Agenda akan disampaikan dalam rapat',
                'tanggalRapat' => $this->formatTanggalIndonesia($rapat->tanggal),
                'waktu' => date('H.i', strtotime($rapat->waktu)) . ' WIB s.d Selesai',
                'tempat' => $rapat->lokasi ?? 'Ruang Rapat DPRD Provinsi Sumatera Selatan',
                'penandatangan' => User::with('role')->whereHas('role', function ($query) {
                    $query->where('name', 'pimpinan');
                })->value('name'),
                'status' => $rapat->status
            ];

            return response()->json([
                'status' => true,
                'message' => 'Surat undangan rapat retrieved successfully',
                'data' => $suratData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:2,3', // 2 = approved, 3 = rejected                
            ]);

            DB::beginTransaction();

            $rapat = Rapat::findOrFail($id);

            $rapat->update([
                'status' => $validated['status'],
            ]);

            $statusText = $validated['status'] == 2 ? 'disetujui' : 'ditolak';

            // Buat notifikasi
            $notif = Notification::create([
                'title'   => $validated['status'] == 2 ? 'Undangan Rapat Baru' : 'Status Rapat Diperbarui',
                'message' => $validated['status'] == 2
                    ? 'Anda diundang untuk menghadiri rapat "' . $rapat->judul . '" yang akan dilaksanakan pada '
                    . Carbon::parse($rapat->tanggal)->translatedFormat('d F Y') .
                    ' pukul ' . Carbon::parse($rapat->jam_mulai)->format('H:i') . ' WIB di '
                    . $rapat->tempat . '.'
                    : 'Rapat dengan judul "' . $rapat->judul . '" telah ' . $statusText . ' oleh pimpinan.',
                'type'    => 'Meeting',
            ]);

            // Tentukan user penerima
            $userIds = collect();

            if ($validated['status'] == 2) {
                // Approved → semua peserta rapat
                $userIds = $rapat->pesertaRapat->pluck('user_id');

                // + semua sekretariat
                $sekretariatUsers = User::with('role')
                    ->whereHas('role', function ($query) {
                        $query->where('name', 'sekretariat');
                    })
                    ->pluck('id');
                $userIds = $userIds->merge($sekretariatUsers)->unique();
            } elseif ($validated['status'] == 3) {
                // Rejected → hanya sekretariat
                $userIds = User::with('role')
                    ->whereHas('role', function ($query) {
                        $query->where('name', 'sekretariat');
                    })
                    ->pluck('id');
            }

            // Attach user ke notifikasi (tanpa duplikat)
            $attachData = $userIds->mapWithKeys(fn($id) => [
                $id => ['is_read' => false, 'read_at' => null]
            ]);
            $notif->users()->syncWithoutDetaching($attachData);

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => "Rapat berhasil {$statusText}",
                'data'    => $rapat
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => 'Terjadi kesalahan saat memperbarui status',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    private function formatTanggalSurat($date)
    {
        setlocale(LC_TIME, 'id_ID.utf8');
        return 'Palembang, ' . strftime('%d %B %Y', strtotime($date));
    }

    private function formatTanggalIndonesia($date)
    {
        $hari = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];

        $bulan = [
            'January' => 'Januari',
            'February' => 'Februari',
            'March' => 'Maret',
            'April' => 'April',
            'May' => 'Mei',
            'June' => 'Juni',
            'July' => 'Juli',
            'August' => 'Agustus',
            'September' => 'September',
            'October' => 'Oktober',
            'November' => 'November',
            'December' => 'Desember'
        ];

        $timestamp = strtotime($date);
        $namaHari = $hari[date('l', $timestamp)];
        $tanggal = date('j', $timestamp);
        $namaBulan = $bulan[date('F', $timestamp)];
        $tahun = date('Y', $timestamp);

        return "{$namaHari}, {$tanggal} {$namaBulan} {$tahun}";
    }

    public function viewApproveNotulen()
    {
        return view('approval.approve-notulen');
    }

    public function detailViewNotulen($id)
    {
        try {
            $notulensi = Notulensi::with('rapat')->findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Detail notulensi retrieved successfully',
                'data' => $notulensi
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function updateStatusNotulen(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:disetujui,ditolak', // 2 = approved, 3 = rejected
            ]);

            DB::beginTransaction();

            $notulensi = Notulensi::findOrFail($id);

            $notulensi->update([
                'status' => $validated['status'],
            ]);

            $statusText = $validated['status'] == 2 ? 'disetujui' : 'ditolak';
            
            $notif = Notification::create([
                'title'   => $validated['status'] == 2 ? 'Notulensi Disetujui' : 'Notulensi Ditolak',
                'message' => $validated['status'] == 2
                    ? 'Notulensi untuk rapat "' . $notulensi->rapat->judul . '" telah disetujui.'
                    : 'Notulensi untuk rapat "' . $notulensi->rapat->judul . '" telah ditolak.',
                'type'    => 'Notulensi',
            ]);

            $userIds = collect();

            $userIds = $notulensi->rapat->pesertaRapat->pluck('user_id');


            $sekretariatUsers = User::with('role')
                ->whereHas('role', function ($query) {
                    $query->where('name', 'sekretariat');
                })
                ->pluck('id');
            $userIds = $userIds->merge($sekretariatUsers)->unique();
            
            $attachData = $userIds->mapWithKeys(fn($id) => [
                $id => ['is_read' => false, 'read_at' => null]
            ]);
            $notif->users()->syncWithoutDetaching($attachData);

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => "Notulensi berhasil {$statusText}",
                'data'    => $notulensi
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => 'Terjadi kesalahan saat memperbarui status',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function viewApprovalLogistik()
    {           
        return view('approval.approve-logistik');
    }

    public function updateStatusLogistik(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:disetujui,ditolak', // disetujui, ditolak
            ]);

            DB::beginTransaction();

            $logistik = PengajuanLogistik::findOrFail($id);

            $logistik->update([
                'status' => $validated['status'],
            ]);

            $statusText = $validated['status'] == 'disetujui' ? 'disetujui' : 'ditolak';
            
            $notif = Notification::create([
                'title'   => $validated['status'] == 'disetujui' ? 'Pengajuan Logistik Disetujui' : 'Pengajuan Logistik Ditolak',
                'message' => $validated['status'] == 'disetujui'
                    ? 'Pengajuan logistik untuk rapat "' . $logistik->rapat->judul . '" telah disetujui.'
                    : 'Pengajuan logistik untuk rapat "' . $logistik->rapat->judul . '" telah ditolak.',
                'type'    => 'Logistik',
            ]);

            $userIds = collect();

            $userIds = $logistik->rapat->pesertaRapat->pluck('user_id');
            $sekretariatUsers = User::with('role')
                ->whereHas('role', function ($query) {
                    $query->where('name', 'sekretariat');
                })
                ->pluck('id');
            $userIds = $userIds->merge($sekretariatUsers)->unique();
            $attachData = $userIds->mapWithKeys(fn($id) => [
                $id => ['is_read' => false, 'read_at' => null]
            ]);
            $notif->users()->syncWithoutDetaching($attachData);
            DB::commit();
            return response()->json([
                'status'  => true,
                'message' => "Pengajuan logistik berhasil {$statusText}",
                'data'    => $logistik
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => 'Terjadi kesalahan saat memperbarui status',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
