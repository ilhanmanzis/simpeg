<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class LocationService
{
    /**
     * Hitung jarak (meter) antara dua koordinat
     */
    public function hitungJarak(
        float $lat1,
        float $lon1,
        float $lat2,
        float $lon2
    ): float {
        $earthRadius = 6371000; // meter

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Ambil alamat dari koordinat menggunakan OSM (Nominatim)
     */
    public function getAlamatDariKoordinatOSM(
        float $lat,
        float $lng
    ): ?string {
        $response = Http::withHeaders([
            'User-Agent' => 'Presensi-App/1.0 (janggarfals1207@gmail.com)',
        ])->get('https://nominatim.openstreetmap.org/reverse', [
            'format' => 'json',
            'lat'    => $lat,
            'lon'    => $lng,
            'zoom'   => 18,
            'addressdetails' => 1,
        ]);

        if (!$response->successful()) {
            return null;
        }

        $data = $response->json();

        return $data['display_name'] ?? null;
    }
}
