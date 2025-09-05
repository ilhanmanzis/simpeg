<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $jumlahDosen = User::where('role', 'dosen')->count();
        $jumlahDosenAktif = User::where('role', 'dosen')->where('status_keaktifan', 'aktif')->count();
        $jumlahDosenNonaktif = User::where('role', 'dosen')->where('status_keaktifan', 'nonaktif')->count();
        $jumlahDosenTersertifikasi = User::where('role', 'dosen')->whereHas('dataDiri', function ($query) {
            $query->where('tersertifikasi', 'sudah');
        })->count();
        $jumlahDosenBelumTersertifikasi = User::where('role', 'dosen')->whereHas('dataDiri', function ($query) {
            $query->where('tersertifikasi', 'tidak');
        })->count();
        $jumlahKaryawan = User::where('role', 'karyawan')->count();
        $jumlahKaryawanAktif = User::where('role', 'karyawan')->where('status_keaktifan', 'aktif')->count();
        $jumlahKaryawanNonaktif = User::where('role', 'karyawan')->where('status_keaktifan', 'nonaktif')->count();
        $data = [
            'page' => 'Dashboard',
            'selected' => 'Dashboard',
            'title' => 'Dashboard',
            'jumlahDosen' => $jumlahDosen,
            'jumlahDosenAktif' => $jumlahDosenAktif,
            'jumlahDosenNonaktif' => $jumlahDosenNonaktif,
            'jumlahKaryawan' => $jumlahKaryawan,
            'jumlahKaryawanAktif' => $jumlahKaryawanAktif,
            'jumlahKaryawanNonaktif' => $jumlahKaryawanNonaktif,
            'jumlahDosenTersertifikasi' => $jumlahDosenTersertifikasi,
            'jumlahDosenBelumTersertifikasi' => $jumlahDosenBelumTersertifikasi,
        ];

        // dd($data);
        return view('admin.dashboard', $data);
    }
    public function dosen()
    {
        $id = Auth::user()->id_user;

        $dosen = User::where('id_user', $id)->with([
            'dataDiri.serdosen',
            'golongan' => fn($q) => $q->where('status', 'aktif')->orderByDesc('id_golongan_user')->limit(1)->with('golongan'),
            'fungsional' => fn($q) => $q->where('status', 'aktif')->orderByDesc('id_fungsional_user')->limit(1)->with('fungsional'),
            'struktural' => fn($q) => $q->where('status', 'aktif')->orderByDesc('id_struktural_user')->limit(1)->with('struktural'),
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
