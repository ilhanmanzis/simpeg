<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dokumens;
use App\Models\Jenjangs;
use App\Models\Pendidikans;
use App\Models\PengajuanPerubahanPendidikans;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yaza\LaravelGoogleDriveStorage\Gdrive;

use Illuminate\Support\Facades\DB;

class PengajuanPendikan extends Controller
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
            'page' => 'Pengajuan Pendidikan',
            'selected' => 'Pengajuan Pendidikan',
            'title' => 'Pengajuan Perubahan Pendidikan',
            'pengajuans' => PengajuanPerubahanPendidikans::where('status', 'pending')->with(['user.dataDiri'])->orderBy('updated_at', 'desc')->paginate(10)->appends(request()->except('page', 'riwayat_page')),
            'riwayats' => PengajuanPerubahanPendidikans::with(['user.dataDiri'])
                ->where('status', '!=', 'pending')
                ->orderBy('updated_at', 'desc')
                ->paginate(10, ['*'], 'riwayat_page')
                // jangan bawa-bawa page default saat pindah halaman riwayat
                ->appends(request()->except('page'))
                // opsional: auto-scroll ke section riwayat
                ->fragment('riwayat'),
        ];

        return view('admin.pengajuan.pendidikan.index', $data);
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
        $pengajuan = PengajuanPerubahanPendidikans::where('id_perubahan', $id)->with(['user.dataDiri', 'jenjang', 'pendidikan.dokumenIjazah', 'pendidikan.dokumenTranskipNilai', 'pendidikan.jenjang'])->first();
        $data = [
            'page' => 'Pengajuan Pendidikan',
            'selected' => 'Pengajuan Pendidikan',
            'title' => 'Pengajuan Perubahan Pendidikan',
            'pengajuan' => $pengajuan
        ];

        if ($pengajuan->status === 'pending') {
            return view('admin.pengajuan.pendidikan.show', $data);
        } else {
            return view('admin.pengajuan.pendidikan.riwayat', $data);
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function tolak(Request $request, string $id)
    {
        // Ambil data user
        $perubahan = PengajuanPerubahanPendidikans::findOrFail($id);

        if ($perubahan->ijazah && Storage::exists('pendidikan/ijazah/' . $perubahan->ijazah)) {
            // Hapus file foto jika ada
            Storage::delete('pendidikan/ijazah/' . $perubahan->ijazah);
        }
        if ($perubahan->transkip_nilai && Storage::exists('pendidikan/transkipNilai/' . $perubahan->transkip_nilai)) {
            // Hapus file foto jika ada
            Storage::delete('pendidikan/transkipNilai/' . $perubahan->transkip_nilai);
        }



        $perubahan->update([
            'id_pendidikan' => null,
            'status' => 'ditolak',
            'keterangan' => $request->keterangan
        ]);


        return redirect()->route('admin.pengajuan.pendidikan')->with('success', 'Pengajuan perubahan pendidikan ditolak.');
    }





    public function setuju(string $id)
    {
        $perubahan = PengajuanPerubahanPendidikans::with([
            'user',
            'pendidikan.dokumenIjazah',
            'pendidikan.dokumenTranskipNilai',
            'pendidikan.jenjang',
            'jenjang', // jika pengajuan membawa id_jenjang baru
        ])->findOrFail($id);

        $user = $perubahan->user;

        return DB::transaction(function () use ($perubahan, $user) {

            // ---- DELETE (hapus pendidikan lama) ----
            if ($perubahan->jenis === 'delete') {
                $pendidikan = $perubahan->pendidikan;
                if ($pendidikan) {
                    if ($pendidikan->dokumenIjazah?->path_file) {
                        try {
                            Gdrive::delete($pendidikan->dokumenIjazah->path_file);
                        } catch (\Throwable $e) {
                        }
                    }
                    if ($pendidikan->dokumenTranskipNilai?->path_file) {
                        try {
                            Gdrive::delete($pendidikan->dokumenTranskipNilai->path_file);
                        } catch (\Throwable $e) {
                        }
                    }
                    $perubahan->create([
                        'id_jenjang'    => $pendidikan->id_jenjang,
                        'id_user' => $perubahan->user->id_user,
                        'institusi'     => $pendidikan->institusi,
                        'program_studi' => $pendidikan->program_studi,
                        'gelar'         => $pendidikan->gelar,
                        'tahun_lulus'   => $pendidikan->tahun_lulus,
                        'ijazah' => null,
                        'transkip_nilai' => null,
                        'status' => 'disetujui',
                        'jenis' => 'delete',
                        'created_at' => $perubahan->created_at
                    ]);
                    $pendidikan->delete();
                }

                $perubahan->update(['status' => 'disetujui']);
                return redirect()->route('admin.pengajuan.pendidikan')
                    ->with('success', 'Pengajuan perubahan pendidikan (hapus) disetujui.');
            }

            // ---- TAMBAH (buat record pendidikan baru dari pengajuan) ----
            if ($perubahan->jenis === 'tambah') {
                // Tentukan folder tujuan di Google Drive
                $targetJenjang = $perubahan->id_jenjang
                    ? Jenjangs::findOrFail($perubahan->id_jenjang)
                    : null;

                $jenjangName = $targetJenjang?->nama_jenjang ?? 'Tanpa Jenjang';
                $targetFolder = "{$user->npp}/pendidikan/{$jenjangName}";
                try {
                    Gdrive::makeDir($targetFolder);
                } catch (\Throwable $e) {
                }

                // Generator nomor_dokumen aman (lock)
                $generateNomor = function () {
                    $last = Dokumens::lockForUpdate()->orderBy('nomor_dokumen', 'desc')->first();
                    $num  = $last ? ((int)$last->nomor_dokumen + 1) : 1;
                    return str_pad($num, 7, '0', STR_PAD_LEFT);
                };

                $newIdIjazah   = null;
                $newIdTranskip = null;

                // Upload IJAZAH dari storage lokal jika ada nama file di pengajuan
                if (!empty($perubahan->ijazah)) {
                    $localPath = storage_path("app/private/pendidikan/ijazah/{$perubahan->ijazah}");
                    if (file_exists($localPath)) {
                        $destName = $perubahan->ijazah;
                        $destPath = "{$targetFolder}/{$destName}";
                        $result   = $this->googleDriveService->uploadFileAndGetUrl($localPath, $destPath);

                        if ($result) {
                            $newIdIjazah = $generateNomor();
                            Dokumens::create([
                                'nomor_dokumen'  => $newIdIjazah,
                                'id_user'        => $user->id_user,
                                'path_file'      => $destPath,
                                'file_id'        => $result['file_id'] ?? null,
                                'view_url'       => $result['view_url'] ?? null,
                                'download_url'   => $result['download_url'] ?? null,
                                'preview_url'    => $result['preview_url'] ?? null,
                                'tanggal_upload' => now(),
                            ]);

                            try {
                                unlink($localPath);
                            } catch (\Throwable $e) {
                            }
                        }
                    }
                }

                // Upload TRANSKIP NILAI dari storage lokal jika ada
                if (!empty($perubahan->transkip_nilai)) {
                    $localPath = storage_path("app/private/pendidikan/transkipNilai/{$perubahan->transkip_nilai}");
                    if (file_exists($localPath)) {
                        $destName = $perubahan->transkip_nilai;
                        $destPath = "{$targetFolder}/{$destName}";
                        $result   = $this->googleDriveService->uploadFileAndGetUrl($localPath, $destPath);

                        if ($result) {
                            $newIdTranskip = $generateNomor();
                            Dokumens::create([
                                'nomor_dokumen'  => $newIdTranskip,
                                'id_user'        => $user->id_user,
                                'path_file'      => $destPath,
                                'file_id'        => $result['file_id'] ?? null,
                                'view_url'       => $result['view_url'] ?? null,
                                'download_url'   => $result['download_url'] ?? null,
                                'preview_url'    => $result['preview_url'] ?? null,
                                'tanggal_upload' => now(),
                            ]);

                            try {
                                unlink($localPath);
                            } catch (\Throwable $e) {
                            }
                        }
                    }
                }

                // Buat record pendidikan baru (pakai data dari pengajuan)
                Pendidikans::create([
                    'id_user'        => $user->id_user,
                    'id_jenjang'     => $perubahan->id_jenjang,
                    'institusi'      => $perubahan->institusi,
                    'program_studi'  => $perubahan->program_studi,
                    'gelar'          => $perubahan->gelar,
                    'tahun_lulus'    => $perubahan->tahun_lulus ?? date('Y'),
                    'ijazah'         => $newIdIjazah,      // FK -> dokumen.nomor_dokumen
                    'transkip_nilai' => $newIdTranskip,    // FK -> dokumen.nomor_dokumen
                ]);

                $perubahan->update([
                    'id_pendidikan' => null,
                    'status' => 'disetujui',
                ]);

                return redirect()->route('admin.pengajuan.pendidikan')
                    ->with('success', 'Pengajuan pendidikan (tambah) disetujui dan data berhasil dibuat.');
            }

            // ---- EDIT (default cabang selain delete/tambah) ----
            $pendidikan = $perubahan->pendidikan;
            if (!$pendidikan) {
                return back()->with('error', 'Data pendidikan asal tidak ditemukan.');
            }

            $oldJenjangName = $pendidikan->jenjang->nama_jenjang ?? '';
            $newJenjang     = $perubahan->id_jenjang ? Jenjangs::findOrFail($perubahan->id_jenjang) : $pendidikan->jenjang;
            $newJenjangName = $newJenjang->nama_jenjang ?? $oldJenjangName;

            $oldFolder = $oldJenjangName ? "{$user->npp}/pendidikan/{$oldJenjangName}" : null;
            $newFolder = "{$user->npp}/pendidikan/{$newJenjangName}";
            try {
                Gdrive::makeDir($newFolder);
            } catch (\Throwable $e) {
            }

            $moveOnDrive = function (?string $oldPath, string $targetFolder) {
                if (!$oldPath) return null;
                $filename = basename($oldPath);
                $newPath  = rtrim($targetFolder, '/') . '/' . $filename;
                if ($oldPath === $newPath) return $newPath;
                $binary = Storage::disk('google')->get($oldPath);
                Storage::disk('google')->put($newPath, $binary);
                try {
                    Gdrive::delete($oldPath);
                } catch (\Throwable $e) {
                }
                return $newPath;
            };

            $folderChanged      = $oldJenjangName !== $newJenjangName;
            $fileLamaIjazahPath = $pendidikan->dokumenIjazah?->path_file;
            $fileLamaTrnPath    = $pendidikan->dokumenTranskipNilai?->path_file;

            $generateNomor = function () {
                $last = Dokumens::lockForUpdate()->orderBy('nomor_dokumen', 'desc')->first();
                $num  = $last ? ((int)$last->nomor_dokumen + 1) : 1;
                return str_pad($num, 7, '0', STR_PAD_LEFT);
            };

            // per-file move: pindahkan yang tidak di-replace
            if ($folderChanged) {
                // Pindah IJAZAH lama hanya jika tidak ada upload ijazah baru
                if (empty($perubahan->ijazah) && $fileLamaIjazahPath && $pendidikan->dokumenIjazah) {
                    if ($new = $moveOnDrive($fileLamaIjazahPath, $newFolder)) {
                        $pendidikan->dokumenIjazah()->update(['path_file' => $new]);
                    }
                }

                // Pindah TRANSKIP lama hanya jika tidak ada upload transkip baru
                if (empty($perubahan->transkip_nilai) && $fileLamaTrnPath && $pendidikan->dokumenTranskipNilai) {
                    if ($new = $moveOnDrive($fileLamaTrnPath, $newFolder)) {
                        $pendidikan->dokumenTranskipNilai()->update(['path_file' => $new]);
                    }
                }
            }


            // Replace ijazah dari file lokal pengajuan (jika ada)
            if (!empty($perubahan->ijazah)) {
                $localPath = storage_path("app/private/pendidikan/ijazah/{$perubahan->ijazah}");
                if (file_exists($localPath)) {
                    $destName = $perubahan->ijazah;
                    $destPath = "{$newFolder}/{$destName}";
                    $result   = $this->googleDriveService->uploadFileAndGetUrl($localPath, $destPath);
                    if ($result) {
                        if ($pendidikan->dokumenIjazah) {
                            $old = $pendidikan->dokumenIjazah->path_file;
                            $pendidikan->dokumenIjazah->update([
                                'path_file'      => $destPath,
                                'file_id'        => $result['file_id'] ?? null,
                                'view_url'       => $result['view_url'] ?? null,
                                'download_url'   => $result['download_url'] ?? null,
                                'preview_url'    => $result['preview_url'] ?? null,
                                'tanggal_upload' => now(),
                            ]);
                            if ($old && $old !== $destPath) {
                                try {
                                    Gdrive::delete($old);
                                } catch (\Throwable $e) {
                                }
                            }
                        } else {
                            $nomor = $generateNomor();
                            Dokumens::create([
                                'nomor_dokumen'  => $nomor,
                                'id_user'        => $user->id_user,
                                'path_file'      => $destPath,
                                'file_id'        => $result['file_id'] ?? null,
                                'view_url'       => $result['view_url'] ?? null,
                                'download_url'   => $result['download_url'] ?? null,
                                'preview_url'    => $result['preview_url'] ?? null,
                                'tanggal_upload' => now(),
                            ]);
                            $pendidikan->ijazah = $nomor;
                            $pendidikan->save();
                        }
                        try {
                            unlink($localPath);
                        } catch (\Throwable $e) {
                        }
                    }
                }
            }

            // Replace transkip_nilai dari file lokal pengajuan (jika ada)
            if (!empty($perubahan->transkip_nilai)) {
                $localPath = storage_path("app/private/pendidikan/transkipNilai/{$perubahan->transkip_nilai}");
                if (file_exists($localPath)) {
                    $destName = $perubahan->transkip_nilai;
                    $destPath = "{$newFolder}/{$destName}";
                    $result   = $this->googleDriveService->uploadFileAndGetUrl($localPath, $destPath);
                    if ($result) {
                        if ($pendidikan->dokumenTranskipNilai) {
                            $old = $pendidikan->dokumenTranskipNilai->path_file;
                            $pendidikan->dokumenTranskipNilai->update([
                                'path_file'      => $destPath,
                                'file_id'        => $result['file_id'] ?? null,
                                'view_url'       => $result['view_url'] ?? null,
                                'download_url'   => $result['download_url'] ?? null,
                                'preview_url'    => $result['preview_url'] ?? null,
                                'tanggal_upload' => now(),
                            ]);
                            if ($old && $old !== $destPath) {
                                try {
                                    Gdrive::delete($old);
                                } catch (\Throwable $e) {
                                }
                            }
                        } else {
                            $nomor = $generateNomor();
                            Dokumens::create([
                                'nomor_dokumen'  => $nomor,
                                'id_user'        => $user->id_user,
                                'path_file'      => $destPath,
                                'file_id'        => $result['file_id'] ?? null,
                                'view_url'       => $result['view_url'] ?? null,
                                'download_url'   => $result['download_url'] ?? null,
                                'preview_url'    => $result['preview_url'] ?? null,
                                'tanggal_upload' => now(),
                            ]);
                            $pendidikan->transkip_nilai = $nomor;
                            $pendidikan->save();
                        }
                        try {
                            unlink($localPath);
                        } catch (\Throwable $e) {
                        }
                    }
                }
            }

            // Update field pendidikan dari pengajuan
            $pendidikan->update([
                'id_jenjang'    => $perubahan->id_jenjang,
                'institusi'     => $perubahan->institusi,
                'program_studi' => $perubahan->program_studi,
                'gelar'         => $perubahan->gelar,
                'tahun_lulus'   => $perubahan->tahun_lulus,
            ]);

            $perubahan->update([
                'id_pendidikan' => null,
                'status' => 'disetujui',
            ]);



            return redirect()->route('admin.pengajuan.pendidikan')
                ->with('success', 'Pengajuan perubahan pendidikan (edit) disetujui.');
        });
    }
}
