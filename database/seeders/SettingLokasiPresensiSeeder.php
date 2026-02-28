<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SettingLokasiPresensi;

class SettingLokasiPresensiSeeder extends Seeder
{
    public function run(): void
    {
        SettingLokasiPresensi::truncate();

        SettingLokasiPresensi::create([
            'latitude' => -7.8229397,
            'longitude' => 110.3728715,
            'radius_meter' => 50,
        ]);
    }
}
