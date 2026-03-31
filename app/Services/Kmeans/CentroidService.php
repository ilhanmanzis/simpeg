<?php

namespace App\Services\Kmeans;

class CentroidService
{
    public function centroidAwal($dataset)
    {
        // ambil dari data yang SUDAH DINORMALISASI
        $x1 = array_column($dataset, 'x1_norm');
        $x2 = array_column($dataset, 'x2_norm');

        $minX1 = min($x1);
        $maxX1 = max($x1);
        $meanX1 = array_sum($x1) / count($x1);

        $minX2 = min($x2);
        $maxX2 = max($x2);
        $meanX2 = array_sum($x2) / count($x2);

        return [
            [
                'cluster' => 'C1',
                'x1_norm' => $maxX1,
                'x2_norm' => $maxX2
            ],
            [
                'cluster' => 'C2',
                'x1_norm' => $meanX1,
                'x2_norm' => $meanX2
            ],
            [
                'cluster' => 'C3',
                'x1_norm' => $minX1,
                'x2_norm' => $minX2
            ]
        ];
    }
}
