<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dokumens;
use App\Models\FungsionalUsers;
use App\Models\PengajuanFungsionals;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengajuanFungsional extends Controller
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
            'page' => 'Pengajuan Fungsional',
            'selected' => 'Pengajuan Fungsional',
            'title' => 'Pengajuan Kenaikan Jabatan Fungsional',
            'pengajuans' => PengajuanFungsionals::where('status', 'pending')->with(['user.dataDiri'])->orderBy('updated_at', 'desc')->paginate(10)->appends(request()->except('page', 'riwayat_page')),
            'riwayats' => PengajuanFungsionals::with(['user.dataDiri'])
                ->where('status', '!=', 'pending')
                ->orderBy('updated_at', 'desc')
                ->paginate(10, ['*'], 'riwayat_page')
                // jangan bawa-bawa page default saat pindah halaman riwayat
                ->appends(request()->except('page'))
                // opsional: auto-scroll ke section riwayat
                ->fragment('riwayat'),
        ];

        return view('admin.pengajuan.fungsional.index', $data);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pengajuan = PengajuanFungsionals::where('id_pengajuan_fungsional', $id)->with(['user.dataDiri'])->first();
        $data = [
            'page' => 'Pengajuan Fungsional',
            'selected' => 'Pengajuan Fungsional',
            'title' => 'Pengajuan Kenaikan Jabatan Fungsional',
            'pengajuan' => $pengajuan
        ];

        if ($pengajuan->status === 'pending') {
            return view('admin.pengajuan.fungsional.show', $data);
        } else {
            return view('admin.pengajuan.fungsional.riwayat', $data);
        }
    }



    public function tolak(Request $request, string $id)
    {
        // Ambil data user
        $perubahan = PengajuanFungsionals::findOrFail($id);

        if ($perubahan->sk && Storage::exists('sk/' . $perubahan->sk)) {
            // Hapus file sk jika ada
            Storage::delete('sk/' . $perubahan->sk);
        }

        // Update status menjadi 'ditolak'
        $perubahan->status = 'ditolak';
        $perubahan->keterangan = $request->input('keterangan');
        $perubahan->save();

        return redirect()->route('admin.pengajuan.fungsional')->with('success', 'Pengajuan kenaikan jabatan fungsional ditolak.');
    }

    public function setuju(string $id)
    {
        $perubahan = PengajuanFungsionals::where('id_pengajuan_fungsional', $id)->with(['fungsional', 'user.dataDiri'])->first();

        $fungsionalSebelumnya = FungsionalUsers::where('id_user', $id)->where('status', 'aktif')->with(['fungsional'])->orderBy('id_fungsional_user', 'desc')->first();


        // Ambil nomor dokumen terakhir
        $lastDokumen = Dokumens::orderBy('nomor_dokumen', 'desc')->first();
        $lastNumber = $lastDokumen ? (int) $lastDokumen->nomor_dokumen : 0;

        // Dokumen sk utama
        $lastNumber++;
        $newId = str_pad($lastNumber, 7, '0', STR_PAD_LEFT);

        $timestampedName = $perubahan->sk;

        $destinationPath = "{$perubahan->user->npp}/fungsional/{$perubahan->fungsional->nama_jabatan}/{$timestampedName}";


        $localPath = storage_path("app/private/sk/{$perubahan->sk}");
        // Upload ke Google Drive
        $result = $this->googleDriveService->uploadFileAndGetUrl($localPath, $destinationPath);

        $dokumen = Dokumens::create([
            'nomor_dokumen' => $newId,
            'path_file' => $destinationPath,
            'file_id' => $result['file_id'],
            'view_url' => $result['view_url'],
            'download_url' => $result['download_url'],
            'preview_url' => $result['preview_url'],
            'id_user' => $perubahan->user->id_user,
            'tanggal_upload' => now()
        ]);

        fungsionalUsers::create([
            'id_user' => $perubahan->user->id_user,
            'id_fungsional' => $perubahan->id_fungsional,
            'tanggal_mulai' => $perubahan->tanggal_mulai,
            'tanggal_selesai' => $perubahan->tanggal_selesai ?? null,
            'angka_kredit' => $perubahan->angka_kredit,
            'status' => 'aktif',
            'sk' => $newId
        ]);



        if ($fungsionalSebelumnya) {
            $fungsionalSebelumnya->update([
                'status' => 'nonaktif',
                'tanggal_selesai' => $fungsionalSebelumnya->tanggal_selesai ?? now()
            ]);
        }

        $perubahan->update([
            'status' => 'disetujui'
        ]);

        Storage::delete('sk/' . $perubahan->sk);

        return redirect()->route('admin.pengajuan.fungsional', $id)->with('success', 'Pengajuan kenaikan jabatan fungsional berhasil disetujui');
    }
}
