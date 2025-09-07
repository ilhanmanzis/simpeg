<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use Illuminate\Http\Request;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Laporan extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::user()->dataDiri->pimpinan !== 'aktif') {
            return abort(403, 'Unauthorized');
        }

        $data = [
            'page' => 'Laporan',
            'selected' => 'Laporan',
            'title' => 'Laporan',
        ];
        return view('dosen.laporan.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if (Auth::user()->dataDiri->pimpinan !== 'aktif') {
            return abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'pegawai'        => 'required|in:all,dosen,karyawan',
            // required_if untuk dosen; nilai yang valid: all/ya/tidak
            'tersertifikasi' => 'nullable|required_if:pegawai,dosen|in:all,ya,tidak',
            'status' => 'nullable|required|in:all,aktif,nonaktif',
            'export'         => 'nullable|in:pdf',
        ], [
            'pegawai.required' => 'Pilih jenis pegawai.',
            'pegawai.in'       => 'Pilihan pegawai tidak valid.',
            'tersertifikasi.required_if' => 'Pilih status tersertifikasi.',
            'tersertifikasi.in' => 'Pilihan tersertifikasi tidak valid.',
        ]);



        $status        = $validated['status'];
        $pegawai        = $validated['pegawai'];
        $tersertifikasi = $pegawai === 'dosen' ? ($validated['tersertifikasi'] ?? 'all') : null;

        // Query dasar + eager load relasi yang dibutuhkan
        $query = User::query()
            ->with(['dataDiri'])
            // Untuk kolom golongan/fungsional/struktural: kita eager load 1 record aktif & terbaru + relasi master-nya
            ->with([
                'golongan' => fn($q) => $q->where('status', 'aktif')->orderByDesc('id_golongan_user')->limit(1)->with('golongan'),
                'fungsional' => fn($q) => $q->where('status', 'aktif')->orderByDesc('id_fungsional_user')->limit(1)->with('fungsional'),
                'struktural' => fn($q) => $q->where('status', 'aktif')->orderByDesc('id_struktural_user')->limit(1)->with('struktural'),
            ])
            // pegawai=all -> hanya karyawan & dosen (exclude admin)
            ->when($pegawai === 'all', fn($q) => $q->whereIn('role', ['karyawan', 'dosen']))
            // selain all -> filter exact role
            ->when($pegawai !== 'all', fn($q) => $q->where('role', $pegawai))
            // filter tersertifikasi hanya untuk dosen & jika bukan 'all'
            ->when($pegawai === 'dosen' && $tersertifikasi && $tersertifikasi !== 'all', function ($q) use ($tersertifikasi) {
                $q->whereHas('dataDiri', function ($qq) use ($tersertifikasi) {
                    $qq->where('tersertifikasi', $tersertifikasi); // 'ya'/'tidak' atau 'sudah'/'tidak' sesuai DB
                });
            })
            ->when($status === 'all', fn($q) => $q->whereIn('status_keaktifan', ['aktif', 'nonaktif']))
            // selain all -> filter exact role
            ->when($status !== 'all', fn($q) => $q->where('status_keaktifan', $status))
            ->orderBy('id_user', 'asc');

        $users = $query->get();

        // Normalisasi nilai "sudah/ya"
        $isSudah = function ($v) {
            return in_array(strtolower(trim((string)$v)), ['sudah', 'ya', '1', 'true'], true);
        };

        // Bentuk baris yang siap dipakai di view PDF
        $rows = $users->map(function ($u) use ($pegawai, $isSudah) {
            $dd   = $u->dataDiri;
            $npp  = $u->npp ?? '-';
            $nama = $dd->name ?? '-';
            $status_keaktifan  = $u->status_keaktifan ?? '-';

            // Ambil 1 record aktif & terbaru (sudah di-limit(1) saat eager load)
            $golAktif  = optional($u->golongan->first());
            $funAktif  = optional($u->fungsional->first());
            $strAktif  = optional($u->struktural->first());

            $golName = optional($golAktif->golongan)->nama_golongan ?? null;
            $funName = optional($funAktif->fungsional)->nama_jabatan ?? null;
            $strName = optional($strAktif->struktural)->nama_jabatan ?? null;

            // Aturan tampilan kolom:
            // - pegawai = all -> jika karyawan maka 3 kolom jabatan diisi '-'
            if ($pegawai === 'all' && $u->role === 'karyawan') {
                $golName = $funName = $strName = '-';
            }

            // - pegawai = dosen -> jika kosong, ganti '-'
            if ($pegawai === 'dosen') {
                $golName = $golName ?: '-';
                $funName = $funName ?: '-';
                $strName = $strName ?: '-';
            }

            // Kolom tersertifikasi (khusus mode dosen)
            $sert = null;
            if ($pegawai === 'dosen') {
                $sert = $dd && isset($dd->tersertifikasi)
                    ? ($isSudah($dd->tersertifikasi) ? 'Sudah' : 'Tidak')
                    : '-';
            }

            return [
                'id_user'       => $u->id_user,
                'role'          => $u->role,
                'npp'           => $npp,
                'nama'          => $nama,
                'golongan'      => $golName ?? '-',
                'fungsional'    => $funName ?? '-',
                'struktural'    => $strName ?? '-',
                'tersertifikasi' => $sert, // hanya terpakai jika pegawai=dosen
                'status_keaktifan'           => $status_keaktifan,
            ];
        });


        $setting = Settings::first();

        $original = public_path('storage/logo/' . ($setting->logo ?? ''));

        $logoFileSrc   = null;
        $logoPngData64 = null; // untuk WEBP → hasil konversi PNG (base64)

        if (is_file($original)) {
            $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));

            if ($ext === 'webp') {
                // Pastikan GD support webp
                if (function_exists('imagecreatefromwebp')) {
                    $im = @imagecreatefromwebp($original);
                    if ($im) {
                        // render ke PNG di buffer (TANPA menyentuh disk)
                        ob_start();
                        imagepng($im, null, 9);
                        imagedestroy($im);
                        $png = ob_get_clean();
                        if ($png !== false && strlen($png) > 0) {
                            $logoPngData64 = 'data:image/png;base64,' . base64_encode($png);
                        }
                    }
                }
                // Jika server GD tidak support WEBP, kita biarkan $logoPngData64 null (logo skip).
            } else {
                // Sudah format didukung → pakai path file lokal
                $logoFileSrc = $original;
            }
        }


        // Export PDF
        $title = 'Laporan Pegawai';
        if ($request->filled('export') && $validated['export'] === 'pdf') {

            $pdf = PDF::setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true, // penting utk img dari URL
                'chroot' => public_path(),
            ])->loadView('dosen.laporan.pdf', [
                'title'          => $title,
                'pegawai'        => $pegawai,
                'tersertifikasi' => $tersertifikasi,
                'data'           => $users, // kalau view butuh akses original user
                'rows'           => $rows,  // view utama pakai ini (sudah diringkas)
                'logoFileSrc'   => $logoFileSrc,
                'logoPngData64' => $logoPngData64,
                'setting'      => $setting,
                'status_keaktifan' => $status
            ])->setPaper('A4', 'portrait');

            return $pdf->download('laporan-pegawai.pdf');
        }


        // Optional: render halaman non-PDF
        return view('dosen.laporan.index', [
            'title'          => $title,
            'pegawai'        => $pegawai,
            'tersertifikasi' => $tersertifikasi,
            'data'           => $users, // kalau view butuh akses original user
            'rows'           => $rows,  // view utama pakai ini (sudah diringkas)
            'logoFileSrc'   => $logoFileSrc,
            'logoPngData64' => $logoPngData64,
            'setting'      => $setting,
            'status_keaktifan' => $status
        ]);
    }
}
