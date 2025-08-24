<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dokumens;
use App\Models\JabatanStrukturals;
use App\Models\StrukturalUsers;
use App\Models\User;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StrukturalUser extends Controller
{
    protected $googleDriveService;

    public function __construct(GoogleDriveService $googleDriveService)
    {
        $this->googleDriveService = $googleDriveService;
    }

    public function index()
    {
        $items = JabatanStrukturals::with([
            'activeCurrent.user.dataDiri',
            'activeCurrent.dokumen',
            'latestAny.user.dataDiri',
            'latestAny.dokumen',
        ])
            ->orderBy('id_struktural', 'asc')
            ->get()
            ->map(function ($jabatan) {
                $active  = $jabatan->activeCurrent;
                $latest  = $jabatan->latestAny;
                $chosen  = $active ?: $latest;

                return [
                    'jabatan'   => $jabatan,
                    'record'    => $chosen,
                    'is_active' => (bool) $active,
                ];
            });

        $data = [
            'page'     => 'Jabatan Struktural',
            'selected' => 'Jabatan Struktural',
            'title'    => 'Data Jabatan Struktural Dosen',
            'items'    => $items,
        ];

        return view('admin.jabatan.struktural.index', $data);
    }

    public function mutasi(string $id)
    {
        $data = [
            'page'       => 'Jabatan Stuktural',
            'selected'   => 'Jabatan Stuktural',
            'title'      => 'Mutasi Jabatan Stuktural Dosen',
            'dosen'      => StrukturalUsers::where('id_struktural', $id)
                ->where('status', 'aktif')
                ->with(['user.dataDiri'])
                ->orderBy('id_struktural_user', 'desc')
                ->first(),
            'struktural' => JabatanStrukturals::findOrFail($id),
            'users'      => User::where('role', 'dosen')
                ->where('status_keaktifan', 'aktif')
                ->get(),
        ];

        return view('admin.jabatan.struktural.mutasi', $data);
    }

    public function mutasiStore(Request $request, string $id)
    {
        $request->validate([
            'user'            => 'required',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date',
            'sk'              => 'required|file|mimes:pdf|max:2048',
        ]);

        $user        = User::findOrFail($request->user);
        $struktural  = JabatanStrukturals::findOrFail($id);
        $sebelumnya  = StrukturalUsers::where('id_struktural', $id)
            ->where('status', 'aktif')
            ->with(['struktural'])
            ->orderBy('id_struktural_user', 'desc')
            ->first();

        // Ambil nomor dokumen terakhir
        $lastDokumen = Dokumens::orderBy('nomor_dokumen', 'desc')->first();
        $lastNumber  = $lastDokumen ? (int) $lastDokumen->nomor_dokumen : 0;

        // Dokumen SK
        $lastNumber++;
        $newId = str_pad($lastNumber, 7, '0', STR_PAD_LEFT);

        $originalName    = $request->file('sk')->getClientOriginalName();
        $timestampedName = time() . '_' . $originalName;
        $destinationPath = "{$user->npp}/struktural/{$struktural->nama_jabatan}/{$timestampedName}";

        // Upload ke Google Drive (pakai service agar dapat file_id & URL)
        $result = $this->googleDriveService->uploadFileAndGetUrl(
            $request->file('sk')->getPathname(),
            $destinationPath
        );

        Dokumens::create([
            'nomor_dokumen'  => $newId,
            'path_file'      => $destinationPath,
            'file_id'        => $result['file_id'] ?? null,
            'view_url'       => $result['view_url'] ?? null,
            'download_url'   => $result['download_url'] ?? null,
            'preview_url'    => $result['preview_url'] ?? null,
            'id_user'        => $user->id_user,
            'tanggal_upload' => now(),
        ]);

        StrukturalUsers::create([
            'id_user'        => $user->id_user,
            'id_struktural'  => $id,
            'tanggal_mulai'  => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai ?? null,
            'status'         => 'aktif',
            'sk'             => $newId,
        ]);

        if ($sebelumnya) {
            $sebelumnya->update([
                'status'          => 'nonaktif',
                'tanggal_selesai' => $sebelumnya->tanggal_selesai ?? now(),
            ]);
        }

        return redirect()->route('admin.jabatan.struktural')
            ->with('success', 'Jabatan struktural Dosen berhasil dimutasi');
    }

    public function create()
    { /* ... */
    }
    public function store(Request $request)
    { /* ... */
    }
    public function edit(string $id)
    { /* ... */
    }
    public function update(Request $request, string $id)
    { /* ... */
    }

    public function show(string $id)
    {
        $struktural = JabatanStrukturals::findOrFail($id);
        $riwayats   = StrukturalUsers::where('id_struktural', $id)
            ->where('status', 'nonaktif')
            ->with(['user.dataDiri', 'struktural', 'dokumen'])
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $data = [
            'page'       => 'Jabatan Stuktural',
            'selected'   => 'Jabatan Stuktural',
            'title'      => 'Riwayat Jabatan Struktural' . $struktural->nama_jabatan,
            'riwayats'   => $riwayats,
            'struktural' => $struktural,
        ];

        return view('admin.jabatan.struktural.riwayat', $data);
    }

    public function destroy(string $id)
    {
        $struktural = StrukturalUsers::where('id_struktural_user', $id)
            ->with(['dokumen', 'struktural', 'user'])
            ->first();

        $strukturalId = JabatanStrukturals::findOrFail($struktural->struktural->id_struktural);

        // Hapus file di Google Drive (disk google)
        if ($struktural->dokumen && $struktural->dokumen->path_file) {
            try {
                Storage::disk('google')->delete($struktural->dokumen->path_file);
            } catch (\Throwable $e) {
                // sengaja di-skip sesuai pola kode sebelumnya
            }
        }

        $struktural->delete();

        return redirect()
            ->route('admin.jabatan.struktural.show', $strukturalId)
            ->with('success', 'Data berhasil dihapus');
    }
}
