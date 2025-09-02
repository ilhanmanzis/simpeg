<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Pengabdians;
use App\Models\PengajuanPengabdians;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Pengabdian extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $judul = $request->get('judul');
        $id = Auth::user()->id_user;

        $data = [
            'page' => 'BKD Pengabdian',
            'selected' => 'BKD Pengabdian',
            'title' => 'BKD Pengabdian',
            'pengabdians' => Pengabdians::where('id_user', $id)
                ->searchJudul($judul)
                ->orderBy('created_at', 'desc')
                ->paginate(10)
                ->appends(request()->except('page', 'riwayat_page')),
            'riwayats' => PengajuanPengabdians::where('status', '!=', 'disetujui')
                ->orderBy('updated_at', 'desc')
                ->paginate(10, ['*'], 'riwayat_page')
                ->appends(request()->except('page'))
                ->fragment('riwayat'),
        ];

        return view('dosen.bkd.pengabdian.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'page' => 'BKD Pengabdian',
            'selected' => 'BKD Pengabdian',
            'title' => 'Tambah BKD Pengabdian',
        ];
        return view('dosen.bkd.pengabdian.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'terima_kasih' => 'required|file|mimes:pdf|max:2048',
            'permohonan' => 'required|file|mimes:pdf|max:2048',
            'tugas' => 'required|file|mimes:pdf|max:2048',
            'modul' => 'required|file|mimes:pdf|max:2048',
            'foto' => 'required|file|mimes:pdf|max:2048',
        ]);

        $idUser = Auth::user()->id_user;

        // permohonan
        $permohonanFile = $request->file("permohonan");
        $permohonanName = time() . '_' . $permohonanFile->getClientOriginalName();
        // Simpan ke storage/app/bkd
        $permohonanFile->storeAs('bkd', $permohonanName);

        // tugas
        $tugasFile = $request->file("tugas");
        $tugasName = time() . '_' . $tugasFile->getClientOriginalName();
        $tugasFile->storeAs('bkd', $tugasName);

        // modul
        $modulFile = $request->file("modul");
        $modulName = time() . '_' . $modulFile->getClientOriginalName();
        $modulFile->storeAs('bkd', $modulName);

        // foto
        $fotoFile = $request->file("foto");
        $fotoName = time() . '_' . $fotoFile->getClientOriginalName();
        $fotoFile->storeAs('bkd', $fotoName);

        // terima kasih
        $terimaKasihFile = $request->file("terima_kasih");
        $terimaKasihName = time() . '_' . $terimaKasihFile->getClientOriginalName();
        $terimaKasihFile->storeAs('bkd', $terimaKasihName);

        PengajuanPengabdians::create([
            'id_user' => $idUser,
            'judul' => $request->input('judul'),
            'lokasi' => $request->input('lokasi'),
            'terimakasih' => $terimaKasihName,
            'permohonan' => $permohonanName,
            'tugas' => $tugasName,
            'modul' => $modulName,
            'foto' => $fotoName,
            'status' => 'pending'
        ]);

        return redirect()->route('dosen.pengabdian')->with('success', 'BKD Pengabdian berhasil Diajukan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $idUser = Auth::user()->id_user;
        $pengabdian = Pengabdians::where('id_pengabdian', $id)->with(['user.dataDiri', 'permohonanPengabdian', 'tugasPengabdian', 'modulPengabdian', 'fotoPengabdian'])->first();
        if (!$pengabdian) {
            abort(404);
        }

        if ($idUser !== $pengabdian->user->id_user) {
            return redirect()->route('dosen.pengabdian')->with('success', 'Anda tidak memiliki akses ke halaman tersebut.');
        }
        $data = [
            'page' => 'BKD Pengabdian',
            'selected' => 'BKD Pengabdian',
            'title' => 'BKD Pengabdian ' . $pengabdian->user->dataDiri->name,
            'pengabdian' => $pengabdian,
        ];
        return view('dosen.bkd.pengabdian.show', $data);
    }

    public function riwayat(string $id)
    {
        $idUser = Auth::user()->id_user;
        $pengabdian = PengajuanPengabdians::where('id_pengajuan', $id)->with(['user.dataDiri'])->first();
        if (!$pengabdian) {
            abort(404);
        }
        if ($idUser !== $pengabdian->user->id_user) {
            return redirect()->route('dosen.pengabdian')->with('success', 'Anda tidak memiliki akses ke halaman tersebut.');
        }
        $data = [
            'page' => 'BKD Pengabdian',
            'selected' => 'BKD Pengabdian',
            'title' => 'BKD Pengabdian ' . $pengabdian->user->dataDiri->name,
            'pengajuan' => $pengabdian,
        ];
        return view('dosen.bkd.pengabdian.riwayat', $data);
    }
}
