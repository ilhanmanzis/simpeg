<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataDiri;
use App\Models\Dokumens;
use App\Models\Jenjangs;
use App\Models\Pendidikans;
use App\Models\User;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use Yaza\LaravelGoogleDriveStorage\Gdrive;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PegawaiKaryawan extends Controller
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
            'page' => 'Karyawan',
            'selected' => 'Karyawan',
            'title' => 'Data Karyawan',
            'karyawans' => User::where('role', 'karyawan')->with(['dataDiri'])->orderBy('created_at', 'desc')->paginate(10)->withQueryString()

        ];
        return view('admin.pegawai.karyawan.index', $data);
    }

    public function createPendidikan(string $id)
    {
        $data = [
            'page' => 'Karyawan',
            'selected' => 'Karyawan',
            'title' => 'Tambah Pendidikan Karyawan',
            'karyawan' => User::where('id_user', $id)->with(['dataDiri'])->first(),
            'jenjangs' => Jenjangs::all()

        ];
        return view('admin.pegawai.karyawan.tambahpendidikan', $data);
    }

    public function storePendidikan(Request $request, string $id)
    {
        $request->validate([
            'jenjang' => 'required|exists:jenjang,id_jenjang',
            'institusi' => 'required|string|max:255',
            'program_studi' => 'nullable|string|max:255',
            'gelar' => 'nullable|string|max:255',
            'tahun_lulus' => 'required|date_format:Y',
            'ijazah' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'transkip_nilai' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // Ambil nomor dokumen terakhir
        $lastDokumen = Dokumens::orderBy('nomor_dokumen', 'desc')->first();
        $lastNumber = $lastDokumen ? (int) $lastDokumen->nomor_dokumen : 0;

        $user = User::findOrFail($id);
        $jenjang = Jenjangs::findOrFail($request->input('jenjang'));

        // Dokumen foto utama
        $newIdIjazah = null;
        $newIdTranskip = null;

        // Handle ijazah file upload
        if ($request->hasFile('ijazah')) {
            $lastNumber++;
            $newIdIjazah = str_pad($lastNumber, 7, '0', STR_PAD_LEFT);
            $originalName = $request->file('ijazah')->getClientOriginalName();
            $timestampedName = time() . '_' . $originalName;
            $destinationPathIjazah = "{$user->npp}/pendidikan/{$jenjang->nama_jenjang}/{$timestampedName}";
            $file = $request->file('ijazah');
            $result = $this->googleDriveService->uploadFileAndGetUrl($file->getPathname(), $destinationPathIjazah);

            if ($result) {
                Dokumens::create([
                    'nomor_dokumen' => $newIdIjazah,
                    'path_file' => $destinationPathIjazah,
                    'id_user' => $user->id_user,
                    'file_id' => $result['file_id'],
                    'view_url' => $result['view_url'],
                    'download_url' => $result['download_url'],
                    'preview_url' => $result['preview_url'],
                    'tanggal_upload' => now()
                ]);
            }
        }
        // Handle transkip nilai file upload
        if ($request->hasFile('transkip_nilai')) {
            $lastNumber++;
            $newIdTranskip = str_pad($lastNumber, 7, '0', STR_PAD_LEFT);
            $originalName = $request->file('transkip_nilai')->getClientOriginalName();
            $timestampedName = time() . '_' . $originalName;
            $destinationPathTranskip = "{$user->npp}/pendidikan/{$jenjang->nama_jenjang}/{$timestampedName}";
            $file = $request->file('transkip_nilai');
            $result = $this->googleDriveService->uploadFileAndGetUrl($file->getPathname(), $destinationPathTranskip);

            if ($result) {
                Dokumens::create([
                    'nomor_dokumen' => $newIdTranskip,
                    'path_file' => $destinationPathTranskip,
                    'id_user' => $user->id_user,
                    'file_id' => $result['file_id'],
                    'view_url' => $result['view_url'],
                    'download_url' => $result['download_url'],
                    'preview_url' => $result['preview_url'],
                    'tanggal_upload' => now()
                ]);
            }
        }


        Pendidikans::create([
            'id_user' => $user->id_user,
            'id_jenjang' => $jenjang->id_jenjang,
            'institusi' => $request->input('institusi'),
            'program_studi' => $request->input('program_studi'),
            'gelar' => $request->input('gelar'),
            'tahun_lulus' => $request->input('tahun_lulus'),
            'ijazah' => $newIdIjazah,
            'transkip_nilai' => $newIdTranskip
        ]);

        return redirect()->route('admin.karyawan.show', $id)->with('success', 'Pendidikan berhasil ditambahkan.');
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
        $data = [
            'page' => 'Karyawan',
            'selected' => 'Karyawan',
            'title' => 'Data Karyawan',
            'karyawan' => User::where('id_user', $id)->with(['dataDiri.dokumen', 'pendidikan.dokumenIjazah', 'pendidikan.dokumenTranskipNilai'])->first()

        ];
        // dd($data);
        return view('admin.pegawai.karyawan.show', $data);
    }

    public function dataDiri(string $id)
    {
        $data = [
            'page' => 'Karyawan',
            'selected' => 'Karyawan',
            'title' => 'Edit Profil Pribadi Karyawan',
            'karyawan' => User::where('id_user', $id)->with(['dataDiri.dokumen'])->first()

        ];
        return view('admin.pegawai.karyawan.profile', $data);
    }

    public function pendidikan(string $id, string $idPendidikan)
    {
        $data = [
            'page' => 'Karyawan',
            'selected' => 'Karyawan',
            'title' => 'Edit Pendidikan Karyawan',
            'karyawan' => User::where('id_user', $id)->with(['dataDiri'])->first(),
            'pendidikan' => Pendidikans::where('id_pendidikan', $idPendidikan)->with(['dokumenIjazah', 'dokumenTranskipNilai'])->first(),
            'jenjangs' => Jenjangs::all()

        ];
        return view('admin.pegawai.karyawan.pendidikan', $data);
    }



    public function dataDiriUpdate(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id . ',id_user',
            'nik' => 'required|max:20',
            'no_hp' => 'required|max:20',
            'tanggal_lahir' => 'required|date',
            'tempat_lahir' => 'required|string|max:255',
            'agama' => 'required|string|max:50',
            'desa' => 'required|string|max:255',
            'rt' => 'required|max:3',
            'rw' => 'required|max:3',
            'jenis_kelamin' => 'required|in:Laki-Laki,Perempuan',
            'kecamatan' => 'required|string|max:255',
            'kabupaten' => 'required|string|max:255',
            'provinsi' => 'required|string|max:255',
            'alamat' => 'required|string',
            'nuptk' => 'required|max:30',
            'nip' => 'nullable|max:30',
            'nidk' => 'nullable|max:30',
            'nidn' => 'nullable|max:30',
            'tanggal_bergabung' => 'required|date',


            // Foto
            'foto' => 'nullable|image|max:2048',
        ]);
        $user = User::findOrFail($id);
        $dataDiri = DataDiri::where('id_user', $id)->with(['dokumen'])->first();

        $user->update([
            'email' => $request->input('email'),
        ]);

        $fileLama = $dataDiri->dokumen->path_file;
        if ($request->hasFile('foto')) {
            // ===== Simpan foto profil langsung ke Google Drive =====
            $originalName = $request->file('foto')->getClientOriginalName();
            $timestampedName = time() . '_' . $originalName;
            $destinationPath = "{$user->npp}/datadiri/{$timestampedName}";
            // Ambil file dari request
            $file = $request->file('foto');
            // Upload langsung ke Google Drive
            $result = $this->googleDriveService->uploadFileAndGetUrl($file->getPathname(), $destinationPath);

            if ($result) {
                // Jika upload berhasil, simpan URL ke database
                $dataDiri->dokumen()->update([
                    'path_file' => $destinationPath,
                    'file_id' => $result['file_id'],
                    'view_url' => $result['view_url'],
                    'download_url' => $result['download_url'],
                    'preview_url' => $result['preview_url'],
                    'tanggal_upload' => now()
                ]);
            }
        }


        $dataDiri->update([
            'name' => $request->input('name'),
            'no_ktp' => $request->input('nik'),
            'no_hp' => $request->input('no_hp'),
            'tanggal_lahir' => $request->input('tanggal_lahir'),
            'tempat_lahir' => $request->input('tempat_lahir'),
            'agama' => $request->input('agama'),
            'desa' => $request->input('desa'),
            'rt' => $request->input('rt'),
            'rw' => $request->input('rw'),
            'jenis_kelamin' => $request->input('jenis_kelamin'),
            'kecamatan' => $request->input('kecamatan'),
            'kabupaten' => $request->input('kabupaten'),
            'provinsi' => $request->input('provinsi'),
            'alamat' => $request->input('alamat'),
            'nuptk' => $request->input('nuptk'),
            'nip' => $request->input('nip'),
            'nidk' => $request->input('nidk'),
            'nidn' => $request->input('nidn'),
            'tanggal_bergabung' => $request->input('tanggal_bergabung'),

        ]);

        // Hapus file lama dari Google Drive jika ada
        if ($dataDiri->dokumen && $dataDiri->dokumen->file_id && $request->hasFile('foto')) {
            if ($fileLama && $fileLama !== $destinationPath) {
                Gdrive::delete($fileLama);
            }
        }
        return redirect()->route('admin.karyawan.show', $id)->with('success', 'Data Diri karyawan berhasil diperbarui.');
    }

    // public function pendidikanUpdate(Request $request, string $id, string $idPendidikan)
    // {
    //     $request->validate([
    //         'jenjang' => 'required|exists:jenjang,id_jenjang',
    //         'institusi' => 'required|string|max:255',
    //         'program_studi' => 'nullable|string|max:255',
    //         'gelar' => 'nullable|string|max:255',
    //         'tahun_lulus' => 'required|date_format:Y',
    //         'ijazah' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
    //         'transkip_nilai' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
    //     ]);

    //     $user = User::findOrFail($id);
    //     $pendidikan = Pendidikans::where('id_pendidikan', $idPendidikan)->with(['dokumenIjazah', 'dokumenTranskipNilai', 'jenjang'])->first();

    //     $oldJenjangName = $pendidikan->jenjang ? $pendidikan->jenjang->nama_jenjang : '';
    //     $newJenjang = Jenjangs::find($request->jenjang);
    //     $newJenjangName = $newJenjang ? $newJenjang->nama_jenjang : '';

    //     $fileLamaIjazah = $pendidikan->dokumenIjazah->path_file ?? null;
    //     $fileLamaTranskip = $pendidikan->dokumenTranskipNilai->path_file ?? null;

    //     // $folderChanged bernilai true jika nama jenjang lama dan baru berbeda, false jika sama
    //     $folderChanged = $oldJenjangName !== $newJenjangName;
    //     if ($folderChanged) {
    //         $oldFolder = "{$user->npp}/pendidikan/{$oldJenjangName}";
    //         $newFolder = "{$user->npp}/pendidikan/{$newJenjangName}";
    //         Gdrive::renameDir($oldFolder, $newFolder);
    //     }

    //     // Rename folder if jenjang changed and no file is uploaded
    //     if ($folderChanged && !$request->hasFile('ijazah')) {
    //         // Update dokumenIjazah path_file
    //         if ($pendidikan->dokumenIjazah && $pendidikan->dokumenIjazah->path_file) {
    //             $filename = basename($fileLamaIjazah);
    //             $newIjazahPath = "{$newFolder}/{$filename}";
    //             $pendidikan->dokumenIjazah()->update(['path_file' => $newIjazahPath]);
    //         }
    //     }
    //     if ($folderChanged && !$request->hasFile('transkip_nilai')) {
    //         // Update dokumenTranskipNilai path_file
    //         if ($pendidikan->dokumenTranskipNilai && $pendidikan->dokumenTranskipNilai->path_file) {
    //             $filename = basename($fileLamaTranskip);
    //             $newTranskipPath = "{$newFolder}/{$filename}";
    //             $pendidikan->dokumenTranskipNilai()->update(['path_file' => $newTranskipPath]);
    //         }
    //     }

    //     if ($folderChanged && $request->hasFile('ijazah')) {
    //         if ($pendidikan->dokumenIjazah && $pendidikan->dokumenIjazah->path_file) {
    //             $filename = basename($fileLamaIjazah);
    //             $newIjazahPath = "{$newFolder}/{$filename}";
    //             $pendidikan->dokumenIjazah()->update(['path_file' => $newIjazahPath]);
    //         }
    //     }
    //     if ($folderChanged && $request->hasFile('transkip_nilai')) {
    //         // Update dokumenTranskipNilai path_file
    //         if ($pendidikan->dokumenTranskipNilai && $pendidikan->dokumenTranskipNilai->path_file) {
    //             $filename = basename($fileLamaTranskip);
    //             $newTranskipPath = "{$newFolder}/{$filename}";
    //             $pendidikan->dokumenTranskipNilai()->update(['path_file' => $newTranskipPath]);
    //         }
    //     }

    //     // Handle ijazah file upload
    //     if ($request->hasFile('ijazah')) {

    //         $originalName = $request->file('ijazah')->getClientOriginalName();
    //         $timestampedName = time() . '_' . $originalName;
    //         $destinationPathIjazah = "{$user->npp}/pendidikan/{$newJenjangName}/{$timestampedName}";
    //         $file = $request->file('ijazah');
    //         $result = $this->googleDriveService->uploadFileAndGetUrl($file->getPathname(), $destinationPathIjazah);

    //         if ($result) {
    //             $pendidikan->dokumenIjazah()->update([
    //                 'path_file' => $destinationPathIjazah,
    //                 'file_id' => $result['file_id'],
    //                 'view_url' => $result['view_url'],
    //                 'download_url' => $result['download_url'],
    //                 'preview_url' => $result['preview_url'],
    //                 'tanggal_upload' => now()
    //             ]);

    //             if ($folderChanged) {
    //                 Gdrive::delete("{$user->npp}/pendidikan/{$newJenjangName}/" . basename($fileLamaIjazah));
    //             } else {

    //                 Gdrive::delete($fileLamaIjazah);
    //                 // Hapus file lama ijazah jika ada

    //             }
    //         }
    //     }

    //     // Handle transkip_nilai file upload
    //     if ($request->hasFile('transkip_nilai')) {
    //         // If jenjang changed, rename folder first and update ijazah path if exists

    //         $originalName = $request->file('transkip_nilai')->getClientOriginalName();
    //         $timestampedName = time() . '_' . $originalName;
    //         $destinationPathTranskip = "{$user->npp}/pendidikan/{$newJenjangName}/{$timestampedName}";
    //         $file = $request->file('transkip_nilai');
    //         $result = $this->googleDriveService->uploadFileAndGetUrl($file->getPathname(), $destinationPathTranskip);

    //         if ($result) {
    //             if ($pendidikan->dokumenTranskipNilai) {
    //                 $pendidikan->dokumenTranskipNilai()->update([
    //                     'path_file' => $destinationPathTranskip,
    //                     'file_id' => $result['file_id'],
    //                     'view_url' => $result['view_url'],
    //                     'download_url' => $result['download_url'],
    //                     'preview_url' => $result['preview_url'],
    //                     'tanggal_upload' => now()
    //                 ]);
    //                 if ($folderChanged) {
    //                     Gdrive::delete("{$user->npp}/pendidikan/{$newJenjangName}/" . basename($fileLamaTranskip));
    //                 } else {
    //                     // Hapus file lama transkip jika ada
    //                     Gdrive::delete($fileLamaTranskip);
    //                 }
    //             } else {
    //                 $lastDokumen = Dokumens::orderBy('nomor_dokumen', 'desc')->first();
    //                 $lastNumber = $lastDokumen ? (int) $lastDokumen->nomor_dokumen : 0;
    //                 $lastNumber++;
    //                 $newId = str_pad($lastNumber, 7, '0', STR_PAD_LEFT);

    //                 $pendidikan->dokumenTranskipNilai()->create([
    //                     'nomor_dokumen' => $newId,
    //                     'id_user' => $user->id_user,
    //                     'path_file' => $destinationPathTranskip,
    //                     'file_id' => $result['file_id'],
    //                     'view_url' => $result['view_url'],
    //                     'download_url' => $result['download_url'],
    //                     'preview_url' => $result['preview_url'],
    //                     'tanggal_upload' => now()
    //                 ]);
    //                 $pendidikan->update([
    //                     'transkip_nilai' => $newId
    //                 ]);
    //             }
    //         }
    //     }

    //     $pendidikan->update([
    //         'id_jenjang' => $request->input('jenjang'),
    //         'institusi' => $request->input('institusi'),
    //         'program_studi' => $request->input('program_studi'),
    //         'gelar' => $request->input('gelar'),
    //         'tahun_lulus' => $request->input('tahun_lulus'),
    //     ]);

    //     return redirect()->route('admin.karyawan.show', $id)->with('success', 'Pendidikan karyawan berhasil diperbarui.');
    // }

    public function pendidikanUpdate(Request $request, string $id, string $idPendidikan)
    {
        $request->validate([
            'jenjang'         => 'required|exists:jenjang,id_jenjang',
            'institusi'       => 'required|string|max:255',
            'program_studi'   => 'nullable|string|max:255',
            'gelar'           => 'nullable|string|max:255',
            'tahun_lulus'     => 'required|date_format:Y',
            'ijazah'          => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'transkip_nilai'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        return DB::transaction(function () use ($request, $id, $idPendidikan) {

            $user = User::findOrFail($id);

            /** @var \App\Models\Pendidikans $pendidikan */
            $pendidikan = Pendidikans::with(['dokumenIjazah', 'dokumenTranskipNilai', 'jenjang'])
                ->where('id_pendidikan', $idPendidikan)->firstOrFail();

            $oldJenjangName = $pendidikan->jenjang->nama_jenjang ?? '';
            $newJenjang     = Jenjangs::findOrFail($request->jenjang);
            $newJenjangName = $newJenjang->nama_jenjang ?? '';

            $oldFolder = $oldJenjangName ? "{$user->npp}/pendidikan/{$oldJenjangName}" : null;
            $newFolder = "{$user->npp}/pendidikan/{$newJenjangName}";

            $fileLamaIjazah   = $pendidikan->dokumenIjazah->path_file ?? null;
            $fileLamaTranskip = $pendidikan->dokumenTranskipNilai->path_file ?? null;

            $folderChanged = $oldJenjangName !== $newJenjangName;

            // Pastikan folder tujuan ada (tidak error kalau sudah ada)
            if ($folderChanged) {
                try {
                    Gdrive::makeDir($newFolder);
                } catch (\Throwable $e) {
                }
            }

            // Helper: copy → delete per-file di Google Drive
            $moveOnDrive = function (?string $oldPath, string $targetFolder) {
                if (!$oldPath) return null;
                $filename = basename($oldPath);
                $newPath  = rtrim($targetFolder, '/') . '/' . $filename;
                if ($oldPath === $newPath) return $newPath;

                // copy
                $blob = Gdrive::get($oldPath);           // $blob->file (binary), $blob->ext
                Storage::disk('google')->put($newPath, $blob->file);
                // delete source
                Gdrive::delete($oldPath);

                return $newPath;
            };

            // ===== Jika jenjang berubah & TIDAK upload baru -> pindahkan file lama satu per satu =====
            if ($folderChanged && !$request->hasFile('ijazah') && $pendidikan->dokumenIjazah && $fileLamaIjazah) {
                if ($newPath = $moveOnDrive($fileLamaIjazah, $newFolder)) {
                    $pendidikan->dokumenIjazah()->update(['path_file' => $newPath]);
                }
            }

            if ($folderChanged && !$request->hasFile('transkip_nilai') && $pendidikan->dokumenTranskipNilai && $fileLamaTranskip) {
                if ($newPath = $moveOnDrive($fileLamaTranskip, $newFolder)) {
                    $pendidikan->dokumenTranskipNilai()->update(['path_file' => $newPath]);
                }
            }

            // ===== Upload/replace Ijazah =====
            if ($request->hasFile('ijazah')) {
                $originalName      = $request->file('ijazah')->getClientOriginalName();
                $timestampedName   = time() . '_' . $originalName;
                $destinationIjazah = "{$newFolder}/{$timestampedName}";
                $file              = $request->file('ijazah');

                // pakai service-mu agar dapat file_id & url
                $result = $this->googleDriveService->uploadFileAndGetUrl($file->getPathname(), $destinationIjazah);

                if ($result) {
                    $pendidikan->dokumenIjazah()
                        ->updateOrCreate(
                            [], // sesuaikan unique key relasi dokumen ijazah jika ada
                            [
                                'path_file'      => $destinationIjazah,
                                'file_id'        => $result['file_id'],
                                'view_url'       => $result['view_url'],
                                'download_url'   => $result['download_url'],
                                'preview_url'    => $result['preview_url'],
                                'tanggal_upload' => now(),
                                'id_user'        => $user->id_user ?? null,
                            ]
                        );

                    if ($fileLamaIjazah && $fileLamaIjazah !== $destinationIjazah) {
                        try {
                            Gdrive::delete($fileLamaIjazah);
                        } catch (\Throwable $e) {
                        }
                    }
                }
            }

            // ===== Upload/replace Transkip Nilai =====
            if ($request->hasFile('transkip_nilai')) {
                $originalName        = $request->file('transkip_nilai')->getClientOriginalName();
                $timestampedName     = time() . '_' . $originalName;
                $destinationTranskip = "{$newFolder}/{$timestampedName}";
                $file                = $request->file('transkip_nilai');

                $result = $this->googleDriveService->uploadFileAndGetUrl($file->getPathname(), $destinationTranskip);

                if ($result) {
                    if ($pendidikan->dokumenTranskipNilai) {
                        $pendidikan->dokumenTranskipNilai()->update([
                            'path_file'      => $destinationTranskip,
                            'file_id'        => $result['file_id'],
                            'view_url'       => $result['view_url'],
                            'download_url'   => $result['download_url'],
                            'preview_url'    => $result['preview_url'],
                            'tanggal_upload' => now(),
                        ]);
                    } else {
                        $last  = Dokumens::orderBy('nomor_dokumen', 'desc')->first();
                        $num   = $last ? (int)$last->nomor_dokumen + 1 : 1;
                        $newId = str_pad($num, 7, '0', STR_PAD_LEFT);

                        $pendidikan->dokumenTranskipNilai()->create([
                            'nomor_dokumen'  => $newId,
                            'id_user'        => $user->id_user ?? null,
                            'path_file'      => $destinationTranskip,
                            'file_id'        => $result['file_id'],
                            'view_url'       => $result['view_url'],
                            'download_url'   => $result['download_url'],
                            'preview_url'    => $result['preview_url'],
                            'tanggal_upload' => now(),
                        ]);

                        $pendidikan->update(['transkip_nilai' => $newId]);
                    }

                    if ($fileLamaTranskip && $fileLamaTranskip !== $destinationTranskip) {
                        try {
                            Gdrive::delete($fileLamaTranskip);
                        } catch (\Throwable $e) {
                        }
                    }
                }
            }

            // ===== Update data pendidikan =====
            $pendidikan->update([
                'id_jenjang'    => $request->jenjang,
                'institusi'     => $request->institusi,
                'program_studi' => $request->program_studi,
                'gelar'         => $request->gelar,
                'tahun_lulus'   => $request->tahun_lulus,
            ]);

            return redirect()->route('admin.karyawan.show', $id)
                ->with('success', 'Pendidikan Karyawan berhasil diperbarui.');
        });
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

    public function deletePendidikan(Request $request, string $id, string $idPendidikan)
    {
        $pendidikan = Pendidikans::where('id_pendidikan', $idPendidikan)->with(['dokumenIjazah', 'dokumenTranskipNilai'])->first();
        if ($pendidikan) {
            // Hapus dokumen ijazah jika ada
            if ($pendidikan->dokumenIjazah) {
                Gdrive::delete($pendidikan->dokumenIjazah->path_file);
            }
            // Hapus dokumen transkip nilai jika ada
            if ($pendidikan->dokumenTranskipNilai) {
                Gdrive::delete($pendidikan->dokumenTranskipNilai->path_file);
            }
            $pendidikan->delete();
            return redirect()->route('admin.karyawan.show', $id)->with('success', 'Data pendidikan berhasil dihapus.');
        }
        return redirect()->route('admin.karyawan.show', $id)->with('error', 'Data pendidikan tidak ditemukan.');
    }
}
