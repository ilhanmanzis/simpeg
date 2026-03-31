<?php

namespace App\Services\Kmeans;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;


class KedisiplinanService
{
    protected $datasetBulanan;
    protected $normalisasi;
    protected $centroidService;
    protected $resultService;

    public function __construct(
        DatasetBulananService $datasetBulanan,
        NormalisasiService $normalisasi,
        CentroidService $centroidService,
        KmeansResultService $resultService
    ) {
        $this->datasetBulanan = $datasetBulanan;
        $this->normalisasi = $normalisasi;
        $this->centroidService = $centroidService;
        $this->resultService = $resultService;
    }

    public function getKedisiplinanUser($bulan, $tahun)
    {
        return Cache::remember(
            "kmeans_presensi_{$bulan}_{$tahun}",
            now()->addHours(6),
            function () use ($bulan, $tahun) {
                $request = new Request([
                    'bulan' => $bulan,
                    'tahun' => $tahun
                ]);

                // =============================
                // DATASET
                // =============================
                $dataset = $this->datasetBulanan->getDataset($request);

                // K-means minimal butuh 3 data (cluster = 3)
                if (empty($dataset) || count($dataset) < 3) {
                    return null;
                }

                // =============================
                // NORMALISASI
                // =============================
                $datasetNormal = $this->normalisasi->minMax($dataset, 'x1');
                $datasetNormal = $this->normalisasi->minMax($datasetNormal, 'x2');

                // =============================
                // CENTROID
                // =============================
                $centroidNormal = $this->centroidService->centroidAwal($datasetNormal);

                // =============================
                // PROSES K-MEANS
                // =============================
                $hasil = $this->resultService->prosesOtomatis($datasetNormal, $centroidNormal);

                return [
                    'dataset'  => $dataset,
                    'clusters' => $hasil['hasil_cluster']
                ];
            }
        );
    }



    public function mappingCluster($cluster)
    {
        return match ($cluster) {

            'C1' => [
                'label' => 'Tinggi',
                'color' => 'text-green-600'
            ],

            'C2' => [
                'label' => 'Sedang',
                'color' => 'text-orange-500'
            ],

            'C3' => [
                'label' => 'Rendah',
                'color' => 'text-red-600'
            ],

            default => [
                'label' => '-',
                'color' => 'text-gray-500'
            ]
        };
    }

    public function getClusterUser($bulan, $tahun, $userId)
    {
        $data = $this->getKedisiplinanUser($bulan, $tahun);

        if (!$data) {
            return null;
        }

        foreach ($data['dataset'] as $index => $row) {

            if ($row['id_user'] == $userId) {
                return $data['clusters'][$index]['cluster'];
            }
        }

        return null;
    }
}
