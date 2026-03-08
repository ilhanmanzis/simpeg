<?php

namespace App\Services\Kmeans;

class KmeansResultService
{

    protected $iterasiService;

    public function __construct(KmeansIterasiService $iterasiService)
    {
        $this->iterasiService = $iterasiService;
    }


    public function prosesOtomatis($dataset, $centroid)
    {

        $iterasi = 1;
        $maxIterasi = 20;

        $semuaIterasi = [];

        while (true) {

            $hasil = $this->iterasiService->prosesIterasi($dataset, $centroid, $iterasi);

            $semuaIterasi[] = $hasil;

            $centroidBaru = $hasil['centroid_baru'];

            // cek konvergen
            if ($this->isKonvergen($centroid, $centroidBaru)) {
                $centroid = $centroidBaru;
                break;
            }

            $centroid = $centroidBaru;

            $iterasi++;

            if ($iterasi > $maxIterasi) {
                break;
            }
        }

        return [

            'total_iterasi' => count($semuaIterasi),
            'iterasi' => $semuaIterasi,
            'centroid_final' => $centroid,
            'hasil_cluster' => end($semuaIterasi)['perhitungan']

        ];
    }


    private function isKonvergen($lama, $baru)
    {

        foreach ($lama as $i => $c) {

            if (

                round($c['x1_norm'], 4) != round($baru[$i]['x1_norm'], 4) ||
                round($c['x2_norm'], 4) != round($baru[$i]['x2_norm'], 4)

            ) {

                return false;
            }
        }

        return true;
    }
}
