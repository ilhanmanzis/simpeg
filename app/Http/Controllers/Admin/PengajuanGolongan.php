<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dokumens;
use App\Models\Golongans;
use App\Models\GolonganUsers;
use App\Models\PengajuanGolongans;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengajuanGolongan extends Controller
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
            'page' => 'Pengajuan Golongan',
            'selected' => 'Pengajuan Golongan',
            'title' => 'Pengajuan Kenaikan Golongan',
            'pengajuans' => PengajuanGolongans::where('status', 'pending')->with(['user.dataDiri'])->orderBy('updated_at', 'desc')->paginate(10)->appends(request()->except('page', 'riwayat_page')),
            'riwayats' => PengajuanGolongans::with(['user.dataDiri'])
                ->where('status', '!=', 'pending')
                ->orderBy('updated_at', 'desc')
                ->paginate(10, ['*'], 'riwayat_page')
                // jangan bawa-bawa page default saat pindah halaman riwayat
                ->appends(request()->except('page'))
                // opsional: auto-scroll ke section riwayat
                ->fragment('riwayat'),
        ];

        return view('admin.pengajuan.golongan.index', $data);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pengajuan = PengajuanGolongans::where('id_pengajuan_golongan', $id)->with(['user.dataDiri'])->first();
        $data = [
            'page' => 'Pengajuan Golongan',
            'selected' => 'Pengajuan Golongan',
            'title' => 'Pengajuan Kenaikan Golongan',
            'pengajuan' => $pengajuan
        ];

        if ($pengajuan->status === 'pending') {
            return view('admin.pengajuan.golongan.show', $data);
        } else {
            return view('admin.pengajuan.golongan.riwayat', $data);
        }
    }



    public function tolak(Request $request, string $id)
    {
        // Ambil data user
        $perubahan = PengajuanGolongans::findOrFail($id);

        if ($perubahan->sk && Storage::exists('sk/' . $perubahan->sk)) {
            // Hapus file sk jika ada
            Storage::delete('sk/' . $perubahan->sk);
        }

        // Update status menjadi 'ditolak'
        $perubahan->status = 'ditolak';
        $perubahan->keterangan = $request->input('keterangan');
        $perubahan->save();

        return redirect()->route('admin.pengajuan.golongan')->with('success', 'Pengajuan kenaikan golongan ditolak.');
    }

    public function setuju(string $id)
    {
        $perubahan = PengajuanGolongans::where('id_pengajuan_golongan', $id)->with(['golongan', 'user.dataDiri'])->first();

        $golonganSebelumnya = GolonganUsers::where('id_user', $id)->where('status', 'aktif')->with(['golongan'])->orderBy('id_golongan_user', 'desc')->first();


        // Ambil nomor dokumen terakhir
        $lastDokumen = Dokumens::orderBy('nomor_dokumen', 'desc')->first();
        $lastNumber = $lastDokumen ? (int) $lastDokumen->nomor_dokumen : 0;

        // Dokumen sk utama
        $lastNumber++;
        $newId = str_pad($lastNumber, 7, '0', STR_PAD_LEFT);

        $timestampedName = $perubahan->sk;
        $namaGolongan = str_replace('/', '-', $perubahan->golongan->nama_golongan);

        $destinationPath = "{$perubahan->user->npp}/golongan/{$namaGolongan}/{$timestampedName}";


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

        GolonganUsers::create([
            'id_user' => $perubahan->user->id_user,
            'id_golongan' => $perubahan->id_golongan,
            'tanggal_mulai' => $perubahan->tanggal_mulai,
            'tanggal_selesai' => $perubahan->tanggal_selesai ?? null,
            'status' => 'aktif',
            'sk' => $newId
        ]);



        if ($golonganSebelumnya) {
            $golonganSebelumnya->update([
                'status' => 'nonaktif',
                'tanggal_selesai' => $golonganSebelumnya->tanggal_selesai ?? now()
            ]);
        }

        $perubahan->update([
            'status' => 'disetujui'
        ]);

        Storage::delete('sk/' . $perubahan->sk);

        return redirect()->route('admin.pengajuan.golongan', $id)->with('success', 'Pengajuan kenaikan golongan berhasil disetujui');
    }
}
