<?php

namespace App\Services\Kmeans;

class KmeansIterasiService
{

    public function prosesIterasi($dataset, $centroid, $iterasi = 1)
    {

        $hasil = [];

        foreach ($dataset as $data) {

            $d1 = $this->euclidean($data, $centroid[0]);
            $d2 = $this->euclidean($data, $centroid[1]);
            $d3 = $this->euclidean($data, $centroid[2]);

            $cluster = $this->tentukanCluster($d1, $d2, $d3);

            $hasil[] = [

                'name' => $data['name'] ?? '',
                'npp' => $data['npp'] ?? '',

                'x1_norm' => $data['x1_norm'],
                'x2_norm' => $data['x2_norm'],

                'd1' => $d1,
                'd2' => $d2,
                'd3' => $d3,

                'cluster' => $cluster

            ];
        }

        $jumlah = $this->jumlahCluster($hasil);

        $centroidBaru = $this->updateCentroid($hasil, $centroid);

        return [

            'iterasi' => $iterasi,
            'centroid_lama' => $centroid,
            'perhitungan' => $hasil,
            'jumlah_cluster' => $jumlah,
            'centroid_baru' => $centroidBaru

        ];
    }


    private function euclidean($data, $centroid)
    {

        return sqrt(

            pow($data['x1_norm'] - $centroid['x1_norm'], 2) +
                pow($data['x2_norm'] - $centroid['x2_norm'], 2)

        );
    }


    private function tentukanCluster($d1, $d2, $d3)
    {

        $distances = [
            'C1' => $d1,
            'C2' => $d2,
            'C3' => $d3
        ];

        asort($distances);

        return array_key_first($distances);
    }


    private function jumlahCluster($data)
    {

        $jumlah = ['C1' => 0, 'C2' => 0, 'C3' => 0];

        foreach ($data as $d) {

            $jumlah[$d['cluster']]++;
        }

        return $jumlah;
    }


    private function updateCentroid($data, $centroidLama)
    {

        $cluster = [

            'C1' => [],
            'C2' => [],
            'C3' => []

        ];

        foreach ($data as $d) {

            $cluster[$d['cluster']][] = $d;
        }

        $centroid = [];

        foreach ($cluster as $key => $items) {

            if (count($items) == 0) {

                $index = array_search($key, array_column($centroidLama, 'cluster'));

                $centroid[] = $centroidLama[$index];

                continue;
            }

            $x1 = array_sum(array_column($items, 'x1_norm')) / count($items);
            $x2 = array_sum(array_column($items, 'x2_norm')) / count($items);

            $centroid[] = [

                'cluster' => $key,
                'x1_norm' => round($x1, 6),
                'x2_norm' => round($x2, 6)

            ];
        }

        return $centroid;
    }
}
