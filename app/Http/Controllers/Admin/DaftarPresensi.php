<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\SettingLokasiPresensi;
use App\Models\StrukturalUsers;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
        return view('admin.presensi.daftar.index', $data);
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
        return view('admin.presensi.daftar.bulan', $data);
    }

    public function dataBulan(Request $request)
    {
        $request->validate([
            'id_user' => 'required',
            'periode' => 'required'
        ]);

        $tanggal = Carbon::createFromFormat('Y-m', $request->periode);

        $role = User::where('id_user', $request->id_user)->value('role');

        $showSks = $role === 'dosen';

        $presensis = Presensi::where('id_user', $request->id_user)
            ->whereMonth('tanggal', $tanggal->month)
            ->whereYear('tanggal', $tanggal->year)
            ->orderBy('tanggal', 'desc')
            ->get()
            ->map(function ($item) use ($role) {

                if (!is_null($item->durasi_menit)) {
                    $jam   = intdiv($item->durasi_menit, 60);
                    $menit = $item->durasi_menit % 60;
                    $item->durasi = sprintf('%02d:%02d:00', $jam, $menit);
                } else {
                    $item->durasi = '00:00:00';
                }
                $item->tanggal_label = Carbon::parse($item->tanggal)->translatedFormat('d-m-Y');

                if ($role === 'admin') {
                    $item->aktivitas = null;
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
            'show_sks' => $showSks
        ]);
    }



    public function showBulan($id)
    {
        $today = Carbon::today();

        $presensi = Presensi::with(['user.dataDiri', 'aktivitas', 'dokumen'])
            ->where('id_presensi', $id)
            ->firstOrFail();

        $lokasiKampus = SettingLokasiPresensi::first();
        $isStruktural = StrukturalUsers::where('id_user', $presensi->id_user)
            ->where('status', 'aktif')
            ->whereDate('tanggal_selesai', '>=', $today)
            ->exists();

        return view('admin.presensi.daftar.detail', [
            'page'      => 'Presensi',
            'selected'  => 'Presensi',
            'title'     => 'Detail Presensi',
            'presensi'  => $presensi,
            'lokasiKampus' => $lokasiKampus,
            'isStruktural' => $isStruktural,
        ]);
    }
}
