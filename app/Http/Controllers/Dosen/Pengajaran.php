<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Pengajarans;
use App\Models\PengajuanPengajaranDetails;
use App\Models\PengajuanPengajarans;
use App\Models\Semesters;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Pengajaran extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $judul = $request->get('judul');
        $id = Auth::user()->id_user;

        $data = [
            'page' => 'BKD Pengajaran',
            'selected' => 'BKD Pengajaran',
            'title' => 'BKD Pengajaran',
            'pengajarans' => Pengajarans::where('id_user', $id)
                // ->searchJudul($judul)
                ->with('semester')
                ->orderBy('created_at', 'desc')
                ->paginate(10)
                ->appends(request()->except('page', 'riwayat_page')),
            'riwayats' => PengajuanPengajarans::where('id_user', $id)->where('status', '!=', 'disetujui')
                ->with('semester')
                ->orderBy('updated_at', 'desc')
                ->paginate(10, ['*'], 'riwayat_page')
                ->appends(request()->except('page'))
                ->fragment('riwayat'),
        ];

        return view('dosen.bkd.pengajaran.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'page' => 'BKD Pengajaran',
            'selected' => 'BKD Pengajaran',
            'title' => 'Tambah BKD Pengajaran',
            'semesters' => Semesters::all()
        ];
        return view('dosen.bkd.pengajaran.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'matkul'                => 'required|array|min:1',
            'matkul.*.nama_matkul'  => 'required|string|max:255',
            'matkul.*.sks'          => 'required|integer|min:1',
            'matkul.*.nilai'        => 'required|file|mimes:pdf|max:2048',
            'sk'                    => 'required|file|mimes:pdf|max:2048',
            'semester'              => 'required|integer|exists:semester,id_semester'
        ]);

        $idUser = Auth::user()->id_user;

        // sk
        $skFile = $request->file("sk");
        $skName = time() . '_' . $skFile->getClientOriginalName();
        // Simpan ke storage/app/bkd
        $skFile->storeAs('bkd', $skName);

        $pengajaran = PengajuanPengajarans::create([
            'id_user' => $idUser,
            'id_semester' => $request->input('semester'),
            'sk' => $skName,
            'status' => 'pending'
        ]);

        $nilaiName = null;
        foreach ($request->input('matkul', []) as $index => $row) {

            // nilai
            $nilaiFile =  $request->file("matkul.$index.nilai");
            $nilaiName = time() . '_' . $nilaiFile->getClientOriginalName();
            // Simpan ke storage/app/bkd
            $nilaiFile->storeAs('bkd', $nilaiName);

            PengajuanPengajaranDetails::create([
                'id_pengajuan_pengajaran' => $pengajaran->id_pengajuan_pengajaran,
                'nama_matkul' => $row['nama_matkul'],
                'sks' => $row['sks'],
                'nilai' => $nilaiName,
            ]);
        }
        return redirect()->route('dosen.pengajaran')->with('success', 'BKD Pengajaran berhasil Diajukan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $idUser = Auth::user()->id_user;
        $pengajaran = Pengajarans::where('id_pengajaran', $id)->with(['user.dataDiri', 'skPengajaran', 'detail.nilaiPengajaran', 'semester'])->first();
        if (!$pengajaran) {
            abort(404);
        }

        if ($idUser !== $pengajaran->user->id_user) {
            return redirect()->route('dosen.pengajaran')->with('success', 'Anda tidak memiliki akses ke halaman tersebut.');
        }
        $data = [
            'page' => 'BKD Pengajaran',
            'selected' => 'BKD Pengajaran',
            'title' => 'BKD Pengajaran ' . $pengajaran->user->dataDiri->name,
            'pengajaran' => $pengajaran,
        ];
        return view('dosen.bkd.pengajaran.show', $data);
    }

    public function riwayat(string $id)
    {
        $idUser = Auth::user()->id_user;
        $pengajaran = PengajuanPengajarans::where('id_pengajuan_pengajaran', $id)->with(['user.dataDiri', 'detail', 'semester'])->first();
        if (!$pengajaran) {
            abort(404);
        }
        if ($idUser !== $pengajaran->user->id_user) {
            return redirect()->route('dosen.pengajaran')->with('success', 'Anda tidak memiliki akses ke halaman tersebut.');
        }
        $data = [
            'page' => 'BKD Pengajaran',
            'selected' => 'BKD Pengajaran',
            'title' => 'BKD Pengajaran ' . $pengajaran->user->dataDiri->name,
            'pengajuan' => $pengajaran,
        ];
        return view('dosen.bkd.pengajaran.riwayat', $data);
    }
}
