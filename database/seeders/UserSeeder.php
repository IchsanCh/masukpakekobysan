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
        $sekretariat = Unit::where('nama_unit', 'Sekretariat')->first();

        // Buat user admin (agendaris) di unit Sekretariat
        User::create([
            'name' => 'Admin Agendaris',
            'username' => 'admin',
            'email' => 'ichsanmuhammed01@gmail.com',
            'password' => Hash::make('password'),
            'no_wa' => '08123456789',
            'unit_id' => $sekretariat?->id,
            'peran' => 'agendaris',
            'is_active' => true,
        ]);

        // Buat user pimpinan di unit yang sama, supaya agendaris di atas
        // punya pimpinan yang bisa diwakili saat membuat/meneruskan disposisi.
        User::create([
            'name' => 'Kepala Dinas',
            'username' => 'pimpinan',
            'email' => 'pimpinan@example.com',
            'password' => Hash::make('password'),
            'no_wa' => '08123456780',
            'unit_id' => $sekretariat?->id,
            'peran' => 'pimpinan',
            'is_active' => true,
        ]);
    }
}