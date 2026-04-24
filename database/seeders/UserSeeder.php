<?php

namespace Database\Seeders;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Buat user admin (agendaris)
        $admin = User::create([
            'name' => 'Admin Agendaris',
            'username' => 'admin',
            'email' => 'ichsanmuhammed01@gmail.com',
            'password' => Hash::make('password'),
            'no_wa' => '08123456789',
            'jabatan_struktural' => 'Agendaris',
            'is_active' => true,
        ]);

        // Assign ke unit Sekretariat sebagai agendaris
        $sekretariat = Unit::where('nama_unit', 'Sekretariat')->first();

        if ($sekretariat) {
            $admin->units()->attach($sekretariat->id, [
                'peran' => 'agendaris',
                'is_primary' => true,
            ]);
        }
    }
}
