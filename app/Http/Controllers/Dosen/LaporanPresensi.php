<?php

namespace App\Http\Controllers\Dosen;

use App\Exports\PresensiSemuaExport;
use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Settings;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

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
        return view('dosen.pimpinan.laporan.presensi.index', $data);
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
        ])->loadView('dosen.pimpinan.laporan.presensi.pdf', [
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
            'rekap-presensi-' . $user->npp . '-' . $user->nama_lengkap . '-' . $periode->format('Y-m') . '.pdf'
        );
    }

    public function cetakSemua(Request $request)
    {

        $request->validate([
            'periode' => 'required|date_format:Y-m',
            'type' => 'required|in:pdf,excel'
        ]);

        $periode = Carbon::createFromFormat('Y-m', $request->periode);

        if ($request->type === 'pdf') {
            return $this->generatePdfSemua($periode);
        }

        if ($request->type === 'excel') {
            return $this->generateExcelSemua($periode);
        }
    }

    private function generatePdfSemua($periode)
    {
        $bulan = $periode->month;
        $tahun = $periode->year;

        $users = User::with(['dataDiri', 'struktural'])
            ->whereIn('role', ['dosen', 'karyawan'])
            ->where('status_keaktifan', 'aktif')
            ->orderBy('npp')
            ->get();

        $rows = [];

        foreach ($users as $index => $user) {

            $role = $user->role;

            $presensis = Presensi::with('aktivitas')
                ->where('id_user', $user->id_user)
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
                ->get();

            /** =====================
             * REKAP STATUS
             * ===================== */
            $hadir = $presensis->where('status_kehadiran', 'hadir')->count();
            $sakit = $presensis->where('status_kehadiran', 'sakit')->count();
            $izin  = $presensis->where('status_kehadiran', 'izin')->count();

            /** =====================
             * TOTAL DURASI
             * ===================== */
            $totalDurasiMenit = 0;

            /** =====================
             * AKTIVITAS
             * ===================== */
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

                /** =====================
                 * HANYA DOSEN PUNYA AKTIVITAS
                 * ===================== */
                if ($role === 'dosen' && $p->aktivitas) {

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

            /** =====================
             * FORMAT DURASI
             * ===================== */
            $jam   = intdiv($totalDurasiMenit, 60);
            $menit = $totalDurasiMenit % 60;

            $durasi = sprintf('%02d:%02d', $jam, $menit);

            /** =====================
             * MEMENUHI / TIDAK
             * ===================== */
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

            /** =====================
             * TOTAL SKS
             * ===================== */
            $totalSKS = $totalSS + $totalSM + $totalPS + $totalPM;

            $rows[] = [
                'no' => $index + 1,
                'npp' => $user->npp,
                'nama' => $user->nama_lengkap ?? '-',

                'hadir' => $hadir,
                'sakit' => $sakit,
                'izin' => $izin,

                'memenuhi' => $memenuhi,
                'tidak_memenuhi' => $tidakMemenuhi,

                'durasi' => $durasi,

                'SS' => $totalSS,
                'SM' => $totalSM,
                'PS' => $totalPS,
                'PM' => $totalPM,

                'total_sks' => $totalSKS,

                'Sem' => $totalSem,
                'Bim' => $totalBim,
                'Uji' => $totalUji,
                'KKL' => $totalKKL,
                'TL' => $totalTL,
            ];
        }
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
        $title = 'Rekap Presensi Semua Pegawai';

        $pdf = PDF::setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => true,
            'chroot'               => public_path(),
            'tempDir'              => storage_path('app/dompdf_temp'),
            'fontDir'              => storage_path('app/dompdf_font'),
        ])->loadView('dosen.pimpinan.laporan.presensi.pdf_semua', [
            'rows' => $rows,
            'periode' => $periode->translatedFormat('F Y'),
            'setting'        => $setting,
            'logoFileSrc'    => $logoFileSrc,
            'logoDataUri'    => $logoDataUri,
            'logoPngData64'  => $logoPngData64,
            'title'          => $title,
        ])->setPaper('A4', 'landscape');

        return $pdf->download(
            'rekap-presensi-' . $periode->format('Y-m') . '.pdf'
        );
    }
    private function generateExcelSemua($periode)
    {
        $bulan = $periode->month;
        $tahun = $periode->year;

        $users = User::with(['dataDiri', 'struktural'])
            ->whereIn('role', ['dosen', 'karyawan'])
            ->where('status_keaktifan', 'aktif')
            ->orderBy('npp')
            ->get();

        $rows = [];

        foreach ($users as $index => $user) {

            $role = $user->role;

            $presensis = Presensi::with('aktivitas')
                ->where('id_user', $user->id_user)
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
                })->orderBy('tanggal', 'asc')
                ->get();

            $hadir = $presensis->where('status_kehadiran', 'hadir')->count();
            $sakit = $presensis->where('status_kehadiran', 'sakit')->count();
            $izin  = $presensis->where('status_kehadiran', 'izin')->count();

            $totalDurasiMenit = 0;

            $totalSS = 0;
            $totalSM = 0;
            $totalPS = 0;
            $totalPM = 0;
            $totalSem = 0;
            $totalBim = 0;
            $totalUji = 0;
            $totalKKL = 0;
            $totalTL = 0;

            foreach ($presensis as $p) {

                if ($p->durasi_menit) {
                    $totalDurasiMenit += $p->durasi_menit;
                }

                /** =====================
                 * HANYA DOSEN PUNYA AKTIVITAS
                 * ===================== */
                if ($role === 'dosen' && $p->aktivitas) {

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

            /** =====================
             * FORMAT DURASI
             * ===================== */
            $jam   = intdiv($totalDurasiMenit, 60);
            $menit = $totalDurasiMenit % 60;

            $durasi = sprintf('%02d:%02d', $jam, $menit);

            /** =====================
             * MEMENUHI / TIDAK
             * ===================== */
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

            /** =====================
             * TOTAL SKS
             * ===================== */
            $totalSKS = $totalSS + $totalSM + $totalPS + $totalPM;

            $rows[] = [
                'no' => $index + 1,
                'npp' => $user->npp,
                'nama' => $user->nama_lengkap ?? '-',

                'hadir' => $hadir ?? 0,
                'sakit' => $sakit ?? 0,
                'izin' => $izin ?? 0,

                'memenuhi' => $memenuhi ?? 0,
                'tidak_memenuhi' => $tidakMemenuhi ?? 0,

                'durasi' => $durasi,

                'SS' => $totalSS ?? 0,
                'SM' => $totalSM ?? 0,
                'PS' => $totalPS ?? 0,
                'PM' => $totalPM ?? 0,

                'total_sks' => $totalSKS ?? 0,

                'Sem' => $totalSem ?? 0,
                'Bim' => $totalBim ?? 0,
                'Uji' => $totalUji ?? 0,
                'KKL' => $totalKKL ?? 0,
                'TL' => $totalTL ?? 0,
            ];
        }
        return Excel::download(
            new PresensiSemuaExport($rows, $periode->translatedFormat('F Y')),
            'rekap-presensi-' . $periode->format('Y-m') . '.xlsx'
        );
    }
}
