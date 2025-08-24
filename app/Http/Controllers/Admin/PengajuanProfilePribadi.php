<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanPerubahanDatas;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengajuanProfilePribadi extends Controller
{
    protected $googleDriveService;

    public function __construct(GoogleDriveService $googleDriveService)
    {
        $this->googleDriveService = $googleDriveService;
    }

    public function index()
    {
        $data = [
            'page'     => 'Pengajuan Profile Pribadi',
            'selected' => 'Pengajuan Profile Pribadi',
            'title'    => 'Pengajuan Perubahan Profile Pribadi',
            'pengajuans' => PengajuanPerubahanDatas::with(['user.dataDiri'])
                ->where('status', 'pending')
                ->orderBy('updated_at', 'desc')
                ->paginate(10)
                ->appends(request()->except('page', 'riwayat_page')),
            'riwayats' => PengajuanPerubahanDatas::with(['user.dataDiri'])
                ->where('status', '!=', 'pending')
                ->orderBy('updated_at', 'desc')
                ->paginate(10, ['*'], 'riwayat_page')
                ->appends(request()->except('page'))
                ->fragment('riwayat'),
        ];

        return view('admin.pengajuan.profile.index', $data);
    }

    public function show(string $id)
    {
        $data = [
            'page'     => 'Pengajuan Profile Pribadi',
            'selected' => 'Pengajuan Profile Pribadi',
            'title'    => 'Pengajuan Profile Pribadi',
            'pengajuan' => PengajuanPerubahanDatas::where('id_perubahan', $id)
                ->with(['user.dataDiri.dokumen'])
                ->first()
        ];

        return view('admin.pengajuan.profile.show', $data);
    }

    public function tolak(Request $request, string $id)
    {
        $perubahan = PengajuanPerubahanDatas::findOrFail($id);

        if ($perubahan->foto && Storage::exists('perubahanProfile/' . $perubahan->foto)) {
            Storage::delete('perubahanProfile/' . $perubahan->foto);
        }

        $perubahan->status     = 'ditolak';
        $perubahan->keterangan = $request->input('keterangan');
        $perubahan->save();

        return redirect()->route('admin.pengajuan.profile')
            ->with('success', 'Pengajuan perubahan profile pribadi ditolak.');
    }

    public function setuju(string $id)
    {
        $perubahan = PengajuanPerubahanDatas::where('id_perubahan', $id)
            ->with(['user.dataDiri.dokumen', 'user'])
            ->first();

        // Simpan path lama & lokal untuk dipakai setelah upload
        $localPath = null;
        $fileLama  = null;

        // Jika ada foto baru pada pengajuan
        if ($perubahan->foto) {
            $localPath = storage_path("app/private/perubahanProfile/{$perubahan->foto}");

            if (!file_exists($localPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak ditemukan.'
                ], 404);
            }

            $fileLama = $perubahan->user->dataDiri?->dokumen?->path_file;

            // Path tujuan di Google Drive
            $destinationPath = "{$perubahan->user->npp}/datadiri/{$perubahan->foto}";

            // Upload ke Google Drive via service (agar dapat file_id & URL)
            $result = $this->googleDriveService->uploadFileAndGetUrl($localPath, $destinationPath);

            if ($result && $perubahan->user->dataDiri && $perubahan->user->dataDiri->dokumen) {
                $perubahan->user->dataDiri->dokumen->update([
                    'path_file'    => $destinationPath,
                    'file_id'      => $result['file_id'] ?? null,
                    'view_url'     => $result['view_url'] ?? null,
                    'download_url' => $result['download_url'] ?? null,
                    'preview_url'  => $result['preview_url'] ?? null,
                    'tanggal_upload' => now(),
                ]);
            }
        }

        // (Sesuai logika kamu) Update field data diri dari pengajuan
        $perubahan->user->dataDiri->update([
            'nuptk'            => $perubahan->nuptk ?? null,
            'nip'              => $perubahan->nip ?? null,
            'nidk'             => $perubahan->nidk ?? null,
            'nidn'             => $perubahan->nidn ?? null,
            'name'             => $perubahan->name,
            'no_ktp'           => $perubahan->no_ktp,
            'no_hp'            => $perubahan->no_hp,
            'tanggal_lahir'    => $perubahan->tanggal_lahir,
            'tempat_lahir'     => $perubahan->tempat_lahir,
            'jenis_kelamin'    => $perubahan->jenis_kelamin,
            'agama'            => $perubahan->agama,
            'tanggal_bergabung' => $perubahan->tanggal_bergabung,
            'alamat'           => $perubahan->alamat,
            'rt'               => $perubahan->rt,
            'rw'               => $perubahan->rw,
            'desa'             => $perubahan->desa,
            'kecamatan'        => $perubahan->kecamatan,
            'kabupaten'        => $perubahan->kabupaten,
            'provinsi'         => $perubahan->provinsi,
        ]);

        // Jika ada foto baru: hapus file lokal & file lama di Google Drive
        if ($perubahan->foto && $localPath) {
            if (file_exists($localPath)) {
                @unlink($localPath);
            }
            if (!empty($fileLama)) {
                // hapus file lama di drive
                try {
                    Storage::disk('google')->delete($fileLama);
                } catch (\Throwable $e) {
                }
            }
        }

        $perubahan->update(['status' => 'disetujui']);

        return redirect()->route('admin.pengajuan.profile')
            ->with('success', 'Pengajuan perubahan profile pribadi disetujui.');
    }
}
