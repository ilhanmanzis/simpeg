<?php

namespace App\Http\Controllers\Admin;

use App\Models\PresensiAktivitas;
use App\Models\PresensiDokumen;
use App\Models\SettingLokasiPresensi;
use App\Models\Dokumens;
use App\Models\StrukturalUsers;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Presensi as PresensiModel;
use App\Services\Kmeans\KedisiplinanService;
use App\Services\LocationService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Services\PresensiService;
use Illuminate\Support\Facades\Cache;

class Presensi extends Controller
{
    protected LocationService $locationService;
    protected PresensiService $presensiService;
    protected $kedisiplinanService;


    public function __construct(
        LocationService $locationService,
        PresensiService $presensiService,
        KedisiplinanService $kedisiplinanService
    ) {
        $this->locationService = $locationService;
        $this->presensiService = $presensiService;
        $this->kedisiplinanService = $kedisiplinanService;
    }
    public function index()
    {
        $bulan = now()->month;
        $tahun = now()->year;
        $periode = Carbon::create($tahun, $bulan)->translatedFormat('F Y');
        $cacheKey = "dashboard_presensi_{$bulan}_{$tahun}";
        $data = Cache::remember($cacheKey, now()->addHours(6), function () use ($bulan, $tahun) {

            /*
        |--------------------------------------------------------------------------
        | LINE CHART
        | Tren Kehadiran Harian (bulan ini)
        |--------------------------------------------------------------------------
        */

            $kehadiranHarian = PresensiModel::select(
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

            $status = PresensiModel::select(
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
            $totalPresensi = PresensiModel::whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->count();

            /*
        |--------------------------------------------------------------------------
        | Durasi CHART
        |--------------------------------------------------------------------------
        */

            $durasiKerja = PresensiModel::select(
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

            $jamMasuk = PresensiModel::select(
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

            $jamPulang = PresensiModel::select(
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

        return view('admin.presensi.dashboard.index', [
            'page' => 'Dashboard Presensi',
            'selected' => 'Dashboard Presensi',
            'title' => 'Dashboard Presensi',
            'periode' => $periode,


            'lineChart' => $data['lineChart'],
            'pieChart' => $data['pieChart'],
            'totalPresensi' => $data['totalPresensi'],
            'durasiChart' => $data['durasiChart'],
            'tahun' => $data['tahun'],
            'jamMasukChart' => $data['jamMasukChart'],
            'jamPulangChart' => $data['jamPulangChart'],
            'clusterChart' => $data['clusterChart']
        ]);
    }

    public function createMasuk()
    {
        $tanggal = now()->toDateString();
        $data = [
            'page'      => 'Dashboard Presensi',
            'selected'  => 'Dashboard Presensi',
            'title'     => 'Input Presensi Pegawai',
            'pegawais' => User::query()
                ->select('id_user', 'npp', 'role')
                ->whereIn('role', ['dosen', 'karyawan'])
                ->where('status_keaktifan', 'aktif')
                ->with('dataDiri:id_data_diri,id_user,name')
                ->withExists([
                    'struktural as has_struktural_aktif' => function ($q) use ($tanggal) {
                        $q->where('status', 'aktif')
                            ->whereDate('tanggal_mulai', '<=', $tanggal)
                            ->where(function ($query) use ($tanggal) {
                                $query->whereNull('tanggal_selesai')
                                    ->orWhereDate('tanggal_selesai', '>=', $tanggal);
                            });
                    }
                ])
                ->get()
        ];

        return view('admin.presensi.dashboard.create', $data);
    }

    public function pulang(Request $request)
    {
        $keyword = $request->get('search');
        $data = [
            'page' => 'Dashboard Presensi',
            'selected' => 'Dashboard Presensi',
            'title' => 'Daftar Belum Presensi Pulang',
            'presensis' => PresensiModel::with(['user.dataDiri'])
                ->whereNull('jam_pulang')
                ->where('status_kehadiran', 'hadir')
                ->when($keyword, function ($query) use ($keyword) {
                    $query->whereHas('user', function ($q) use ($keyword) {
                        $q->searchPegawai($keyword);
                    });
                })
                ->orderBy('jam_datang', 'asc')
                ->get()
        ];
        return view('admin.presensi.dashboard.pulang', $data);
    }

    public function prosesPulang($id)
    {
        $presensi = PresensiModel::with(['user.dataDiri'])
            ->where('id_presensi', $id)
            ->firstOrFail();
        if ($presensi->jam_pulang) {
            return redirect()->route('admin.presensi.pulang')
                ->with('error', 'Presensi sudah memiliki jam pulang.');
        }

        $todayDate = Carbon::today()->toDateString();

        $isStruktural = false;
        if ($presensi->user->role == 'dosen') {
            $isStruktural = StrukturalUsers::where('id_user', $presensi->user->id_user)
                ->where('status', 'aktif')
                ->whereDate('tanggal_selesai', '>=', $todayDate)
                ->exists();
        }

        $jamWajib = $this->presensiService->getJamWajib(
            $presensi->user,
            $presensi->tanggal
        );

        $jamDatangCarbon = Carbon::parse(
            $presensi->tanggal . ' ' . $presensi->jam_datang
        );

        $jamPulangDefault = $jamDatangCarbon->copy()->addHours($jamWajib);

        $data = [
            'page' => 'Dashboard Presensi',
            'selected' => 'Dashboard Presensi',
            'title' => 'Input Presensi Pulang',
            'presensi' => $presensi,
            'isStruktural' => $isStruktural,
            'jamWajib' => $jamWajib,
            'jamPulangDefault' => $jamPulangDefault,
        ];
        return view('admin.presensi.dashboard.proses', $data);
    }

    public function createIzin()
    {
        $data = [
            'page' => 'Dashboard Presensi',
            'selected' => 'Dashboard Presensi',
            'title' => 'Input Izin Pegawai',
            'pegawais' => User::query()
                ->select('id_user', 'npp', 'role')
                ->whereIn('role', ['dosen', 'karyawan'])
                ->where('status_keaktifan', 'aktif')
                ->with('dataDiri:id_data_diri,id_user,name')
                ->get()
        ];
        return view('admin.presensi.dashboard.izin', $data);
    }

    public function createSakit()
    {
        $data = [
            'page' => 'Dashboard Presensi',
            'selected' => 'Dashboard Presensi',
            'title' => 'Input Sakit Pegawai',
            'pegawais' => User::query()
                ->select('id_user', 'npp', 'role')
                ->whereIn('role', ['dosen', 'karyawan'])
                ->where('status_keaktifan', 'aktif')
                ->with('dataDiri:id_data_diri,id_user,name')
                ->get()
        ];
        return view('admin.presensi.dashboard.sakit', $data);
    }


    public function storeMasuk(Request $request)
    {
        $request->validate([
            'id_user'    => 'required|exists:users,id_user',
            'tanggal'    => 'required|date',
            'jam_datang' => 'required|date_format:H:i',
            'is_pulang'  => 'nullable|boolean',
            'jam_pulang' => 'nullable|required_if:is_pulang,1|date_format:H:i',


            // aktivitas (opsional)
            'sks_siang' => 'nullable|integer|min:0',
            'sks_malam' => 'nullable|integer|min:0',
            'sks_praktikum_siang' => 'nullable|integer|min:0',
            'sks_praktikum_malam' => 'nullable|integer|min:0',

            'mata_kuliah' => 'nullable|string',
            'kegiatan'    => 'nullable|string',

            'seminar_jumlah' => 'nullable|integer',
            'seminar_keterangan' => 'nullable|string',

            'pembimbing_jumlah' => 'nullable|integer',
            'pembimbing_keterangan' => 'nullable|string',

            'penguji_jumlah' => 'nullable|integer',
            'penguji_keterangan' => 'nullable|string',

            'kkl_jumlah' => 'nullable|integer',
            'kkl_keterangan' => 'nullable|string',

            'tugas_luar_jumlah' => 'nullable|integer',
            'tugas_luar_keterangan' => 'nullable|string',

            'kegiatan_karyawan' => 'nullable|string',

            // foto
            'foto'   => 'nullable|array|max:3',
            'foto.*' => 'image|max:2048',
        ]);

        $user   = User::where('id_user', $request->id_user)->firstOrFail();
        $tanggal = $request->tanggal;

        // ==============================
        // CEK DUPLIKAT (unique id_user + tanggal)
        // ==============================
        if (PresensiModel::where('id_user', $user->id_user)
            ->whereDate('tanggal', $tanggal)
            ->exists()
        ) {

            return back()->with('error', 'Presensi pada tanggal ' . Carbon::parse($tanggal)->format('d F Y') . ' sudah ada.');
        }

        // ==============================
        // LOKASI DEFAULT (DARI SETTING)
        // ==============================
        $lokasi = SettingLokasiPresensi::first();

        $lat  = $lokasi?->latitude;
        $long = $lokasi?->longitude;

        // ==============================
        // HITUNG DURASI & STATUS JAM
        // ==============================
        $durasiMenit = null;
        $statusJamKerja = 'hijau';
        $alamat = $this->locationService->getAlamatDariKoordinatOSM(
            $lat,
            $long
        );

        if ($request->boolean('is_pulang')) {

            $jamDatang = Carbon::parse($tanggal . ' ' . $request->jam_datang);
            $jamPulang = Carbon::parse($tanggal . ' ' . $request->jam_pulang);

            $durasiData = $this->presensiService->hitungDurasi(
                $jamDatang,
                $jamPulang
            );

            $jamWajib = $this->presensiService->getJamWajib($user, $request->tanggal);

            $statusJamKerja = $this->presensiService->getStatusJamKerja(
                $durasiData['durasi_jam'],
                $jamWajib
            );
            $durasiMenit = $durasiData['durasi_menit'];
        }


        DB::transaction(function () use (
            $request,
            $user,
            $tanggal,
            $lat,
            $long,
            $alamat,
            $durasiMenit,
            $statusJamKerja
        ) {

            // ==============================
            // SIMPAN PRESENSI
            // ==============================
            if ($request->boolean('is_pulang')) {
                $presensi = PresensiModel::create([
                    'id_user'   => $user->id_user,
                    'tanggal'   => $tanggal,
                    'jam_datang' => $request->jam_datang,
                    'jam_pulang' => $request->jam_pulang,

                    'durasi_menit' => $durasiMenit,
                    'alamat_datang' => $alamat,
                    'alamat_pulang' => $alamat,

                    'lat_datang' => $lat,
                    'long_datang' => $long,
                    'jarak_datang' => 0,
                    'status_lokasi_datang' => 'didalam_radius',

                    'lat_pulang' => $request->jam_pulang ? $lat : null,
                    'long_pulang' => $request->jam_pulang ? $long : null,
                    'jarak_pulang' => 0,
                    'status_lokasi_pulang' => 'didalam_radius',

                    'status_jam_kerja' => $statusJamKerja,
                    'status_kehadiran' => 'hadir',
                ]);

                // ==============================
                // SIMPAN AKTIVITAS (jika ada pulang)
                // ==============================

                PresensiAktivitas::create([
                    'id_presensi' => $presensi->id_presensi,

                    'sks_siang' => $request->sks_siang,
                    'sks_malam' => $request->sks_malam,
                    'sks_praktikum_siang' => $request->sks_praktikum_siang,
                    'sks_praktikum_malam' => $request->sks_praktikum_malam,

                    'mata_kuliah' => $request->mata_kuliah,
                    'kegiatan'    => $request->kegiatan ?? $request->kegiatan_karyawan,

                    'seminar_jumlah' => $request->seminar_jumlah,
                    'seminar_keterangan' => $request->seminar_keterangan,

                    'pembimbing_jumlah' => $request->pembimbing_jumlah,
                    'pembimbing_keterangan' => $request->pembimbing_keterangan,

                    'penguji_jumlah' => $request->penguji_jumlah,
                    'penguji_keterangan' => $request->penguji_keterangan,

                    'kkl_jumlah' => $request->kkl_jumlah,
                    'kkl_keterangan' => $request->kkl_keterangan,

                    'tugas_luar_jumlah' => $request->tugas_luar_jumlah,
                    'tugas_luar_keterangan' => $request->tugas_luar_keterangan,
                ]);

                // ==============================
                // UPLOAD FOTO (OPTIONAL)
                // ==============================
                if ($request->hasFile('foto')) {

                    $lastDokumen = Dokumens::orderBy('nomor_dokumen', 'desc')->first();
                    $lastNumber  = $lastDokumen ? (int)$lastDokumen->nomor_dokumen : 0;

                    foreach ($request->file('foto') as $i => $file) {

                        $lastNumber++;
                        $nomorDokumen = str_pad($lastNumber, 7, '0', STR_PAD_LEFT);

                        $filename = time() . '_' . $i . '_' . $file->getClientOriginalName();
                        $path     = "{$user->npp}/presensi/{$tanggal}/{$filename}";

                        $result = app(\App\Services\GoogleDriveService::class)
                            ->uploadFileAndGetUrl($file->getPathname(), $path);

                        Dokumens::create([
                            'nomor_dokumen'  => $nomorDokumen,
                            'path_file'      => $path,
                            'file_id'        => $result['file_id'],
                            'view_url'       => $result['view_url'],
                            'download_url'   => $result['download_url'],
                            'preview_url'    => $result['preview_url'],
                            'id_user'        => $user->id_user,
                            'tanggal_upload' => now(),
                        ]);

                        PresensiDokumen::create([
                            'id_presensi'   => $presensi->id_presensi,
                            'nomor_dokumen' => $nomorDokumen,
                        ]);
                    }
                }
            } else {
                $presensi = PresensiModel::create([
                    'id_user'   => $user->id_user,
                    'tanggal'   => $tanggal,
                    'jam_datang' => $request->jam_datang,
                    'lat_datang' => $lat,
                    'long_datang' => $long,
                    'jarak_datang' => 0,
                    'status_lokasi_datang' => 'didalam_radius',
                    'status_kehadiran' => 'hadir',
                    'alamat_datang' => $alamat,
                ]);
            }
        });

        return redirect()->route('admin.presensi')
            ->with('success', 'Presensi berhasil disimpan.');
    }

    public function storePulang(Request $request)
    {
        $request->validate([
            'id_presensi'    => 'required|exists:presensis,id_presensi',
            'jam_pulang' => 'required|date_format:H:i',


            // aktivitas (opsional)
            'sks_siang' => 'nullable|integer|min:0',
            'sks_malam' => 'nullable|integer|min:0',
            'sks_praktikum_siang' => 'nullable|integer|min:0',
            'sks_praktikum_malam' => 'nullable|integer|min:0',

            'mata_kuliah' => 'nullable|string',
            'kegiatan'    => 'nullable|string',

            'seminar_jumlah' => 'nullable|integer',
            'seminar_keterangan' => 'nullable|string',

            'pembimbing_jumlah' => 'nullable|integer',
            'pembimbing_keterangan' => 'nullable|string',

            'penguji_jumlah' => 'nullable|integer',
            'penguji_keterangan' => 'nullable|string',

            'kkl_jumlah' => 'nullable|integer',
            'kkl_keterangan' => 'nullable|string',

            'tugas_luar_jumlah' => 'nullable|integer',
            'tugas_luar_keterangan' => 'nullable|string',


            // foto
            'foto'   => 'nullable|array|max:3',
            'foto.*' => 'image|max:2048',
        ]);

        $presensi = PresensiModel::with(['user.dataDiri'])
            ->where('id_presensi', $request->id_presensi)
            ->firstOrFail();
        if ($presensi->jam_pulang) {
            return redirect()->route('admin.presensi.pulang')
                ->with('error', 'Presensi sudah memiliki jam pulang.');
        }
        $user   = $presensi->user;

        // ==============================
        // LOKASI DEFAULT (DARI SETTING)
        // ==============================
        $lokasi = SettingLokasiPresensi::first();

        $lat  = $lokasi?->latitude;
        $long = $lokasi?->longitude;

        // ==============================
        // HITUNG DURASI & STATUS JAM
        // ==============================
        $durasiMenit = null;
        $statusJamKerja = 'hijau';

        $jamDatang = Carbon::parse($presensi->tanggal . ' ' . $presensi->jam_datang);
        $jamPulang = Carbon::parse($presensi->tanggal . ' ' . $request->jam_pulang);

        $durasiData = $this->presensiService->hitungDurasi(
            $jamDatang,
            $jamPulang
        );

        $jamWajib = $this->presensiService->getJamWajib($user, $presensi->tanggal);

        $statusJamKerja = $this->presensiService->getStatusJamKerja(
            $durasiData['durasi_jam'],
            $jamWajib
        );

        $durasiMenit = $durasiData['durasi_menit'];

        $alamatPulang = $this->locationService->getAlamatDariKoordinatOSM(
            $lat,
            $long
        );

        DB::transaction(function () use (
            $request,
            $user,
            $lat,
            $long,
            $durasiMenit,
            $presensi,
            $alamatPulang,
            $statusJamKerja
        ) {

            // ==============================
            // SIMPAN PRESENSI
            // ==============================

            $presensi->update([
                'jam_pulang' => $request->jam_pulang,

                'durasi_menit' => $durasiMenit,
                'lat_pulang' => $lat,
                'long_pulang' => $long,
                'jarak_pulang' => 0,
                'status_lokasi_pulang' => 'didalam_radius',
                'alamat_pulang' => $alamatPulang,
                'status_jam_kerja' => $statusJamKerja,
                'status_kehadiran' => 'hadir',
            ]);

            // ==============================
            // SIMPAN AKTIVITAS (jika ada pulang)
            // ==============================

            PresensiAktivitas::create([
                'id_presensi' => $presensi->id_presensi,

                'sks_siang' => $user->role == 'karyawan' ? $request->sks_siang : null,
                'sks_malam' => $user->role == 'karyawan' ? $request->sks_malam : null,
                'sks_praktikum_siang' => $user->role == 'karyawan' ? $request->sks_praktikum_siang : null,
                'sks_praktikum_malam' => $user->role == 'karyawan' ? $request->sks_praktikum_malam : null,

                'mata_kuliah' => $user->role == 'karyawan' ? $request->mata_kuliah : null,
                'kegiatan'    =>  $request->kegiatan,

                'seminar_jumlah' => $user->role == 'karyawan' ? $request->seminar_jumlah : null,
                'seminar_keterangan' => $user->role == 'karyawan' ? $request->seminar_keterangan : null,

                'pembimbing_jumlah' => $user->role == 'karyawan' ? $request->pembimbing_jumlah : null,
                'pembimbing_keterangan' => $user->role == 'karyawan' ? $request->pembimbing_keterangan : null,

                'penguji_jumlah' => $user->role == 'karyawan' ? $request->penguji_jumlah : null,
                'penguji_keterangan' => $user->role == 'karyawan' ? $request->penguji_keterangan : null,

                'kkl_jumlah' => $user->role == 'karyawan' ? $request->kkl_jumlah : null,
                'kkl_keterangan' => $user->role == 'karyawan' ? $request->kkl_keterangan : null,

                'tugas_luar_jumlah' => $user->role == 'karyawan' ? $request->tugas_luar_jumlah : null,
                'tugas_luar_keterangan' => $user->role == 'karyawan' ? $request->tugas_luar_keterangan : null,
            ]);

            // ==============================
            // UPLOAD FOTO (OPTIONAL)
            // ==============================
            if ($request->hasFile('foto')) {

                $lastDokumen = Dokumens::orderBy('nomor_dokumen', 'desc')->first();
                $lastNumber  = $lastDokumen ? (int)$lastDokumen->nomor_dokumen : 0;

                foreach ($request->file('foto') as $i => $file) {

                    $lastNumber++;
                    $nomorDokumen = str_pad($lastNumber, 7, '0', STR_PAD_LEFT);

                    $filename = time() . '_' . $i . '_' . $file->getClientOriginalName();
                    $path     = "{$user->npp}/presensi/{$presensi->tanggal}/{$filename}";

                    $result = app(\App\Services\GoogleDriveService::class)
                        ->uploadFileAndGetUrl($file->getPathname(), $path);

                    Dokumens::create([
                        'nomor_dokumen'  => $nomorDokumen,
                        'path_file'      => $path,
                        'file_id'        => $result['file_id'],
                        'view_url'       => $result['view_url'],
                        'download_url'   => $result['download_url'],
                        'preview_url'    => $result['preview_url'],
                        'id_user'        => $user->id_user,
                        'tanggal_upload' => now(),
                    ]);

                    PresensiDokumen::create([
                        'id_presensi'   => $presensi->id_presensi,
                        'nomor_dokumen' => $nomorDokumen,
                    ]);
                }
            }
        });

        return redirect()->route('admin.presensi.pulang')
            ->with('success', 'Presensi berhasil disimpan.');
    }


    public function storeSakit(Request $request)
    {
        $request->validate([
            'id_user' => 'required|exists:users,id_user',
            'tanggal' => 'required|date',
            'tanggal_sampai' => 'nullable|date|after_or_equal:tanggal',
            'keterangan' => 'nullable|string'
        ]);

        $idUser = $request->id_user;
        $tanggalMulai = Carbon::parse($request->tanggal);
        $tanggalSampai = $request->tanggal_sampai
            ? Carbon::parse($request->tanggal_sampai)
            : Carbon::parse($request->tanggal);

        $dates = [];

        // =============================
        // Generate semua tanggal
        // =============================
        $currentDate = $tanggalMulai->copy();

        while ($currentDate->lte($tanggalSampai)) {

            // Jika tanggal_sampai diisi, skip sabtu & minggu
            if ($request->tanggal_sampai) {
                if ($currentDate->isSaturday() || $currentDate->isSunday()) {
                    $currentDate->addDay();
                    continue;
                }
            }

            $dates[] = $currentDate->copy();
            $currentDate->addDay();
        }

        // =============================
        // CEK DUPLIKAT PRESENSI
        // =============================
        foreach ($dates as $date) {
            $exists = PresensiModel::where('id_user', $idUser)
                ->whereDate('tanggal', $date->toDateString())
                ->exists();

            if ($exists) {
                return back()->with(
                    'error',
                    'Presensi tanggal ' .
                        $date->translatedFormat('d F Y') .
                        ' sudah ada.'
                );
            }
        }

        // =============================
        // SIMPAN SEMUA DALAM TRANSACTION
        // =============================
        DB::transaction(function () use ($dates, $idUser, $request) {

            foreach ($dates as $date) {
                PresensiModel::create([
                    'id_user' => $idUser,
                    'tanggal' => $date->toDateString(),
                    'status_kehadiran' => 'sakit',
                    'keterangan' => $request->keterangan,
                ]);
            }
        });

        return redirect()
            ->route('admin.presensi')
            ->with('success', 'Presensi sakit berhasil disimpan.');
    }


    public function storeIzin(Request $request)
    {
        $request->validate([
            'id_user' => 'required|exists:users,id_user',
            'tanggal' => 'required|date',
            'tanggal_sampai' => 'nullable|date|after_or_equal:tanggal',
            'keterangan' => 'nullable|string'
        ]);

        $idUser = $request->id_user;
        $tanggalMulai = Carbon::parse($request->tanggal);
        $tanggalSampai = $request->tanggal_sampai
            ? Carbon::parse($request->tanggal_sampai)
            : Carbon::parse($request->tanggal);

        $dates = [];

        // =============================
        // Generate semua tanggal
        // =============================

        $currentDate = $tanggalMulai->copy();

        while ($currentDate->lte($tanggalSampai)) {

            // Jika tanggal_sampai diisi, skip sabtu & minggu
            if ($request->tanggal_sampai) {
                if ($currentDate->isSaturday() || $currentDate->isSunday()) {
                    $currentDate->addDay();
                    continue;
                }
            }

            $dates[] = $currentDate->copy();
            $currentDate->addDay();
        }

        // =============================
        // CEK DUPLIKAT PRESENSI
        // =============================
        foreach ($dates as $date) {
            $exists = PresensiModel::where('id_user', $idUser)
                ->whereDate('tanggal', $date->toDateString())
                ->exists();

            if ($exists) {
                return back()->with(
                    'error',
                    'Presensi tanggal ' .
                        $date->translatedFormat('d F Y') .
                        ' sudah ada.'
                );
            }
        }

        // =============================
        // SIMPAN SEMUA DALAM TRANSACTION
        // =============================
        DB::transaction(function () use ($dates, $idUser, $request) {

            foreach ($dates as $date) {
                PresensiModel::create([
                    'id_user' => $idUser,
                    'tanggal' => $date->toDateString(),
                    'status_kehadiran' => 'izin',
                    'keterangan' => $request->keterangan,
                ]);
            }
        });

        return redirect()
            ->route('admin.presensi')
            ->with('success', 'Presensi izin berhasil disimpan.');
    }
}
