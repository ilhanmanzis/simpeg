<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\DataDiri;

class UserDataDiriSeeder extends Seeder
{
    public function run(): void
    {
        $total = 25;
        $nppAwal = 2001001;

        for ($i = 1; $i <= $total; $i++) {

            // Tentukan role
            $role = $i <= 20 ? 'dosen' : 'karyawan';

            // Nama & Email
            $nama = 'Pegawai ' . chr(64 + $i);
            $email = strtolower(str_replace(' ', '', $nama)) . '@gmail.com';

            // Buat User
            $user = User::create([
                'npp' => $nppAwal++,
                'email' => $email,
                'password' => Hash::make('pegawai'),
                'status_keaktifan' => 'aktif',
                'role' => $role,
            ]);

            // Buat Data Diri
            DataDiri::create([
                'id_user' => $user->id_user,
                'name' => $nama,
                'no_ktp' => fake()->numerify('################'),
                'no_hp' => null,
                'tempat_lahir' => 'Tegal',
                'tanggal_lahir' => '2003-07-12',
                'jenis_kelamin' => fake()->randomElement(['Laki-Laki', 'Perempuan']),
                'agama' => 'Islam',
                'tanggal_bergabung' => '2020-01-01',
                'alamat' => null,
                'rt' => null,
                'rw' => null,
                'desa' => null,
                'kecamatan' => null,
                'kabupaten' => null,
                'provinsi' => null,
                'foto' => null,
                'tersertifikasi' => 'tidak',
                'serdos' => null,
                'pimpinan' => 'nonaktif',
                'bpjs' => null,
                'anak' => 0,
                'istri' => 0,
                'golongan_darah' => '-',
            ]);
        }
    }
}
