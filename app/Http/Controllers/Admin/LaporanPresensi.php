<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Settings;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanPresensi extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = [
            'page' => 'Laporan Presensi',
            'selected' => 'Laporan',
            'title' => 'Laporan Presensi Pegawai',
            'pegawais' => User::query()
                ->select('id_user', 'npp')
                ->whereIn('role', ['dosen', 'karyawan'])
                ->where('status_keaktifan', 'aktif')
                ->with('dataDiri:id_data_diri,id_user,name')
                ->get()
        ];
        return view('admin.laporan.presensi.index', $data);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_user' => 'required|exists:users,id_user',
            'periode' => 'required|date_format:Y-m',
        ]);

        $periode = Carbon::createFromFormat('Y-m', $validated['periode']);
        $bulan   = $periode->month;
        $tahun   = $periode->year;

        /** =========================
         *  DATA USER
         *  ========================= */
        $user = User::with('dataDiri')
            ->where('id_user', $validated['id_user'])
            ->firstOrFail();

        $role = $user->role; // dosen / karyawan

        /** =========================
         *  DATA PRESENSI
         *  ========================= */
        $presensis = Presensi::where('id_user', $user->id_user)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
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
        ])->loadView('admin.laporan.presensi.pdf', [
            'title'          => $title,
            'user'           => $user,
            'role'           => $role,
            'periode'        => $periode->translatedFormat('F Y'),
            'presensis'      => $presensis,
            'rekap'          => $rekap,
            'setting'        => $setting,
            'logoFileSrc'    => $logoFileSrc,
            'logoDataUri'    => $logoDataUri,
            'logoPngData64'  => $logoPngData64,
        ])->setPaper('A4', 'portrait');

        return $pdf->download(
            'rekap-presensi-' . $user->npp . '-' . $user->dataDiri->name . '-' . $periode->format('Y-m') . '.pdf'
        );
    }










    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'id_user' => 'required',
    //         'periode' => 'required'
    //     ]);

    //     $tanggal = Carbon::createFromFormat('Y-m', $request->periode);

    //     $role = User::where('id_user', $request->id_user)->value('role');


    //     $presensis = Presensi::where('id_user', $request->id_user)
    //         ->whereMonth('tanggal', $tanggal->month)
    //         ->whereYear('tanggal', $tanggal->year)
    //         ->orderBy('tanggal', 'desc')
    //         ->get()
    //         ->map(function ($item) use ($role) {

    //             if (!is_null($item->durasi_menit)) {
    //                 $jam   = intdiv($item->durasi_menit, 60);
    //                 $menit = $item->durasi_menit % 60;
    //                 $item->durasi = sprintf('%02d:%02d:00', $jam, $menit);
    //             } else {
    //                 $item->durasi = '00:00:00';
    //             }
    //             $item->tanggal_label = Carbon::parse($item->tanggal)->translatedFormat('d-m-Y');

    //             if ($role === 'admin') {
    //                 $item->aktivitas = null;
    //             }
    //             return $item;
    //         });

    //     $hadir = $presensis->where('status_kehadiran', 'hadir')->count();
    //     $sakit = $presensis->where('status_kehadiran', 'sakit')->count();
    //     $izin  = $presensis->where('status_kehadiran', 'izin')->count();
    //     $label = 'Data Presensi ' . $tanggal->translatedFormat('F Y');
    // }
}
