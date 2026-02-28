<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dokumens;
use App\Models\FungsionalUsers;
use App\Models\GolonganUsers;
use App\Models\JabatanFungsionals;
use App\Models\User;
use App\Services\GoogleDriveService;
use App\Services\SerdosService;
use Illuminate\Http\Request;

class FungsionalUser extends Controller
{
    protected $googleDriveService;

    public function __construct(GoogleDriveService $googleDriveService)
    {
        $this->googleDriveService = $googleDriveService;
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = [
            'page' => 'Jabatan Fungsional',
            'selected' => 'Jabatan Fungsional',
            'title' => 'Data Jabatan Fungsional Dosen',
            'user' => User::where('id_user', $id)->with('dataDiri')->first(),
            'dosen' => FungsionalUsers::where('id_user', $id)->where('status', 'aktif')->with(['user.dataDiri', 'fungsional', 'dokumen'])->orderBy('id_fungsional_user', 'desc')->first(),

            'riwayats' => FungsionalUsers::where('id_user', $id)->with(['user', 'fungsional', 'dokumen'])->orderBy('created_at', 'desc')->paginate(10)->withQueryString()
        ];

        return view('admin.jabatan.fungsional.show', $data);
    }

    public function mutasi(string $id)
    {
        $data = [
            'page' => 'Jabatan Fungsional',
            'selected' => 'Jabatan Fungsional',
            'title' => 'Mutasi Jabatan Fungsional Dosen',
            'user' => User::where('id_user', $id)->with('dataDiri')->first(),
            'dosen' => FungsionalUsers::where('id_user', $id)->where('status', 'aktif')->with(['fungsional'])->orderBy('id_fungsional_user', 'desc')->first(),
            'golongan' => GolonganUsers::where('id_user', $id)->where('status', 'aktif')->with(['golongan'])->orderBy('id_golongan_user', 'desc')->first(),
            'fungsionals' => JabatanFungsionals::with('golongan')->get()
        ];

        return view('admin.jabatan.fungsional.mutasi', $data);
    }

    public function mutasiStore(Request $request, string $id, SerdosService $serdosService)
    {
        $request->validate([
            'fungsional' => 'required',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date',
            'angka_kredit' => 'nullable',
            'sk' => 'required|file|mimes:pdf|max:2048'
        ]);

        $user = User::findOrFail($id);
        $fungsional = JabatanFungsionals::findOrFail($request->fungsional);

        $fungsionalSebelumnya = FungsionalUsers::where('id_user', $id)->where('status', 'aktif')->with(['fungsional'])->orderBy('id_fungsional_user', 'desc')->first();

        // Ambil nomor dokumen terakhir
        $lastDokumen = Dokumens::orderBy('nomor_dokumen', 'desc')->first();
        $lastNumber = $lastDokumen ? (int) $lastDokumen->nomor_dokumen : 0;

        // Dokumen foto utama
        $lastNumber++;
        $newId = str_pad($lastNumber, 7, '0', STR_PAD_LEFT);

        $originalName = $request->file('sk')->getClientOriginalName();
        $timestampedName = time() . '_' . $originalName;
        $destinationPath = "{$user->npp}/fungsional/{$fungsional->nama_jabatan}/{$timestampedName}";

        // Upload ke Google Drive (tetap pakai method yang sama sesuai logika kamu)
        $result = $this->googleDriveService->uploadFileAndGetUrl($request->file('sk')->getPathname(), $destinationPath);

        $dokumen = Dokumens::create([
            'nomor_dokumen' => $newId,
            'path_file' => $destinationPath,
            'file_id' => $result['file_id'],
            'view_url' => $result['view_url'],
            'download_url' => $result['download_url'],
            'preview_url' => $result['preview_url'],
            'id_user' => $user->id_user,
            'tanggal_upload' => now()
        ]);

        FungsionalUsers::create([
            'id_user' => $user->id_user,
            'id_fungsional' => $request->fungsional,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai ?? null,
            'status' => 'aktif',
            'angka_kredit' => $request->angka_kredit ?? null,
            'sk' => $newId
        ]);

        if ($fungsionalSebelumnya) {
            $fungsionalSebelumnya->update([
                'status' => 'nonaktif',
                'tanggal_selesai' => $fungsionalSebelumnya->tanggal_selesai ?? now()
            ]);
        }
        $serdosService->clearCache($user->id_user);

        return redirect()->route('admin.jabatan.fungsional.show', $id)->with('success', 'Jabatan Fungsional Dosen berhasil dimutasi');
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, SerdosService $serdosService)
    {
        $fungsional = FungsionalUsers::where('id_fungsional_user', $id)->with(['dokumen', 'user'])->first();

        $user = $fungsional->user->id_user;

        // Hapus file di Google Drive berdasarkan file_id yang kamu simpan.
        // (Jika data lama belum punya file_id, bagian ini akan dilewati—logika tetap sama: fokus ke penghapusan data fungsional.)
        if ($fungsional->dokumen?->file_id) {
            try {
                $this->googleDriveService->deleteById($fungsional->dokumen->file_id);
            } catch (\Throwable $e) {
                // biarkan silent agar perilaku tetap sesuai alur kamu
            }
        }

        // Hapus record fungsional (tetap sesuai logika kamu)
        $fungsional->delete();
        $serdosService->clearCache($user->id_user);

        return redirect()->route('admin.jabatan.fungsional.show', $user)->with('success', 'Data berhasil dihapus');
    }
}
