<?php

namespace App\Observers;

use App\Models\Presensi;
use Illuminate\Support\Facades\Cache;

class PresensiObserver
{
    /**
     * Handle the Presensi "created" event.
     */
    public function created(Presensi $presensi): void
    {
        $this->clearCache($presensi);
    }

    /**
     * Handle the Presensi "updated" event.
     */
    public function updated(Presensi $presensi): void
    {
        // hanya clear cache jika field penting berubah
        if ($presensi->wasChanged([
            'status_kehadiran',
            'jam_datang',
            'jam_pulang',
            'durasi_menit'
        ])) {

            $this->clearCache($presensi);
        }
    }

    /**
     * Clear cache jika dataset berubah
     */
    private function clearCache($presensi)
    {
        $bulan = \Carbon\Carbon::parse($presensi->tanggal)->month;
        $tahun = \Carbon\Carbon::parse($presensi->tanggal)->year;

        $cacheKey = "kmeans_presensi_{$bulan}_{$tahun}";
        $dashboardKey = "dashboard_presensi_{$bulan}_{$tahun}";


        // jika sakit / izin
        if (in_array($presensi->status_kehadiran, ['sakit', 'izin'])) {
            Cache::forget($cacheKey);
            Cache::forget($dashboardKey);
            return;
        }

        // jika hadir harus lengkap
        if (
            $presensi->status_kehadiran === 'hadir' &&
            !is_null($presensi->jam_datang) &&
            !is_null($presensi->jam_pulang) &&
            !is_null($presensi->durasi_menit)
        ) {
            Cache::forget($cacheKey);
            Cache::forget($dashboardKey);
        }
    }
}
