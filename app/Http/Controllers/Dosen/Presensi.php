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
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class Presensi extends Controller
{
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
        $jarak = $this->hitungJarak(
            $request->latitude,
            $request->longitude,
            $lokasi->latitude,
            $lokasi->longitude
        );

        $alamatDatang = $this->getAlamatDariKoordinatOSM(
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

        return redirect()->route('dosen.presensi')->with('success', 'Presensi masuk berhasil dicatat.');
    }

    /**
     * Hitung jarak (meter) antara dua koordinat
     */
    private function hitungJarak($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }


    private function getAlamatDariKoordinatOSM($lat, $lng)
    {
        $response = Http::withHeaders([
            'User-Agent' => 'Presensi-App/1.0 (janggarfals1207@gmail.com)',
        ])->get('https://nominatim.openstreetmap.org/reverse', [
            'format' => 'json',
            'lat'    => $lat,
            'lon'    => $lng,
            'zoom'   => 18,
            'addressdetails' => 1,
        ]);

        if (!$response->successful()) {
            return null;
        }

        $data = $response->json();

        // 🔴 Fallback paling aman & lengkap
        if (!empty($data['display_name'])) {
            return $data['display_name'];
        }

        return null;
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

        // =============================
        // LOKASI KAMPUS
        // =============================
        $lokasi = SettingLokasiPresensi::first();

        $jarak = $this->hitungJarak(
            $request->latitude,
            $request->longitude,
            $lokasi->latitude,
            $lokasi->longitude
        );

        $alamatPulang = $this->getAlamatDariKoordinatOSM(
            $request->latitude,
            $request->longitude
        );


        // =============================
        // CEK STRUKTURAL AKTIF
        // =============================
        $isStruktural = StrukturalUsers::where('id_user', $user->id_user)
            ->where('status', 'aktif')
            ->whereDate('tanggal_selesai', '>=', $today)
            ->exists();

        $jamWajib = $isStruktural ? 7 : 6;


        // =============================
        // HITUNG DURASI
        // =============================
        $jamDatang = Carbon::parse($presensi->jam_datang);
        $jamPulang = Carbon::now();

        $durasiMenit = $jamDatang->diffInMinutes($jamPulang);
        $durasiJam   = intdiv($durasiMenit, 60);

        // =============================
        // STATUS JAM KERJA
        // =============================
        if ($durasiJam >= $jamWajib) {
            $statusJamKerja = 'hijau';
        } elseif ($durasiJam >= 4) {
            $statusJamKerja = 'kuning';
        } else {
            $statusJamKerja = 'merah';
        }

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

        return redirect()->route('dosen.presensi')
            ->with('success', 'Presensi pulang & aktivitas berhasil disimpan.');
    }
}
