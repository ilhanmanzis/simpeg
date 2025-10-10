<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;


class Laporan extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = [
            'page' => 'Laporan',
            'selected' => 'Laporan',
            'title' => 'Laporan',
        ];
        return view('admin.laporan.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $today = Carbon::today()->toDateString();
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




        $pegawai        = $validated['pegawai'];
        $status        = $validated['status'];
        $tersertifikasi = $pegawai === 'dosen' ? ($validated['tersertifikasi'] ?? 'all') : null;

        // Query dasar + eager load relasi yang dibutuhkan
        $query = User::query()
            ->with(['dataDiri'])
            // Untuk kolom golongan/fungsional/struktural: kita eager load 1 record aktif & terbaru + relasi master-nya
            ->with([
                'golongan' => fn($q) => $q->where('status', 'aktif')->orderByDesc('id_golongan_user')->limit(1)->with('golongan'),
                'fungsional' => fn($q) => $q->where('status', 'aktif')->orderByDesc('id_fungsional_user')->limit(1)->with('fungsional'),
                'struktural' => fn($q) => $q->where('status', 'aktif')->whereDate('tanggal_selesai', '>=', $today)->orderByDesc('id_struktural_user')->limit(1)->with('struktural'),
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
            $status_keaktifan  = $u->status_keaktifan ?? '-';
            $nama = $dd->name ?? '-';

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
                'status_keaktifan'           => $status_keaktifan,
                'nama'          => $nama,
                'golongan'      => $golName ?? '-',
                'fungsional'    => $funName ?? '-',
                'struktural'    => $strName ?? '-',
                'tersertifikasi' => $sert, // hanya terpakai jika pegawai=dosen
            ];
        });

        $setting = Settings::first();

        $original = public_path('storage/logo/' . ($setting->logo ?? ''));
        $alt      = storage_path('app/public/logo/' . ($setting->logo ?? ''));

        // pilih path yang benar-benar ada
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
                    if ($png !== false && strlen($png) > 0) {
                        $logoPngData64 = 'data:image/png;base64,' . base64_encode($png);
                    }
                }
            } else {
                $bytes = @file_get_contents($path);
                if ($bytes !== false) {
                    $mime = function_exists('mime_content_type') ? (mime_content_type($path) ?: 'image/png') : 'image/png';
                    $logoDataUri = 'data:' . $mime . ';base64,' . base64_encode($bytes);
                }
            }
            // fallback terakhir (kalau perlu), tapi sering tak terpakai kalau sudah base64
            $logoFileSrc = 'file://' . $path;
        }


        // Export PDF
        if ($request->filled('export') && $validated['export'] === 'pdf') {
            $title = 'Laporan Pegawai';

            $pdf = PDF::setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
                'chroot'               => public_path(),
                'tempDir'              => storage_path('app/dompdf_temp'),
                'fontDir'              => storage_path('app/dompdf_font'),
            ])->loadView('admin.laporan.pdf', [
                'title'            => $title,
                'pegawai'          => $pegawai,
                'tersertifikasi'   => $tersertifikasi,
                'data'             => $users,
                'rows'             => $rows,
                'logoFileSrc'      => $logoFileSrc,
                'logoDataUri'      => $logoDataUri,   // <=== TAMBAHKAN INI
                'logoPngData64'    => $logoPngData64,
                'setting'          => $setting,
                'status_keaktifan' => $status
            ])->setPaper('A4', 'portrait');


            return $pdf->download('laporan-pegawai.pdf');
        }

        // Optional: render halaman non-PDF
        return view('admin.laporan.index', [
            'title'          => 'Laporan Pegawai',
            'pegawai'        => $pegawai,
            'tersertifikasi' => $tersertifikasi,
            'data'           => $users,
            'rows'           => $rows,
            'status_keaktifan' => $status
        ]);
    }

    public function individu($id_user)
    {
        $today = Carbon::today()->toDateString();
        // dd($id_user);
        // Ambil user + relasi
        $user = User::where('id_user', $id_user)
            ->with([
                'dataDiri.dokumen',
                // aktif & terbaru
                'golongan'   => fn($q) => $q->where('status', 'aktif')->orderByDesc('id_golongan_user')->limit(1)->with('golongan'),
                'fungsional' => fn($q) => $q->where('status', 'aktif')->orderByDesc('id_fungsional_user')->limit(1)->with('fungsional'),
                'struktural' => fn($q) => $q->where('status', 'aktif')->whereDate('tanggal_selesai', '>=', $today)->orderByDesc('id_struktural_user')->limit(1)->with('struktural'),
                // semua pendidikan + jenjang
                'pendidikan' => fn($q) => $q->with('jenjang')->orderBy('id_jenjang')->orderByDesc('id_pendidikan'),
            ])
            ->firstOrFail();

        $dd   = $user->dataDiri;
        $role = $user->role;

        // Jabatan aktif (untuk dosen saja)
        $golAktif = optional($user->golongan->first());
        $funAktif = optional($user->fungsional->first());
        $strAktif = optional($user->struktural->first());

        $jabatanAktif = [
            'Golongan'           => optional($golAktif->golongan)->nama_golongan ?? '-',
            'Jabatan Fungsional' => optional($funAktif->fungsional)->nama_jabatan ?? '-',
            'Jabatan Struktural' => optional($strAktif->struktural)->nama_jabatan ?? '-',
        ];

        // Biodata (user + dataDiri)
        $biodata = [
            'Nama'              => $dd->name ?? '-',
            'NPP'               => $user->npp ?? '-',
            // NIP/NUPTK/NIDK/NIDN disembunyikan untuk karyawan (di-view kita kondisikan)
            'NIP'               => $dd->nip ?? '-',
            'NUPTK'             => $dd->nuptk ?? '-',
            'NIDK'              => $dd->nidk ?? '-',
            'NIDN'              => $dd->nidn ?? '-',
            'NIK'            => $dd->no_ktp ?? '-',
            'No HP'             => $dd->no_hp ?? '-',
            'Tempat Lahir'      => $dd->tempat_lahir ?? '-',
            'Tanggal Lahir'     => $dd->tanggal_lahir ? \Carbon\Carbon::parse($dd->tanggal_lahir)->format('d M Y') : '-',
            'Jenis Kelamin'     => $dd->jenis_kelamin ?? '-',
            'Agama'             => $dd->agama ?? '-',

            'Alamat'            => $dd->alamat ?? '-',
            'RT / RW'           => trim(($dd->rt ?? '') . ' / ' . ($dd->rw ?? ''), ' / ') ?: '-',
            'Desa/Kelurahan'    => $dd->desa ?? '-',
            'Kecamatan'         => $dd->kecamatan ?? '-',
            'Kabupaten'         => $dd->kabupaten ?? '-',
            'Provinsi'          => $dd->provinsi ?? '-',
            'Golongan Darah'          => $dd->golongan_darah ?? '-',
            'Nomor BPJS'          => $dd->bpjs ?? '-',
            'Jumlah Anak'          => $dd->anak ?? '-',
            ...(
                (($dd->jenis_kelamin ?? '') === 'Laki-Laki')
                ? ['Jumlah Istri' => ($dd->istri ?? '-')]
                : []
            ),
            'Tanggal Bergabung' => $dd->tanggal_bergabung ? \Carbon\Carbon::parse($dd->tanggal_bergabung)->format('d M Y') : '-',
            'Status'          => $user->status_keaktifan ?? '-',
        ];

        // ===== FOTO dari relasi Dokumen =====
        $fotoUrl = null;
        $fotoDataUri = null;
        $doc = optional($dd)->dokumen; // relasi belongsTo(Dokumens::class, 'foto','nomor_dokumen')

        if ($doc) {
            // pilih prioritas URL
            if (!empty($doc->download_url)) {
                $fotoUrl = $doc->download_url;
            } elseif (!empty($doc->view_url)) {
                $fotoUrl = $doc->view_url;
            } elseif (!empty($doc->preview_url)) {
                $fotoUrl = $doc->preview_url;
            } elseif (!empty($doc->file_id)) {
                // URL langsung "view" Google Drive (pastikan file public)
                $fotoUrl = "https://drive.google.com/uc?export=view&id={$doc->file_id}";
            }

            // Fallback: jadikan base64 (kalau URL perlu auth atau tidak bisa diakses engine PDF)
            try {
                if ($fotoUrl) {
                    $res = Http::timeout(10)->get($fotoUrl);
                    if ($res->ok() && strlen($res->body()) > 0) {
                        $mime = $res->header('Content-Type') ?: 'image/jpeg';
                        $fotoDataUri = 'data:' . $mime . ';base64,' . base64_encode($res->body());
                    }
                }
            } catch (\Throwable $e) {
                // biarkan kosong — PDF tetap jalan tanpa foto
            }
        }

        $title = 'Biodata Pegawai';

        $setting = Settings::first();

        $original = public_path('storage/logo/' . ($setting->logo ?? ''));
        $alt      = storage_path('app/public/logo/' . ($setting->logo ?? ''));

        // pilih path yang benar-benar ada
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
                    if ($png !== false && strlen($png) > 0) {
                        $logoPngData64 = 'data:image/png;base64,' . base64_encode($png);
                    }
                }
            } else {
                $bytes = @file_get_contents($path);
                if ($bytes !== false) {
                    $mime = function_exists('mime_content_type') ? (mime_content_type($path) ?: 'image/png') : 'image/png';
                    $logoDataUri = 'data:' . $mime . ';base64,' . base64_encode($bytes);
                }
            }
            // fallback terakhir (kalau perlu), tapi sering tak terpakai kalau sudah base64
            $logoFileSrc = 'file://' . $path;
        }


        $pdf = PDF::setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => true,
            'chroot'               => public_path(),
            'tempDir'              => storage_path('app/dompdf_temp'),
            'fontDir'              => storage_path('app/dompdf_font'),
        ])->loadView('admin.laporan.pegawai', [
            'title'         => $title,
            'user'          => $user,
            'role'          => $role,
            'biodata'       => $biodata,
            'jabatanAktif'  => $jabatanAktif,
            'pendidikan'    => $user->pendidikan,
            'now'           => now()->timezone('Asia/Jakarta'),
            'fotoUrl'       => $fotoUrl,
            'fotoDataUri'   => $fotoDataUri,
            'logoFileSrc'   => $logoFileSrc,
            'logoDataUri'   => $logoDataUri,     // <=== TAMBAHKAN INI
            'logoPngData64' => $logoPngData64,
            'setting'       => $setting,
        ])->setPaper('A4', 'portrait');


        return $pdf->download("biodata-{$user->dataDiri->name}.pdf");
    }
}
