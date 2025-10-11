<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\GoogleDriveService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Dashboard extends Controller
{
    protected $googleDriveService;

    public function __construct(GoogleDriveService $googleDriveService)
    {
        $this->googleDriveService = $googleDriveService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function admin()
    {


        /* =========================
         * 1) Jumlah Dosen & Tendik
         * ========================= */
        $counts =  [
            'dosen' => [
                'aktif'    => User::where('role', 'dosen')->where('status_keaktifan', 'aktif')->count(),
                'nonaktif' => User::where('role', 'dosen')->where('status_keaktifan', '!=', 'aktif')->count(),
            ],
            'tendik' => [
                'aktif'    => User::where('role', 'karyawan')->where('status_keaktifan', 'aktif')->count(),
                'nonaktif' => User::where('role', 'karyawan')->where('status_keaktifan', '!=', 'aktif')->count(),
            ],
        ];

        /* =========================
         * 2) Pendidikan Terakhir
         *    (jenjang tertinggi = MIN(id_jenjang))
         * ========================= */
        $sub = DB::table('pendidikan as p')
            ->select('p.id_user', DB::raw('MIN(p.id_jenjang) as top_jenjang'))
            ->groupBy('p.id_user');

        $eduDosen = DB::table('users as u')
            ->joinSub($sub, 'px', 'px.id_user', '=', 'u.id_user')
            ->join('jenjang as j', 'j.id_jenjang', '=', 'px.top_jenjang')
            ->where('u.role', 'dosen')
            ->groupBy('j.id_jenjang', 'j.nama_jenjang')
            ->orderBy('j.id_jenjang')
            ->select('j.nama_jenjang', DB::raw('COUNT(*) as total'))
            ->pluck('total', 'j.nama_jenjang')
            ->toArray();

        $eduTendik = DB::table('users as u')
            ->joinSub($sub, 'px', 'px.id_user', '=', 'u.id_user')
            ->join('jenjang as j', 'j.id_jenjang', '=', 'px.top_jenjang')
            ->where('u.role', 'karyawan')
            ->groupBy('j.id_jenjang', 'j.nama_jenjang')
            ->orderBy('j.id_jenjang')
            ->select('j.nama_jenjang', DB::raw('COUNT(*) as total'))
            ->pluck('total', 'j.nama_jenjang')
            ->toArray();

        // tampilkan hanya jenjang yang ada datanya
        $labels = collect(array_keys($eduDosen))->merge(array_keys($eduTendik))->unique()->values();

        $pendidikan = [];
        foreach ($labels as $label) {
            $d = (int)($eduDosen[$label]  ?? 0);
            $t = (int)($eduTendik[$label] ?? 0);
            if (($d + $t) > 0) {
                $pendidikan[$label] = ['dosen' => $d, 'tendik' => $t];
            }
        }


        /* =========================
         * 3) Golongan (khusus dosen)
         * ========================= */
        $golongan = DB::table('golongan_user as gu')
            ->join('users as u', 'u.id_user', '=', 'gu.id_user')
            ->join('golongan as g', 'g.id_golongan', '=', 'gu.id_golongan')
            ->where('u.role', 'dosen')
            ->where('gu.status', 'aktif')
            ->groupBy('g.id_golongan', 'g.nama_golongan')
            ->orderBy('g.id_golongan')
            ->select('g.nama_golongan', DB::raw('COUNT(*) as total'))
            ->pluck('total', 'g.nama_golongan')
            ->toArray();

        /* =========================
         * 4) Jabatan Fungsional (dosen)
         * ========================= */
        $fungsional =  DB::table('jabatan_fungsional_user as fu')
            ->join('users as u', 'u.id_user', '=', 'fu.id_user')
            ->join('jabatan_fungsional as jf', 'jf.id_fungsional', '=', 'fu.id_fungsional')
            ->where('u.role', 'dosen')
            ->where('fu.status', 'aktif')
            ->groupBy('jf.id_fungsional', 'jf.nama_jabatan')
            ->orderBy('jf.id_fungsional')
            ->select('jf.nama_jabatan', DB::raw('COUNT(*) as total'))
            ->pluck('total', 'jf.nama_jabatan')
            ->toArray();

        $totalDosen = ($counts['dosen']['aktif'] ?? 0) + ($counts['dosen']['nonaktif'] ?? 0);

        // sudah tersertifikasi
        $certified = User::where('role', 'dosen')
            ->whereHas('dataDiri', fn($q) => $q->where('tersertifikasi', 'sudah'))
            ->count();

        // belum tersertifikasi (fallback aman)
        $notCertified = max(0, $totalDosen - $certified);

        $counts['dosen']['tersertifikasi']        = $certified;
        $counts['dosen']['belum_tersertifikasi']  = $notCertified;

        $stats = [
            'counts'      => $counts,
            'pendidikan'  => $pendidikan, // label => ['dosen'=>x,'tendik'=>y]
            'golongan'    => $golongan,   // label => total
            'fungsional'  => $fungsional, // label => total
        ];

        // 
        $data = [
            'page' => 'Dashboard',
            'selected' => 'Dashboard',
            'title' => 'Dashboard',

            'stats' => $stats
        ];

        // dd($data);
        return view('admin.dashboard', $data);
    }
    public function dosen()
    {
        $today = Carbon::today()->toDateString();
        $id = Auth::user()->id_user;

        $dosen = User::where('id_user', $id)->with([
            'dataDiri.serdosen',
            'golongan' => fn($q) => $q->where('status', 'aktif')->orderByDesc('id_golongan_user')->limit(1)->with('golongan'),
            'fungsional' => fn($q) => $q->where('status', 'aktif')->orderByDesc('id_fungsional_user')->limit(1)->with('fungsional'),
            'struktural' => fn($q) => $q->where('status', 'aktif')->whereDate('tanggal_selesai', '>=', $today)->orderByDesc('id_struktural_user')->limit(1)->with('struktural'),
            'pendidikan' => fn($q) => $q->orderBy('id_jenjang')->latest()->limit(1)->with('jenjang'),
        ])->firstOrFail();

        $data = [
            'page' => 'Dashboard',
            'selected' => 'Dashboard',
            'title' => 'Dashboard',
            'dosen' => $dosen,
            'golongan' => $dosen->golongan->first()?->golongan->nama_golongan ?? 'Belum ada golongan',
            'fungsional' => $dosen->fungsional->first()?->fungsional->nama_jabatan ?? 'Belum ada Jabatan Fungsional',
            'struktural' => $dosen->struktural->first()?->struktural->nama_jabatan ?? 'Belum ada Jabatan Struktural',
            'pendidikan' => $dosen->pendidikan->first() ?? 'Belum ada pendidikan',
        ];

        return view('dosen.dashboard', $data);
    }
    public function karyawan()
    {
        $id = Auth::user()->id_user;

        $karyawan = User::where('id_user', $id)->with([
            'dataDiri',

            'pendidikan' => fn($q) => $q->orderBy('id_jenjang')->latest()->limit(1)->with('jenjang'),
        ])->firstOrFail();

        $data = [
            'page' => 'Dashboard',
            'selected' => 'Dashboard',
            'title' => 'Dashboard',
            'karyawan' => $karyawan,
            'pendidikan' => $karyawan->pendidikan->first() ?? 'Belum ada pendidikan',
        ];
        return view('karyawan.dashboard', $data);
    }
}
