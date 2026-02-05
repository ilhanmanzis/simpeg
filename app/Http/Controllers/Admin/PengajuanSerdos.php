<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dokumens;
use App\Models\PengajuanSerdoss;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        $data = [
            'page'     => 'Pengajuan Serdos',
            'selected' => 'Pengajuan Serdos',
            'title'    => 'Pengajuan  Sertifikat Dosen',
            'pengajuans' => PengajuanSerdoss::where('status', 'pending')
                ->with(['user.dataDiri'])
                ->orderBy('updated_at', 'desc')
                ->paginate(10)
                ->appends(request()->except('page', 'riwayat_page')),
            'riwayats' => PengajuanSerdoss::with(['user.dataDiri'])
                ->where('status', '!=', 'pending')
                ->orderBy('updated_at', 'desc')
                ->paginate(10, ['*'], 'riwayat_page')
                ->appends(request()->except('page'))
                ->fragment('riwayat'),
        ];

        return view('admin.pengajuan.serdos.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

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
        $pengajuan = PengajuanSerdoss::where('id_pengajuan', $id)
            ->with(['user.dataDiri'])
            ->firstOrFail();

        $data = [
            'page'     => 'Pengajuan Serdos',
            'selected' => 'Pengajuan Serdos',
            'title'    => 'Pengajuan Serdos',
            'pengajuan' => $pengajuan
        ];

        if ($pengajuan->status === 'pending') {
            return view('admin.pengajuan.serdos.show', $data);
        }
        return view('admin.pengajuan.serdos.riwayat', $data);
    }

    public function tolak(Request $request, string $id)
    {
        $perubahan = PengajuanSerdoss::findOrFail($id);

        if ($perubahan->serdos && Storage::exists('sertifikat/' . $perubahan->serdos)) {
            Storage::delete('sertifikat/' . $perubahan->serdos);
        }

        $perubahan->status     = 'ditolak';
        $perubahan->keterangan = $request->input('keterangan');
        $perubahan->save();

        return redirect()->route('admin.pengajuan.serdos')
            ->with('success', 'Pengajuan serdos ditolak.');
    }
    public function setuju(string $id)
    {
        $perubahan = PengajuanSerdoss::with([
            'user.dataDiri.serdosen',
        ])->findOrFail($id);

        $user = $perubahan->user;

        $oldFileId = $user->dataDiri->serdosen->file_id ?? null;

        $tersertifikasi = $perubahan->tersertifikasi;

        if ($tersertifikasi === 'sudah') {

            if ($user->dataDiri->serdos !== null && $user->dataDiri->serdos !== '') {


                $localPath = storage_path("app/private/sertifikat/{$perubahan->serdos}");
                $destinationSerdosPath = "{$user->npp}/datadiri/serdos/{$perubahan->serdos}";

                // Upload ke Google Drive
                $resultSerdos = $this->googleDriveService->uploadFileAndGetUrl(
                    $localPath,
                    $destinationSerdosPath
                );

                $user->dataDiri->serdosen()->update([
                    'path_file'      => $destinationSerdosPath,
                    'file_id'        => $resultSerdos['file_id'],
                    'view_url'       => $resultSerdos['view_url'],
                    'download_url'   => $resultSerdos['download_url'],
                    'preview_url'    => $resultSerdos['preview_url'],
                    'tanggal_upload' => now()
                ]);

                // Hapus file lama di Drive kalau ada file baru & punya file_id lama
                if ($oldFileId) {
                    try {
                        $this->googleDriveService->deleteById($oldFileId);
                    } catch (\Throwable $e) {
                        // diamkan agar alur tetap sama
                    }
                }
            } else {
                // Ambil nomor dokumen terakhir
                $lastDokumen = Dokumens::orderBy('nomor_dokumen', 'desc')->first();
                $lastNumber  = $lastDokumen ? (int) $lastDokumen->nomor_dokumen : 0;

                $lastNumber++;
                $newIdSerdos = str_pad($lastNumber, 7, '0', STR_PAD_LEFT);

                $localPath = storage_path("app/private/sertifikat/{$perubahan->serdos}");
                $destinationSerdosPath = "{$user->npp}/datadiri/serdos/{$perubahan->serdos}";

                // Upload ke Google Drive
                $resultSerdos = $this->googleDriveService->uploadFileAndGetUrl(
                    $localPath,
                    $destinationSerdosPath
                );
                Dokumens::create([
                    'nomor_dokumen'  => $newIdSerdos,
                    'path_file'      => $destinationSerdosPath,
                    'file_id'        => $resultSerdos['file_id'],
                    'view_url'       => $resultSerdos['view_url'],
                    'download_url'   => $resultSerdos['download_url'],
                    'preview_url'    => $resultSerdos['preview_url'],
                    'id_user'        => $user->id_user,
                    'tanggal_upload' => now()
                ]);

                $user->dataDiri->update([
                    'tersertifikasi' => $tersertifikasi,
                    'serdos'        => $newIdSerdos,
                ]);
            }
            if ($perubahan->serdos && Storage::exists('sertifikat/' . $perubahan->serdos)) {
                Storage::delete('sertifikat/' . $perubahan->serdos);
            }
            $perubahan->status     = 'disetujui';
            $perubahan->keterangan = null;
            $perubahan->save();
        } else {
            // Hapus file lama di Drive kalau ada file baru & punya file_id lama
            if ($oldFileId) {
                try {
                    $this->googleDriveService->deleteById($oldFileId);
                } catch (\Throwable $e) {
                    // diamkan agar alur tetap sama
                }
            }
            $user->dataDiri->update([
                'tersertifikasi' => $tersertifikasi,
                'serdos'        => null,
            ]);
            $perubahan->status     = 'disetujui';
            $perubahan->keterangan = null;
            $perubahan->save();
        }
        return redirect()->route('admin.pengajuan.serdos')->with('success', 'Sertifikat Dosen berhasil diperbarui.');
    }
}
