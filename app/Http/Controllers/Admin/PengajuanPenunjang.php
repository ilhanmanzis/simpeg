<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dokumens;
use App\Models\PengajuanPenunjangs;
use App\Models\Penunjangs;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengajuanPenunjang extends Controller
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
        $data = [
            'page'     => 'Pengajuan BKD Penunjang',
            'selected' => 'Pengajuan BKD Penunjang',
            'title'    => 'Pengajuan BKD Penunjang',
            'pengajuans' => PengajuanPenunjangs::where('status', 'pending')
                ->with(['user.dataDiri'])
                ->orderBy('updated_at', 'desc')
                ->paginate(10)
                ->appends(request()->except('page', 'riwayat_page')),
            'riwayats' => PengajuanPenunjangs::with(['user.dataDiri'])
                ->where('status', '!=', 'pending')
                ->orderBy('updated_at', 'desc')
                ->paginate(10, ['*'], 'riwayat_page')
                ->appends(request()->except('page'))
                ->fragment('riwayat'),
        ];

        return view('admin.pengajuan.bkd.penunjang.index', $data);
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pengajuan = PengajuanPenunjangs::where('id_pengajuan', $id)
            ->with(['user.dataDiri'])
            ->firstOrFail();

        $data = [
            'page'     => 'Pengajuan BKD Penunjang',
            'selected' => 'Pengajuan BKD Penunjang',
            'title'    => 'Pengajuan BKD Penunjang',
            'pengajuan' => $pengajuan
        ];

        if ($pengajuan->status === 'pending') {
            return view('admin.pengajuan.bkd.penunjang.show', $data);
        }
        return view('admin.pengajuan.bkd.penunjang.riwayat', $data);
    }

    public function tolak(Request $request, string $id)
    {
        $perubahan = PengajuanPenunjangs::findOrFail($id);

        if ($perubahan->dokumen && Storage::exists('bkd/' . $perubahan->dokumen)) {
            Storage::delete('bkd/' . $perubahan->dokumen);
        }


        $perubahan->update([
            'status'        => 'ditolak',
            'keterangan'    => $request->keterangan
        ]);

        return redirect()->route('admin.pengajuan.penunjang')
            ->with('success', 'Pengajuan perubahan penunjang ditolak.');
    }

    public function setuju(string $id)
    {
        $perubahan = PengajuanPenunjangs::where('id_pengajuan', $id)->with(['user.dataDiri'])->firstOrFail();
        // nomor_dokumen terakhir
        $lastDokumen = Dokumens::orderBy('nomor_dokumen', 'desc')->first();
        $lastNumber  = $lastDokumen ? (int) $lastDokumen->nomor_dokumen : 0;


        // surat dokumen
        $lastNumber++;
        $dokumenId = str_pad($lastNumber, 7, '0', STR_PAD_LEFT);

        $dokumen = storage_path("app/private/bkd/{$perubahan->dokumen}");

        if (!file_exists($dokumen)) {
            return response()->json([
                'success' => false,
                'message' => 'File tidak ditemukan.'
            ], 404);
        }

        // Path tujuan di Google Drive
        $dokumenDestinationPath = "{$perubahan->user->npp}/bkd/penunjang/{$perubahan->name}/{$perubahan->dokumen}";

        // Upload ke Google Drive via service (agar dapat file_id & URL)
        $dokumenResult = $this->googleDriveService->uploadFileAndGetUrl($dokumen, $dokumenDestinationPath);

        Dokumens::create([
            'nomor_dokumen'  => $dokumenId,
            'path_file'      => $dokumenDestinationPath,
            'file_id'        => $dokumenResult['file_id'] ?? null,
            'view_url'       => $dokumenResult['view_url'] ?? null,
            'download_url'   => $dokumenResult['download_url'] ?? null,
            'preview_url'    => $dokumenResult['preview_url'] ?? null,
            'id_user'        => $perubahan->id_user,
            'tanggal_upload' => now()
        ]);

        Penunjangs::create([
            'id_user' => $perubahan->id_user,
            'name' => $perubahan->name,
            'penyelenggara' => $perubahan->penyelenggara,
            'tanggal_diperoleh' => $perubahan->tanggal_diperoleh,
            'dokumen' => $dokumenId,
        ]);

        //    hapus file di storage
        @unlink($dokumen);

        $perubahan->status = 'disetujui';

        $perubahan->save();

        return redirect()->route('admin.pengajuan.penunjang')
            ->with('success', 'Pengajuan BKD Penunjang disetujui.');
    }
}
