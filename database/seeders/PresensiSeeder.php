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
        // Presensi::query()->delete();

        $start = Carbon::create(2025, 10, 1);
        $end   = Carbon::create(2026, 2, 28);

        $users = User::whereBetween('id_user', [2, 26])->get();

        foreach ($users as $user) {

            $kategori = match (true) {
                $user->id_user <= 6  => 'full',     // sangat disiplin
                $user->id_user <= 18 => 'normal',   // normal
                default              => 'buruk',    // kurang disiplin
            };

            $tanggal = $start->copy();

            while ($tanggal <= $end) {

                if ($tanggal->isWeekend()) {
                    $tanggal->addDay();
                    continue;
                }

                // Default hadir
                $statusKehadiran = 'hadir';

                if ($kategori === 'normal') {
                    // ±1–2x sebulan izin/sakit
                    if (rand(1, 20) === 1) {
                        $statusKehadiran = rand(0, 1) ? 'izin' : 'sakit';
                    }
                }

                if ($kategori === 'buruk') {
                    // sering izin/sakit tapi TIDAK alpha
                    if (rand(1, 4) !== 1) {
                        $statusKehadiran = rand(0, 1) ? 'izin' : 'sakit';
                    }
                }

                // Jika tidak hadir
                if ($statusKehadiran !== 'hadir') {
                    Presensi::create([
                        'id_user' => $user->id_user,
                        'tanggal' => $tanggal->toDateString(),
                        'status_kehadiran' => $statusKehadiran,
                    ]);

                    $tanggal->addDay();
                    continue;
                }

                // Tentukan durasi kerja
                if ($user->role === 'dosen') {
                    $durasiJam = rand(3, 8);
                    $statusJam = $durasiJam < 4 ? 'merah'
                        : ($durasiJam < 6 ? 'kuning' : 'hijau');
                } else {
                    $durasiJam = rand(4, 9);
                    $statusJam = $durasiJam < 4 ? 'merah'
                        : ($durasiJam < 8 ? 'kuning' : 'hijau');
                }

                $jamDatang = '08:00:00';
                $jamPulang = Carbon::createFromTimeString($jamDatang)
                    ->addHours($durasiJam)
                    ->format('H:i:s');

                Presensi::create([
                    'id_user' => $user->id_user,
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

                $tanggal->addDay();
            }
        }
    }
}
