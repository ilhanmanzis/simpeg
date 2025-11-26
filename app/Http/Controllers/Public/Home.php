<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Setting;
use App\Models\JabatanStrukturals;
use App\Models\Penelitians;
use App\Models\Pengabdians;
use App\Models\Settings;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class Home extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        // TTL cache (silakan atur)
        $today = Carbon::today()->toDateString();

        /* =========================
         * 1) Jabatan Struktural
         * ========================= */
        $strukturals = JabatanStrukturals::with([
            // Ambil yang status AKTIF dan MASIH BERLAKU (tanggal_selesai null ATAU >= today)
            'activeCurrent' => function ($q) use ($today) {
                $q->where('status', 'aktif')
                    ->where(function ($w) use ($today) {
                        $w->whereNull('tanggal_selesai')
                            ->orWhereDate('tanggal_selesai', '>=', $today);
                    })
                    ->with(['user.dataDiri', 'dokumen']);
            },
            // latestAny tetap eager untuk fallback cepat kalau activeCurrent kosong
            'latestAny' => function ($q) {
                $q->with(['user.dataDiri', 'dokumen']);
            },
        ])
            ->orderBy('id_struktural', 'asc')
            ->get()
            ->map(function ($jabatan) use ($today) {   // <<==== pakai $today di sini
                $active  = $jabatan->activeCurrent; // model atau null
                $latest  = $jabatan->latestAny;     // model atau null
                $chosen  = $active ?: $latest;      // prioritas aktif yang masih berlaku

                // default: aktif jika relasi activeCurrent ada
                $isActive = (bool) $active;

                // aturan barumu: jika tanggal_selesai ada & >= today → paksa nonaktif
                if ($chosen && $jabatan->tanggal_selesai) {
                    if (Carbon::parse($jabatan->tanggal_selesai)->greaterThanOrEqualTo($today)) {
                        $isActive = false;
                    }
                }

                return [
                    'jabatan'   => $jabatan,
                    'record'    => $chosen,
                    'is_active' => $isActive,       // <<==== gunakan hasil koreksi
                ];
            })
            ->values(); // reset index biar bersih


        /* =========================
         * 2) Jumlah Dosen & Tendik
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
         * 3) Pendidikan Terakhir
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
         * 4) Golongan (khusus dosen)
         * ========================= */
        $golongan =  DB::table('golongan_user as gu')
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
         * 5) Jabatan Fungsional (dosen)
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

        $stats = [
            'counts'      => $counts,
            'pendidikan'  => $pendidikan, // label => ['dosen'=>x,'tendik'=>y]
            'golongan'    => $golongan,   // label => total
            'fungsional'  => $fungsional, // label => total
        ];


        /* =========================
        * 6) Carousel Penelitian + Pengabdian
        * ========================= */

        $penelitian = Penelitians::with(['user.dataDiri'])
            ->select(
                'id_penelitian as id',
                'judul',
                'id_user',
                'created_at',
                DB::raw('"penelitian" as tipe')
            )
            ->latest()
            ->take(5)
            ->get();

        $pengabdian = Pengabdians::with(['user.dataDiri'])
            ->select(
                'id_pengabdian as id',
                'judul',
                'lokasi',
                'id_user',
                'created_at',
                DB::raw('"pengabdian" as tipe')
            )
            ->latest()
            ->take(5)
            ->get();

        // gabungkan 2 tabel → ambil 5 data terbaru
        $carouselItems = $penelitian
            ->concat($pengabdian)
            ->sortByDesc('created_at')
            ->take(5)
            ->values();

        $data = [
            'page' => 'Home',
            'title' => 'Selamat datang di Webiste Sistem Kepegawaian STMIK EL RAHMA Yogyakarta',
            'setting'  => Settings::first(),
            'strukturals' => $strukturals,
            'stats' => $stats,
            'carouselItems' => $carouselItems
        ];

        return view('public/home', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
