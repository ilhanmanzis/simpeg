<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\StrukturalUsers;
use App\Models\User;

class PresensiService
{
    /*
    |--------------------------------------------------------------------------
    | HITUNG DURASI (UNIVERSAL)
    |--------------------------------------------------------------------------
    */
    public function hitungDurasi(Carbon $jamDatang, Carbon $jamPulang): array
    {
        // Jika pulang lewat tengah malam
        if ($jamPulang->lessThan($jamDatang)) {
            $jamPulang->addDay();
        }

        $durasiMenit = $jamDatang->diffInMinutes($jamPulang);
        $durasiJam   = intdiv($durasiMenit, 60);

        return [
            'durasi_menit' => $durasiMenit,
            'durasi_jam'   => $durasiJam,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | JAM WAJIB BERDASARKAN ROLE & STRUKTURAL
    |--------------------------------------------------------------------------
    */
    public function getJamWajib(User $user, string $tanggal): int
    {
        if ($user->role !== 'dosen') {
            return 8;
        }

        return $this->cekStrukturalAktif($user->id_user, $tanggal)
            ? 7
            : 6;
    }

    /*
    |--------------------------------------------------------------------------
    | STATUS JAM KERJA
    |--------------------------------------------------------------------------
    */
    public function getStatusJamKerja(int $durasiJam, int $jamWajib): string
    {
        if ($durasiJam >= $jamWajib) {
            return 'hijau';
        }

        if ($durasiJam >= 4) {
            return 'kuning';
        }

        return 'merah';
    }

    /*
    |--------------------------------------------------------------------------
    | CEK STRUKTURAL AKTIF
    |--------------------------------------------------------------------------
    */
    public function cekStrukturalAktif(int $userId, string $tanggal): bool
    {
        return StrukturalUsers::where('id_user', $userId)
            ->where('status', 'aktif')
            ->whereDate('tanggal_mulai', '<=', $tanggal)
            ->where(function ($q) use ($tanggal) {
                $q->whereNull('tanggal_selesai')
                    ->orWhereDate('tanggal_selesai', '>=', $tanggal);
            })
            ->exists();
    }

    /*
    |--------------------------------------------------------------------------
    | FORMAT DURASI UNTUK VIEW
    |--------------------------------------------------------------------------
    */
    public function formatDurasi(?int $durasiMenit): string
    {
        if (is_null($durasiMenit)) {
            return '00:00:00';
        }

        $jam   = intdiv($durasiMenit, 60);
        $menit = $durasiMenit % 60;

        return sprintf('%02d:%02d:00', $jam, $menit);
    }
}
