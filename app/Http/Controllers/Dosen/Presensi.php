<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Dokumens;
use App\Models\Presensi as PresensiModel;
use App\Models\PresensiAktivitas;
use App\Models\PresensiDokumen;
use App\Models\SettingLokasiPresensi;
use App\Models\StrukturalUsers;
use App\Models\User;
use App\Services\LocationService;
use App\Services\PresensiService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Settings;
use App\Services\Kmeans\KedisiplinanService;


class Presensi extends Controller
{
    protected LocationService $locationService;
    protected PresensiService $presensiService;
    protected KedisiplinanService $kedisiplinanService;

    public function __construct(
        LocationService $locationService,
        PresensiService $presensiService,
        KedisiplinanService $kedisiplinanService
    ) {
        $this->locationService = $locationService;
        $this->presensiService = $presensiService;
        $this->kedisiplinanService = $kedisiplinanService;
    }


    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $today = Carbon::today();
        $bulan = Carbon::now()->month;
        $tahun = Carbon::now()->year;
        $userId = Auth::id();

        $cacheKey = "presensi_avg_{$userId}_{$bulan}_{$tahun}";
        $avgData = Cache::remember($cacheKey, now()->addHours(12), function () use ($userId, $bulan, $tahun) {

            $data = PresensiModel::where('id_user', $userId)
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


        // =============================
        // PRESENSI USER LOGIN HARI INI
        // =============================
        $presensiHariIni = PresensiModel::where('id_user', Auth::id())
            ->whereDate('tanggal', $today)
            ->first();

        // =============================
        // DAFTAR PRESENSI HARI INI
        // =============================
        $daftarPresensiHariIni = PresensiModel::with(['user.dataDiri'])
            ->whereDate('tanggal', $today)
            ->orderBy('jam_datang', 'asc')
            ->get()
            ->map(function ($item) {

                // =============================
                // DURASI DARI durasi_menit
                // =============================
                if (!is_null($item->durasi_menit)) {
                    $jam   = intdiv($item->durasi_menit, 60);
                    $menit = $item->durasi_menit % 60;
                    $durasi = sprintf('%02d:%02d:00', $jam, $menit);
                } else {
                    $durasi = '00:00:00';
                }

                $item->durasi = $durasi;

                return $item;
            });


        // lokasi kampus
        $lokasiKampus = SettingLokasiPresensi::first();

        // cek struktural dosen
        $dosenStrukturalAktif = StrukturalUsers::where('id_user', Auth::id())
            ->where('status', 'aktif')
            ->whereDate('tanggal_selesai', '>=', $today)
            ->exists();


        // =============================
        // DATA UNTUK VIEW
        // =============================



        $cluster = $this->kedisiplinanService
            ->getClusterUser($bulan, $tahun, Auth::id());

        $kedisiplinan = $this->kedisiplinanService
            ->mappingCluster($cluster);

        $data = [
            'title' => 'Presensi',
            'page' => 'Presensi',
            'selected' => 'Presensi',
            'presensiHariIni' => $presensiHariIni,
            'daftarPresensiHariIni' => $daftarPresensiHariIni,
            'tanggalHariIni' => $today->translatedFormat('l, d F Y'),
            'jamSekarang' => Carbon::now()->format('H:i:s'),
            'lokasiKampus' => $lokasiKampus,
            'isStruktural' => $dosenStrukturalAktif,
            'avgJamMasuk' => $avgData['avgJamMasuk'],
            'avgJamPulang' => $avgData['avgJamPulang'],
            'avgJamKerja' => $avgData['avgJamKerja'],
            'bulan' => Carbon::now()->translatedFormat('F'),
            'kedisiplinan' => $kedisiplinan['label'],
            'warnaKedisiplinan' => $kedisiplinan['color'],
        ];

        return view('dosen.presensi.index', $data);
    }

    public function storeMasuk(Request $request)
    {
        $request->validate([
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $user = Auth::user();

        // Cegah double presensi masuk
        $sudahMasuk = PresensiModel::where('id_user', $user->id_user)
            ->whereDate('tanggal', now()->toDateString())
            ->exists();

        if ($sudahMasuk) {
            return back()->with('error', 'Anda sudah melakukan presensi masuk hari ini.');
        }

        // Ambil lokasi presensi (setting admin)
        $lokasi = SettingLokasiPresensi::first();

        // Hitung jarak
        $jarak = $this->locationService->hitungJarak(
            $request->latitude,
            $request->longitude,
            $lokasi->latitude,
            $lokasi->longitude
        );

        $alamatDatang = $this->locationService->getAlamatDariKoordinatOSM(
            $request->latitude,
            $request->longitude
        );



        $presensi = PresensiModel::create([
            'id_user'              => $user->id_user,
            'tanggal'              => now()->toDateString(),
            'jam_datang'           => now()->format('H:i:s'),
            'lat_datang'      => $request->latitude,
            'long_datang'     => $request->longitude,
            'alamat_datang'     => $alamatDatang,
            'jarak_datang'   => $jarak,
            'status_lokasi_datang' => $jarak <= $lokasi->radius_meter
                ? 'didalam_radius'
                : 'diluar_radius',
        ]);
        $this->clearPresensiCache($user->id_user, $presensi->tanggal);

        return redirect()->route('dosen.presensi')->with('success', 'Presensi masuk berhasil dicatat.');
    }



    public function pulang()
    {
        $today = Carbon::today();

        // =============================
        // PRESENSI HARI INI USER LOGIN
        // =============================
        $presensiHariIni = PresensiModel::where('id_user', Auth::id())
            ->whereDate('tanggal', $today)
            ->first();

        // =============================
        // VALIDASI LOGIKA
        // =============================
        if (!$presensiHariIni) {
            return redirect()
                ->route('dosen.presensi')
                ->with('error', 'Anda belum melakukan presensi masuk.');
        }

        if ($presensiHariIni->jam_pulang) {
            return redirect()
                ->route('dosen.presensi')
                ->with('error', 'Presensi pulang sudah dilakukan.');
        }

        if ($presensiHariIni->status_kehadiran == 'izin' || $presensiHariIni->status_kehadiran == 'sakit' || $presensiHariIni->status_kehadiran == 'alpha') {
            return redirect()
                ->route('dosen.presensi')
                ->with('error', 'tidak dapat melakukan presensi.');
        }



        // =============================
        // CEK DOSEN STRUKTURAL AKTIF
        // =============================
        $todayDate = Carbon::today()->toDateString();

        $dosenStrukturalAktif = StrukturalUsers::where('id_user', Auth::id())
            ->where('status', 'aktif')
            ->whereDate('tanggal_selesai', '>=', $todayDate)
            ->exists();


        // lokasi kampus
        $lokasiKampus = SettingLokasiPresensi::first();

        // =============================
        // DATA UNTUK VIEW
        // =============================
        $data = [
            'title'            => 'Presensi Pulang',
            'page'             => 'Presensi',
            'selected'         => 'Presensi',
            'presensiHariIni'  => $presensiHariIni,
            'jamSekarang'      => Carbon::now()->format('H:i:s'),
            'isStruktural'     => $dosenStrukturalAktif,
            'lokasiKampus'     => $lokasiKampus,
        ];

        return view('dosen.presensi.pulang', $data);
    }



    public function storePulang(Request $request)
    {
        $request->validate([
            // lokasi
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',

            // aktivitas
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

            // foto bukti
            'foto'   => 'nullable|array|max:3',
            'foto.*' => 'image|max:2048',
        ]);


        $user  = Auth::user();
        $today = now()->toDateString();

        // =============================
        // AMBIL PRESENSI HARI INI
        // =============================
        $presensi = PresensiModel::where('id_user', $user->id_user)
            ->whereDate('tanggal', $today)
            ->firstOrFail();

        if ($presensi->jam_pulang) {
            return back()->with('error', 'Presensi pulang sudah dilakukan.');
        }
        if ($presensi->status_kehadiran == 'izin' || $presensi->status_kehadiran == 'sakit' || $presensi->status_kehadiran == 'alpha') {
            return redirect()
                ->route('dosen.presensi')
                ->with('error', 'tidak dapat melakukan presensi.');
        }

        // =============================
        // LOKASI KAMPUS
        // =============================
        $lokasi = SettingLokasiPresensi::first();

        $jarak = $this->locationService->hitungJarak(
            $request->latitude,
            $request->longitude,
            $lokasi->latitude,
            $lokasi->longitude
        );

        $alamatPulang = $this->locationService->getAlamatDariKoordinatOSM(
            $request->latitude,
            $request->longitude
        );




        // =============================
        // HITUNG DURASI
        // =============================
        $jamDatang = Carbon::parse($presensi->jam_datang);
        $jamPulang = Carbon::now();
        $durasiData = $this->presensiService->hitungDurasi(
            $jamDatang,
            $jamPulang
        );

        $jamWajib = $this->presensiService->getJamWajib($user, $today);

        $statusJamKerja = $this->presensiService->getStatusJamKerja(
            $durasiData['durasi_jam'],
            $jamWajib
        );
        $durasiMenit = $durasiData['durasi_menit'];


        DB::transaction(function () use (
            $request,
            $presensi,
            $jamPulang,
            $durasiMenit,
            $jarak,
            $alamatPulang,
            $lokasi,
            $user,
            $statusJamKerja
        ) {

            // =============================
            // UPDATE PRESENSI
            // =============================
            $presensi->update([
                'jam_pulang'    => $jamPulang->format('H:i:s'),
                'lat_pulang'    => $request->latitude,
                'long_pulang'   => $request->longitude,
                'alamat_pulang' => $alamatPulang,
                'jarak_pulang'  => $jarak,
                'durasi_menit'  => $durasiMenit,
                'status_lokasi_pulang' => $jarak <= $lokasi->radius_meter
                    ? 'didalam_radius'
                    : 'diluar_radius',
                'status_jam_kerja' => $statusJamKerja,
            ]);

            // =============================
            // SIMPAN AKTIVITAS
            // =============================
            PresensiAktivitas::create([
                'id_presensi' => $presensi->id_presensi,
                'sks_siang'   => $request->sks_siang,
                'sks_malam'   => $request->sks_malam,
                'sks_praktikum_siang' => $request->sks_praktikum_siang,
                'sks_praktikum_malam' => $request->sks_praktikum_malam,
                'mata_kuliah' => $request->mata_kuliah,
                'kegiatan'    => $request->kegiatan,

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

            // =============================
            // UPLOAD FOTO BUKTI (MAX 3)
            // =============================
            if ($request->hasFile('foto')) {
                $lastDokumen = Dokumens::orderBy('nomor_dokumen', 'desc')->first();
                $lastNumber  = $lastDokumen ? (int)$lastDokumen->nomor_dokumen : 0;

                foreach ($request->file('foto') as $i => $file) {
                    $lastNumber++;
                    $nomorDokumen = str_pad($lastNumber, 7, '0', STR_PAD_LEFT);

                    $originalName = $file->getClientOriginalName();
                    $filename     = time() . '_' . $i . '_' . $originalName;
                    $path         = "{$user->npp}/presensi/{$presensi->tanggal}/{$filename}";

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
        $this->clearPresensiCache($user->id_user, $presensi->tanggal);

        return redirect()->route('dosen.presensi')
            ->with('success', 'Presensi pulang & aktivitas berhasil disimpan.');
    }
    public function cekPresensi(Request $request)
    {
        $user = User::with('struktural')->find(Auth::id());
        $role = $user->role;
        $userId = $user->id_user;
        $periode = $request->get('periode');

        if (is_null($periode)) {
            $periode = now()->format('Y-m');
        }


        $tanggal = Carbon::createFromFormat('Y-m', $periode);

        $bulan = $tanggal->month;
        $tahun = $tanggal->year;
        $memenuhi = 0;
        $tidakMemenuhi = 0;
        $presensis = PresensiModel::where('id_user', $userId)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'desc')
            ->get()
            ->map(function ($item) use ($user, $role, &$memenuhi, &$tidakMemenuhi) {

                if (!is_null($item->durasi_menit)) {
                    $jam   = intdiv($item->durasi_menit, 60);
                    $menit = $item->durasi_menit % 60;
                    $item->durasi = sprintf('%02d:%02d:00', $jam, $menit);
                } else {
                    $item->durasi = '00:00:00';
                }

                // =========================
                // Hitung pemenuhan jam kerja
                // =========================
                if ($item->status_kehadiran === 'hadir') {

                    $jamWajib = 360;

                    foreach ($user->struktural as $struktural) {

                        if (
                            $struktural->status === 'aktif' &&
                            $item->tanggal >= $struktural->tanggal_mulai &&
                            (
                                $struktural->tanggal_selesai === null ||
                                $item->tanggal <= $struktural->tanggal_selesai
                            )
                        ) {
                            $jamWajib = 420;
                            break;
                        }
                    }


                    if (($item->durasi_menit ?? 0) >= $jamWajib) {
                        $memenuhi++;
                    } else {
                        $tidakMemenuhi++;
                    }
                }

                return $item;
            });

        // ================= REKAP STATUS KEHADIRAN =================
        $jumlahHadir = $presensis->where('status_kehadiran', 'hadir')->count();

        $jumlahSakit = $presensis->where('status_kehadiran', 'sakit')->count();

        $jumlahIzin = $presensis->where('status_kehadiran', 'izin')->count();

        return view('dosen.presensi.cek', [
            'page'      => 'Presensi',
            'selected'  => 'Presensi',
            'title'     => 'Cek Presensi',
            'presensis' => $presensis,
            'bulan'     => $bulan,
            'tahun'     => $tahun,
            'label'     => $tanggal->translatedFormat('F Y'),
            'jumlahHadir'   => $jumlahHadir,
            'jumlahSakit'   => $jumlahSakit,
            'jumlahIzin'    => $jumlahIzin,
            'memenuhi' => $memenuhi,
            'tidakMemenuhi' => $tidakMemenuhi,
        ]);
    }

    public function detailPresensi($id)
    {
        $today = Carbon::today();
        $userId = Auth::id();

        $presensi = PresensiModel::with(['user.dataDiri', 'aktivitas', 'dokumen'])
            ->where('id_user', $userId)
            ->where('id_presensi', $id)
            ->firstOrFail();

        if ($userId != $presensi->id_user) {
            return redirect()->route('dosen.presensi.cek')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        $lokasiKampus = SettingLokasiPresensi::first();
        $isStruktural = StrukturalUsers::where('id_user', $userId)
            ->where('status', 'aktif')
            ->whereDate('tanggal_selesai', $presensi->tanggal)
            ->exists();

        // dd($presensi);
        return view('dosen.presensi.detail', [
            'page'      => 'Presensi',
            'selected'  => 'Presensi',
            'title'     => 'Detail Presensi',
            'presensi'  => $presensi,
            'lokasiKampus' => $lokasiKampus,
            'isStruktural' => $isStruktural,
        ]);
    }

    private function clearPresensiCache($userId, $tanggal)
    {
        $bulan = Carbon::parse($tanggal)->month;
        $tahun = Carbon::parse($tanggal)->year;

        $cacheKey = "presensi_avg_{$userId}_{$bulan}_{$tahun}";

        Cache::forget($cacheKey);
    }
    public function cetakPdf(Request $request)
    {
        $periode = $request->periode ?? now()->format('Y-m');

        $tanggal = Carbon::createFromFormat('Y-m', $periode);
        $bulan   = $tanggal->month;
        $tahun   = $tanggal->year;



        /** =========================
         *  DATA USER
         *  ========================= */

        $user = User::with(['struktural', 'dataDiri'])->where('id_user', Auth::id())->firstOrFail();
        $role = $user->role;

        /** =========================
         *  DATA PRESENSI
         *  ========================= */
        $presensis = PresensiModel::where('id_user', $user->id_user)
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
            ->orderBy('tanggal', 'asc')
            ->get()
            ->map(function ($p) use ($role) {

                // Format tanggal
                $p->tanggal_label = Carbon::parse($p->tanggal)->translatedFormat('d-m-Y');

                // Datang & Pulang
                $p->datang = $p->jam_datang ?? '-';
                $p->pulang = $p->jam_pulang ?? '-';

                // Durasi
                if (!is_null($p->durasi_menit)) {
                    $jam   = intdiv($p->durasi_menit, 60);
                    $menit = $p->durasi_menit % 60;
                    $p->durasi = sprintf('%02d:%02d:00', $jam, $menit);
                } else {
                    $p->durasi = '-';
                }

                // Jika bukan hadir
                if ($p->status_kehadiran !== 'hadir') {
                    $p->keterangan = ucfirst($p->status_kehadiran);
                } else {
                    $p->keterangan = null;
                }

                // Khusus karyawan → buang kolom aktivitas
                if ($role === 'karyawan') {
                    $p->aktivitas = null;
                }

                return $p;
            });

        /** =========================
         *  REKAP STATUS
         *  ========================= */
        $rekap = [
            'hadir' => $presensis->where('status_kehadiran', 'hadir')->count(),
            'sakit' => $presensis->where('status_kehadiran', 'sakit')->count(),
            'izin'  => $presensis->where('status_kehadiran', 'izin')->count(),
        ];

        /** =========================
         *  HEADER / KOP PDF
         *  ========================= */
        $setting = Settings::first();

        // === LOGO HANDLING (sama seperti laporan pegawai)
        $original = public_path('storage/logo/' . ($setting->logo ?? ''));
        $alt      = storage_path('app/public/logo/' . ($setting->logo ?? ''));

        $path = is_file($original) ? $original : (is_file($alt) ? $alt : null);

        $logoFileSrc   = null;
        $logoDataUri   = null;
        $logoPngData64 = null;

        if ($path) {
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            if ($ext === 'webp' && function_exists('imagecreatefromwebp')) {
                if ($im = @imagecreatefromwebp($path)) {
                    ob_start();
                    imagepng($im, null, 9);
                    imagedestroy($im);
                    $png = ob_get_clean();
                    if ($png) {
                        $logoPngData64 = 'data:image/png;base64,' . base64_encode($png);
                    }
                }
            } else {
                $bytes = @file_get_contents($path);
                if ($bytes !== false) {
                    $mime = mime_content_type($path) ?: 'image/png';
                    $logoDataUri = 'data:' . $mime . ';base64,' . base64_encode($bytes);
                }
            }

            $logoFileSrc = 'file://' . $path;
        }

        $totalDurasiMenit = 0;

        $totalSS  = 0;
        $totalSM  = 0;
        $totalPS  = 0;
        $totalPM  = 0;
        $totalSem = 0;
        $totalBim = 0;
        $totalUji = 0;
        $totalKKL = 0;
        $totalTL  = 0;

        foreach ($presensis as $p) {

            if ($p->durasi_menit) {
                $totalDurasiMenit += $p->durasi_menit;
            }

            if ($p->aktivitas) {
                $totalSS  += $p->aktivitas->sks_siang ?? 0;
                $totalSM  += $p->aktivitas->sks_malam ?? 0;
                $totalPS  += $p->aktivitas->sks_praktikum_siang ?? 0;
                $totalPM  += $p->aktivitas->sks_praktikum_malam ?? 0;
                $totalSem += $p->aktivitas->seminar_jumlah ?? 0;
                $totalBim += $p->aktivitas->pembimbing_jumlah ?? 0;
                $totalUji += $p->aktivitas->penguji_jumlah ?? 0;
                $totalKKL += $p->aktivitas->kkl_jumlah ?? 0;
                $totalTL  += $p->aktivitas->tugas_luar_jumlah ?? 0;
            }
        }

        $jam   = intdiv($totalDurasiMenit, 60);
        $menit = $totalDurasiMenit % 60;

        $totalDurasi = sprintf('%02d:%02d:00', $jam, $menit);
        $memenuhi = 0;
        $tidakMemenuhi = 0;

        foreach ($presensis as $p) {

            if ($p->status_kehadiran !== 'hadir') {
                continue;
            }

            $jamWajib = 480;

            if ($role === 'dosen') {

                $jamWajib = 360;

                foreach ($user->struktural as $struktural) {

                    if (
                        $struktural->status === 'aktif' &&
                        $p->tanggal >= $struktural->tanggal_mulai &&
                        (
                            $struktural->tanggal_selesai === null ||
                            $p->tanggal <= $struktural->tanggal_selesai
                        )
                    ) {
                        $jamWajib = 420;
                        break;
                    }
                }
            }

            if (($p->durasi_menit ?? 0) >= $jamWajib) {
                $memenuhi++;
            } else {
                $tidakMemenuhi++;
            }
        }

        /** =========================
         *  EXPORT PDF
         *  ========================= */
        $title = 'Rekap Presensi Pegawai';

        $pdf = PDF::setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => true,
            'chroot'               => public_path(),
            'tempDir'              => storage_path('app/dompdf_temp'),
            'fontDir'              => storage_path('app/dompdf_font'),
        ])->loadView('dosen.presensi.pdf', [
            'title'          => $title,
            'user'           => $user,
            'role'           => $role,
            'periode'        => $tanggal->translatedFormat('F Y'),
            'presensis'      => $presensis,
            'rekap'          => $rekap,
            'setting'        => $setting,
            'logoFileSrc'    => $logoFileSrc,
            'logoDataUri'    => $logoDataUri,
            'logoPngData64'  => $logoPngData64,
            'totalDurasi' => $totalDurasi,
            'totalSS' => $totalSS,
            'totalSM' => $totalSM,
            'totalPS' => $totalPS,
            'totalPM' => $totalPM,
            'totalSem' => $totalSem,
            'totalBim' => $totalBim,
            'totalUji' => $totalUji,
            'totalKKL' => $totalKKL,
            'totalTL' => $totalTL,
            'memenuhi' => $memenuhi,
            'tidakMemenuhi' => $tidakMemenuhi,
        ])->setPaper('A4', 'portrait');

        return $pdf->download(
            'rekap-presensi-' . $user->npp . '-' . $user->dataDiri->name . '-' . $tanggal->format('Y-m') . '.pdf'
        );
    }
}
