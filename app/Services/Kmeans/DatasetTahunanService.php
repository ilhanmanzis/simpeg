<?php

namespace App\Services\Kmeans;

use App\Models\Presensi;
use Illuminate\Http\Request;

class DatasetTahunanService
{
    public function getDataset(Request $request)
    {
        $tahun = $request->tahun;

        $presensis = Presensi::with(['user.struktural', 'user.dataDiri'])
            ->whereHas('user', function ($q) {
                $q->where('status_keaktifan', 'aktif');
            })
            ->whereYear('tanggal', $tahun)
            ->get();

        $dataset = [];

        // kelompokkan presensi per user
        $groupUser = $presensis->groupBy('id_user');

        foreach ($groupUser as $idUser => $rows) {

            $totalDurasi = 0;
            $totalPemenuhan = 0;
            $totalHari = 0;
            $totalHadir = 0;

            $user = $rows->first()->user;

            foreach ($rows as $presensi) {

                $durasi = $presensi->durasi_menit ?? 0;

                // ======================
                // Tentukan jam kerja wajib
                // ======================

                if ($user->role === 'karyawan') {

                    $jamWajib = 480;
                } else {

                    $jamWajib = 360;

                    foreach ($user->struktural as $struktural) {

                        if (
                            $struktural->status === 'aktif' &&
                            $presensi->tanggal >= $struktural->tanggal_mulai &&
                            (
                                $struktural->tanggal_selesai === null ||
                                $presensi->tanggal <= $struktural->tanggal_selesai
                            )
                        ) {
                            $jamWajib = 420;
                            break;
                        }
                    }
                }

                // ======================
                // Hitung pemenuhan jam kerja
                // ======================

                $pemenuhan = 0;

                if ($jamWajib > 0) {
                    $pemenuhan = ($durasi / $jamWajib) * 100;
                }

                if ($pemenuhan > 120) {
                    $pemenuhan = 120;
                }

                // ======================
                // Hitung kehadiran
                // ======================

                $totalHari++;

                if ($presensi->status_kehadiran === 'hadir') {
                    $totalHadir++;
                }

                // ======================
                // Akumulasi
                // ======================

                $totalDurasi += $durasi;
                $totalPemenuhan += $pemenuhan;
            }

            // ======================
            // Hitung variabel K-Means
            // ======================

            // x1 = rata-rata pemenuhan jam kerja
            $x1 = $totalPemenuhan / max($totalHari, 1);

            // x2 = persentase kehadiran
            $x2 = ($totalHadir / max($totalHari, 1)) * 100;

            $dataset[] = [
                'id_user' => $idUser,
                'name' => $user->dataDiri->name,
                'npp' => $user->npp,

                'x1' => round($x1, 2),
                'x2' => round($x2, 2),

                'total_hadir' => $totalHadir,
                'total_presensi' => $totalHari,
            ];
        }

        return $dataset;
    }
}
