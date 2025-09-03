<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Admin\PengajuanPenunjang;
use App\Http\Controllers\Controller;
use App\Models\PengajuanPenunjangs;
use App\Models\Penunjangs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Penunjang extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $judul = $request->get('judul');
        $id = Auth::user()->id_user;

        $data = [
            'page' => 'BKD Penunjang',
            'selected' => 'BKD Penunjang',
            'title' => 'BKD Penunjang',
            'penunjangs' => Penunjangs::where('id_user', $id)
                ->searchJudul($judul)
                ->orderBy('created_at', 'desc')
                ->paginate(10)
                ->appends(request()->except('page', 'riwayat_page')),
            'riwayats' => PengajuanPenunjangs::where('id_user', $id)->where('status', '!=', 'disetujui')
                ->orderBy('updated_at', 'desc')
                ->paginate(10, ['*'], 'riwayat_page')
                ->appends(request()->except('page'))
                ->fragment('riwayat'),
        ];

        return view('dosen.bkd.penunjang.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'page' => 'BKD Penunjang',
            'selected' => 'BKD Penunjang',
            'title' => 'Tambah BKD Penunjang',
        ];
        return view('dosen.bkd.penunjang.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'penyelenggara' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'dokumen' => 'required|file|mimes:pdf|max:2048',
        ]);

        $idUser = Auth::user()->id_user;



        $dokumenFile = $request->file("dokumen");
        $dokumenName = time() . '_' . $dokumenFile->getClientOriginalName();
        $dokumenFile->storeAs('bkd', $dokumenName);

        PengajuanPenunjangs::create([
            'id_user' => $idUser,
            'name' => $request->input('judul'),
            'penyelenggara' => $request->input('penyelenggara'),
            'tanggal_diperoleh' => $request->input('tanggal'),
            'dokumen' => $dokumenName,
            'status' => 'pending'
        ]);

        return redirect()->route('dosen.penunjang')->with('success', 'BKD Penunjang berhasil Diajukan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $idUser = Auth::user()->id_user;
        $penunjang = Penunjangs::where('id_penunjang', $id)->with(['user.dataDiri', 'dokumenPenunjang'])->first();
        if (!$penunjang) {
            abort(404);
        }

        if ($idUser !== $penunjang->user->id_user) {
            return redirect()->route('dosen.penunjang')->with('success', 'Anda tidak memiliki akses ke halaman tersebut.');
        }
        $data = [
            'page' => 'BKD Penunjang',
            'selected' => 'BKD Penunjang',
            'title' => 'BKD Penunjang ' . $penunjang->user->dataDiri->name,
            'penunjang' => $penunjang,
        ];
        return view('dosen.bkd.penunjang.show', $data);
    }

    public function riwayat(string $id)
    {
        $idUser = Auth::user()->id_user;
        $penunjang = PengajuanPenunjangs::where('id_pengajuan', $id)->with(['user.dataDiri'])->first();
        if (!$penunjang) {
            abort(404);
        }
        if ($idUser !== $penunjang->user->id_user) {
            return redirect()->route('dosen.penunjang')->with('success', 'Anda tidak memiliki akses ke halaman tersebut.');
        }
        $data = [
            'page' => 'BKD Penunjang',
            'selected' => 'BKD Penunjang',
            'title' => 'BKD Penunjang ' . $penunjang->user->dataDiri->name,
            'pengajuan' => $penunjang,
        ];
        return view('dosen.bkd.penunjang.riwayat', $data);
    }
}
