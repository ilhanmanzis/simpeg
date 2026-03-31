<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\Kmeans\DatasetBulananService;
use App\Services\Kmeans\DatasetTahunanService;
use App\Services\Kmeans\NormalisasiService;
use App\Services\Kmeans\KmeansIterasiService;
use App\Services\Kmeans\KmeansResultService;
use App\Services\Kmeans\CentroidService;

class KlasterisasiController extends Controller
{

    protected $datasetBulanan;
    protected $datasetTahunan;
    protected $normalisasi;
    protected $iterasiService;
    protected $resultService;
    protected $centroidService;

    public function __construct(
        DatasetBulananService $datasetBulanan,
        DatasetTahunanService $datasetTahunan,
        NormalisasiService $normalisasi,
        KmeansIterasiService $iterasiService,
        KmeansResultService $resultService,
        CentroidService $centroidService
    ) {
        $this->datasetBulanan = $datasetBulanan;
        $this->datasetTahunan = $datasetTahunan;
        $this->normalisasi = $normalisasi;
        $this->iterasiService = $iterasiService;
        $this->resultService = $resultService;
        $this->centroidService = $centroidService;
    }


    /**
     * Halaman utama
     */
    public function index()
    {
        return view('dosen.pimpinan.klasterisasi.index', [
            'title' => 'Klasterisasi Presensi Pegawai',
            'page' => 'Daftar Presensi Pegawai',
            'selected' => 'Daftar Presensi Pegawai'
        ]);
    }


    /**
     * ==============================
     * PROSES ITERASI MANUAL
     * ==============================
     */
    public function proses(Request $request)
    {

        $periode = $request->mode;

        /*
        |--------------------------------------------------------------------------
        | DATASET
        |--------------------------------------------------------------------------
        */

        if ($periode == 'bulan') {

            $dataset = $this->datasetBulanan->getDataset($request);
        } else {

            $dataset = $this->datasetTahunan->getDataset($request);
        }

        // ================= CEK DATASET =================
        if (empty($dataset)) {

            return response()->json([
                'status' => 'kosong',
                'message' => 'Data presensi pada periode tersebut tidak tersedia.'
            ], 422);
        }
        if (count($dataset) < 3) {
            return response()->json([
                'message' => 'Jumlah data kurang dari jumlah cluster'
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | NORMALISASI DATASET
        |--------------------------------------------------------------------------
        */

        $datasetNormal = $dataset;

        $datasetNormal = $this->normalisasi->minMax($datasetNormal, 'x1');
        $datasetNormal = $this->normalisasi->minMax($datasetNormal, 'x2');


        /*
        |--------------------------------------------------------------------------
        | CENTROID
        |--------------------------------------------------------------------------
        */

        if ($request->centroid) {

            // iterasi selanjutnya
            $centroidNormal = $request->centroid;
        } else {

            $centroidNormal = $this->centroidService->centroidAwal($datasetNormal);
        }


        /*
        |--------------------------------------------------------------------------
        | ITERASI
        |--------------------------------------------------------------------------
        */

        $iterasi = $request->iterasi ?? 1;

        $hasil = $this->iterasiService
            ->prosesIterasi($datasetNormal, $centroidNormal, $iterasi);


        /*
        |--------------------------------------------------------------------------
        | CEK KONVERGEN
        |--------------------------------------------------------------------------
        */

        $konvergen = true;

        foreach ($centroidNormal as $i => $c) {

            if (
                round($c['x1_norm'], 4) != round($hasil['centroid_baru'][$i]['x1_norm'], 4) ||
                round($c['x2_norm'], 4) != round($hasil['centroid_baru'][$i]['x2_norm'], 4)
            ) {

                $konvergen = false;
                break;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'dataset' => $dataset,
            'normalisasi' => $datasetNormal,

            'centroid_normal' => $centroidNormal,

            'iterasi' => $hasil,
            'konvergen' => $konvergen

        ]);
    }


    /**
     * ==============================
     * HASIL OTOMATIS SAMPAI KONVERGEN
     * ==============================
     */
    public function hasil(Request $request)
    {

        $periode = $request->mode;

        /*
        |--------------------------------------------------------------------------
        | DATASET
        |--------------------------------------------------------------------------
        */

        if ($periode == 'bulan') {

            $dataset = $this->datasetBulanan->getDataset($request);
        } else {

            $dataset = $this->datasetTahunan->getDataset($request);
        }
        // CEK DATASET
        if (empty($dataset)) {

            return response()->json([
                'status' => 'kosong',
                'message' => 'Data presensi pada periode tersebut tidak tersedia.'
            ], 422);
        }
        if (count($dataset) < 3) {
            return response()->json([
                'message' => 'Jumlah data kurang dari jumlah cluster'
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | NORMALISASI DATASET
        |--------------------------------------------------------------------------
        */

        $datasetNormal = $dataset;

        $datasetNormal = $this->normalisasi->minMax($datasetNormal, 'x1');
        $datasetNormal = $this->normalisasi->minMax($datasetNormal, 'x2');


        /*
        |--------------------------------------------------------------------------
        | CENTROID AWAL
        |--------------------------------------------------------------------------
        */
        // centroid langsung dari data yang SUDAH DINORMALISASI
        $centroidNormal = $this->centroidService
            ->centroidAwal($datasetNormal);

        /*
        |--------------------------------------------------------------------------
        | PROSES OTOMATIS
        |--------------------------------------------------------------------------
        */

        $hasil = $this->resultService
            ->prosesOtomatis($datasetNormal, $centroidNormal);


        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'dataset' => $dataset,
            'normalisasi' => $datasetNormal,

            'centroid_normal' => $centroidNormal,

            'hasil' => $hasil

        ]);
    }
}
