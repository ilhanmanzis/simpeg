<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penelitians;
use App\Models\PengajuanPenelitians;
use App\Notifications\StatusPengajuanNotification;
use App\Services\NotificationService;
use App\Services\SerdosService;
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
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pengajuan = PengajuanPenelitians::where('id_pengajuan', $id)
            ->with(['user.dataDiri', 'index'])
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
        $user = $perubahan->user;

        $perubahan->status     = 'ditolak';
        $perubahan->keterangan = $request->input('keterangan');
        $perubahan->save();
        NotificationService::notifyUser(
            $user,
            'Pengajuan BKD Penelitian Ditolak',
            'Pengajuan BKD Penelitian ditolak. Alasan: '
                . $request->keterangan ?? '-',
            'dosen.penelitian.riwayat',
            [
                'id'    => $perubahan->id_pengajuan,
                'jenis' => 'penelitian'
            ]
        );
        $user->notify(
            new StatusPengajuanNotification($perubahan, $perubahan->status, 'BKD Penelitian', 'dosen.penelitian.riwayat', $perubahan->id_pengajuan)
        );

        return redirect()->route('admin.pengajuan.penelitian')
            ->with('success', 'Pengajuan BKD Penelitian ditolak.');
    }

    public function setuju(string $id, SerdosService $serdosService)
    {
        $perubahan = PengajuanPenelitians::findOrFail($id);
        $user = $perubahan->user;

        Penelitians::create([
            'id_user' => $perubahan->id_user,
            'judul' => $perubahan->judul,
            'url' => $perubahan->url,
            'id_index' => $perubahan->id_index,
        ]);

        $perubahan->status     = 'disetujui';

        $perubahan->save();

        NotificationService::notifyUser(
            $user,
            'Pengajuan BKD Penelitian Disetujui',
            'Pengajuan BKD Penelitian Anda telah disetujui oleh admin.',
            'dosen.penelitian.riwayat',
            [
                'id'    => $perubahan->id_pengajuan,
                'jenis' => 'penelitian'
            ]
        );
        $serdosService->clearCache($user->id_user);
        $user->notify(
            new StatusPengajuanNotification($perubahan, $perubahan->status, 'BKD Penelitian', 'dosen.penelitian.riwayat', $perubahan->id_pengajuan)
        );

        return redirect()->route('admin.pengajuan.penelitian')
            ->with('success', 'Pengajuan BKD Penelitian disetujui.');
    }
}
