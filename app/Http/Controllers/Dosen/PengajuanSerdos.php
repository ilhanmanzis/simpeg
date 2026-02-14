<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\PengajuanSerdoss;
use App\Models\User;
use App\Services\GoogleDriveService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengajuanSerdos extends Controller
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
            'page' => 'Pengajuan Serdos',
            'selected' => 'Pengajuan Serdos',
            'title' => 'Pengajuan Sertifikat Dosen',
            'pengajuans' => PengajuanSerdoss::where('id_user', $id)->orderBy('updated_at', 'desc')->paginate(10)->withQueryString()
        ];

        return view('dosen.pengajuan.serdos.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $id = Auth::user()->id_user;
        $data = [
            'page'     => 'Pengajuan Serdos',
            'selected' => 'Pengajuan Serdos',
            'title'    => 'Ubah Serdos Dosen',
            'dosen'    => User::where('id_user', $id)->with(['dataDiri.serdosen'])->first()
        ];
        return view('dosen.pengajuan.serdos.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $user = Auth::user();
        $id = $user->id_user;
        $request->validate([
            'tersertifikasi'         => 'required',
            'serdos'                 => 'required_if:tersertifikasi,sudah|file|mimes:pdf|max:2048',
        ]);

        if ($request->tersertifikasi === 'sudah') {
            if ($request->hasFile('serdos')) {
                // ===== Simpan serdos =====
                $originalName = $request->file('serdos')->getClientOriginalName();
                $timestampedName = 'serdos_' . time() . '_' . $originalName;
                // Simpan ke storage/app/sertifikat
                $request->file('serdos')->storeAs('sertifikat', $timestampedName);
            }
            $pengajuan = PengajuanSerdoss::create([
                'id_user'        => $id,
                'tersertifikasi' => $request->tersertifikasi,
                'serdos'        => $timestampedName,
                'status'         => 'pending',
            ]);
        } else {
            $pengajuan =  PengajuanSerdoss::create([
                'id_user'        => $id,
                'tersertifikasi' => $request->tersertifikasi,
                'serdos'        => null,
                'status'         => 'pending',
            ]);
        }

        NotificationService::notifyAdmin(
            'Pengajuan Perubahan Serdos Baru',
            'Ada pengajuan perubahan serdos dari '
                . $user->dataDiri->name,
            'admin.pengajuan.serdos.show',
            [
                'id'    => $pengajuan->id_pengajuan,
                'jenis' => 'serdos'
            ]
        );
        return redirect()->route('dosen.pengajuan.serdos')->with('success', 'Sertifikat Dosen berhasil diajukan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pengajuan = PengajuanSerdoss::where('id_pengajuan', $id)->with(['user.dataDiri'])->first();
        $idUser = Auth::user()->id_user;
        if (!$pengajuan) {
            abort(404);
        }

        if ($idUser != $pengajuan->user->id_user) {
            return redirect()->route('dosen.pengajuan.serdos')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }
        $data = [
            'page' => 'Pengajuan Serdos',
            'selected' => 'Pengajuan Serdos',
            'title' => 'Pengajuan Sertifikat Dosen',
            'pengajuan' => $pengajuan,
        ];



        if ($pengajuan->status === 'pending') {
            return view('dosen.pengajuan.serdos.show', $data);
        } else {
            return view('dosen.pengajuan.serdos.riwayat', $data);
        }
    }
}
