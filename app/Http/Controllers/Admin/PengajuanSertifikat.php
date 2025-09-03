<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dokumens;
use App\Models\PengajuanSertifikats;
use App\Models\Sertifikats;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PengajuanSertifikat extends Controller
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
            'page'     => 'Pengajuan Sertifikat',
            'selected' => 'Pengajuan Sertifikat',
            'title'    => 'Pengajuan Sertifikat',
            'pengajuans' => PengajuanSertifikats::where('status', 'pending')
                ->with(['user.dataDiri'])
                ->orderBy('updated_at', 'desc')
                ->paginate(10)
                ->appends(request()->except('page', 'riwayat_page')),
            'riwayats' => PengajuanSertifikats::with(['user.dataDiri'])
                ->where('status', '!=', 'pending')
                ->orderBy('updated_at', 'desc')
                ->paginate(10, ['*'], 'riwayat_page')
                ->appends(request()->except('page'))
                ->fragment('riwayat'),
        ];

        return view('admin.pengajuan.sertifikat.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

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
        $pengajuan = PengajuanSertifikats::where('id_pengajuan', $id)
            ->with(['user.dataDiri', 'sertifikat'])
            ->firstOrFail();

        $data = [
            'page'     => 'Pengajuan Sertifikat',
            'selected' => 'Pengajuan Sertifikat',
            'title'    => 'Pengajuan Sertifikat',
            'pengajuan' => $pengajuan
        ];
        // dd($data);

        if ($pengajuan->status === 'pending') {
            return view('admin.pengajuan.sertifikat.show', $data);
        }
        return view('admin.pengajuan.sertifikat.riwayat', $data);
    }

    public function tolak(Request $request, string $id)
    {
        $perubahan = PengajuanSertifikats::findOrFail($id);

        if ($perubahan->dokumen && Storage::exists('sertifikat/' . $perubahan->dokumen)) {
            Storage::delete('sertifikat/' . $perubahan->dokumen);
        }

        $sertifikat = $perubahan->sertifikat;

        $perubahan->update([
            'id_sertifikat' => null,
            'nama_sertifikat' => $sertifikat->nama_sertifikat ?? $perubahan->nama_sertifikat,
            'kategori' => $sertifikat->kategori ?? $perubahan->kategori,
            'penyelenggara' => $sertifikat->penyelenggara ?? $perubahan->penyelenggara,
            'tanggal_diperoleh' => $sertifikat->tanggal_diperoleh ?? $perubahan->tanggal_diperoleh,
            'tanggal_selesai' => $sertifikat->tanggal_selesai ?? $perubahan->tanggal_selesai,
            'status'        => 'ditolak',
            'keterangan'    => $request->keterangan
        ]);

        return redirect()->route('admin.pengajuan.sertifikat')
            ->with('success', 'Pengajuan perubahan sertifikat ditolak.');
    }

    public function setuju(string $id)
    {
        $perubahan = PengajuanSertifikats::with([
            'user.dataDiri',
        ])->findOrFail($id);

        $user = $perubahan->user;

        return DB::transaction(function () use ($perubahan, $user) {

            // ---- DELETE ----
            if ($perubahan->jenis === 'hapus') {
                $sertifikat = $perubahan->sertifikat;
                if ($sertifikat) {
                    if ($sertifikat->dokumenSertifikat?->path_file) {
                        try {
                            $this->googleDriveService->deleteById($sertifikat->dokumenSertifikat->file_id);
                        } catch (\Throwable $e) {
                        }
                    }


                    $perubahan->update([
                        'id_sertifikat' => null,
                        'nama_sertifikat' => $sertifikat->nama_sertifikat,
                        'kategori' => $sertifikat->kategori,
                        'penyelenggara' => $sertifikat->penyelenggara,
                        'tanggal_diperoleh' => $sertifikat->tanggal_diperoleh,
                        'tanggal_selesai' => $sertifikat->tanggal_selesai,
                        'status'        => 'disetujui',
                    ]);

                    $sertifikat->delete();
                }




                return redirect()->route('admin.pengajuan.sertifikat')
                    ->with('success', 'Pengajuan perubahan sertifikat (hapus) disetujui.');
            }

            // ---- TAMBAH ----
            if ($perubahan->jenis === 'tambah') {

                $generateNomor = function () {
                    $last = Dokumens::lockForUpdate()->orderBy('nomor_dokumen', 'desc')->first();
                    $num  = $last ? ((int)$last->nomor_dokumen + 1) : 1;
                    return str_pad($num, 7, '0', STR_PAD_LEFT);
                };


                $newIdDokumen = null;
                if (!empty($perubahan->dokumen)) {
                    $localPath = storage_path("app/private/sertifikat/{$perubahan->dokumen}");
                    if (is_file($localPath)) {
                        $destPath = "{$user->npp}/sertifikat/{$perubahan->dokumen}";
                        $result   = $this->googleDriveService->uploadFileAndGetUrl($localPath, $destPath);

                        if ($result) {
                            $newIdDokumen = $generateNomor();
                            Dokumens::create([
                                'nomor_dokumen'  => $newIdDokumen,
                                'id_user'        => $user->id_user,
                                'path_file'      => $destPath,
                                'file_id'        => $result['file_id'] ?? null,
                                'view_url'       => $result['view_url'] ?? null,
                                'download_url'   => $result['download_url'] ?? null,
                                'preview_url'    => $result['preview_url'] ?? null,
                                'tanggal_upload' => now(),
                            ]);
                            if ($perubahan->dokumen && Storage::exists('sertifikat/' . $perubahan->dokumen)) {
                                Storage::delete('sertifikat/' . $perubahan->dokumen);
                            }
                        }
                    }
                }



                Sertifikats::create([
                    'id_user'             => $user->id_user,
                    'nama_sertifikat'     => $perubahan->nama_sertifikat,
                    'kategori'            => $perubahan->kategori,
                    'penyelenggara'       => $perubahan->penyelenggara,
                    'tanggal_selesai'     => $perubahan->tanggal_selesai,
                    'tanggal_diperoleh'   => $perubahan->tanggal_diperoleh,
                    'dokumen'             => $newIdDokumen ?? null,
                ]);

                $perubahan->update([
                    'id_sertifikat' => null,
                    'status'        => 'disetujui',
                ]);

                return redirect()->route('admin.pengajuan.sertifikat')
                    ->with('success', 'Pengajuan Sertifikat (tambah) disetujui dan data berhasil dibuat.');
            }

            // ---- EDIT ----
            $sertifikat = $perubahan->sertifikat;
            if (!$sertifikat) {
                return back()->with('error', 'Data sertifikat asal tidak ditemukan.');
            }

            $file_id = $sertifikat->dokumenSertifikat->file_id;

            $newIdDokumen = null;
            if (!empty($perubahan->dokumen)) {
                $localPath = storage_path("app/private/sertifikat/{$perubahan->dokumen}");
                if (is_file($localPath)) {
                    $destPath = "{$user->npp}/sertifikat/{$perubahan->dokumen}";
                    $result   = $this->googleDriveService->uploadFileAndGetUrl($localPath, $destPath);

                    if ($result) {
                        $sertifikat->dokumenSertifikat->update([
                            'path_file'      => $destPath,
                            'file_id'        => $result['file_id'] ?? null,
                            'view_url'       => $result['view_url'] ?? null,
                            'download_url'   => $result['download_url'] ?? null,
                            'preview_url'    => $result['preview_url'] ?? null,
                            'tanggal_upload' => now(),
                        ]);
                        if ($perubahan->dokumen && Storage::exists('sertifikat/' . $perubahan->dokumen)) {
                            Storage::delete('sertifikat/' . $perubahan->dokumen);
                        }

                        $this->googleDriveService->deleteById($file_id);
                    }
                }
            }


            // Update field sertifikat dari pengajuan
            $sertifikat->update([
                'nama_sertifikat'       => $perubahan->nama_sertifikat,
                'kategori'              => $perubahan->kategori,
                'penyelenggara'         => $perubahan->penyelenggara,
                'tanggal_diperoleh'     => $perubahan->tanggal_diperoleh,
                'tanggal_selesai'       => $perubahan->tanggal_selesai,
            ]);

            $perubahan->update([
                'id_sertifikat' => null,
                'status'        => 'disetujui',
            ]);

            return redirect()->route('admin.pengajuan.sertifikat')
                ->with('success', 'Pengajuan perubahan sertifikat (edit) disetujui.');
        });
    }
}
