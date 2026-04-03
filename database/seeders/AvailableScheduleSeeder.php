<?php

namespace Database\Seeders;

use App\Models\KetersediaanPribadi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AvailableScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [3, 4, 5];
        $dates = [
            '2025-09-08',
            '2025-09-09',
            '2025-09-10',
            '2025-09-11',
            '2025-09-12',
            '2025-09-13',
        ];
        $workHours = [
            ['08:00', '10:00'],
            ['10:00', '12:00'],
            ['13:00', '15:00'],
            ['15:00', '17:00'],
        ];
        
        $meetingDate = '2025-09-10';
        $meetingSlot = ['10:00', '12:00'];
        
        foreach ($users as $user) {
            KetersediaanPribadi::create([
                'user_id' => $user,
                'tanggal' => $meetingDate,
                'waktu_mulai' => $meetingSlot[0],
                'waktu_selesai' => $meetingSlot[1],
            ]);
        }
        
        foreach ($users as $user) {
            foreach ($dates as $date) {                
                if ($date === $meetingDate) {
                    continue;
                }                
                $slot = $workHours[array_rand($workHours)];
                KetersediaanPribadi::create([
                    'user_id' => $user,
                    'tanggal' => $date,
                    'waktu_mulai' => $slot[0],
                    'waktu_selesai' => $slot[1],
                ]);
            }
        }
    }
}
