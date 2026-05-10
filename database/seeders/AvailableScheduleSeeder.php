<?php

namespace Database\Seeders;

use App\Models\KetersediaanPribadi;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AvailableScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('role_id', 3)->pluck('id')->values();
        if ($users->isEmpty()) {
            return;
        }

        // Seed meeting date for AI integration testing
        $meetingDate = '2026-05-10';
        // Variety of slots to exercise AI recommendation logic (full-day, overlapping, and distinct slots)
        $meetingSlots = [
            ['full_day' => true],
            ['start' => '08:00', 'end' => '10:00'],
            ['start' => '09:00', 'end' => '11:00'],
            ['start' => '13:00', 'end' => '15:00'],
            ['start' => '14:00', 'end' => '16:00'],
        ];

        foreach ($users as $index => $userId) {
            $slot = $meetingSlots[$index % count($meetingSlots)];
            $fullDay = $slot['full_day'] ?? false;
            $start = $fullDay ? '00:00' : $slot['start'];
            $end = $fullDay ? '23:59' : $slot['end'];

            KetersediaanPribadi::create([
                'user_id' => $userId,
                'tanggal' => $meetingDate,
                'waktu_mulai' => $start,
                'waktu_selesai' => $end,
                'full_day' => $fullDay,
            ]);
        }

        // Additional nearby dates to provide context availability
        $extraDates = [
            '2026-05-08',
            '2026-05-09',
            '2026-05-11',
            '2026-05-12',
        ];
        $extraSlots = [
            ['08:00', '09:30'],
            ['13:00', '15:00'],
            ['15:00', '17:00'],
        ];

        foreach ($users as $index => $userId) {
            foreach ($extraDates as $dateIndex => $date) {
                $slot = $extraSlots[($index + $dateIndex) % count($extraSlots)];
                $fullDay = ($index === 0 && $date === '2025-09-09');
                $start = $fullDay ? '00:00' : $slot[0];
                $end = $fullDay ? '23:59' : $slot[1];

                KetersediaanPribadi::create([
                    'user_id' => $userId,
                    'tanggal' => $date,
                    'waktu_mulai' => $start,
                    'waktu_selesai' => $end,
                    'full_day' => $fullDay,
                ]);
            }
        }
    }
}
