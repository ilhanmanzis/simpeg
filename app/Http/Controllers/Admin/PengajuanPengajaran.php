<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dokumens;
use App\Models\PengajaranDetails;
use App\Models\Pengajarans;
use App\Models\PengajuanPengajarans;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengajuanPengajaran extends Controller
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
            'page'     => 'Pengajuan BKD Pengajaran',
            'selected' => 'Pengajuan BKD Pengajaran',
            'title'    => 'Pengajuan BKD Pengajaran',
            'pengajuans' => PengajuanPengajarans::where('status', 'pending')
                ->with(['user.dataDiri'])
                ->orderBy('updated_at', 'desc')
                ->paginate(10)
                ->appends(request()->except('page', 'riwayat_page')),
            'riwayats' => PengajuanPengajarans::with(['user.dataDiri'])
                ->where('status', '!=', 'pending')
                ->orderBy('updated_at', 'desc')
                ->paginate(10, ['*'], 'riwayat_page')
                ->appends(request()->except('page'))
                ->fragment('riwayat'),
        ];

        return view('admin.pengajuan.bkd.pengajaran.index', $data);
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pengajuan = PengajuanPengajarans::where('id_pengajuan_pengajaran', $id)
            ->with(['user.dataDiri', 'detail'])
            ->firstOrFail();

        $data = [
            'page'     => 'Pengajuan BKD Pengajaran',
            'selected' => 'Pengajuan BKD Pengajaran',
            'title'    => 'Pengajuan BKD Pengajaran',
            'pengajuan' => $pengajuan
        ];

        if ($pengajuan->status === 'pending') {
            return view('admin.pengajuan.bkd.pengajaran.show', $data);
        }
        return view('admin.pengajuan.bkd.pengajaran.riwayat', $data);
    }

    public function tolak(Request $request, string $id)
    {
        $perubahan = PengajuanPengajarans::where('id_pengajuan_pengajaran', $id)->with(['detail'])->firstOrFail();

        if ($perubahan->sk && Storage::exists('bkd/' . $perubahan->sk)) {
            Storage::delete('bkd/' . $perubahan->sk);
        }


        foreach ($perubahan->detail as $detail) {
            if ($detail->nilai && Storage::exists('bkd/' . $detail->nilai)) {
                Storage::delete('bkd/' . $detail->nilai);
            }
        }

        $perubahan->update([
            'status'        => 'ditolak',
            'keterangan'    => $request->keterangan
        ]);

        return redirect()->route('admin.pengajuan.pengajaran')
            ->with('success', 'Pengajuan perubahan Pengajaran ditolak.');
    }

    public function setuju(string $id)
    {
        $perubahan = PengajuanPengajarans::where('id_pengajuan_pengajaran', $id)->with(['user.dataDiri', 'detail', 'semester'])->firstOrFail();
        // nomor_dokumen terakhir
        $lastDokumen = Dokumens::orderBy('nomor_dokumen', 'desc')->first();
        $lastNumber  = $lastDokumen ? (int) $lastDokumen->nomor_dokumen : 0;


        // surat sk
        $lastNumber++;
        $skId = str_pad($lastNumber, 7, '0', STR_PAD_LEFT);

        $sk = storage_path("app/private/bkd/{$perubahan->sk}");

        if (!file_exists($sk)) {
            return response()->json([
                'success' => false,
                'message' => 'File tidak ditemukan.'
            ], 404);
        }

        // Path tujuan di Google Drive
        $skDestinationPath = "{$perubahan->user->npp}/bkd/pengajaran/{$perubahan->semester->nama_semester}/{$perubahan->sk}";

        // Upload ke Google Drive via service (agar dapat file_id & URL)
        $skResult = $this->googleDriveService->uploadFileAndGetUrl($sk, $skDestinationPath);

        Dokumens::create([
            'nomor_dokumen'  => $skId,
            'path_file'      => $skDestinationPath,
            'file_id'        => $skResult['file_id'] ?? null,
            'view_url'       => $skResult['view_url'] ?? null,
            'download_url'   => $skResult['download_url'] ?? null,
            'preview_url'    => $skResult['preview_url'] ?? null,
            'id_user'        => $perubahan->id_user,
            'tanggal_upload' => now()
        ]);


        $pengajaran = Pengajarans::create([
            'id_user' => $perubahan->id_user,
            'id_semester' => $perubahan->id_semester,
            'sk' => $skId,
        ]);


        foreach ($perubahan->detail as $index => $detail) {
            // nilai
            $lastNumber++;
            $nilaiId = str_pad($lastNumber, 7, '0', STR_PAD_LEFT);

            $nilai = storage_path("app/private/bkd/{$detail->nilai}");

            if (!file_exists($nilai)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak ditemukan.'
                ], 404);
            }

            // Path tujuan di Google Drive
            $nilaiDestinationPath = "{$perubahan->user->npp}/bkd/pengajaran/{$perubahan->semester->nama_semester}/{$detail->nilai}";

            // Upload ke Google Drive via service (agar dapat file_id & URL)
            $nilaiResult = $this->googleDriveService->uploadFileAndGetUrl($nilai, $nilaiDestinationPath);

            Dokumens::create([
                'nomor_dokumen'  => $nilaiId,
                'path_file'      => $nilaiDestinationPath,
                'file_id'        => $nilaiResult['file_id'] ?? null,
                'view_url'       => $nilaiResult['view_url'] ?? null,
                'download_url'   => $nilaiResult['download_url'] ?? null,
                'preview_url'    => $nilaiResult['preview_url'] ?? null,
                'id_user'        => $perubahan->id_user,
                'tanggal_upload' => now()
            ]);

            PengajaranDetails::create([
                'id_pengajaran' => $pengajaran->id_pengajaran,
                'nama_matkul' => $detail->nama_matkul,
                'sks' => $detail->sks,
                'nilai' => $nilaiId
            ]);
        }


        if ($perubahan->sk && Storage::exists('bkd/' . $perubahan->sk)) {
            Storage::delete('bkd/' . $perubahan->sk);
        }


        foreach ($perubahan->detail as $detail) {
            if ($detail->nilai && Storage::exists('bkd/' . $detail->nilai)) {
                Storage::delete('bkd/' . $detail->nilai);
            }
        }


        $perubahan->status = 'disetujui';

        $perubahan->save();

        return redirect()->route('admin.pengajuan.pengajaran')
            ->with('success', 'Pengajuan BKD Pengajaran disetujui.');
    }
}
