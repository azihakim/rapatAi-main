<?php

namespace Database\Seeders;

use App\Models\KetersediaanPribadi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AvailableScheduleSeeder extends Seeder
{
    /**
     * Seed kegiatan anggota untuk bulan Juli 2026.
     */
    public function run(): void
    {
        // User IDs: 3 = Budi Santoso, 4 = Siti Aminah, 5 = Andi Wijaya
        $schedules = [
            // ========== Budi Santoso (user_id: 3) ==========
            // Minggu 1
            ['user_id' => 3, 'tanggal' => '2026-07-01', 'waktu_mulai' => '08:00', 'waktu_selesai' => '11:00'],
            ['user_id' => 3, 'tanggal' => '2026-07-02', 'waktu_mulai' => '13:00', 'waktu_selesai' => '15:30'],
            ['user_id' => 3, 'tanggal' => '2026-07-03', 'waktu_mulai' => '09:00', 'waktu_selesai' => '12:00'],
            // Minggu 2
            ['user_id' => 3, 'tanggal' => '2026-07-06', 'waktu_mulai' => '00:00', 'waktu_selesai' => '23:59'], // Seharian - Kunjungan Kerja
            ['user_id' => 3, 'tanggal' => '2026-07-07', 'waktu_mulai' => '08:30', 'waktu_selesai' => '10:30'],
            ['user_id' => 3, 'tanggal' => '2026-07-08', 'waktu_mulai' => '14:00', 'waktu_selesai' => '16:30'],
            ['user_id' => 3, 'tanggal' => '2026-07-09', 'waktu_mulai' => '09:00', 'waktu_selesai' => '11:00'],
            ['user_id' => 3, 'tanggal' => '2026-07-10', 'waktu_mulai' => '15:00', 'waktu_selesai' => '17:00'],
            // Minggu 3
            ['user_id' => 3, 'tanggal' => '2026-07-13', 'waktu_mulai' => '08:00', 'waktu_selesai' => '10:00'],
            ['user_id' => 3, 'tanggal' => '2026-07-14', 'waktu_mulai' => '13:00', 'waktu_selesai' => '15:00'],
            ['user_id' => 3, 'tanggal' => '2026-07-15', 'waktu_mulai' => '00:00', 'waktu_selesai' => '23:59'], // Seharian - Rapat Paripurna
            ['user_id' => 3, 'tanggal' => '2026-07-16', 'waktu_mulai' => '09:30', 'waktu_selesai' => '12:00'],
            ['user_id' => 3, 'tanggal' => '2026-07-17', 'waktu_mulai' => '14:00', 'waktu_selesai' => '16:00'],
            // Minggu 4
            ['user_id' => 3, 'tanggal' => '2026-07-20', 'waktu_mulai' => '08:00', 'waktu_selesai' => '11:30'],
            ['user_id' => 3, 'tanggal' => '2026-07-21', 'waktu_mulai' => '13:30', 'waktu_selesai' => '15:30'],
            ['user_id' => 3, 'tanggal' => '2026-07-22', 'waktu_mulai' => '09:00', 'waktu_selesai' => '11:00'],
            ['user_id' => 3, 'tanggal' => '2026-07-23', 'waktu_mulai' => '15:00', 'waktu_selesai' => '17:30'],
            ['user_id' => 3, 'tanggal' => '2026-07-24', 'waktu_mulai' => '08:00', 'waktu_selesai' => '10:00'],
            // Minggu 5
            ['user_id' => 3, 'tanggal' => '2026-07-27', 'waktu_mulai' => '09:00', 'waktu_selesai' => '12:00'],
            ['user_id' => 3, 'tanggal' => '2026-07-29', 'waktu_mulai' => '13:00', 'waktu_selesai' => '16:00'],
            ['user_id' => 3, 'tanggal' => '2026-07-31', 'waktu_mulai' => '00:00', 'waktu_selesai' => '23:59'], // Seharian

            // ========== Siti Aminah (user_id: 4) ==========
            // Minggu 1
            ['user_id' => 4, 'tanggal' => '2026-07-01', 'waktu_mulai' => '13:00', 'waktu_selesai' => '15:00'],
            ['user_id' => 4, 'tanggal' => '2026-07-02', 'waktu_mulai' => '08:00', 'waktu_selesai' => '10:00'],
            ['user_id' => 4, 'tanggal' => '2026-07-03', 'waktu_mulai' => '15:00', 'waktu_selesai' => '17:00'],
            // Minggu 2
            ['user_id' => 4, 'tanggal' => '2026-07-06', 'waktu_mulai' => '09:00', 'waktu_selesai' => '11:30'],
            ['user_id' => 4, 'tanggal' => '2026-07-07', 'waktu_mulai' => '00:00', 'waktu_selesai' => '23:59'], // Seharian
            ['user_id' => 4, 'tanggal' => '2026-07-08', 'waktu_mulai' => '08:00', 'waktu_selesai' => '10:00'],
            ['user_id' => 4, 'tanggal' => '2026-07-09', 'waktu_mulai' => '13:30', 'waktu_selesai' => '15:30'],
            ['user_id' => 4, 'tanggal' => '2026-07-10', 'waktu_mulai' => '09:00', 'waktu_selesai' => '11:00'],
            // Minggu 3
            ['user_id' => 4, 'tanggal' => '2026-07-13', 'waktu_mulai' => '14:00', 'waktu_selesai' => '16:30'],
            ['user_id' => 4, 'tanggal' => '2026-07-14', 'waktu_mulai' => '08:00', 'waktu_selesai' => '10:30'],
            ['user_id' => 4, 'tanggal' => '2026-07-15', 'waktu_mulai' => '00:00', 'waktu_selesai' => '23:59'], // Seharian - Rapat Paripurna
            ['user_id' => 4, 'tanggal' => '2026-07-16', 'waktu_mulai' => '09:00', 'waktu_selesai' => '11:00'],
            ['user_id' => 4, 'tanggal' => '2026-07-17', 'waktu_mulai' => '15:30', 'waktu_selesai' => '17:30'],
            // Minggu 4
            ['user_id' => 4, 'tanggal' => '2026-07-20', 'waktu_mulai' => '13:00', 'waktu_selesai' => '15:00'],
            ['user_id' => 4, 'tanggal' => '2026-07-21', 'waktu_mulai' => '08:30', 'waktu_selesai' => '11:00'],
            ['user_id' => 4, 'tanggal' => '2026-07-22', 'waktu_mulai' => '14:00', 'waktu_selesai' => '16:00'],
            ['user_id' => 4, 'tanggal' => '2026-07-23', 'waktu_mulai' => '09:00', 'waktu_selesai' => '12:00'],
            // Minggu 5
            ['user_id' => 4, 'tanggal' => '2026-07-27', 'waktu_mulai' => '08:00', 'waktu_selesai' => '10:00'],
            ['user_id' => 4, 'tanggal' => '2026-07-28', 'waktu_mulai' => '13:00', 'waktu_selesai' => '15:30'],
            ['user_id' => 4, 'tanggal' => '2026-07-30', 'waktu_mulai' => '15:00', 'waktu_selesai' => '17:00'],

            // ========== Andi Wijaya (user_id: 5) ==========
            // Minggu 1
            ['user_id' => 5, 'tanggal' => '2026-07-01', 'waktu_mulai' => '09:00', 'waktu_selesai' => '11:30'],
            ['user_id' => 5, 'tanggal' => '2026-07-02', 'waktu_mulai' => '15:00', 'waktu_selesai' => '17:00'],
            ['user_id' => 5, 'tanggal' => '2026-07-03', 'waktu_mulai' => '08:00', 'waktu_selesai' => '10:00'],
            // Minggu 2
            ['user_id' => 5, 'tanggal' => '2026-07-06', 'waktu_mulai' => '13:00', 'waktu_selesai' => '15:00'],
            ['user_id' => 5, 'tanggal' => '2026-07-07', 'waktu_mulai' => '08:00', 'waktu_selesai' => '11:00'],
            ['user_id' => 5, 'tanggal' => '2026-07-08', 'waktu_mulai' => '00:00', 'waktu_selesai' => '23:59'], // Seharian
            ['user_id' => 5, 'tanggal' => '2026-07-09', 'waktu_mulai' => '14:00', 'waktu_selesai' => '16:00'],
            ['user_id' => 5, 'tanggal' => '2026-07-10', 'waktu_mulai' => '08:30', 'waktu_selesai' => '10:30'],
            // Minggu 3
            ['user_id' => 5, 'tanggal' => '2026-07-13', 'waktu_mulai' => '09:00', 'waktu_selesai' => '11:00'],
            ['user_id' => 5, 'tanggal' => '2026-07-14', 'waktu_mulai' => '15:00', 'waktu_selesai' => '17:30'],
            ['user_id' => 5, 'tanggal' => '2026-07-15', 'waktu_mulai' => '00:00', 'waktu_selesai' => '23:59'], // Seharian - Rapat Paripurna
            ['user_id' => 5, 'tanggal' => '2026-07-16', 'waktu_mulai' => '13:00', 'waktu_selesai' => '15:00'],
            ['user_id' => 5, 'tanggal' => '2026-07-17', 'waktu_mulai' => '08:00', 'waktu_selesai' => '10:30'],
            // Minggu 4
            ['user_id' => 5, 'tanggal' => '2026-07-20', 'waktu_mulai' => '15:00', 'waktu_selesai' => '17:00'],
            ['user_id' => 5, 'tanggal' => '2026-07-21', 'waktu_mulai' => '09:00', 'waktu_selesai' => '11:30'],
            ['user_id' => 5, 'tanggal' => '2026-07-22', 'waktu_mulai' => '00:00', 'waktu_selesai' => '23:59'], // Seharian
            ['user_id' => 5, 'tanggal' => '2026-07-23', 'waktu_mulai' => '08:00', 'waktu_selesai' => '10:00'],
            ['user_id' => 5, 'tanggal' => '2026-07-24', 'waktu_mulai' => '13:00', 'waktu_selesai' => '16:00'],
            // Minggu 5
            ['user_id' => 5, 'tanggal' => '2026-07-28', 'waktu_mulai' => '09:00', 'waktu_selesai' => '12:00'],
            ['user_id' => 5, 'tanggal' => '2026-07-29', 'waktu_mulai' => '14:00', 'waktu_selesai' => '16:30'],
            ['user_id' => 5, 'tanggal' => '2026-07-30', 'waktu_mulai' => '08:00', 'waktu_selesai' => '10:00'],
            ['user_id' => 5, 'tanggal' => '2026-07-31', 'waktu_mulai' => '13:00', 'waktu_selesai' => '15:30'],
        ];

        foreach ($schedules as $schedule) {
            KetersediaanPribadi::create($schedule);
        }
    }
}
