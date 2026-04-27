<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\SettingLokasiPresensi;
use App\Models\StrukturalUsers;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DaftarPresensi extends Controller
{


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (is_null($request->get('tanggal'))) {
            $today = Carbon::today();
        } else {
            $today = $request->get('tanggal');
        }

        // =============================
        // DAFTAR PRESENSI HARI INI
        // =============================
        $daftarPresensi = Presensi::with(['user.dataDiri'])
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
            'title' => 'Daftar Presensi Pegawai',
            'page' => 'Daftar Presensi Pegawai',
            'selected' => 'Daftar Presensi Pegawai',
            'daftarPresensi' => $daftarPresensi
        ];
        return view('dosen.pimpinan.presensi.index', $data);
    }

    public function bulan()
    {
        $data = [
            'title' => 'Daftar Presensi Bulanan Pegawai',
            'page' => 'Daftar Presensi Pegawai',
            'selected' => 'Daftar Presensi Pegawai',
            'pegawais' => User::query()
                ->select('id_user', 'npp')
                ->whereIn('role', ['dosen', 'karyawan'])
                ->where('status_keaktifan', 'aktif')
                ->with('dataDiri:id_data_diri,id_user,name')
                ->get()
        ];
        return view('dosen.pimpinan.presensi.bulan', $data);
    }

    public function dataBulan(Request $request)
    {
        $request->validate([
            'id_user' => 'required',
            'periode' => 'required'
        ]);

        $tanggal = Carbon::createFromFormat('Y-m', $request->periode);

        $user = User::with('struktural')->where('id_user', $request->id_user)->first();
        $role = $user->role;

        $showSks = $role === 'dosen';

        $memenuhi = 0;
        $tidakMemenuhi = 0;
        $presensis = Presensi::where('id_user', $request->id_user)
            ->whereMonth('tanggal', $tanggal->month)
            ->whereYear('tanggal', $tanggal->year)
            ->orderBy('tanggal', 'desc')
            ->get()
            ->map(function ($item) use ($role, $user, &$memenuhi, &$tidakMemenuhi) {

                if (!is_null($item->durasi_menit)) {
                    $jam   = intdiv($item->durasi_menit, 60);
                    $menit = $item->durasi_menit % 60;
                    $item->durasi = sprintf('%02d:%02d:00', $jam, $menit);
                } else {
                    $item->durasi = '00:00:00';
                }

                $item->tanggal_label = Carbon::parse($item->tanggal)->translatedFormat('d-m-Y');

                // =========================
                // Hitung memenuhi / tidak
                // =========================
                if ($item->status_kehadiran === 'hadir') {

                    if ($role === 'karyawan') {

                        $jamWajib = 480;
                    } else {

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
                    }

                    if (($item->durasi_menit ?? 0) >= $jamWajib) {
                        $memenuhi++;
                    } else {
                        $tidakMemenuhi++;
                    }
                }

                return $item;
            });

        return response()->json([
            'status' => 'success',
            'data' => $presensis,
            'rekap' => [
                'hadir' => $presensis->where('status_kehadiran', 'hadir')->count(),
                'sakit' => $presensis->where('status_kehadiran', 'sakit')->count(),
                'izin'  => $presensis->where('status_kehadiran', 'izin')->count(),
            ],
            'label' => $tanggal->translatedFormat('F Y'),
            'role' => $role,
            'show_sks' => $showSks,
            'rekap_jam' => [
                'memenuhi' => $memenuhi,
                'tidak_memenuhi' => $tidakMemenuhi
            ],
        ]);
    }

    public function showBulan($id)
    {

        $presensi = Presensi::with(['user.dataDiri', 'aktivitas', 'dokumen'])
            ->where('id_presensi', $id)
            ->firstOrFail();

        $lokasiKampus = SettingLokasiPresensi::first();
        $isStruktural = StrukturalUsers::where('id_user', $presensi->id_user)
            ->where('status', 'aktif')
            ->whereDate('tanggal_selesai', $presensi->tanggal)
            ->exists();

        return view('dosen.pimpinan.presensi.detail', [
            'page' => 'Daftar Presensi Pegawai',
            'selected' => 'Daftar Presensi Pegawai',
            'title'     => 'Detail Presensi',
            'presensi'  => $presensi,
            'lokasiKampus' => $lokasiKampus,
            'isStruktural' => $isStruktural,
        ]);
    }
}
