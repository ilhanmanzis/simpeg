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
use Illuminate\Support\Facades\Storage;

class PegawaiDosen extends Controller
{
    protected $googleDriveService;

    public function __construct(GoogleDriveService $googleDriveService)
    {
        $this->googleDriveService = $googleDriveService;
    }

    public function index(Request $request)
    {
        $keyword = $request->get('dosen');
        $data = [
            'page'     => 'Dosen',
            'selected' => 'Dosen',
            'title'    => 'Data Dosen',
            'dosens'   => User::where('role', 'dosen')->with(['dataDiri'])
                ->when($keyword, function ($query) use ($keyword) {
                    $query->searchDosen($keyword);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10)
                ->withQueryString()
        ];
        return view('admin.pegawai.dosen.index', $data);
    }

    public function create()
    {
        $data = [
            'page'     => 'Dosen',
            'selected' => 'Dosen',
            'title'    => 'Tambah Data Dosen',
            'jenjangs' => Jenjangs::all(),
        ];

        return view('admin.pegawai.dosen.create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'password'               => 'required|string|min:6',
            'password_confirmation'  => 'same:password',
            'name'                   => 'required|string|max:255',
            'email'                  => 'required|email|max:255|unique:users,email',
            'nik'                    => 'required|max:20',
            'no_hp'                  => 'required|max:20|unique:data_diri,no_ktp',
            'tanggal_lahir'          => 'required|date',
            'tempat_lahir'           => 'required|string|max:255',
            'agama'                  => 'required|string|max:50',
            'desa'                   => 'required|string|max:255',
            'rt'                     => 'required|max:3',
            'rw'                     => 'required|max:3',
            'jenis_kelamin'          => 'required|in:Laki-Laki,Perempuan',
            'kecamatan'              => 'required|string|max:255',
            'kabupaten'              => 'required|string|max:255',
            'provinsi'               => 'required|string|max:255',
            'alamat'                 => 'required|string',
            'npp'                    => 'required|max:30|unique:users,npp',
            'nuptk'                  => 'required|max:30|unique:data_diri,nuptk',
            'nip'                    => 'nullable|max:30|unique:data_diri,nip',
            'nidk'                   => 'nullable|max:30|unique:data_diri,nidk',
            'nidn'                   => 'nullable|max:30|unique:data_diri,nidn',
            'tersertifikasi'         => 'required',
            'tanggal_bergabung'      => 'required|date',
            'pendidikan'                             => 'required|array|min:1',
            'pendidikan.*.jenjang'                   => 'required|integer|max:255',
            'pendidikan.*.tahun_lulus'               => 'required|max:5',
            'pendidikan.*.program_studi'             => 'required|string|max:255',
            'pendidikan.*.gelar'                     => 'nullable|string|max:255',
            'pendidikan.*.institusi'                 => 'nullable|string|max:255',
            'pendidikan.*.ijazah'                    => 'required|file|mimes:pdf|max:2048',
            'pendidikan.*.transkip_nilai'            => 'nullable|file|mimes:pdf|max:2048',
            'foto'                                   => 'required|image|max:2048',
            'serdos'                                 => 'nullable|file|mimes:pdf|max:2048',

            'golongan_darah' => 'required|in:A,B,AB,O,-',
            'bpjs'           => 'nullable',
            'anak'    => 'required|integer|min:0',
            'istri'   => 'required_if:jenis_kelamin,Laki-Laki|nullable|integer|min:0',
        ], [
            'password.required'      => 'Password harus di isi',
            'password.min'           => 'Password minimal 6 karakter',
            'password_confirmation.same' => 'Konfirmasi Password tidak sama',
        ]);

        // Ambil nomor dokumen terakhir
        $lastDokumen = Dokumens::orderBy('nomor_dokumen', 'desc')->first();
        $lastNumber  = $lastDokumen ? (int) $lastDokumen->nomor_dokumen : 0;

        // Dokumen foto utama
        $lastNumber++;
        $newId = str_pad($lastNumber, 7, '0', STR_PAD_LEFT);

        $originalName    = $request->file('foto')->getClientOriginalName();
        $timestampedName = time() . '_' . $originalName;
        $destinationPath = "{$request->npp}/datadiri/{$timestampedName}";

        // Upload ke Google Drive
        $result = $this->googleDriveService->uploadFileAndGetUrl(
            $request->file('foto')->getPathname(),
            $destinationPath
        );

        // tambah user
        $user = User::create([
            'email'            => $request->email,
            'npp'              => $request->npp,
            'password'         => Hash::make($request->password),
            'status_keaktifan' => 'aktif',
            'role'             => 'dosen'
        ]);

        Dokumens::create([
            'nomor_dokumen'  => $newId,
            'path_file'      => $destinationPath,
            'file_id'        => $result['file_id'],
            'view_url'       => $result['view_url'],
            'download_url'   => $result['download_url'],
            'preview_url'    => $result['preview_url'],
            'id_user'        => $user->id_user,
            'tanggal_upload' => now()
        ]);

        if ($request->tersertifikasi === 'sudah') {

            $lastNumber++;
            $newIdSerdos = str_pad($lastNumber, 7, '0', STR_PAD_LEFT);

            $originalName    = $request->file('serdos')->getClientOriginalName();
            $timestampedName = time() . '_' . $originalName;
            $destinationSerdosPath = "{$request->npp}/datadiri/serdos/{$timestampedName}";

            // Upload ke Google Drive
            $resultSerdos = $this->googleDriveService->uploadFileAndGetUrl(
                $request->file('serdos')->getPathname(),
                $destinationSerdosPath
            );
            Dokumens::create([
                'nomor_dokumen'  => $newIdSerdos,
                'path_file'      => $destinationSerdosPath,
                'file_id'        => $resultSerdos['file_id'],
                'view_url'       => $resultSerdos['view_url'],
                'download_url'   => $resultSerdos['download_url'],
                'preview_url'    => $resultSerdos['preview_url'],
                'id_user'        => $user->id_user,
                'tanggal_upload' => now()
            ]);
        }

        DataDiri::create([
            'id_user'          => $user->id_user,
            'nuptk'            => $request->nuptk,
            'nip'              => $request->nip ?? null,
            'nidk'             => $request->nidk ?? null,
            'nidn'             => $request->nidn ?? null,
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
            'tersertifikasi'   => $request->tersertifikasi,
            'serdos'             => $newIdSerdos ?? null,
            'bpjs'         => $request->bpjs ?? null,
            'istri'         => $request->istri ?? 0,
            'anak'         => $request->anak ?? 0,
            'golongan_darah'         => $request->golongan_darah,
        ]);

        $newIdIjazah = null;
        $newIdT      = null;

        // Dokumen pendidikan
        foreach ($request->pendidikan as $index => $pendidikanData) {
            $jenjang = Jenjangs::find($pendidikanData['jenjang']);
            if (!$jenjang) {
                continue;
            }
            $namaJenjang = $jenjang->nama_jenjang;

            $newIdIjazah = null;
            $newIdT      = null;

            // Upload Ijazah
            if ($request->hasFile("pendidikan.$index.ijazah")) {
                $lastNumber++;
                $newIdIjazah    = str_pad($lastNumber, 7, '0', STR_PAD_LEFT);
                $originalName   = $request->file("pendidikan.$index.ijazah")->getClientOriginalName();
                $timestampedName = time() . '_' . $originalName;
                $ijazahPath     = "{$user->npp}/pendidikan/{$namaJenjang}/{$timestampedName}";

                $ijazahResult = $this->googleDriveService->uploadFileAndGetUrl(
                    $request->file("pendidikan.$index.ijazah")->getPathname(),
                    $ijazahPath
                );

                Dokumens::create([
                    'nomor_dokumen'  => $newIdIjazah,
                    'path_file'      => $ijazahPath,
                    'file_id'        => $ijazahResult['file_id'],
                    'view_url'       => $ijazahResult['view_url'],
                    'download_url'   => $ijazahResult['download_url'],
                    'preview_url'    => $ijazahResult['preview_url'],
                    'id_user'        => $user->id_user,
                    'tanggal_upload' => now()
                ]);
            }

            // Upload Transkrip Nilai
            if ($request->hasFile("pendidikan.$index.transkip_nilai")) {
                $lastNumber++;
                $newIdT           = str_pad($lastNumber, 7, '0', STR_PAD_LEFT);
                $originalNameT    = $request->file("pendidikan.$index.transkip_nilai")->getClientOriginalName();
                $timestampedNameT = time() . '_' . $originalNameT;
                $transkipPath     = "{$user->npp}/pendidikan/{$namaJenjang}/{$timestampedNameT}";

                $transkipResult = $this->googleDriveService->uploadFileAndGetUrl(
                    $request->file("pendidikan.$index.transkip_nilai")->getPathname(),
                    $transkipPath
                );

                Dokumens::create([
                    'nomor_dokumen'  => $newIdT,
                    'path_file'      => $transkipPath,
                    'file_id'        => $transkipResult['file_id'],
                    'view_url'       => $transkipResult['view_url'],
                    'download_url'   => $transkipResult['download_url'],
                    'preview_url'    => $transkipResult['preview_url'],
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

        return redirect()->route('admin.dosen')->with('success', 'Data dosen berhasil ditambahkan');
    }

    public function createPendidikan(string $id)
    {
        $data = [
            'page'     => 'Dosen',
            'selected' => 'Dosen',
            'title'    => 'Tambah Pendidikan Dosen',
            'dosen'    => User::where('id_user', $id)->with(['dataDiri'])->first(),
            'jenjangs' => Jenjangs::all()
        ];
        return view('admin.pegawai.dosen.tambahpendidikan', $data);
    }

    public function storePendidikan(Request $request, string $id)
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

        $lastDokumen = Dokumens::orderBy('nomor_dokumen', 'desc')->first();
        $lastNumber  = $lastDokumen ? (int) $lastDokumen->nomor_dokumen : 0;

        $user    = User::findOrFail($id);
        $jenjang = Jenjangs::findOrFail($request->input('jenjang'));

        $newIdIjazah   = null;
        $newIdTranskip = null;

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

        return redirect()->route('admin.dosen.show', $id)->with('success', 'Pendidikan berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        $data = [
            'page'     => 'Dosen',
            'selected' => 'Dosen',
            'title'    => 'Data Dosen',
            'dosen'    => User::where('id_user', $id)
                ->with([
                    'dataDiri.dokumen',
                    'dataDiri.serdosen',
                    'pendidikan' => function ($q) {
                        $q->orderBy('id_jenjang', 'asc'); // <-- urut dari kecil ke besar
                    },
                    'pendidikan.dokumenIjazah',
                    'pendidikan.dokumenTranskipNilai'
                ])
                ->first()
        ];

        // dd($data);
        return view('admin.pegawai.dosen.show', $data);
    }


    public function password(string $id)
    {
        $data = [
            'page'     => 'Dosen',
            'selected' => 'Dosen',
            'title'    => 'Ubah Password Dosen',
            'dosen'    => User::where('id_user', $id)->with(['dataDiri'])->first()
        ];
        return view('admin.pegawai.dosen.password', $data);
    }

    public function npp(string $id)
    {
        $data = [
            'page'     => 'Dosen',
            'selected' => 'Dosen',
            'title'    => 'Ubah NPP Dosen',
            'dosen'    => User::where('id_user', $id)->with(['dataDiri'])->first()
        ];
        return view('admin.pegawai.dosen.npp', $data);
    }

    public function nppUpdate(Request $request, $id)
    {
        $request->validate([
            'npp' => 'required|string|max:30|unique:users,npp,' . $id . ',id_user',
        ]);

        $user   = User::findOrFail($id);
        $oldNpp = $user->npp;
        $newNpp = $request->npp;

        // Update NPP user
        $user->npp = $newNpp;
        $user->save();

        // Pindahkan file-file di Google Drive ke folder NPP baru (berdasarkan file_id)
        Dokumens::where('id_user', $id)
            ->where('path_file', 'like', $oldNpp . '/%')
            ->chunkById(100, function ($docs) use ($oldNpp, $newNpp) {
                foreach ($docs as $doc) {
                    if (!$doc->file_id) continue;

                    // path lama: oldNpp/xxx/yyy/filename.ext
                    $relative = preg_replace('#^' . preg_quote($oldNpp, '#') . '/#', '', $doc->path_file);
                    $parts    = array_values(array_filter(explode('/', $relative)));
                    if (empty($parts)) continue;

                    $filename = array_pop($parts);
                    $folderStructure = array_merge([$newNpp], $parts); // ex: [$newNpp, 'pendidikan', 'S1']

                    try {
                        $this->googleDriveService->moveFileTo($doc->file_id, $folderStructure, $filename);
                    } catch (\Throwable $e) {
                        // abaikan agar alur tetap sama
                    }
                }
            });

        // Update semua dokumen path_file di DB (logika kamu dipertahankan)
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

        return redirect()->route('admin.dosen.show', $id)->with('success', 'NPP dan path file dokumen berhasil diperbarui.');
    }

    public function dataDiri(string $id)
    {
        $data = [
            'page'     => 'Dosen',
            'selected' => 'Dosen',
            'title'    => 'Edit Profile Pribadi Dosen',
            'dosen'    => User::where('id_user', $id)->with(['dataDiri.dokumen'])->first()
        ];
        return view('admin.pegawai.dosen.profile', $data);
    }

    public function pendidikan(string $id, string $idPendidikan)
    {
        $data = [
            'page'        => 'Dosen',
            'selected'    => 'Dosen',
            'title'       => 'Edit Pendidikan Dosen',
            'dosen'       => User::where('id_user', $id)->with(['dataDiri'])->first(),
            'pendidikan'  => Pendidikans::where('id_pendidikan', $idPendidikan)->with(['dokumenIjazah', 'dokumenTranskipNilai'])->first(),
            'jenjangs'    => Jenjangs::all()
        ];
        return view('admin.pegawai.dosen.pendidikan', $data);
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

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('admin.dosen.show', $id)->with('success', 'Password berhasil diperbarui');
    }



    public function dataDiriUpdate(Request $request, string $id)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|max:255|unique:users,email,' . $id . ',id_user',
            'nik'              => 'required|max:20',
            'no_hp'            => 'required|max:20',
            'tanggal_lahir'    => 'required|date',
            'tempat_lahir'     => 'required|string|max:255',
            'agama'            => 'required|string|max:50',
            'desa'             => 'required|string|max:255',
            'rt'               => 'required|max:3',
            'rw'               => 'required|max:3',
            'jenis_kelamin'    => 'required|in:Laki-Laki,Perempuan',
            'kecamatan'        => 'required|string|max:255',
            'kabupaten'        => 'required|string|max:255',
            'provinsi'         => 'required|string|max:255',
            'alamat'           => 'required|string',
            'nuptk'            => 'required|max:30',
            'nip'              => 'nullable|max:30',
            'nidk'             => 'nullable|max:30',
            'nidn'             => 'nullable|max:30',
            'tanggal_bergabung' => 'required|date',
            'foto'             => 'nullable|image|max:2048',
            'golongan_darah' => 'required|in:A,B,AB,O,-',
            'bpjs'           => 'nullable',
            'anak'    => 'required|integer|min:0',
            'istri'   => 'required_if:jenis_kelamin,Laki-Laki|nullable|integer|min:0',
        ]);



        $user     = User::findOrFail($id);
        $dataDiri = DataDiri::where('id_user', $id)->with(['dokumen'])->first();

        $user->update([
            'email' => $request->input('email'),
        ]);

        // simpan file_id lama sebelum diupdate
        $oldFileId = $dataDiri->dokumen->file_id ?? null;

        $fileLamaPath = $dataDiri->dokumen->path_file ?? null;
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
            'bpjs'              => $request->input('bpjs') ?? null,
            'istri'             => $request->input('istri') ?? 0,
            'anak'              => $request->input('anak') ?? 0,
            'golongan_darah'              => $request->input('golongan_darah'),
        ]);

        // Hapus file lama di Drive kalau ada file baru & punya file_id lama
        if ($oldFileId && $request->hasFile('foto')) {
            try {
                $this->googleDriveService->deleteById($oldFileId);
            } catch (\Throwable $e) {
                // diamkan agar alur tetap sama
            }
        }

        // 👉 Set flag agar halaman tujuan (200 OK) kirim Clear-Site-Data
        //session()->put('clear_site_data_once', true);

        return redirect()
            ->route('admin.dosen.show', $id)
            ->withHeaders([
                // Hapus HTTP cache, Cache Storage, dan SW
                'Clear-Site-Data' => '"cache", "storage", "executionContexts"',
            ])
            ->with('success', 'Data Diri Dosen berhasil diperbarui.');
    }

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

            $fileLamaIjazahPath   = $pendidikan->dokumenIjazah->path_file ?? null;
            $fileLamaTranskipPath = $pendidikan->dokumenTranskipNilai->path_file ?? null;

            $oldIjazahId   = $pendidikan->dokumenIjazah->file_id ?? null;
            $oldTranskipId = $pendidikan->dokumenTranskipNilai->file_id ?? null;

            $folderChanged = $oldJenjangName !== $newJenjangName;

            // Jika folder berubah & TIDAK upload baru -> pindahkan file lama (move by file_id)
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

            // Replace ijazah (jika diupload)
            if ($request->hasFile('ijazah')) {
                $originalName      = $request->file('ijazah')->getClientOriginalName();
                $timestampedName   = time() . '_' . $originalName;
                $destinationIjazah = "{$newFolder}/{$timestampedName}";
                $result            = $this->googleDriveService->uploadFileAndGetUrl($request->file('ijazah')->getPathname(), $destinationIjazah);

                if ($result) {
                    $pendidikan->dokumenIjazah()
                        ->updateOrCreate(
                            [], // mengikuti logika relasi yang ada
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

            // Replace transkip nilai (jika diupload)
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
                        $last = Dokumens::orderBy('nomor_dokumen', 'desc')->first();
                        $num  = $last ? ((int)$last->nomor_dokumen + 1) : 1;
                        $newIdDoc = str_pad($num, 7, '0', STR_PAD_LEFT);

                        $pendidikan->dokumenTranskipNilai()->create([
                            'nomor_dokumen'  => $newIdDoc,
                            'id_user'        => $user->id_user ?? null,
                            'path_file'      => $destinationTranskip,
                            'file_id'        => $result['file_id'],
                            'view_url'       => $result['view_url'],
                            'download_url'   => $result['download_url'],
                            'preview_url'    => $result['preview_url'],
                            'tanggal_upload' => now(),
                        ]);
                        $pendidikan->update(['transkip_nilai' => $newIdDoc]);
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

            return redirect()->route('admin.dosen.show', $id)
                ->with('success', 'Pendidikan Dosen berhasil diperbarui.');
        });
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
            return redirect()->route('admin.dosen.show', $id)->with('success', 'Data pendidikan berhasil dihapus.');
        }
        return redirect()->route('admin.dosen.show', $id)->with('error', 'Data pendidikan tidak ditemukan.');
    }

    public function status(string $id)
    {
        $user = User::findOrFail($id);

        $status = $user->status_keaktifan === 'aktif' ? 'nonaktif' : 'aktif';
        $user->update([
            'status_keaktifan' => $status
        ]);

        return redirect()->route('admin.dosen.show', $id)->with('success', 'Status berhasil diperbarui');
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        // Jangan izinkan menghapus user admin/karyawan
        if ($user->role === 'admin') {
            return back()->with('error', 'Tidak dapat menghapus admin.');
        }
        if ($user->role === 'karyawan') {
            return back()->with('error', 'Tidak dapat menghapus karyawan.');
        }

        // Hapus semua file drive milik user berdasarkan file_id (pengganti deleteDir by path)
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

        // (Logika lain tetap sama)
        $user->delete();

        return redirect()->route('admin.dosen')->with('success', 'Data berhasil dihapus');
    }

    public function serdos(string $id)
    {
        $data = [
            'page'     => 'Dosen',
            'selected' => 'Dosen',
            'title'    => 'Ubah Serdos Dosen',
            'dosen'    => User::where('id_user', $id)->with(['dataDiri.serdosen'])->first()
        ];
        return view('admin.pegawai.dosen.serdos', $data);
    }

    public function serdosUpdate(Request $request, $id)
    {
        $request->validate([
            'tersertifikasi'         => 'required',
            'serdos'                 => 'nullable|file|mimes:pdf|max:2048',
        ]);

        $user   = User::where('id_user', $id)->with('dataDiri.serdosen')->first();
        $oldFileId = $user->dataDiri->serdosen->file_id ?? null;

        $tersertifikasi = $request->tersertifikasi;

        if ($request->tersertifikasi === 'sudah') {

            if ($user->dataDiri->serdos !== null && $user->dataDiri->serdos !== '') {
                // simpan file_id lama sebelum diupdate

                $originalName    = $request->file('serdos')->getClientOriginalName();
                $timestampedName = time() . '_' . $originalName;
                $destinationSerdosPath = "{$user->npp}/datadiri/serdos/{$timestampedName}";

                // Upload ke Google Drive
                $resultSerdos = $this->googleDriveService->uploadFileAndGetUrl(
                    $request->file('serdos')->getPathname(),
                    $destinationSerdosPath
                );

                $user->dataDiri->serdosen()->update([
                    'path_file'      => $destinationSerdosPath,
                    'file_id'        => $resultSerdos['file_id'],
                    'view_url'       => $resultSerdos['view_url'],
                    'download_url'   => $resultSerdos['download_url'],
                    'preview_url'    => $resultSerdos['preview_url'],
                    'tanggal_upload' => now()
                ]);

                // Hapus file lama di Drive kalau ada file baru & punya file_id lama
                if ($oldFileId && $request->hasFile('serdos')) {
                    try {
                        $this->googleDriveService->deleteById($oldFileId);
                    } catch (\Throwable $e) {
                        // diamkan agar alur tetap sama
                    }
                }
            } else {
                // Ambil nomor dokumen terakhir
                $lastDokumen = Dokumens::orderBy('nomor_dokumen', 'desc')->first();
                $lastNumber  = $lastDokumen ? (int) $lastDokumen->nomor_dokumen : 0;

                $lastNumber++;
                $newIdSerdos = str_pad($lastNumber, 7, '0', STR_PAD_LEFT);

                $originalName    = $request->file('serdos')->getClientOriginalName();
                $timestampedName = time() . '_' . $originalName;
                $destinationSerdosPath = "{$user->npp}/datadiri/serdos/{$timestampedName}";

                // Upload ke Google Drive
                $resultSerdos = $this->googleDriveService->uploadFileAndGetUrl(
                    $request->file('serdos')->getPathname(),
                    $destinationSerdosPath
                );
                Dokumens::create([
                    'nomor_dokumen'  => $newIdSerdos,
                    'path_file'      => $destinationSerdosPath,
                    'file_id'        => $resultSerdos['file_id'],
                    'view_url'       => $resultSerdos['view_url'],
                    'download_url'   => $resultSerdos['download_url'],
                    'preview_url'    => $resultSerdos['preview_url'],
                    'id_user'        => $user->id_user,
                    'tanggal_upload' => now()
                ]);

                $user->dataDiri->update([
                    'tersertifikasi' => $tersertifikasi,
                    'serdos'        => $newIdSerdos,
                ]);
            }
        } else {
            // Hapus file lama di Drive kalau ada file baru & punya file_id lama
            if ($oldFileId) {
                try {
                    $this->googleDriveService->deleteById($oldFileId);
                } catch (\Throwable $e) {
                    // diamkan agar alur tetap sama
                }
            }
            $user->dataDiri->update([
                'tersertifikasi' => $tersertifikasi,
                'serdos'        => null,
            ]);
        }
        return redirect()->route('admin.dosen.show', $id)->with('success', 'Sertifikat Dosen berhasil diperbarui.');
    }
}
