<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        User::create([
            'name' => 'Admin Sekretariat',
            'email' => 'sekretariat@example.com',
            'password' => Hash::make('password'),
            'jabatan' => 'Sekretariat',
            'komisi' => 'Sekretariat',
            'nip' => '1234567890',
            'role_id' => 1
        ]);


        User::create([
            'name' => 'Pimpinan',
            'email' => 'pimpinan@example.com',
            'password' => Hash::make('password'),
            'jabatan' => 'Pimpinan',
            'komisi' => 'Pimpinan',
            'nip' => '0987654321',
            'role_id' => 2
        ]);


        User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi.santoso@example.com',
            'password' => Hash::make('password'),
            'jabatan' => 'Pimpinan Badan Musyawarah DPRD Provinsi SumSel.',
            'komisi' => 'Badan Musyawarah',
            'nip' => '1122334455',
            'role_id' => 3
        ]);


        User::create([
            'name' => 'Siti Aminah',
            'email' => 'siti.aminah@example.com',
            'password' => Hash::make('password'),
            'jabatan' => 'Anggota Badan Musyawarah DPRD Provinsi SumSel.',
            'komisi' => 'Badan Musyawarah',
            'nip' => '2233445566',
            'role_id' => 3
        ]);
        User::create([
            'name' => 'Andi Wijaya',
            'email' => 'andi.wijaya@example.com',
            'password' => Hash::make('password'),
            'jabatan' => 'Sekretaris Daerah Provinsi Sumsel.',
            'komisi' => 'Sekretariat',
            'nip' => '3344556677',            
            'role_id' => 3
        ]);
    }
}
