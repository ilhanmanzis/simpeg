<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penelitians;
use App\Models\PengajuanPenelitians;
use Illuminate\Http\Request;

class PengajuanPenelitian extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = [
            'page'     => 'Pengajuan BKD Penelitian',
            'selected' => 'Pengajuan BKD Penelitian',
            'title'    => 'Pengajuan BKD Penelitian',
            'pengajuans' => PengajuanPenelitians::where('status', 'pending')
                ->with(['user.dataDiri'])
                ->orderBy('updated_at', 'desc')
                ->paginate(10)
                ->appends(request()->except('page', 'riwayat_page')),
            'riwayats' => PengajuanPenelitians::with(['user.dataDiri'])
                ->where('status', '!=', 'pending')
                ->orderBy('updated_at', 'desc')
                ->paginate(10, ['*'], 'riwayat_page')
                ->appends(request()->except('page'))
                ->fragment('riwayat'),
        ];

        return view('admin.pengajuan.bkd.penelitian.index', $data);
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
        $pengajuan = PengajuanPenelitians::where('id_pengajuan', $id)
            ->with(['user.dataDiri'])
            ->firstOrFail();

        $data = [
            'page'     => 'Pengajuan BKD Penelitian',
            'selected' => 'Pengajuan BKD Penelitian',
            'title'    => 'Pengajuan BKD Penelitian',
            'pengajuan' => $pengajuan
        ];

        if ($pengajuan->status === 'pending') {
            return view('admin.pengajuan.bkd.penelitian.show', $data);
        }
        return view('admin.pengajuan.bkd.penelitian.riwayat', $data);
    }

    public function tolak(Request $request, string $id)
    {
        $perubahan = PengajuanPenelitians::findOrFail($id);

        $perubahan->status     = 'ditolak';
        $perubahan->keterangan = $request->input('keterangan');
        $perubahan->save();

        return redirect()->route('admin.pengajuan.penelitian')
            ->with('success', 'Pengajuan BKD Penelitian ditolak.');
    }

    public function setuju(string $id)
    {
        $perubahan = PengajuanPenelitians::findOrFail($id);

        Penelitians::create([
            'id_user' => $perubahan->id_user,
            'judul' => $perubahan->judul,
            'url' => $perubahan->url,
        ]);

        $perubahan->status     = 'disetujui';

        $perubahan->save();

        return redirect()->route('admin.pengajuan.penelitian')
            ->with('success', 'Pengajuan BKD Penelitian disetujui.');
    }
}
