<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Presensi;
use Carbon\Carbon;

class PresensiSeeder extends Seeder
{
    public function run(): void
    {
        $start = Carbon::create(2020, 1, 1);
        $end   = Carbon::create(2026, 2, 28);

        $currentMonth = $start->copy()->startOfMonth();

        while ($currentMonth <= $end) {

            $bulanStart = $currentMonth->copy()->startOfMonth();
            $bulanEnd   = $currentMonth->copy()->endOfMonth();

            /*
            ============================
            AMBIL USER
            ============================
            */

            $dosen = User::whereBetween('id_user', [2, 20])->pluck('id_user')->toArray();
            $karyawan = User::whereBetween('id_user', [21, 25])->pluck('id_user')->toArray();

            shuffle($dosen);
            shuffle($karyawan);

            /*
            ============================
            BAGI KATEGORI BULAN INI
            ============================
            */

            $kategoriUser = [];

            // DOSEN
            foreach (array_slice($dosen, 0, 15) as $id) {
                $kategoriUser[$id] = 'full';
            }

            foreach (array_slice($dosen, 15, 3) as $id) {
                $kategoriUser[$id] = 'normal';
            }

            foreach (array_slice($dosen, 18, 2) as $id) {
                $kategoriUser[$id] = 'buruk';
            }

            // KARYAWAN
            foreach (array_slice($karyawan, 0, 3) as $id) {
                $kategoriUser[$id] = 'full';
            }

            foreach (array_slice($karyawan, 3, 1) as $id) {
                $kategoriUser[$id] = 'normal';
            }

            foreach (array_slice($karyawan, 4, 1) as $id) {
                $kategoriUser[$id] = 'buruk';
            }

            /*
            ============================
            GENERATE PRESENSI BULAN INI
            ============================
            */

            $tanggal = $bulanStart->copy();

            while ($tanggal <= $bulanEnd && $tanggal <= $end) {

                if ($tanggal->isWeekend()) {
                    $tanggal->addDay();
                    continue;
                }

                foreach ($kategoriUser as $id_user => $kategori) {

                    $user = User::find($id_user);

                    $statusKehadiran = 'hadir';

                    if ($kategori === 'normal') {
                        if (rand(1, 20) === 1) {
                            $statusKehadiran = rand(0, 1) ? 'izin' : 'sakit';
                        }
                    }

                    if ($kategori === 'buruk') {
                        if (rand(1, 4) !== 1) {
                            $statusKehadiran = rand(0, 1) ? 'izin' : 'sakit';
                        }
                    }

                    if ($statusKehadiran !== 'hadir') {

                        Presensi::create([
                            'id_user' => $id_user,
                            'tanggal' => $tanggal->toDateString(),
                            'status_kehadiran' => $statusKehadiran,
                        ]);

                        continue;
                    }

                    /*
                    ============================
                    STANDAR JAM
                    ============================
                    */

                    $standarJam = $user->role === 'dosen' ? 6 : 8;

                    /*
                    ============================
                    DURASI
                    ============================
                    */

                    if ($kategori === 'full') {

                        $durasiJam = rand($standarJam, $standarJam + 1);
                    } elseif ($kategori === 'normal') {

                        $durasiJam = rand($standarJam - 1, $standarJam);
                    } else {

                        $durasiJam = rand(2, $standarJam - 2);
                    }

                    /*
                    ============================
                    STATUS JAM
                    ============================
                    */

                    if ($durasiJam < ($standarJam * 0.5)) {
                        $statusJam = 'merah';
                    } elseif ($durasiJam < $standarJam) {
                        $statusJam = 'kuning';
                    } else {
                        $statusJam = 'hijau';
                    }

                    $jamDatang = '08:00:00';

                    $jamPulang = Carbon::createFromTimeString($jamDatang)
                        ->addHours($durasiJam)
                        ->format('H:i:s');

                    Presensi::create([
                        'id_user' => $id_user,
                        'tanggal' => $tanggal->toDateString(),

                        'jam_datang' => $jamDatang,
                        'jam_pulang' => $jamPulang,
                        'durasi_menit' => $durasiJam * 60,

                        'lat_datang' => -7.8229397,
                        'long_datang' => 110.3728715,
                        'lat_pulang' => -7.8229397,
                        'long_pulang' => 110.3728715,
                        'jarak_datang' => 0,
                        'jarak_pulang' => 0,

                        'status_lokasi_datang' => 'didalam_radius',
                        'status_lokasi_pulang' => 'didalam_radius',

                        'status_jam_kerja' => $statusJam,
                        'status_kehadiran' => 'hadir',
                    ]);
                }

                $tanggal->addDay();
            }

            $currentMonth->addMonth();
        }
    }
}
