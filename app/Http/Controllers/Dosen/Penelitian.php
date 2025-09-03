<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Penelitians;
use App\Models\PengajuanPenelitians;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Penelitian extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $judul = $request->get('judul');
        $id = Auth::user()->id_user;

        $data = [
            'page' => 'BKD Penelitian',
            'selected' => 'BKD Penelitian',
            'title' => 'BKD Penelitian',
            'penelitians' => Penelitians::where('id_user', $id)
                ->searchJudul($judul)
                ->orderBy('created_at', 'desc')
                ->paginate(10)
                ->appends(request()->except('page', 'riwayat_page')),
            'riwayats' => PengajuanPenelitians::where('id_user', $id)->where('status', '!=', 'disetujui')
                ->orderBy('updated_at', 'desc')
                ->paginate(10, ['*'], 'riwayat_page')
                ->appends(request()->except('page'))
                ->fragment('riwayat'),
        ];

        return view('dosen.bkd.penelitian.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'page' => 'BKD Penelitian',
            'selected' => 'BKD Penelitian',
            'title' => 'Tambah BKD Penelitian',
        ];
        return view('dosen.bkd.penelitian.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'url' => 'required|string|max:255',
        ]);

        $idUser = Auth::user()->id_user;

        PengajuanPenelitians::create([
            'id_user' => $idUser,
            'judul' => $request->input('judul'),
            'url' => $request->input('url'),
            'status' => 'pending'
        ]);

        return redirect()->route('dosen.penelitian')->with('success', 'BKD Penelitian berhasil Diajukan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $idUser = Auth::user()->id_user;

        $penelitian = Penelitians::where('id_penelitian', $id)->with(['user.dataDiri'])->first();

        if (!$penelitian) {
            abort(404);
        }
        if ($idUser !== $penelitian->user->id_user) {
            return redirect()->route('dosen.penelitian')->with('success', 'Anda tidak memiliki akses ke halaman tersebut.');
        }
        $data = [
            'page' => 'BKD Penelitian',
            'selected' => 'BKD Penelitian',
            'title' => 'BKD Penelitian ' . $penelitian->user->dataDiri->name,
            'penelitian' => $penelitian,
        ];
        return view('dosen.bkd.penelitian.show', $data);
    }

    public function riwayat(string $id)
    {
        $idUser = Auth::user()->id_user;
        $pengajuan = PengajuanPenelitians::where('id_pengajuan', $id)->with(['user.dataDiri'])->first();
        if (!$pengajuan) {
            abort(404);
        }
        if ($idUser !== $pengajuan->user->id_user) {
            return redirect()->route('dosen.penelitian')->with('success', 'Anda tidak memiliki akses ke halaman tersebut.');
        }
        $data = [
            'page' => 'BKD Penelitian',
            'selected' => 'BKD Penelitian',
            'title' => 'BKD Penelitian ' . $pengajuan->user->dataDiri->name,
            'pengajuan' => $pengajuan,
        ];
        return view('dosen.bkd.penelitian.riwayat', $data);
    }
}
