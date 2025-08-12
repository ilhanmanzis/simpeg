<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanPerubahanDatas;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yaza\LaravelGoogleDriveStorage\Gdrive;

class PengajuanProfilePribadi extends Controller
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
            'page' => 'Pengajuan Profile Pribadi',
            'selected' => 'Pengajuan Profile Pribadi',
            'title' => 'Pengajuan Perubahan Profile Pribadi',
            'pengajuans' => PengajuanPerubahanDatas::with(['user.dataDiri'])->where('status', 'pending')->orderBy('updated_at', 'desc')->paginate(10)->withQueryString(),
            'riwayats' => PengajuanPerubahanDatas::with(['user.dataDiri'])
                ->where('status', '!=', 'pending')
                ->orderBy('updated_at', 'desc')
                ->paginate(10)
                ->withQueryString()
        ];

        return view('admin.pengajuan.profile.index', $data);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = [
            'page' => 'Pengajuan Profile Pribadi',
            'selected' => 'Pengajuan Profile Pribadi',
            'title' => 'Pengajuan Profile Pribadi',
            'pengajuan' => PengajuanPerubahanDatas::where('id_perubahan', $id)->with(['user.dataDiri.dokumen'])->first()
        ];

        return view('admin.pengajuan.profile.show', $data);
    }


    public function tolak(Request $request, string $id)
    {
        // Ambil data user
        $perubahan = PengajuanPerubahanDatas::findOrFail($id);

        if ($perubahan->foto && Storage::exists('perubahanProfile/' . $perubahan->foto)) {
            // Hapus file foto jika ada
            Storage::delete('perubahanProfile/' . $perubahan->foto);
        }

        // Update status menjadi 'ditolak'
        $perubahan->status = 'ditolak';
        $perubahan->keterangan = $request->input('keterangan');
        $perubahan->save();

        return redirect()->route('admin.pengajuan.profile')->with('success', 'Pengajuan perubahan profile pribadi ditolak.');
    }


    public function setuju(string $id)
    {
        $perubahan = PengajuanPerubahanDatas::where('id_perubahan', $id)->with(['user.dataDiri.dokumen'])->first();


        // jika ada foto 
        if ($perubahan->foto) {
            // Path file utama
            $localPath = storage_path("app/private/perubahanProfile/{$perubahan->foto}");
            $fileLama = $perubahan->user->dataDiri->dokumen->path_file;
            if (!file_exists($localPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak ditemukan.'
                ], 404);
            }

            // Path file tujuan
            $destinationPath = "{$perubahan->user->npp}/datadiri/{$perubahan->foto}";
            // Upload ke Google Drive
            $result = $this->googleDriveService->uploadFileAndGetUrl($localPath, $destinationPath);

            if ($result) {
                $perubahan->user->dataDiri->dokumen->update([
                    'path_file' => $destinationPath,
                    'file_id' => $result['file_id'],
                    'view_url' => $result['view_url'],
                    'download_url' => $result['download_url'],
                    'preview_url' => $result['preview_url'],
                ]);
            }
        }

        // update email
        $perubahan->user->update(['email' => $perubahan->email]);

        // Data diri
        $perubahan->user->dataDiri->update([
            'nuptk' => $perubahan->nuptk ?? null,
            'nip' => $perubahan->nip ?? null,
            'nidk' => $perubahan->nidk ?? null,
            'nidn' => $perubahan->nidn ?? null,
            'name' => $perubahan->name,
            'no_ktp' => $perubahan->no_ktp,
            'no_hp' => $perubahan->no_hp,
            'tanggal_lahir' => $perubahan->tanggal_lahir,
            'tempat_lahir' => $perubahan->tempat_lahir,
            'jenis_kelamin' => $perubahan->jenis_kelamin,
            'agama' => $perubahan->agama,
            'tanggal_bergabung' => $perubahan->tanggal_bergabung,
            'alamat' => $perubahan->alamat,
            'rt' => $perubahan->rt,
            'rw' => $perubahan->rw,
            'desa' => $perubahan->desa,
            'kecamatan' => $perubahan->kecamatan,
            'kabupaten' => $perubahan->kabupaten,
            'provinsi' => $perubahan->provinsi,
        ]);






        if ($perubahan->foto) {
            // Hapus foto utama setelah upload sukses
            if (file_exists($localPath)) {
                unlink($localPath);
                Gdrive::delete($fileLama);
            }
        }

        $perubahan->update([
            'status' => 'disetujui'
        ]);

        return redirect()->route('admin.pengajuan.profile')->with('success', 'Pengajuan perubahan profile pribadi disetujui.');
    }
}
