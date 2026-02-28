<?php

namespace App\Services;

use App\Models\Penelitians;
use App\Models\Pengajarans;
use App\Models\Pengabdians;
use App\Models\Penunjangs;
use App\Models\Pendidikans;
use App\Models\FungsionalUsers;
use Illuminate\Support\Facades\Cache;

class SerdosService
{
    public function check(int $userId): bool
    {
        $cacheKey = "serdos_status_user_{$userId}";

        return Cache::remember($cacheKey, now()->addHours(24), function () use ($userId) {

            // ================= BKD =================
            $penelitian = Penelitians::where('id_user', $userId)->count();
            $pengajaran = Pengajarans::where('id_user', $userId)->count();
            $pengabdian = Pengabdians::where('id_user', $userId)->count();
            $penunjang  = Penunjangs::where('id_user', $userId)->count();

            $memenuhiBKD =
                $penelitian >= 4 &&
                $pengajaran >= 4 &&
                $pengabdian >= 4 &&
                $penunjang  >= 4;

            // ================= Pendidikan =================
            $punyaS2AtauS3 = Pendidikans::where('id_user', $userId)
                ->whereHas('jenjang', function ($q) {
                    $q->whereIn('id_jenjang', [1, 2]);
                })
                ->exists();

            // ================= Fungsional =================
            $punyaFungsionalAktif = FungsionalUsers::where('id_user', $userId)
                ->where('status', 'aktif')
                ->exists();

            return $punyaS2AtauS3 &&
                $punyaFungsionalAktif &&
                $memenuhiBKD;
        });
    }

    /**
     * Optional: hapus cache manual
     */
    public function clearCache(int $userId): void
    {
        Cache::forget("serdos_status_user_{$userId}");
    }
}
