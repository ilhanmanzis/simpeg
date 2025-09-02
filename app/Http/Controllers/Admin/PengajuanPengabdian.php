<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dokumens;
use App\Models\Pengabdians;
use App\Models\PengajuanPengabdians;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengajuanPengabdian extends Controller
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
            'page'     => 'Pengajuan BKD Pengabdian',
            'selected' => 'Pengajuan BKD Pengabdian',
            'title'    => 'Pengajuan BKD Pengabdian',
            'pengajuans' => PengajuanPengabdians::where('status', 'pending')
                ->with(['user.dataDiri'])
                ->orderBy('updated_at', 'desc')
                ->paginate(10)
                ->appends(request()->except('page', 'riwayat_page')),
            'riwayats' => PengajuanPengabdians::with(['user.dataDiri'])
                ->where('status', '!=', 'pending')
                ->orderBy('updated_at', 'desc')
                ->paginate(10, ['*'], 'riwayat_page')
                ->appends(request()->except('page'))
                ->fragment('riwayat'),
        ];

        return view('admin.pengajuan.bkd.pengabdian.index', $data);
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pengajuan = PengajuanPengabdians::where('id_pengajuan', $id)
            ->with(['user.dataDiri'])
            ->firstOrFail();

        $data = [
            'page'     => 'Pengajuan BKD Pengabdian',
            'selected' => 'Pengajuan BKD Pengabdian',
            'title'    => 'Pengajuan BKD Pengabdian',
            'pengajuan' => $pengajuan
        ];

        if ($pengajuan->status === 'pending') {
            return view('admin.pengajuan.bkd.pengabdian.show', $data);
        }
        return view('admin.pengajuan.bkd.pengabdian.riwayat', $data);
    }

    public function tolak(Request $request, string $id)
    {
        $perubahan = PengajuanPengabdians::findOrFail($id);

        if ($perubahan->permohonan && Storage::exists('bkd/' . $perubahan->permohonan)) {
            Storage::delete('bkd/' . $perubahan->permohonan);
        }
        if ($perubahan->tugas && Storage::exists('bkd/' . $perubahan->tugas)) {
            Storage::delete('bkd/' . $perubahan->tugas);
        }
        if ($perubahan->modul && Storage::exists('bkd/' . $perubahan->modul)) {
            Storage::delete('bkd/' . $perubahan->modul);
        }
        if ($perubahan->foto && Storage::exists('bkd/' . $perubahan->foto)) {
            Storage::delete('bkd/' . $perubahan->foto);
        }
        if ($perubahan->terimakasih && Storage::exists('bkd/' . $perubahan->terimakasih)) {
            Storage::delete('bkd/' . $perubahan->terimakasih);
        }

        $perubahan->update([
            'status'        => 'ditolak',
            'keterangan'    => $request->keterangan
        ]);

        return redirect()->route('admin.pengajuan.pengabdian')
            ->with('success', 'Pengajuan perubahan pengabdian ditolak.');
    }

    public function setuju(string $id)
    {
        $perubahan = PengajuanPengabdians::where('id_pengajuan', $id)->with(['user.dataDiri'])->firstOrFail();

        // nomor_dokumen terakhir
        $lastDokumen = Dokumens::orderBy('nomor_dokumen', 'desc')->first();
        $lastNumber  = $lastDokumen ? (int) $lastDokumen->nomor_dokumen : 0;


        // surat permohonan
        $lastNumber++;
        $permohonanId = str_pad($lastNumber, 7, '0', STR_PAD_LEFT);

        $permohonan = storage_path("app/private/bkd/{$perubahan->permohonan}");

        if (!file_exists($permohonan)) {
            return response()->json([
                'success' => false,
                'message' => 'File tidak ditemukan.'
            ], 404);
        }

        // Path tujuan di Google Drive
        $permohonanDestinationPath = "{$perubahan->user->npp}/bkd/pengabdian/{$perubahan->judul}/{$perubahan->permohonan}";

        // Upload ke Google Drive via service (agar dapat file_id & URL)
        $permohonanResult = $this->googleDriveService->uploadFileAndGetUrl($permohonan, $permohonanDestinationPath);

        Dokumens::create([
            'nomor_dokumen'  => $permohonanId,
            'path_file'      => $permohonanDestinationPath,
            'file_id'        => $permohonanResult['file_id'] ?? null,
            'view_url'       => $permohonanResult['view_url'] ?? null,
            'download_url'   => $permohonanResult['download_url'] ?? null,
            'preview_url'    => $permohonanResult['preview_url'] ?? null,
            'id_user'        => $perubahan->id_user,
            'tanggal_upload' => now()
        ]);


        // surat tugas
        $lastNumber++;
        $tugasId = str_pad($lastNumber, 7, '0', STR_PAD_LEFT);

        $tugas = storage_path("app/private/bkd/{$perubahan->tugas}");

        if (!file_exists($tugas)) {
            return response()->json([
                'success' => false,
                'message' => 'File tidak ditemukan.'
            ], 404);
        }

        // Path tujuan di Google Drive
        $tugasDestinationPath = "{$perubahan->user->npp}/bkd/pengabdian/{$perubahan->judul}/{$perubahan->tugas}";

        // Upload ke Google Drive via service (agar dapat file_id & URL)
        $tugasResult = $this->googleDriveService->uploadFileAndGetUrl($tugas, $tugasDestinationPath);

        Dokumens::create([
            'nomor_dokumen'  => $tugasId,
            'path_file'      => $tugasDestinationPath,
            'file_id'        => $tugasResult['file_id'] ?? null,
            'view_url'       => $tugasResult['view_url'] ?? null,
            'download_url'   => $tugasResult['download_url'] ?? null,
            'preview_url'    => $tugasResult['preview_url'] ?? null,
            'id_user'        => $perubahan->id_user,
            'tanggal_upload' => now()
        ]);


        // modul
        $lastNumber++;
        $modulId = str_pad($lastNumber, 7, '0', STR_PAD_LEFT);

        $modul = storage_path("app/private/bkd/{$perubahan->modul}");

        if (!file_exists($modul)) {
            return response()->json([
                'success' => false,
                'message' => 'File tidak ditemukan.'
            ], 404);
        }

        // Path tujuan di Google Drive
        $modulDestinationPath = "{$perubahan->user->npp}/bkd/pengabdian/{$perubahan->judul}/{$perubahan->modul}";

        // Upload ke Google Drive via service (agar dapat file_id & URL)
        $modulResult = $this->googleDriveService->uploadFileAndGetUrl($modul, $modulDestinationPath);

        Dokumens::create([
            'nomor_dokumen'  => $modulId,
            'path_file'      => $modulDestinationPath,
            'file_id'        => $modulResult['file_id'] ?? null,
            'view_url'       => $modulResult['view_url'] ?? null,
            'download_url'   => $modulResult['download_url'] ?? null,
            'preview_url'    => $modulResult['preview_url'] ?? null,
            'id_user'        => $perubahan->id_user,
            'tanggal_upload' => now()
        ]);

        // foto
        $lastNumber++;
        $fotoId = str_pad($lastNumber, 7, '0', STR_PAD_LEFT);

        $foto = storage_path("app/private/bkd/{$perubahan->foto}");

        if (!file_exists($foto)) {
            return response()->json([
                'success' => false,
                'message' => 'File tidak ditemukan.'
            ], 404);
        }

        // Path tujuan di Google Drive
        $fotoDestinationPath = "{$perubahan->user->npp}/bkd/pengabdian/{$perubahan->judul}/{$perubahan->foto}";

        // Upload ke Google Drive via service (agar dapat file_id & URL)
        $fotoResult = $this->googleDriveService->uploadFileAndGetUrl($foto, $fotoDestinationPath);

        Dokumens::create([
            'nomor_dokumen'  => $fotoId,
            'path_file'      => $fotoDestinationPath,
            'file_id'        => $fotoResult['file_id'] ?? null,
            'view_url'       => $fotoResult['view_url'] ?? null,
            'download_url'   => $fotoResult['download_url'] ?? null,
            'preview_url'    => $fotoResult['preview_url'] ?? null,
            'id_user'        => $perubahan->id_user,
            'tanggal_upload' => now()
        ]);

        // terima kasih
        $lastNumber++;
        $terimaKasihId = str_pad($lastNumber, 7, '0', STR_PAD_LEFT);

        $terimaKasih = storage_path("app/private/bkd/{$perubahan->terimakasih}");

        if (!file_exists($terimaKasih)) {
            return response()->json([
                'success' => false,
                'message' => 'File tidak ditemukan.'
            ], 404);
        }

        // Path tujuan di Google Drive
        $terimaKasihDestinationPath = "{$perubahan->user->npp}/bkd/pengabdian/{$perubahan->judul}/{$perubahan->terimaKasih}";

        // Upload ke Google Drive via service (agar dapat file_id & URL)
        $terimaKasihResult = $this->googleDriveService->uploadFileAndGetUrl($terimaKasih, $terimaKasihDestinationPath);

        Dokumens::create([
            'nomor_dokumen'  => $terimaKasihId,
            'path_file'      => $terimaKasihDestinationPath,
            'file_id'        => $terimaKasihResult['file_id'] ?? null,
            'view_url'       => $terimaKasihResult['view_url'] ?? null,
            'download_url'   => $terimaKasihResult['download_url'] ?? null,
            'preview_url'    => $terimaKasihResult['preview_url'] ?? null,
            'id_user'        => $perubahan->id_user,
            'tanggal_upload' => now()
        ]);



        Pengabdians::create([
            'id_user' => $perubahan->id_user,
            'judul' => $perubahan->judul,
            'lokasi' => $perubahan->lokasi,
            'terimakasih' => $terimaKasihId,
            'permohonan' => $permohonanId,
            'tugas' => $tugasId,
            'modul' => $modulId,
            'foto' => $fotoId,
        ]);

        //    hapus file di storage
        @unlink($permohonan);
        @unlink($tugas);
        @unlink($modul);
        @unlink($foto);
        @unlink($terimaKasih);

        $perubahan->status = 'disetujui';

        $perubahan->save();

        return redirect()->route('admin.pengajuan.pengabdian')
            ->with('success', 'Pengajuan BKD Pengabdian disetujui.');
    }
}
