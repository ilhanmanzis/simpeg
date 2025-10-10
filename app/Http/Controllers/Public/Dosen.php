<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Sertifikats;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class Dosen extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $keyword = $request->get('dosen');
        $data = [
            'page' => 'Dosen',
            'title' => 'Data Dosen',
            'dosens'   => User::where('role', 'dosen')->with(['dataDiri'])
                ->when($keyword, function ($query) use ($keyword) {
                    $query->searchDosen($keyword);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10)
                ->withQueryString()
        ];

        return view('public/dosen', $data);
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
    public function show(Request $request, string $id)
    {
        $today = Carbon::today()->toDateString();

        $judul = $request->get('judul');

        $dosen = User::where('npp', $id)->where('role', 'dosen')->with([
            'dataDiri.dokumen',
            'golongan' => function ($q) {
                $q->where('status', 'aktif')->orderByDesc('id_golongan_user')
                    ->with('golongan');
            },
            'fungsional' => function ($q) {
                $q->where('status', 'aktif')->orderByDesc('id_fungsional_user')
                    ->with('fungsional');
            },
            'struktural' => function ($q) use ($today) {
                $q->where('status', 'aktif')->whereDate('tanggal_selesai', '>=', $today)->orderByDesc('id_struktural_user')
                    ->with('struktural');
            },
            'pendidikan' => function ($q) {
                $q->orderBy('id_jenjang')
                    ->with('jenjang');
            },
        ])->firstOrFail();
        $sertifikats = Sertifikats::where('id_user', $dosen->id_user)
            ->searchJudul($judul)
            ->with(['kategori', 'dokumenSertifikat'])
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();
        $data = [
            'page' => 'Dosen',
            'title' => 'Data Dosen',
            'dosen'   => $dosen,
            'sertifikats' => $sertifikats
        ];

        // dd($sertifikats);
        return view('public/dosen-show', $data);
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
