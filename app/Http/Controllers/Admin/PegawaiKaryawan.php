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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
    public function index(Request $request)
    {
        $keyword = $request->get('karyawan');
        $data = [
            'page'      => 'Karyawan',
            'selected'  => 'Karyawan',
            'title'     => 'Data Karyawan',
            'karyawans' => User::where('role', 'karyawan')->with(['dataDiri'])
                ->when($keyword, function ($query) use ($keyword) {
                    $query->searchKaryawan($keyword);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10)
                ->withQueryString()
        ];
        return view('admin.pegawai.karyawan.index', $data);
    }

    public function createPendidikan(string $id)
    {
        $data = [
            'page'      => 'Karyawan',
            'selected'  => 'Karyawan',
            'title'     => 'Tambah Pendidikan Karyawan',
            'karyawan'  => User::where('id_user', $id)->with(['dataDiri'])->first(),
            'jenjangs'  => Jenjangs::all()
        ];
        return view('admin.pegawai.karyawan.tambahpendidikan', $data);
    }

    public function storePendidikan(Request $request, string $id)
    {
        $request->validate([
            'jenjang'        => 'required|exists:jenjang,id_jenjang',
            'institusi'      => 'required|string|max:255',
            'program_studi'  => 'nullable|string|max:255',
            'gelar'          => 'nullable|string|max:255',
            'tahun_lulus'    => 'required|date_format:Y',
            'ijazah'         => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'transkip_nilai' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $lastDokumen = Dokumens::orderBy('nomor_dokumen', 'desc')->first();
        $lastNumber  = $lastDokumen ? (int) $lastDokumen->nomor_dokumen : 0;

        $user    = User::findOrFail($id);
        $jenjang = Jenjangs::findOrFail($request->input('jenjang'));

        $newIdIjazah   = null;
        $newIdTranskip = null;

        // ijazah
        if ($request->hasFile('ijazah')) {
            $lastNumber++;
            $newIdIjazah     = str_pad($lastNumber, 7, '0', STR_PAD_LEFT);
            $originalName    = $request->file('ijazah')->getClientOriginalName();
            $timestampedName = time() . '_' . $originalName;
            $destIjazah      = "{$user->npp}/pendidikan/{$jenjang->nama_jenjang}/{$timestampedName}";
            $result          = $this->googleDriveService->uploadFileAndGetUrl($request->file('ijazah')->getPathname(), $destIjazah);

            if ($result) {
                Dokumens::create([
                    'nomor_dokumen'  => $newIdIjazah,
                    'path_file'      => $destIjazah,
                    'id_user'        => $user->id_user,
                    'file_id'        => $result['file_id'],
                    'view_url'       => $result['view_url'],
                    'download_url'   => $result['download_url'],
                    'preview_url'    => $result['preview_url'],
                    'tanggal_upload' => now()
                ]);
            }
        }

        // transkip nilai
        if ($request->hasFile('transkip_nilai')) {
            $lastNumber++;
            $newIdTranskip   = str_pad($lastNumber, 7, '0', STR_PAD_LEFT);
            $originalName    = $request->file('transkip_nilai')->getClientOriginalName();
            $timestampedName = time() . '_' . $originalName;
            $destTranskip    = "{$user->npp}/pendidikan/{$jenjang->nama_jenjang}/{$timestampedName}";
            $result          = $this->googleDriveService->uploadFileAndGetUrl($request->file('transkip_nilai')->getPathname(), $destTranskip);

            if ($result) {
                Dokumens::create([
                    'nomor_dokumen'  => $newIdTranskip,
                    'path_file'      => $destTranskip,
                    'id_user'        => $user->id_user,
                    'file_id'        => $result['file_id'],
                    'view_url'       => $result['view_url'],
                    'download_url'   => $result['download_url'],
                    'preview_url'    => $result['preview_url'],
                    'tanggal_upload' => now()
                ]);
            }
        }

        Pendidikans::create([
            'id_user'        => $user->id_user,
            'id_jenjang'     => $jenjang->id_jenjang,
            'institusi'      => $request->input('institusi'),
            'program_studi'  => $request->input('program_studi'),
            'gelar'          => $request->input('gelar'),
            'tahun_lulus'    => $request->input('tahun_lulus'),
            'ijazah'         => $newIdIjazah,
            'transkip_nilai' => $newIdTranskip
        ]);

        return redirect()->route('admin.karyawan.show', $id)->with('success', 'Pendidikan berhasil ditambahkan.');
    }

    public function create()
    {
        $data = [
            'page'     => 'Karyawan',
            'selected' => 'Karyawan',
            'title'    => 'Tambah Data Karyawan',
            'jenjangs' => Jenjangs::all(),
        ];

        return view('admin.pegawai.karyawan.create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'password'                         => 'required|string|min:6',
            'password_confirmation'            => 'same:password',
            'name'                             => 'required|string|max:255',
            'email'                            => 'required|email|max:255|unique:users,email',
            'nik'                              => 'required|max:20',
            'no_hp'                            => 'required|max:20|unique:data_diri,no_ktp',
            'tanggal_lahir'                    => 'required|date',
            'tempat_lahir'                     => 'required|string|max:255',
            'agama'                            => 'required|string|max:50',
            'desa'                             => 'required|string|max:255',
            'rt'                               => 'required|max:3',
            'rw'                               => 'required|max:3',
            'jenis_kelamin'                    => 'required|in:Laki-Laki,Perempuan',
            'kecamatan'                        => 'required|string|max:255',
            'kabupaten'                        => 'required|string|max:255',
            'provinsi'                         => 'required|string|max:255',
            'alamat'                           => 'required|string',
            'npp'                              => 'required|max:30|unique:users,npp',
            'tanggal_bergabung'                => 'required|date',
            'pendidikan'                       => 'required|array|min:1',
            'pendidikan.*.jenjang'             => 'required|integer|max:255',
            'pendidikan.*.tahun_lulus'         => 'required|max:5',
            'pendidikan.*.program_studi'       => 'required|string|max:255',
            'pendidikan.*.gelar'               => 'nullable|string|max:255',
            'pendidikan.*.institusi'           => 'nullable|string|max:255',
            'pendidikan.*.ijazah'              => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'pendidikan.*.transkip_nilai'      => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'foto'                             => 'required|image|max:2048',
        ], [
            'password.required'      => 'Password harus di isi',
            'password.min'           => 'Password minimal 6 karakter',
            'password_confirmation.same' => 'Konfirmasi Password tidak sama',
        ]);

        $lastDokumen = Dokumens::orderBy('nomor_dokumen', 'desc')->first();
        $lastNumber  = $lastDokumen ? (int) $lastDokumen->nomor_dokumen : 0;

        // foto utama
        $lastNumber++;
        $newId          = str_pad($lastNumber, 7, '0', STR_PAD_LEFT);
        $originalName   = $request->file('foto')->getClientOriginalName();
        $timestamped    = time() . '_' . $originalName;
        $destFoto       = "{$request->npp}/datadiri/{$timestamped}";
        $uploadFoto     = $this->googleDriveService->uploadFileAndGetUrl($request->file('foto')->getPathname(), $destFoto);

        // user
        $user = User::create([
            'email'            => $request->email,
            'npp'              => $request->npp,
            'password'         => Hash::make($request->password),
            'status_keaktifan' => 'aktif',
            'role'             => 'karyawan'
        ]);

        Dokumens::create([
            'nomor_dokumen'  => $newId,
            'path_file'      => $destFoto,
            'file_id'        => $uploadFoto['file_id'],
            'view_url'       => $uploadFoto['view_url'],
            'download_url'   => $uploadFoto['download_url'],
            'preview_url'    => $uploadFoto['preview_url'],
            'id_user'        => $user->id_user,
            'tanggal_upload' => now()
        ]);

        DataDiri::create([
            'id_user'          => $user->id_user,
            'name'             => $request->name,
            'no_ktp'           => $request->nik,
            'no_hp'            => $request->no_hp,
            'tanggal_lahir'    => $request->tanggal_lahir,
            'tempat_lahir'     => $request->tempat_lahir,
            'jenis_kelamin'    => $request->jenis_kelamin,
            'agama'            => $request->agama,
            'tanggal_bergabung' => $request->tanggal_bergabung,
            'alamat'           => $request->alamat,
            'rt'               => $request->rt,
            'rw'               => $request->rw,
            'desa'             => $request->desa,
            'kecamatan'        => $request->kecamatan,
            'kabupaten'        => $request->kabupaten,
            'provinsi'         => $request->provinsi,
            'foto'             => $newId,
        ]);

        // pendidikan
        $newIdIjazah = null;
        $newIdT      = null;
        foreach ($request->pendidikan as $index => $pendidikanData) {
            $jenjang = Jenjangs::find($pendidikanData['jenjang']);
            if (!$jenjang) continue;

            $namaJenjang = $jenjang->nama_jenjang;

            $newIdIjazah = null;
            $newIdT      = null;

            if ($request->hasFile("pendidikan.$index.ijazah")) {
                $lastNumber++;
                $newIdIjazah     = str_pad($lastNumber, 7, '0', STR_PAD_LEFT);
                $orig             = $request->file("pendidikan.$index.ijazah")->getClientOriginalName();
                $ts               = time() . '_' . $orig;
                $ijazahPath       = "{$user->npp}/pendidikan/{$namaJenjang}/{$ts}";
                $ijazahUpload     = $this->googleDriveService->uploadFileAndGetUrl(
                    $request->file("pendidikan.$index.ijazah")->getPathname(),
                    $ijazahPath
                );

                Dokumens::create([
                    'nomor_dokumen'  => $newIdIjazah,
                    'path_file'      => $ijazahPath,
                    'file_id'        => $ijazahUpload['file_id'],
                    'view_url'       => $ijazahUpload['view_url'],
                    'download_url'   => $ijazahUpload['download_url'],
                    'preview_url'    => $ijazahUpload['preview_url'],
                    'id_user'        => $user->id_user,
                    'tanggal_upload' => now()
                ]);
            }

            if ($request->hasFile("pendidikan.$index.transkip_nilai")) {
                $lastNumber++;
                $newIdT           = str_pad($lastNumber, 7, '0', STR_PAD_LEFT);
                $origT            = $request->file("pendidikan.$index.transkip_nilai")->getClientOriginalName();
                $tsT              = time() . '_' . $origT;
                $transkipPath     = "{$user->npp}/pendidikan/{$namaJenjang}/{$tsT}";
                $transkipUpload   = $this->googleDriveService->uploadFileAndGetUrl(
                    $request->file("pendidikan.$index.transkip_nilai")->getPathname(),
                    $transkipPath
                );

                Dokumens::create([
                    'nomor_dokumen'  => $newIdT,
                    'path_file'      => $transkipPath,
                    'file_id'        => $transkipUpload['file_id'],
                    'view_url'       => $transkipUpload['view_url'],
                    'download_url'   => $transkipUpload['download_url'],
                    'preview_url'    => $transkipUpload['preview_url'],
                    'id_user'        => $user->id_user,
                    'tanggal_upload' => now()
                ]);
            }

            Pendidikans::create([
                'id_user'        => $user->id_user,
                'id_jenjang'     => $jenjang->id_jenjang,
                'institusi'      => $pendidikanData['institusi'] ?? null,
                'tahun_lulus'    => $pendidikanData['tahun_lulus'],
                'ijazah'         => $newIdIjazah ?? null,
                'transkip_nilai' => $newIdT ?? null,
                'gelar'          => $pendidikanData['gelar'] ?? null,
                'program_studi'  => $pendidikanData['program_studi'],
            ]);
        }

        return redirect()->route('admin.karyawan')->with('success', 'Data karyawan berhasil ditambahkan');
    }

    public function show(string $id)
    {
        $data = [
            'page'     => 'Karyawan',
            'selected' => 'Karyawan',
            'title'    => 'Data Karyawan',
            'karyawan' => User::where('id_user', $id)
                ->with([
                    'dataDiri.dokumen',
                    'pendidikan' => function ($q) {
                        $q->orderBy('id_jenjang', 'asc'); // urutkan pendidikan dari jenjang kecil → besar
                    },
                    'pendidikan.dokumenIjazah',
                    'pendidikan.dokumenTranskipNilai'
                ])
                ->first()
        ];

        return view('admin.pegawai.karyawan.show', $data);
    }


    public function dataDiri(string $id)
    {
        $data = [
            'page'      => 'Karyawan',
            'selected'  => 'Karyawan',
            'title'     => 'Edit Profil Pribadi Karyawan',
            'karyawan'  => User::where('id_user', $id)->with(['dataDiri.dokumen'])->first()
        ];
        return view('admin.pegawai.karyawan.profile', $data);
    }

    public function pendidikan(string $id, string $idPendidikan)
    {
        $data = [
            'page'        => 'Karyawan',
            'selected'    => 'Karyawan',
            'title'       => 'Edit Pendidikan Karyawan',
            'karyawan'    => User::where('id_user', $id)->with(['dataDiri'])->first(),
            'pendidikan'  => Pendidikans::where('id_pendidikan', $idPendidikan)->with(['dokumenIjazah', 'dokumenTranskipNilai'])->first(),
            'jenjangs'    => Jenjangs::all()
        ];
        return view('admin.pegawai.karyawan.pendidikan', $data);
    }

    public function dataDiriUpdate(Request $request, string $id)
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'email'             => 'required|email|max:255|unique:users,email,' . $id . ',id_user',
            'nik'               => 'required|max:20',
            'no_hp'             => 'required|max:20',
            'tanggal_lahir'     => 'required|date',
            'tempat_lahir'      => 'required|string|max:255',
            'agama'             => 'required|string|max:50',
            'desa'              => 'required|string|max:255',
            'rt'                => 'required|max:3',
            'rw'                => 'required|max:3',
            'jenis_kelamin'     => 'required|in:Laki-Laki,Perempuan',
            'kecamatan'         => 'required|string|max:255',
            'kabupaten'         => 'required|string|max:255',
            'provinsi'          => 'required|string|max:255',
            'alamat'            => 'required|string',
            'nuptk'             => 'required|max:30',
            'nip'               => 'nullable|max:30',
            'nidk'              => 'nullable|max:30',
            'nidn'              => 'nullable|max:30',
            'tanggal_bergabung' => 'required|date',
            'foto'              => 'nullable|image|max:2048',
        ]);

        $user     = User::findOrFail($id);
        $dataDiri = DataDiri::where('id_user', $id)->with(['dokumen'])->first();

        $user->update([
            'email' => $request->input('email'),
        ]);

        $oldFileId = $dataDiri->dokumen->file_id ?? null;

        if ($request->hasFile('foto')) {
            $originalName    = $request->file('foto')->getClientOriginalName();
            $timestampedName = time() . '_' . $originalName;
            $destinationPath = "{$user->npp}/datadiri/{$timestampedName}";
            $result          = $this->googleDriveService->uploadFileAndGetUrl($request->file('foto')->getPathname(), $destinationPath);

            if ($result) {
                $dataDiri->dokumen()->update([
                    'path_file'      => $destinationPath,
                    'file_id'        => $result['file_id'],
                    'view_url'       => $result['view_url'],
                    'download_url'   => $result['download_url'],
                    'preview_url'    => $result['preview_url'],
                    'tanggal_upload' => now()
                ]);
            }
        }

        $dataDiri->update([
            'name'              => $request->input('name'),
            'no_ktp'            => $request->input('nik'),
            'no_hp'             => $request->input('no_hp'),
            'tanggal_lahir'     => $request->input('tanggal_lahir'),
            'tempat_lahir'      => $request->input('tempat_lahir'),
            'agama'             => $request->input('agama'),
            'desa'              => $request->input('desa'),
            'rt'                => $request->input('rt'),
            'rw'                => $request->input('rw'),
            'jenis_kelamin'     => $request->input('jenis_kelamin'),
            'kecamatan'         => $request->input('kecamatan'),
            'kabupaten'         => $request->input('kabupaten'),
            'provinsi'          => $request->input('provinsi'),
            'alamat'            => $request->input('alamat'),
            'nuptk'             => $request->input('nuptk'),
            'nip'               => $request->input('nip'),
            'nidk'              => $request->input('nidk'),
            'nidn'              => $request->input('nidn'),
            'tanggal_bergabung' => $request->input('tanggal_bergabung'),
        ]);

        // Hapus file lama di Drive jika ada file baru
        if ($oldFileId && $request->hasFile('foto')) {
            try {
                $this->googleDriveService->deleteById($oldFileId);
            } catch (\Throwable $e) {
            }
        }

        return redirect()->route('admin.karyawan.show', $id)->with('success', 'Data Diri karyawan berhasil diperbarui.');
    }

    public function pendidikanUpdate(Request $request, string $id, string $idPendidikan)
    {
        $request->validate([
            'jenjang'        => 'required|exists:jenjang,id_jenjang',
            'institusi'      => 'required|string|max:255',
            'program_studi'  => 'nullable|string|max:255',
            'gelar'          => 'nullable|string|max:255',
            'tahun_lulus'    => 'required|date_format:Y',
            'ijazah'         => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'transkip_nilai' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        return DB::transaction(function () use ($request, $id, $idPendidikan) {

            $user = User::findOrFail($id);

            /** @var \App\Models\Pendidikans $pendidikan */
            $pendidikan = Pendidikans::with(['dokumenIjazah', 'dokumenTranskipNilai', 'jenjang'])
                ->where('id_pendidikan', $idPendidikan)->firstOrFail();

            $oldJenjangName = $pendidikan->jenjang->nama_jenjang ?? '';
            $newJenjang     = Jenjangs::findOrFail($request->jenjang);
            $newJenjangName = $newJenjang->nama_jenjang ?? '';

            $newFolder = "{$user->npp}/pendidikan/{$newJenjangName}";

            $fileLamaIjazahPath   = $pendidikan->dokumenIjazah->path_file ?? null;
            $fileLamaTranskipPath = $pendidikan->dokumenTranskipNilai->path_file ?? null;

            $oldIjazahId   = $pendidikan->dokumenIjazah->file_id ?? null;
            $oldTranskipId = $pendidikan->dokumenTranskipNilai->file_id ?? null;

            $folderChanged = $oldJenjangName !== $newJenjangName;

            // Jika folder berubah & tidak ada upload baru -> pindahkan file lama (by file_id)
            if ($folderChanged && !$request->hasFile('ijazah') && $oldIjazahId) {
                try {
                    $this->googleDriveService->moveFileTo($oldIjazahId, [$user->npp, 'pendidikan', $newJenjangName], basename($fileLamaIjazahPath));
                    $pendidikan->dokumenIjazah()->update(['path_file' => $newFolder . '/' . basename($fileLamaIjazahPath)]);
                } catch (\Throwable $e) {
                }
            }

            if ($folderChanged && !$request->hasFile('transkip_nilai') && $oldTranskipId) {
                try {
                    $this->googleDriveService->moveFileTo($oldTranskipId, [$user->npp, 'pendidikan', $newJenjangName], basename($fileLamaTranskipPath));
                    $pendidikan->dokumenTranskipNilai()->update(['path_file' => $newFolder . '/' . basename($fileLamaTranskipPath)]);
                } catch (\Throwable $e) {
                }
            }

            // Upload/replace Ijazah
            if ($request->hasFile('ijazah')) {
                $originalName      = $request->file('ijazah')->getClientOriginalName();
                $timestampedName   = time() . '_' . $originalName;
                $destinationIjazah = "{$newFolder}/{$timestampedName}";
                $result            = $this->googleDriveService->uploadFileAndGetUrl($request->file('ijazah')->getPathname(), $destinationIjazah);

                if ($result) {
                    $pendidikan->dokumenIjazah()
                        ->updateOrCreate(
                            [],
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

                    if ($oldIjazahId) {
                        try {
                            $this->googleDriveService->deleteById($oldIjazahId);
                        } catch (\Throwable $e) {
                        }
                    }
                }
            }

            // Upload/replace Transkip
            if ($request->hasFile('transkip_nilai')) {
                $originalName        = $request->file('transkip_nilai')->getClientOriginalName();
                $timestampedName     = time() . '_' . $originalName;
                $destinationTranskip = "{$newFolder}/{$timestampedName}";
                $result              = $this->googleDriveService->uploadFileAndGetUrl($request->file('transkip_nilai')->getPathname(), $destinationTranskip);

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

                    if ($oldTranskipId) {
                        try {
                            $this->googleDriveService->deleteById($oldTranskipId);
                        } catch (\Throwable $e) {
                        }
                    }
                }
            }

            // Update field pendidikan
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

    public function edit(string $id)
    { /* kosong */
    }
    public function update(Request $request, string $id)
    { /* kosong */
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        // larangan sesuai logika kamu
        if ($user->role === 'admin')   return back()->with('error', 'Tidak dapat menghapus admin.');
        if ($user->role === 'dosen')   return back()->with('error', 'Tidak dapat menghapus dosen.');

        // Hapus semua file Drive milik user berdasarkan file_id
        Dokumens::where('id_user', $user->id_user)
            ->whereNotNull('file_id')
            ->chunkById(200, function ($docs) {
                foreach ($docs as $doc) {
                    try {
                        $this->googleDriveService->deleteById($doc->file_id);
                    } catch (\Throwable $e) {
                    }
                }
            });

        $user->delete();

        return redirect()->route('admin.karyawan')->with('success', 'Data berhasil dihapus');
    }

    public function deletePendidikan(Request $request, string $id, string $idPendidikan)
    {
        $pendidikan = Pendidikans::where('id_pendidikan', $idPendidikan)
            ->with(['dokumenIjazah', 'dokumenTranskipNilai'])
            ->first();

        if ($pendidikan) {
            if ($pendidikan->dokumenIjazah?->file_id) {
                try {
                    $this->googleDriveService->deleteById($pendidikan->dokumenIjazah->file_id);
                } catch (\Throwable $e) {
                }
            }
            if ($pendidikan->dokumenTranskipNilai?->file_id) {
                try {
                    $this->googleDriveService->deleteById($pendidikan->dokumenTranskipNilai->file_id);
                } catch (\Throwable $e) {
                }
            }
            $pendidikan->delete();
            return redirect()->route('admin.karyawan.show', $id)->with('success', 'Data pendidikan berhasil dihapus.');
        }
        return redirect()->route('admin.karyawan.show', $id)->with('error', 'Data pendidikan tidak ditemukan.');
    }

    public function password(string $id)
    {
        $data = [
            'page'     => 'Karyawan',
            'selected' => 'Karyawan',
            'title'    => 'Ubah Password Karyawan',
            'karyawan' => User::where('id_user', $id)->with(['dataDiri'])->first()
        ];
        return view('admin.pegawai.karyawan.password', $data);
    }

    public function passwordUpdate(Request $request, string $id)
    {
        $request->validate([
            'password'              => 'required|string|min:6',
            'password_confirmation' => 'same:password',
        ], [
            'password.required'     => 'Password harus di isi',
            'password.min'          => 'Password minimal 6 karakter',
            'password_confirmation.same' => 'Konfirmasi Password tidak sama',
        ]);

        $user = User::findOrFail($id);
        $user->update(['password' => Hash::make($request->password)]);

        return redirect()->route('admin.karyawan.show', $id)->with('success', 'Password berhasil diperbarui');
    }

    public function npp(string $id)
    {
        $data = [
            'page'     => 'Karyawan',
            'selected' => 'Karyawan',
            'title'    => 'Ubah NPP Karyawan',
            'karyawan' => User::where('id_user', $id)->with(['dataDiri'])->first()
        ];
        return view('admin.pegawai.karyawan.npp', $data);
    }

    public function nppUpdate(Request $request, $id)
    {
        $request->validate([
            'npp' => 'required|string|max:30|unique:users,npp,' . $id . ',id_user',
        ]);

        $user   = User::findOrFail($id);
        $oldNpp = $user->npp;
        $newNpp = $request->npp;

        // update user
        $user->npp = $newNpp;
        $user->save();

        // Pindahkan file di Google Drive berdasarkan file_id ke struktur folder baru
        Dokumens::where('id_user', $id)
            ->where('path_file', 'like', $oldNpp . '/%')
            ->chunkById(100, function ($docs) use ($oldNpp, $newNpp) {
                foreach ($docs as $doc) {
                    if (!$doc->file_id) continue;

                    $relative = preg_replace('#^' . preg_quote($oldNpp, '#') . '/#', '', $doc->path_file);
                    $parts    = array_values(array_filter(explode('/', $relative)));
                    if (empty($parts)) continue;

                    $filename        = array_pop($parts);
                    $folderStructure = array_merge([$newNpp], $parts);

                    try {
                        $this->googleDriveService->moveFileTo($doc->file_id, $folderStructure, $filename);
                    } catch (\Throwable $e) {
                    }
                }
            });

        // Update path_file di DB (logika kamu dipertahankan)
        Dokumens::where('id_user', $id)
            ->where('path_file', 'like', $oldNpp . '/%')
            ->chunkById(50, function ($dokumens) use ($oldNpp, $newNpp) {
                foreach ($dokumens as $dokumen) {
                    $dokumen->path_file = preg_replace(
                        '#^' . preg_quote($oldNpp, '#') . '/#',
                        $newNpp . '/',
                        $dokumen->path_file
                    );
                    $dokumen->save();
                }
            });

        return redirect()->route('admin.karyawan.show', $id)->with('success', 'NPP dan path file dokumen berhasil diperbarui.');
    }

    public function status(string $id)
    {
        $user = User::findOrFail($id);
        $status = $user->status_keaktifan === 'aktif' ? 'nonaktif' : 'aktif';
        $user->update(['status_keaktifan' => $status]);

        return redirect()->route('admin.karyawan.show', $id)->with('success', 'Status berhasil diperbarui');
    }
}
