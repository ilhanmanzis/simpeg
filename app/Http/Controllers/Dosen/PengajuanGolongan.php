<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Golongans;
use App\Models\GolonganUsers;
use App\Models\PengajuanGolongans;
use App\Models\User;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengajuanGolongan extends Controller
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
        $id = Auth::user()->id_user;

        $data = [
            'page' => 'Pengajuan Golongan',
            'selected' => 'Pengajuan Golongan',
            'title' => 'Pengajuan Kenaikan Golongan',
            'pengajuans' => PengajuanGolongans::where('id_user', $id)->orderBy('updated_at', 'desc')->paginate(10)->withQueryString()
        ];

        return view('dosen.pengajuan.golongan.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $id = Auth::user()->id_user;
        $data = [
            'page' => 'Pengajuan Golongan',
            'selected' => 'Pengajuan Golongan',
            'title' => 'Tambah Pengajuan Kenaikan Golongan',
            'user' => User::where('id_user', $id)->with('dataDiri')->first(),
            'dosen' => GolonganUsers::where('id_user', $id)->where('status', 'aktif')->with(['golongan'])->orderBy('id_golongan_user', 'desc')->first(),
            'golongans' => Golongans::all()
        ];
        return view('dosen.pengajuan.golongan.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'golongan' => 'required',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date',
            'sk' => 'required|file|mimes:pdf|max:2048'
        ]);

        $id = Auth::user()->id_user;
        $golongan = Golongans::findOrFail($request->golongan);

        $timestampedName = null;
        if ($request->hasFile('sk')) {
            // ===== Simpan sk profil =====
            $originalName = $request->file('sk')->getClientOriginalName();
            $timestampedName = 'golongan_' . time() . '_' . $originalName;
            // Simpan ke storage/app/sk
            $request->file('sk')->storeAs('sk', $timestampedName);
        }

        PengajuanGolongans::create([
            'id_user' => $id,
            'id_golongan' => $golongan->id_golongan,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai ?? null,
            'status' => 'pending',
            'sk' => $timestampedName
        ]);

        return redirect()->route('dosen.pengajuan.golongan')->with('Kenaikan golongan berhasil diajukan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pengajuan = PengajuanGolongans::where('id_pengajuan_golongan', $id)->with(['user.dataDiri', 'golongan'])->first();
        $data = [
            'page' => 'Pengajuan Golongan',
            'selected' => 'Pengajuan Golongan',
            'title' => 'Pengajuan Kenaikan Golongan',
            'pengajuan' => $pengajuan,


        ];

        // dd($pengajuan);

        if ($pengajuan->status === 'pending') {
            return view('dosen.pengajuan.golongan.show', $data);
        } else {
            return view('dosen.pengajuan.golongan.riwayat', $data);
        }
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
