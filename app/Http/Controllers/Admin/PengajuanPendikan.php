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
use Illuminate\Support\Facades\DB;

class PengajuanPendikan extends Controller
{
    protected $googleDriveService;

    public function __construct(GoogleDriveService $googleDriveService)
    {
        $this->googleDriveService = $googleDriveService;
    }

    public function index()
    {
        $data = [
            'page'     => 'Pengajuan Pendidikan',
            'selected' => 'Pengajuan Pendidikan',
            'title'    => 'Pengajuan Perubahan Pendidikan',
            'pengajuans' => PengajuanPerubahanPendidikans::where('status', 'pending')
                ->with(['user.dataDiri'])
                ->orderBy('updated_at', 'desc')
                ->paginate(10)
                ->appends(request()->except('page', 'riwayat_page')),
            'riwayats' => PengajuanPerubahanPendidikans::with(['user.dataDiri'])
                ->where('status', '!=', 'pending')
                ->orderBy('updated_at', 'desc')
                ->paginate(10, ['*'], 'riwayat_page')
                ->appends(request()->except('page'))
                ->fragment('riwayat'),
        ];

        return view('admin.pengajuan.pendidikan.index', $data);
    }

    public function create() {}
    public function store(Request $request) {}

    public function show(string $id)
    {
        $pengajuan = PengajuanPerubahanPendidikans::where('id_perubahan', $id)
            ->with([
                'user.dataDiri',
                'jenjang',
                'pendidikan.dokumenIjazah',
                'pendidikan.dokumenTranskipNilai',
                'pendidikan.jenjang'
            ])->first();

        $data = [
            'page'     => 'Pengajuan Pendidikan',
            'selected' => 'Pengajuan Pendidikan',
            'title'    => 'Pengajuan Perubahan Pendidikan',
            'pengajuan' => $pengajuan
        ];

        return $pengajuan && $pengajuan->status === 'pending'
            ? view('admin.pengajuan.pendidikan.show', $data)
            : view('admin.pengajuan.pendidikan.riwayat', $data);
    }


    public function tolak(Request $request, string $id)
    {
        $perubahan = PengajuanPerubahanPendidikans::findOrFail($id);

        if ($perubahan->ijazah && Storage::exists('pendidikan/ijazah/' . $perubahan->ijazah)) {
            Storage::delete('pendidikan/ijazah/' . $perubahan->ijazah);
        }
        if ($perubahan->transkip_nilai && Storage::exists('pendidikan/transkipNilai/' . $perubahan->transkip_nilai)) {
            Storage::delete('pendidikan/transkipNilai/' . $perubahan->transkip_nilai);
        }

        $perubahan->update([
            'id_pendidikan' => null,
            'status'        => 'ditolak',
            'keterangan'    => $request->keterangan
        ]);

        return redirect()->route('admin.pengajuan.pendidikan')
            ->with('success', 'Pengajuan perubahan pendidikan ditolak.');
    }

    public function setuju(string $id)
    {
        $perubahan = PengajuanPerubahanPendidikans::with([
            'user',
            'pendidikan.dokumenIjazah',
            'pendidikan.dokumenTranskipNilai',
            'pendidikan.jenjang',
            'jenjang',
        ])->findOrFail($id);

        $user = $perubahan->user;

        return DB::transaction(function () use ($perubahan, $user) {

            // ---- DELETE ----
            if ($perubahan->jenis === 'delete') {
                $pendidikan = $perubahan->pendidikan;
                if ($pendidikan) {
                    if ($pendidikan->dokumenIjazah?->path_file) {
                        try {
                            Storage::disk('google')->delete($pendidikan->dokumenIjazah->path_file);
                        } catch (\Throwable $e) {
                        }
                    }
                    if ($pendidikan->dokumenTranskipNilai?->path_file) {
                        try {
                            Storage::disk('google')->delete($pendidikan->dokumenTranskipNilai->path_file);
                        } catch (\Throwable $e) {
                        }
                    }

                    // (Tetap ikuti logika kamu yang lama)
                    $perubahan->create([
                        'id_jenjang'      => $pendidikan->id_jenjang,
                        'id_user'         => $perubahan->user->id_user,
                        'institusi'       => $pendidikan->institusi,
                        'program_studi'   => $pendidikan->program_studi,
                        'gelar'           => $pendidikan->gelar,
                        'tahun_lulus'     => $pendidikan->tahun_lulus,
                        'ijazah'          => null,
                        'transkip_nilai'  => null,
                        'status'          => 'disetujui',
                        'jenis'           => 'delete',
                        'created_at'      => $perubahan->created_at
                    ]);
                    $pendidikan->delete();
                }

                $perubahan->update(['status' => 'disetujui']);

                return redirect()->route('admin.pengajuan.pendidikan')
                    ->with('success', 'Pengajuan perubahan pendidikan (hapus) disetujui.');
            }

            // ---- TAMBAH ----
            if ($perubahan->jenis === 'tambah') {
                $targetJenjang = $perubahan->id_jenjang
                    ? Jenjangs::findOrFail($perubahan->id_jenjang)
                    : null;

                $jenjangName  = $targetJenjang?->nama_jenjang ?? 'Tanpa Jenjang';
                $targetFolder = "{$user->npp}/pendidikan/{$jenjangName}";
                try {
                    Storage::disk('google')->makeDirectory($targetFolder);
                } catch (\Throwable $e) {
                }

                $generateNomor = function () {
                    $last = Dokumens::lockForUpdate()->orderBy('nomor_dokumen', 'desc')->first();
                    $num  = $last ? ((int)$last->nomor_dokumen + 1) : 1;
                    return str_pad($num, 7, '0', STR_PAD_LEFT);
                };

                $newIdIjazah   = null;
                $newIdTranskip = null;

                if (!empty($perubahan->ijazah)) {
                    $localPath = storage_path("app/private/pendidikan/ijazah/{$perubahan->ijazah}");
                    if (is_file($localPath)) {
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
                            @unlink($localPath);
                        }
                    }
                }

                if (!empty($perubahan->transkip_nilai)) {
                    $localPath = storage_path("app/private/pendidikan/transkipNilai/{$perubahan->transkip_nilai}");
                    if (is_file($localPath)) {
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
                            @unlink($localPath);
                        }
                    }
                }

                Pendidikans::create([
                    'id_user'        => $user->id_user,
                    'id_jenjang'     => $perubahan->id_jenjang,
                    'institusi'      => $perubahan->institusi,
                    'program_studi'  => $perubahan->program_studi,
                    'gelar'          => $perubahan->gelar,
                    'tahun_lulus'    => $perubahan->tahun_lulus ?? date('Y'),
                    'ijazah'         => $newIdIjazah,
                    'transkip_nilai' => $newIdTranskip,
                ]);

                $perubahan->update([
                    'id_pendidikan' => null,
                    'status'        => 'disetujui',
                ]);

                return redirect()->route('admin.pengajuan.pendidikan')
                    ->with('success', 'Pengajuan pendidikan (tambah) disetujui dan data berhasil dibuat.');
            }

            // ---- EDIT ----
            $pendidikan = $perubahan->pendidikan;
            if (!$pendidikan) {
                return back()->with('error', 'Data pendidikan asal tidak ditemukan.');
            }

            $oldJenjangName = $pendidikan->jenjang->nama_jenjang ?? '';
            $newJenjang     = $perubahan->id_jenjang ? Jenjangs::findOrFail($perubahan->id_jenjang) : $pendidikan->jenjang;
            $newJenjangName = $newJenjang->nama_jenjang ?? $oldJenjangName;

            $newFolder = "{$user->npp}/pendidikan/{$newJenjangName}";
            try {
                Storage::disk('google')->makeDirectory($newFolder);
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
                    Storage::disk('google')->delete($oldPath);
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

            if ($folderChanged) {
                if (empty($perubahan->ijazah) && $fileLamaIjazahPath && $pendidikan->dokumenIjazah) {
                    if ($new = $moveOnDrive($fileLamaIjazahPath, $newFolder)) {
                        $pendidikan->dokumenIjazah()->update(['path_file' => $new]);
                    }
                }
                if (empty($perubahan->transkip_nilai) && $fileLamaTrnPath && $pendidikan->dokumenTranskipNilai) {
                    if ($new = $moveOnDrive($fileLamaTrnPath, $newFolder)) {
                        $pendidikan->dokumenTranskipNilai()->update(['path_file' => $new]);
                    }
                }
            }

            // Replace ijazah
            if (!empty($perubahan->ijazah)) {
                $localPath = storage_path("app/private/pendidikan/ijazah/{$perubahan->ijazah}");
                if (is_file($localPath)) {
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
                                    Storage::disk('google')->delete($old);
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
                        @unlink($localPath);
                    }
                }
            }

            // Replace transkip_nilai
            if (!empty($perubahan->transkip_nilai)) {
                $localPath = storage_path("app/private/pendidikan/transkipNilai/{$perubahan->transkip_nilai}");
                if (is_file($localPath)) {
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
                                    Storage::disk('google')->delete($old);
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
                        @unlink($localPath);
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
                'status'        => 'disetujui',
            ]);

            return redirect()->route('admin.pengajuan.pendidikan')
                ->with('success', 'Pengajuan perubahan pendidikan (edit) disetujui.');
        });
    }
}
