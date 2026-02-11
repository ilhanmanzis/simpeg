<?php

namespace App\Http\Controllers\Karyawan;

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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

use function PHPUnit\Framework\isNull;

class Presensi extends Controller
{
    protected LocationService $locationService;
    protected PresensiService $presensiService;

    public function __construct(
        LocationService $locationService,
        PresensiService $presensiService
    ) {
        $this->locationService = $locationService;
        $this->presensiService = $presensiService;
    }
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $today = Carbon::today();

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

        // =============================
        // DATA UNTUK VIEW
        // =============================
        $data = [
            'title' => 'Presensi',
            'page' => 'Presensi',
            'selected' => 'Presensi',
            'presensiHariIni' => $presensiHariIni,
            'daftarPresensiHariIni' => $daftarPresensiHariIni,
            'tanggalHariIni' => $today->translatedFormat('l, d F Y'),
            'jamSekarang' => Carbon::now()->format('H:i:s'),
            'lokasiKampus' => $lokasiKampus,
        ];

        return view('karyawan.presensi.index', $data);
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



        PresensiModel::create([
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

        return redirect()->route('karyawan.presensi')->with('success', 'Presensi masuk berhasil dicatat.');
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
                ->route('karyawan.presensi')
                ->with('error', 'Anda belum melakukan presensi masuk.');
        }

        if ($presensiHariIni->jam_pulang) {
            return redirect()
                ->route('karyawan.presensi')
                ->with('error', 'Presensi pulang sudah dilakukan.');
        }

        if ($presensiHariIni->status_kehadiran == 'izin' || $presensiHariIni->status_kehadiran == 'sakit' || $presensiHariIni->status_kehadiran == 'alpha') {
            return redirect()
                ->route('karyawan.presensi')
                ->with('error', 'tidak dapat melakukan presensi.');
        }


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
            'lokasiKampus'     => $lokasiKampus,
        ];

        return view('karyawan.presensi.pulang', $data);
    }



    public function storePulang(Request $request)
    {
        $request->validate([
            // lokasi
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'kegiatan'    => 'nullable|string',
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
                ->route('karyawan.presensi')
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

        $jamWajib = $this->presensiService->getJamWajib(
            $user,
            $today
        );

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
                'kegiatan'    => $request->kegiatan,
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

        return redirect()->route('karyawan.presensi')
            ->with('success', 'Presensi pulang & aktivitas berhasil disimpan.');
    }

    public function cekPresensi(Request $request)
    {
        $userId = Auth::id();
        $periode = $request->get('periode');

        if (is_null($periode)) {
            $periode = now()->format('Y-m');
        }

        $tanggal = Carbon::createFromFormat('Y-m', $periode);

        $bulan = $tanggal->month;
        $tahun = $tanggal->year;

        $presensis = PresensiModel::where('id_user', $userId)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'desc')
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

                // =============================
                // STATUS WARNA (HIJAU / KUNING / MERAH)
                // =============================
                $durasiJam = 0;

                if (!is_null($item->durasi_menit)) {
                    $durasiJam = intdiv($item->durasi_menit, 60);
                }

                if ($durasiJam >= 6) {
                    $statusJamKerja = 'hijau';
                } elseif ($durasiJam >= 4) {
                    $statusJamKerja = 'kuning';
                } elseif (is_null($item->durasi_menit)) {
                    $statusJamKerja = '';
                } else {
                    $statusJamKerja = 'merah';
                }

                $item->durasi = $durasi;
                $item->status_jam_kerja = $statusJamKerja;

                return $item;
            });

        // ================= REKAP STATUS KEHADIRAN =================
        $jumlahHadir = PresensiModel::where('id_user', $userId)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('status_kehadiran', 'hadir')
            ->count();

        $jumlahSakit = PresensiModel::where('id_user', $userId)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('status_kehadiran', 'sakit')
            ->count();

        $jumlahIzin = PresensiModel::where('id_user', $userId)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('status_kehadiran', 'izin')
            ->count();

        return view('karyawan.presensi.cek', [
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

        $lokasiKampus = SettingLokasiPresensi::first();


        // dd($presensi);
        return view('karyawan.presensi.detail', [
            'page'      => 'Presensi',
            'selected'  => 'Presensi',
            'title'     => 'Detail Presensi',
            'presensi'  => $presensi,
            'lokasiKampus' => $lokasiKampus,
        ]);
    }
}
