<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\User;
use App\Services\GoogleDriveService;
use App\Services\Kmeans\KedisiplinanService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Dashboard extends Controller
{
    protected $googleDriveService;
    protected KedisiplinanService $kedisiplinanService;

    public function __construct(
        GoogleDriveService $googleDriveService,
        KedisiplinanService $kedisiplinanService
    ) {
        $this->googleDriveService = $googleDriveService;
        $this->kedisiplinanService = $kedisiplinanService;
    }


    /**
     * Show the form for creating a new resource.
     */
    public function admin()
    {


        /* =========================
         * 1) Jumlah Dosen & Tendik
         * ========================= */
        $counts =  [
            'dosen' => [
                'aktif'    => User::where('role', 'dosen')->where('status_keaktifan', 'aktif')->count(),
                'nonaktif' => User::where('role', 'dosen')->where('status_keaktifan', '!=', 'aktif')->count(),
            ],
            'tendik' => [
                'aktif'    => User::where('role', 'karyawan')->where('status_keaktifan', 'aktif')->count(),
                'nonaktif' => User::where('role', 'karyawan')->where('status_keaktifan', '!=', 'aktif')->count(),
            ],
        ];

        /* =========================
         * 2) Pendidikan Terakhir
         *    (jenjang tertinggi = MIN(id_jenjang))
         * ========================= */
        $sub = DB::table('pendidikan as p')
            ->select('p.id_user', DB::raw('MIN(p.id_jenjang) as top_jenjang'))
            ->groupBy('p.id_user');

        $eduDosen = DB::table('users as u')
            ->joinSub($sub, 'px', 'px.id_user', '=', 'u.id_user')
            ->join('jenjang as j', 'j.id_jenjang', '=', 'px.top_jenjang')
            ->where('u.role', 'dosen')
            ->groupBy('j.id_jenjang', 'j.nama_jenjang')
            ->orderBy('j.id_jenjang')
            ->select('j.nama_jenjang', DB::raw('COUNT(*) as total'))
            ->pluck('total', 'j.nama_jenjang')
            ->toArray();

        $eduTendik = DB::table('users as u')
            ->joinSub($sub, 'px', 'px.id_user', '=', 'u.id_user')
            ->join('jenjang as j', 'j.id_jenjang', '=', 'px.top_jenjang')
            ->where('u.role', 'karyawan')
            ->groupBy('j.id_jenjang', 'j.nama_jenjang')
            ->orderBy('j.id_jenjang')
            ->select('j.nama_jenjang', DB::raw('COUNT(*) as total'))
            ->pluck('total', 'j.nama_jenjang')
            ->toArray();

        // tampilkan hanya jenjang yang ada datanya
        $labels = collect(array_keys($eduDosen))->merge(array_keys($eduTendik))->unique()->values();

        $pendidikan = [];
        foreach ($labels as $label) {
            $d = (int)($eduDosen[$label]  ?? 0);
            $t = (int)($eduTendik[$label] ?? 0);
            if (($d + $t) > 0) {
                $pendidikan[$label] = ['dosen' => $d, 'tendik' => $t];
            }
        }


        /* =========================
         * 3) Golongan (khusus dosen)
         * ========================= */
        $golongan = DB::table('golongan_user as gu')
            ->join('users as u', 'u.id_user', '=', 'gu.id_user')
            ->join('golongan as g', 'g.id_golongan', '=', 'gu.id_golongan')
            ->where('u.role', 'dosen')
            ->where('gu.status', 'aktif')
            ->groupBy('g.id_golongan', 'g.nama_golongan')
            ->orderBy('g.id_golongan')
            ->select('g.nama_golongan', DB::raw('COUNT(*) as total'))
            ->pluck('total', 'g.nama_golongan')
            ->toArray();

        /* =========================
         * 4) Jabatan Fungsional (dosen)
         * ========================= */
        $fungsional =  DB::table('jabatan_fungsional_user as fu')
            ->join('users as u', 'u.id_user', '=', 'fu.id_user')
            ->join('jabatan_fungsional as jf', 'jf.id_fungsional', '=', 'fu.id_fungsional')
            ->where('u.role', 'dosen')
            ->where('fu.status', 'aktif')
            ->groupBy('jf.id_fungsional', 'jf.nama_jabatan')
            ->orderBy('jf.id_fungsional')
            ->select('jf.nama_jabatan', DB::raw('COUNT(*) as total'))
            ->pluck('total', 'jf.nama_jabatan')
            ->toArray();

        $totalDosen = ($counts['dosen']['aktif'] ?? 0) + ($counts['dosen']['nonaktif'] ?? 0);

        // sudah tersertifikasi
        $certified = User::where('role', 'dosen')
            ->whereHas('dataDiri', fn($q) => $q->where('tersertifikasi', 'sudah'))
            ->count();

        // belum tersertifikasi (fallback aman)
        $notCertified = max(0, $totalDosen - $certified);

        $counts['dosen']['tersertifikasi']        = $certified;
        $counts['dosen']['belum_tersertifikasi']  = $notCertified;

        $stats = [
            'counts'      => $counts,
            'pendidikan'  => $pendidikan, // label => ['dosen'=>x,'tendik'=>y]
            'golongan'    => $golongan,   // label => total
            'fungsional'  => $fungsional, // label => total
        ];

        $bulan = now()->month;
        $tahun = now()->year;
        $periode = Carbon::create($tahun, $bulan)->translatedFormat('F Y');
        $cacheKey = "dashboard_presensi_{$bulan}_{$tahun}";
        $dataCache = Cache::remember($cacheKey, now()->addHours(6), function () use ($bulan, $tahun) {

            /*
        |--------------------------------------------------------------------------
        | LINE CHART
        | Tren Kehadiran Harian (bulan ini)
        |--------------------------------------------------------------------------
        */

            $kehadiranHarian = Presensi::select(
                DB::raw('DAY(tanggal) as hari'),
                'status_kehadiran',
                DB::raw('COUNT(*) as total')
            )
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->whereIn('status_kehadiran', ['hadir', 'sakit', 'izin'])
                ->where(function ($q) {

                    // hadir
                    $q->where(function ($sub) {
                        $sub->whereNotNull('jam_datang')
                            ->whereNotNull('jam_pulang')
                            ->whereNotNull('durasi_menit');
                    })

                        // sakit / izin
                        ->orWhereIn('status_kehadiran', ['sakit', 'izin']);
                })
                ->groupBy('hari', 'status_kehadiran')
                ->orderBy('hari')
                ->get();

            $labels = $kehadiranHarian->pluck('hari')->unique()->values();

            $hadir = [];
            $sakit = [];
            $izin  = [];

            foreach ($labels as $hari) {

                $hadir[] = $kehadiranHarian
                    ->where('hari', $hari)
                    ->where('status_kehadiran', 'hadir')
                    ->first()->total ?? 0;

                $sakit[] = $kehadiranHarian
                    ->where('hari', $hari)
                    ->where('status_kehadiran', 'sakit')
                    ->first()->total ?? 0;

                $izin[] = $kehadiranHarian
                    ->where('hari', $hari)
                    ->where('status_kehadiran', 'izin')
                    ->first()->total ?? 0;
            }

            $lineChart = [
                'labels' => $labels,
                'hadir' => $hadir,
                'sakit' => $sakit,
                'izin' => $izin
            ];

            /*
        |--------------------------------------------------------------------------
        | PIE CHART
        | Status Kehadiran
        |--------------------------------------------------------------------------
        */

            $status = Presensi::select(
                'status_kehadiran',
                DB::raw('COUNT(*) as total')
            )
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->where(function ($q) {

                    // hadir
                    $q->where(function ($sub) {
                        $sub->whereNotNull('jam_datang')
                            ->whereNotNull('jam_pulang')
                            ->whereNotNull('durasi_menit');
                    })

                        // sakit / izin
                        ->orWhereIn('status_kehadiran', ['sakit', 'izin']);
                })
                ->groupBy('status_kehadiran')
                ->pluck('total', 'status_kehadiran');

            $pieChart = [
                'hadir' => $status['hadir'] ?? 0,
                'sakit' => $status['sakit'] ?? 0,
                'izin' => $status['izin'] ?? 0
            ];
            $totalPresensi = Presensi::whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->count();

            /*
        |--------------------------------------------------------------------------
        | Durasi CHART
        |--------------------------------------------------------------------------
        */

            $durasiKerja = Presensi::select(
                DB::raw('MONTH(tanggal) as bulan'),
                DB::raw('AVG(durasi_menit) as rata_durasi')
            )
                ->whereYear('tanggal', $tahun)
                ->where(function ($q) {

                    // hadir
                    $q->where(function ($sub) {
                        $sub->whereNotNull('jam_datang')
                            ->whereNotNull('jam_pulang')
                            ->whereNotNull('durasi_menit');
                    });
                })
                ->groupBy('bulan')
                ->orderBy('bulan')
                ->get();

            $labels = [];
            $data = [];

            foreach ($durasiKerja as $row) {

                $labels[] = Carbon::create()->month($row->bulan)->translatedFormat('F');

                // ubah menit ke jam desimal
                $data[] = round($row->rata_durasi / 60, 2);
            }

            $durasiChart = [
                'labels' => $labels,
                'data' => $data
            ];

            /*
        |--------------------------------------------------------------------------
        | DISTRIBUSI JAM MASUK
        |--------------------------------------------------------------------------
        */

            $jamMasuk = Presensi::select(
                DB::raw('HOUR(jam_datang) as jam'),
                DB::raw('COUNT(*) as total')
            )
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->where(function ($q) {

                    // hadir
                    $q->where(function ($sub) {
                        $sub->whereNotNull('jam_datang')
                            ->whereNotNull('jam_pulang')
                            ->whereNotNull('durasi_menit');
                    });
                })
                ->groupBy('jam')
                ->orderBy('jam')
                ->get();

            $jamMasukChart = [
                'labels' => $jamMasuk->pluck('jam')->map(fn($j) => $j . ':00'),
                'data' => $jamMasuk->pluck('total')
            ];

            /*
        |--------------------------------------------------------------------------
        | DISTRIBUSI JAM PULANG
        |--------------------------------------------------------------------------
        */

            $jamPulang = Presensi::select(
                DB::raw('HOUR(jam_pulang) as jam'),
                DB::raw('COUNT(*) as total')
            )
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->where(function ($q) {

                    // hadir
                    $q->where(function ($sub) {
                        $sub->whereNotNull('jam_datang')
                            ->whereNotNull('jam_pulang')
                            ->whereNotNull('durasi_menit');
                    });
                })
                ->groupBy('jam')
                ->orderBy('jam')
                ->get();

            $jamPulangChart = [
                'labels' => $jamPulang->pluck('jam')->map(fn($j) => $j . ':00'),
                'data' => $jamPulang->pluck('total')
            ];

            /*
        |--------------------------------------------------------------------------
        | CLUSTER KEDISIPLINAN
        |--------------------------------------------------------------------------
        */

            $cluster = [
                'tinggi' => 0,
                'sedang' => 0,
                'rendah' => 0
            ];

            $kmeans = $this->kedisiplinanService->getKedisiplinanUser($bulan, $tahun);

            if ($kmeans) {

                foreach ($kmeans['clusters'] as $row) {

                    $mapping = $this->kedisiplinanService->mappingCluster($row['cluster']);

                    if ($mapping['label'] == 'Tinggi') {
                        $cluster['tinggi']++;
                    }

                    if ($mapping['label'] == 'Sedang') {
                        $cluster['sedang']++;
                    }

                    if ($mapping['label'] == 'Rendah') {
                        $cluster['rendah']++;
                    }
                }
            }

            $clusterChart = [
                'labels' => ['Tinggi', 'Sedang', 'Rendah'],
                'data' => [
                    $cluster['tinggi'],
                    $cluster['sedang'],
                    $cluster['rendah']
                ]
            ];

            return [
                'lineChart' => $lineChart,
                'pieChart' => $pieChart,
                'totalPresensi' => $totalPresensi,
                'durasiChart' => $durasiChart,
                'jamMasukChart' => $jamMasukChart,
                'jamPulangChart' => $jamPulangChart,
                'clusterChart' => $clusterChart,
                'tahun' => $tahun
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */
        $data = [
            'page' => 'Dashboard',
            'selected' => 'Dashboard',
            'title' => 'Dashboard',

            'stats' => $stats,
            'periode' => $periode,


            'lineChart' => $dataCache['lineChart'],
            'pieChart' => $dataCache['pieChart'],
            'totalPresensi' => $dataCache['totalPresensi'],
            'durasiChart' => $dataCache['durasiChart'],
            'tahun' => $dataCache['tahun'],
            'jamMasukChart' => $dataCache['jamMasukChart'],
            'jamPulangChart' => $dataCache['jamPulangChart'],
            'clusterChart' => $dataCache['clusterChart']
        ];



        return view('admin.dashboard', $data);
    }
    public function dosen()
    {
        $today = Carbon::today()->toDateString();
        $id = Auth::user()->id_user;
        $bulan = Carbon::now()->month;
        $tahun = Carbon::now()->year;

        $dosen = User::where('id_user', $id)->with([
            'dataDiri.serdosen',
            'golongan' => fn($q) => $q->where('status', 'aktif')->orderByDesc('id_golongan_user')->limit(1)->with('golongan'),
            'fungsional' => fn($q) => $q->where('status', 'aktif')->orderByDesc('id_fungsional_user')->limit(1)->with('fungsional'),
            'struktural' => fn($q) => $q->where('status', 'aktif')->whereDate('tanggal_selesai', '>=', $today)->orderByDesc('id_struktural_user')->limit(1)->with('struktural'),
            'pendidikan' => fn($q) => $q->orderBy('id_jenjang')->latest()->limit(1)->with('jenjang'),
        ])->firstOrFail();


        $cacheKey = "presensi_avg_{$id}_{$bulan}_{$tahun}";
        $avgData = Cache::remember($cacheKey, now()->addHours(12), function () use ($id, $bulan, $tahun) {

            $data = Presensi::where('id_user', $id)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->get();

            // =============================
            // RATA JAM MASUK
            // =============================
            $avgJamMasuk = $data
                ->whereNotNull('jam_datang')
                ->avg(fn($p) => strtotime($p->jam_datang));

            $avgJamMasuk = $avgJamMasuk ? date('H:i:s', $avgJamMasuk) . ' WIB' : '-';

            // =============================
            // RATA JAM PULANG
            // =============================
            $avgJamPulang = $data
                ->whereNotNull('jam_pulang')
                ->avg(fn($p) => strtotime($p->jam_pulang));

            $avgJamPulang = $avgJamPulang ? date('H:i:s', $avgJamPulang) . ' WIB' : '-';

            // =============================
            // RATA JAM KERJA
            // =============================
            $avgDurasi = $data
                ->whereNotNull('durasi_menit')
                ->avg('durasi_menit');

            if ($avgDurasi) {
                $jam = intdiv($avgDurasi, 60);
                $menit = $avgDurasi % 60;
                $avgJamKerja = sprintf('%02d:%02d:00', $jam, $menit);
            } else {
                $avgJamKerja = '-';
            }

            return [
                'avgJamMasuk' => $avgJamMasuk,
                'avgJamPulang' => $avgJamPulang,
                'avgJamKerja' => $avgJamKerja
            ];
        });
        $cluster = $this->kedisiplinanService
            ->getClusterUser($bulan, $tahun, Auth::id());

        $kedisiplinan = $this->kedisiplinanService
            ->mappingCluster($cluster);

        $data = [
            'page' => 'Dashboard',
            'selected' => 'Dashboard',
            'title' => 'Dashboard',
            'dosen' => $dosen,
            'golongan' => $dosen->golongan->first()?->golongan->nama_golongan ?? 'Belum ada golongan',
            'fungsional' => $dosen->fungsional->first()?->fungsional->nama_jabatan ?? 'Belum ada Jabatan Fungsional',
            'struktural' => $dosen->struktural->first()?->struktural->nama_jabatan ?? 'Belum ada Jabatan Struktural',
            'pendidikan' => $dosen->pendidikan->first() ?? 'Belum ada pendidikan',
            'avgJamMasuk' => $avgData['avgJamMasuk'],
            'avgJamPulang' => $avgData['avgJamPulang'],
            'avgJamKerja' => $avgData['avgJamKerja'],
            'bulan' => Carbon::now()->translatedFormat('F'),
            'kedisiplinan' => $kedisiplinan['label'],
            'warnaKedisiplinan' => $kedisiplinan['color'],
        ];

        return view('dosen.dashboard', $data);
    }
    public function karyawan()
    {
        $id = Auth::user()->id_user;

        $today = Carbon::today();
        $bulan = Carbon::now()->month;
        $tahun = Carbon::now()->year;

        $karyawan = User::where('id_user', $id)->with([
            'dataDiri',

            'pendidikan' => fn($q) => $q->orderBy('id_jenjang')->latest()->limit(1)->with('jenjang'),
        ])->firstOrFail();


        $cacheKey = "presensi_avg_{$id}_{$bulan}_{$tahun}";
        $avgData = Cache::remember($cacheKey, now()->addHours(12), function () use ($id, $bulan, $tahun) {

            $data = Presensi::where('id_user', $id)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->get();

            // =============================
            // RATA JAM MASUK
            // =============================
            $avgJamMasuk = $data
                ->whereNotNull('jam_datang')
                ->avg(fn($p) => strtotime($p->jam_datang));

            $avgJamMasuk = $avgJamMasuk ? date('H:i:s', $avgJamMasuk) . ' WIB' : '-';

            // =============================
            // RATA JAM PULANG
            // =============================
            $avgJamPulang = $data
                ->whereNotNull('jam_pulang')
                ->avg(fn($p) => strtotime($p->jam_pulang));

            $avgJamPulang = $avgJamPulang ? date('H:i:s', $avgJamPulang) . ' WIB' : '-';

            // =============================
            // RATA JAM KERJA
            // =============================
            $avgDurasi = $data
                ->whereNotNull('durasi_menit')
                ->avg('durasi_menit');

            if ($avgDurasi) {
                $jam = intdiv($avgDurasi, 60);
                $menit = $avgDurasi % 60;
                $avgJamKerja = sprintf('%02d:%02d:00', $jam, $menit);
            } else {
                $avgJamKerja = '-';
            }

            return [
                'avgJamMasuk' => $avgJamMasuk,
                'avgJamPulang' => $avgJamPulang,
                'avgJamKerja' => $avgJamKerja
            ];
        });

        $cluster = $this->kedisiplinanService
            ->getClusterUser($bulan, $tahun, Auth::id());

        $kedisiplinan = $this->kedisiplinanService
            ->mappingCluster($cluster);
        $data = [
            'page' => 'Dashboard',
            'selected' => 'Dashboard',
            'title' => 'Dashboard',
            'karyawan' => $karyawan,
            'pendidikan' => $karyawan->pendidikan->first() ?? 'Belum ada pendidikan',
            'avgJamMasuk' => $avgData['avgJamMasuk'],
            'avgJamPulang' => $avgData['avgJamPulang'],
            'avgJamKerja' => $avgData['avgJamKerja'],
            'bulan' => Carbon::now()->translatedFormat('F'),
            'kedisiplinan' => $kedisiplinan['label'],
            'warnaKedisiplinan' => $kedisiplinan['color'],
        ];
        return view('karyawan.dashboard', $data);
    }
}
