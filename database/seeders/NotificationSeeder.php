<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        // Create sample notifications
        $notifications = [
            [
                'title' => 'Rapat Mingguan Telah Dijadwalkan',
                'message' => 'Rapat mingguan tim telah dijadwalkan pada Senin, 30 September 2025 pukul 09:00.',
                'type' => 'meeting'
            ],
            [
                'title' => 'Permintaan Persetujuan Logistik',
                'message' => 'Terdapat permintaan persetujuan untuk pengajuan logistik rapat yang perlu Anda tinjau.',
                'type' => 'approval'
            ],
            [
                'title' => 'Pengingat: Rapat Dimulai 1 Jam Lagi',
                'message' => 'Rapat evaluasi bulanan akan dimulai dalam 1 jam. Pastikan Anda sudah mempersiapkan materi presentasi.',
                'type' => 'reminder'
            ],
            [
                'title' => 'Logistik Rapat Telah Disetujui',
                'message' => 'Pengajuan logistik untuk rapat tanggal 2 Oktober telah disetujui dan akan segera disiapkan.',
                'type' => 'logistics'
            ],
            [
                'title' => 'Sistem Maintenance Selesai',
                'message' => 'Maintenance sistem E-Notulen telah selesai dilakukan. Semua fitur kini dapat digunakan normal.',
                'type' => 'system'
            ],
            [
                'title' => 'Notulensi Rapat Tersedia',
                'message' => 'Notulensi rapat evaluasi project ABC telah selesai dibuat dan siap untuk ditinjau.',
                'type' => 'meeting'
            ]
        ];

        // Get all users
        $users = User::all();

        foreach ($notifications as $notificationData) {
            // Create notification
            $notification = Notification::create($notificationData);

            // Assign to all users with random read status
            foreach ($users as $user) {
                UserNotification::create([
                    'notification_id' => $notification->id,
                    'user_id' => $user->id,
                    'is_read' => fake()->boolean(30), // 30% chance of being read
                    'read_at' => fake()->boolean(30) ? now()->subDays(rand(0, 5)) : null,
                ]);
            }
        }
    }
}