<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['nama_unit' => 'Sekretariat', 'is_active' => true],
            ['nama_unit' => 'Bidang Penanaman Modal', 'is_active' => true],
            ['nama_unit' => 'Bidang Perizinan', 'is_active' => true],
        ];

        foreach ($units as $unit) {
            Unit::create($unit);
        }
    }
}
