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
        $end   = Carbon::create(2026, 4, 28);

        $currentMonth = $start->copy()->startOfMonth();

        while ($currentMonth <= $end) {

            $bulanStart = $currentMonth->copy()->startOfMonth();
            $bulanEnd   = $currentMonth->copy()->endOfMonth();

            /*
            ============================
            AMBIL USER SEKALI
            ============================
            */

            $users = User::whereBetween('id_user', [2, 26])
                ->get()
                ->keyBy('id_user');

            /*
            ============================
            BAGI KATEGORI BULAN
            ============================
            */

            $kategoriUser = [];

            $selaluHadir = [2, 3, 4, 5, 6, 7, 8, 9, 10, 22, 23];

            foreach ($users as $id => $user) {

                if (in_array($id, $selaluHadir)) {

                    $kategoriUser[$id] = 'full';
                } else {

                    // random tiap bulan
                    $kategoriUser[$id] = rand(0, 1) ? 'normal' : 'buruk';
                }
            }

            /*
            ============================
            GENERATE PRESENSI
            ============================
            */

            $tanggal = $bulanStart->copy();

            while ($tanggal <= $bulanEnd && $tanggal <= $end) {

                if ($tanggal->isWeekend()) {
                    $tanggal->addDay();
                    continue;
                }

                foreach ($kategoriUser as $id_user => $kategori) {

                    $user = $users[$id_user];

                    /*
                    ============================
                    STATUS KEHADIRAN
                    ============================
                    */

                    $statusKehadiran = 'hadir';

                    if ($kategori === 'normal') {

                        if (rand(1, 5) === 1) {
                            $statusKehadiran = rand(0, 1) ? 'izin' : 'sakit';
                        }
                    }

                    if ($kategori === 'buruk') {

                        if (rand(1, 2) === 1) {
                            $statusKehadiran = rand(0, 1) ? 'izin' : 'sakit';
                        }
                    }

                    /*
                    ============================
                    JIKA IZIN / SAKIT
                    ============================
                    */

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
                    DURASI KERJA
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

                    /*
                    ============================
                    JAM DATANG
                    ============================
                    */

                    if (rand(1, 10) <= 2) {

                        $jamDatang = Carbon::createFromTime(9, rand(0, 20), 0);
                    } else {

                        $jamDatang = Carbon::createFromTime(8, rand(0, 20), 0);
                    }

                    $jamPulang = $jamDatang->copy()
                        ->addHours($durasiJam)
                        ->format('H:i:s');

                    /*
                    ============================
                    SIMPAN DATA
                    ============================
                    */

                    Presensi::create([

                        'id_user' => $id_user,
                        'tanggal' => $tanggal->toDateString(),

                        'jam_datang' => $jamDatang->format('H:i:s'),
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
