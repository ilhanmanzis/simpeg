<?php

namespace Database\Seeders;

use App\Models\Golongans;
use App\Models\JabatanFungsionals;
use App\Models\JabatanStrukturals;
use App\Models\Jenjangs;
use App\Models\KategoriSertifikats;
use App\Models\Semesters;
use App\Models\Settings;
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
        // 
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

        // golongan
        $golongans = [
            ['nama_golongan' => 'I/A'],
            ['nama_golongan' => 'I/B'],
            ['nama_golongan' => 'I/C'],
            ['nama_golongan' => 'I/D'],
            ['nama_golongan' => 'II/A'],
            ['nama_golongan' => 'II/B'],
            ['nama_golongan' => 'II/C'],
            ['nama_golongan' => 'II/D'],
            ['nama_golongan' => 'III/A'],
            ['nama_golongan' => 'III/B'],
            ['nama_golongan' => 'III/C'],
            ['nama_golongan' => 'III/D'],
            ['nama_golongan' => 'IV/A'],
            ['nama_golongan' => 'IV/B'],
            ['nama_golongan' => 'IV/C'],
            ['nama_golongan' => 'IV/D'],
        ];

        foreach ($golongans as $golongan) {
            Golongans::factory()->create($golongan);
        }

        // jabatan fungsional
        $fungsionals = [
            [
                'nama_jabatan' => 'Asisten Ahli',
                'id_golongan' => 10 // III/b = urutan ke-10
            ],
            [
                'nama_jabatan' => 'Lektor',
                'id_golongan' => 9 // III/A
            ],

            [
                'nama_jabatan' => 'Lektor Kepala',
                'id_golongan' => 13 // IV/a
            ],

            [
                'nama_jabatan' => 'Guru Besar',
                'id_golongan' => 15 // IV/c
            ],

        ];

        foreach ($fungsionals as $fungsional) {
            JabatanFungsionals::factory()->create($fungsional);
        }

        // jabatan struktural
        $strukturals = [
            ['nama_jabatan' => 'Ketua'],
            ['nama_jabatan' => 'Wakil Ketua'],
            ['nama_jabatan' => 'Kaprodi Informatika'],
            ['nama_jabatan' => 'Kaprodi Sistem Informasi'],
        ];
        foreach ($strukturals as $struktural) {
            JabatanStrukturals::factory()->create($struktural);
        }


        // semester
        $semesters = [
            [
                'nama_semester' => 'TA 2024/2025 Ganjil'
            ],
            [
                'nama_semester' => 'TA 2024/2025 Genap'
            ],
            [
                'nama_semester' => 'TA 2025/2026 Ganjil'
            ],
            [
                'nama_semester' => 'TA 2025/2026 Genap'
            ],
            [
                'nama_semester' => 'TA 2026/2027 Ganjil'
            ],
            [
                'nama_semester' => 'TA 2026/2027 Genap'
            ],
        ];

        foreach ($semesters as $semester) {
            Semesters::factory()->create($semester);
        }

        Settings::factory()->create([
            'name' => 'Sistem Kepegawaian El-Rahma',
            'logo' => 'logo.webp',
            'register' => 'aktif'
        ]);


        // kategori sertifikat
        $categories = [
            [
                'name' => 'Sertifikat Kompetensi'
            ],


        ];

        foreach ($categories as $category) {
            KategoriSertifikats::factory()->create($category);
        }
    }
}
