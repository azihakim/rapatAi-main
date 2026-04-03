<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {        
        Role::create([
            'name' => 'sekretariat',
            'description' => 'Sekretariat',
        ]);

        Role::create([
            'name' => 'pimpinan',
            'description' => 'Pimpinan',
        ]);

        Role::create([
            'name' => 'anggota',
            'description' => 'Anggota',
        ]);

    }
}
