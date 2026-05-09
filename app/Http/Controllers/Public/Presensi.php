<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Presensi as PresensiModel;
use App\Models\PresensiAktivitas;
use App\Models\SettingLokasiPresensi;
use App\Models\StrukturalUsers;
use App\Models\User;
use App\Services\LocationService;
use App\Services\PresensiService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class Presensi extends Controller
{

    protected LocationService $locationService;
    protected PresensiService $presensiService;

    public function __construct(
        LocationService $locationService,
        PresensiService $presensiService,
    ) {
        $this->locationService = $locationService;
        $this->presensiService = $presensiService;
    }

    public function index(Request $request)
    {
        $today = Carbon::today();

        // =============================
        // DAFTAR PRESENSI HARI INI
        // =============================
        $daftarPresensi = PresensiModel::with(['user.dataDiri'])
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
        $data = [
            'title' => 'Presensi Pegawai',
            'page' => 'Presensi Pegawai',
            'selected' => 'Presensi Pegawai',
            'daftarPresensi' => $daftarPresensi
        ];
        return view('admin.presensi.public.index', $data);
    }

    public function storeMasuk(Request $request, User $user)
    {
        if ($request->filled('id_user')) {
            $user = User::findOrFail($request->input('id_user'));
        }

        $tanggal = now()->toDateString();
        $sudahMasuk = PresensiModel::where('id_user', $user->id_user)
            ->whereDate('tanggal', $tanggal)
            ->exists();

        if ($sudahMasuk) {
            return back()->with('error', 'Anda sudah melakukan presensi masuk hari ini.');
        }

        $lokasi = SettingLokasiPresensi::first();
        if (! $lokasi) {
            return back()->with('error', 'Pengaturan lokasi presensi belum disiapkan.');
        }

        $alamatDatang = $this->locationService->getAlamatDariKoordinatOSM(
            $lokasi->latitude,
            $lokasi->longitude
        );

        $presensi = PresensiModel::create([
            'id_user' => $user->id_user,
            'tanggal' => $tanggal,
            'jam_datang' => now()->format('H:i:s'),
            'lat_datang' => $lokasi->latitude,
            'long_datang' => $lokasi->longitude,
            'alamat_datang' => $alamatDatang,
            'jarak_datang' => 0,
            'status_lokasi_datang' => 'didalam_radius',
            'status_kehadiran' => 'hadir',
        ]);

        $this->clearPresensiCache($user->id_user, $presensi->tanggal);

        return redirect()->route('public.presensi')->with('success', 'Presensi masuk berhasil dicatat.');
    }

    /**
     * Handle public login and presensi flow.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $login = $request->input('email');
        $user = User::where('email', $login)
            ->orWhere('npp', $login)
            ->first();

        if (! $user || ! in_array($user->role, ['dosen', 'karyawan'])) {
            return back()->with('error', 'User tidak ditemukan.');
        }

        $credentials = ['password' => $request->password];
        if ($user->email === $login) {
            $credentials['email'] = $login;
        } else {
            $credentials['npp'] = $login;
        }

        if (! Auth::validate($credentials)) {
            return back()->with('error', 'Email/NPP dan password salah.');
        }

        $tanggal = now()->toDateString();
        $presensi = PresensiModel::where('id_user', $user->id_user)
            ->whereDate('tanggal', $tanggal)
            ->first();

        if (! $presensi) {
            $request->merge(['id_user' => $user->id_user]);
            return $this->storeMasuk($request, $user);
        }

        if ($presensi->jam_pulang) {
            return back()->with('error', 'Anda telah presensi hari ini.');
        }

        $request->merge(['id_user' => $user->id_user]);
        return $this->pulang($request);
    }

    public function pulang(Request $request)
    {
        $request->validate([
            'id_user' => 'required|exists:users,id_user',
        ]);

        $user = User::with('dataDiri')->findOrFail($request->input('id_user'));
        if (! in_array($user->role, ['dosen', 'karyawan'])) {
            return back()->with('error', 'User tidak ditemukan.');
        }

        $presensi = PresensiModel::with('user.dataDiri')
            ->where('id_user', $user->id_user)
            ->whereDate('tanggal', now()->toDateString())
            ->first();

        if (! $presensi) {
            return back()->with('error', 'Data presensi hari ini tidak ditemukan.');
        }

        if ($presensi->jam_pulang) {
            return back()->with('error', 'Anda telah presensi hari ini.');
        }

        $isStruktural = false;
        if ($user->role === 'dosen') {
            $isStruktural = StrukturalUsers::where('id_user', $user->id_user)->exists();
        }


        return view('admin.presensi.public.pulang', [
            'title' => 'Presensi Pulang',
            'page' => 'Presensi Pulang',
            'selected' => 'Presensi Pegawai',
            'user' => $user,
            'presensi' => $presensi,
            'isStruktural' => $isStruktural,
        ]);
    }

    public function storePulang(Request $request)
    {
        $request->validate([
            'id_presensi' => 'required|exists:presensis,id_presensi',
            'id_user' => 'required|exists:users,id_user',
        ]);

        $user = User::with('dataDiri')->findOrFail($request->input('id_user'));
        if (! in_array($user->role, ['dosen', 'karyawan'])) {
            return back()->with('error', 'User tidak ditemukan.');
        }

        $presensi = PresensiModel::with('user.dataDiri')
            ->where('id_presensi', $request->input('id_presensi'))
            ->firstOrFail();

        if ($presensi->id_user != $user->id_user) {
            return back()->with('error', 'Data presensi tidak sesuai.');
        }

        if ($presensi->jam_pulang) {
            return back()->with('error', 'Anda telah presensi pulang hari ini.');
        }

        $lokasi = SettingLokasiPresensi::first();
        if (! $lokasi) {
            return back()->with('error', 'Pengaturan lokasi presensi belum disiapkan.');
        }

        $jamDatang = Carbon::parse($presensi->tanggal . ' ' . $presensi->jam_datang);
        $jamPulang = Carbon::now();
        $durasiData = $this->presensiService->hitungDurasi(
            $jamDatang,
            $jamPulang
        );

        $jamWajib = $this->presensiService->getJamWajib($user, $presensi->tanggal);
        $statusJamKerja = $this->presensiService->getStatusJamKerja(
            $durasiData['durasi_jam'],
            $jamWajib
        );

        $alamatPulang = $this->locationService->getAlamatDariKoordinatOSM(
            $lokasi->latitude,
            $lokasi->longitude
        );

        $presensi->update([
            'jam_pulang' => $jamPulang->format('H:i:s'),
            'durasi_menit' => $durasiData['durasi_menit'],
            'lat_pulang' => $lokasi->latitude,
            'long_pulang' => $lokasi->longitude,
            'jarak_pulang' => 0,
            'status_lokasi_pulang' => 'didalam_radius',
            'alamat_pulang' => $alamatPulang,
            'status_jam_kerja' => $statusJamKerja,
            'status_kehadiran' => 'hadir',
        ]);

        PresensiAktivitas::create([
            'id_presensi' => $presensi->id_presensi,

            'sks_siang' => $user->role == 'dosen' ? $request->sks_siang : null,
            'sks_malam' => $user->role == 'dosen' ? $request->sks_malam : null,
            'sks_praktikum_siang' => $user->role == 'dosen' ? $request->sks_praktikum_siang : null,
            'sks_praktikum_malam' => $user->role == 'dosen' ? $request->sks_praktikum_malam : null,

            'mata_kuliah' => $user->role == 'dosen' ? $request->mata_kuliah : null,
            'kegiatan'    =>  $request->kegiatan,

            'seminar_jumlah' => $user->role == 'dosen' ? $request->seminar_jumlah : null,
            'seminar_keterangan' => $user->role == 'dosen' ? $request->seminar_keterangan : null,

            'pembimbing_jumlah' => $user->role == 'dosen' ? $request->pembimbing_jumlah : null,
            'pembimbing_keterangan' => $user->role == 'dosen' ? $request->pembimbing_keterangan : null,

            'penguji_jumlah' => $user->role == 'dosen' ? $request->penguji_jumlah : null,
            'penguji_keterangan' => $user->role == 'dosen' ? $request->penguji_keterangan : null,

            'kkl_jumlah' => $user->role == 'dosen' ? $request->kkl_jumlah : null,
            'kkl_keterangan' => $user->role == 'dosen' ? $request->kkl_keterangan : null,

            'tugas_luar_jumlah' => $user->role == 'dosen' ? $request->tugas_luar_jumlah : null,
            'tugas_luar_keterangan' => $user->role == 'dosen' ? $request->tugas_luar_keterangan : null,
        ]);

        $this->clearPresensiCache($user->id_user, $presensi->tanggal);

        return redirect()->route('public.presensi')->with('success', 'Presensi pulang berhasil dicatat.');
    }

    private function clearPresensiCache($userId, $tanggal)
    {
        $bulan = Carbon::parse($tanggal)->month;
        $tahun = Carbon::parse($tanggal)->year;

        $cacheKey = "presensi_avg_{$userId}_{$bulan}_{$tahun}";

        Cache::forget($cacheKey);
    }
}
