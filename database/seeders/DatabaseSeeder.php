<?php

namespace Database\Seeders;

use App\Models\Jenjangs;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create();
        $jenjangs = [
            [
                'nama_jenjang' => 'S3',
            ],
            [
                'nama_jenjang' => 'S2',
            ],
            [
                'nama_jenjang' => 'S1',
            ],
            [
                'nama_jenjang' => 'D4',
            ],
            [
                'nama_jenjang' => 'D3',
            ],
            [
                'nama_jenjang' => 'D2',
            ],
            [
                'nama_jenjang' => 'D1',
            ],
            [
                'nama_jenjang' => 'SMK',
            ],
            [
                'nama_jenjang' => 'MA',
            ],
            [
                'nama_jenjang' => 'SMA',
            ],
            [
                'nama_jenjang' => 'SMP',
            ],
            [
                'nama_jenjang' => 'MTS',
            ],
            [
                'nama_jenjang' => 'SD',
            ],
            [
                'nama_jenjang' => 'MI',
            ],
        ];

        foreach ($jenjangs as $jenjang) {
            Jenjangs::factory()->create($jenjang);
        }
    }
}
