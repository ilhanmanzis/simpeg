<?php

namespace App\Services\Kmeans;

class CentroidService
{

    public function centroidAwal($dataset)
    {

        $x1 = array_column($dataset, 'x1');
        $x2 = array_column($dataset, 'x2');

        $minX1 = min($x1);
        $maxX1 = max($x1);
        $meanX1 = array_sum($x1) / count($x1);

        $minX2 = min($x2);
        $maxX2 = max($x2);
        $meanX2 = array_sum($x2) / count($x2);

        return [

            [
                'cluster' => 'C1',
                'x1' => $maxX1,
                'x2' => $maxX2
            ],

            [
                'cluster' => 'C2',
                'x1' => $meanX1,
                'x2' => $meanX2
            ],

            [
                'cluster' => 'C3',
                'x1' => $minX1,
                'x2' => $minX2
            ]

        ];
    }


    public function normalisasiCentroid($centroid, $dataset)
    {

        if (empty($dataset) || empty($centroid)) {
            return [];
        }

        $x1 = array_column($dataset, 'x1');
        $x2 = array_column($dataset, 'x2');

        $minX1 = min($x1);
        $maxX1 = max($x1);

        $minX2 = min($x2);
        $maxX2 = max($x2);

        $result = [];

        foreach ($centroid as $c) {

            $x1_norm = ($maxX1 - $minX1) == 0
                ? 0
                : ($c['x1'] - $minX1) / ($maxX1 - $minX1);

            $x2_norm = ($maxX2 - $minX2) == 0
                ? 0
                : ($c['x2'] - $minX2) / ($maxX2 - $minX2);

            $result[] = [

                'cluster' => $c['cluster'],
                'x1_norm' => $x1_norm,
                'x2_norm' => $x2_norm

            ];
        }

        return $result;
    }
}
