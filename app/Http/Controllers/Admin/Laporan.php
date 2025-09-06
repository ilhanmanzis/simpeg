<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


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
        $validated = $request->validate([
            'pegawai'        => 'required|in:all,dosen,karyawan',
            // required_if untuk dosen; nilai yang valid: all/ya/tidak
            'tersertifikasi' => 'nullable|required_if:pegawai,dosen|in:all,ya,tidak',
            'export'         => 'nullable|in:pdf',
        ], [
            'pegawai.required' => 'Pilih jenis pegawai.',
            'pegawai.in'       => 'Pilihan pegawai tidak valid.',
            'tersertifikasi.required_if' => 'Pilih status tersertifikasi.',
            'tersertifikasi.in' => 'Pilihan tersertifikasi tidak valid.',
        ]);




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

            // Ambil 1 record aktif & terbaru (sudah di-limit(1) saat eager load)
            $golAktif  = optional($u->golongan->first());
            $funAktif  = optional($u->fungsional->first());
            $strAktif  = optional($u->struktural->first());

            $golName = optional($golAktif->golongan)->nama ?? ($dd->golongan ?? null);
            $funName = optional($funAktif->fungsional)->nama ?? ($dd->jabatan_fungsional ?? null);
            $strName = optional($strAktif->struktural)->nama ?? ($dd->jabatan_struktural ?? null);

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
            ];
        });

        // Export PDF
        if ($request->filled('export') && $validated['export'] === 'pdf') {
            $title = 'Laporan Pegawai';

            $pdf = PDF::loadView('admin.laporan.pdf', [
                'title'          => $title,
                'pegawai'        => $pegawai,
                'tersertifikasi' => $tersertifikasi,
                'data'           => $users, // kalau view butuh akses original user
                'rows'           => $rows,  // view utama pakai ini (sudah diringkas)
            ])->setPaper('A4', 'portrait');

            return $pdf->stream('laporan-pegawai.pdf');
        }

        // Optional: render halaman non-PDF
        return view('admin.laporan.index', [
            'title'          => 'Laporan Pegawai',
            'pegawai'        => $pegawai,
            'tersertifikasi' => $tersertifikasi,
            'data'           => $users,
            'rows'           => $rows,
        ]);
    }
}
