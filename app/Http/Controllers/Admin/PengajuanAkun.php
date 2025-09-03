<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataDiri;
use App\Models\Dokumens;
use App\Models\Pendidikans;
use App\Models\RegisterPendidikans;
use App\Models\Registers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\GoogleDriveService;
use Illuminate\Support\Facades\DB;

class PengajuanAkun extends Controller
{
    protected $googleDriveService;

    public function __construct(GoogleDriveService $googleDriveService)
    {
        $this->googleDriveService = $googleDriveService;
    }

    public function index()
    {
        $pengajuans = Registers::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends(request()->except('page', 'riwayat_page'));

        $riwayats = Registers::where('status', '!=', 'pending')
            ->orderBy('updated_at', 'desc')
            ->paginate(10, ['*'], 'riwayat_page')
            ->appends(request()->except('page'))
            ->fragment('riwayat');

        $data = [
            'selected'   => 'Pengajuan',
            'page'       => 'Pengajuan Akun',
            'title'      => 'Pengajuan Akun',
            'pengajuans' => $pengajuans,
            'riwayats'   => $riwayats
        ];

        return view('admin.pengajuan.akun.index', $data);
    }

    public function create()
    { /* kosong */
    }
    public function store(Request $request)
    { /* kosong */
    }
    public function edit(string $id)
    { /* kosong */
    }
    public function update(Request $request, string $id)
    { /* kosong */
    }
    public function destroy(string $id)
    { /* kosong */
    }

    public function show(string $id)
    {
        $data = [
            'selected'  => 'Pengajuan',
            'page'      => 'Pengajuan Akun',
            'title'     => 'Pengajuan Akun',
            'pengajuan' => Registers::where('id_register', $id)
                ->with(['registerPendidikan.jenjang'])
                ->first(),
        ];
        return view('admin.pengajuan.akun.show', $data);
    }

    public function setuju(string $id)
    {
        $register = Registers::with(['registerPendidikan.jenjang'])->findOrFail($id);

        // path foto lokal
        $localPath = storage_path("app/private/register/{$register->foto}");
        if (!file_exists($localPath)) {
            return response()->json(['success' => false, 'message' => 'File tidak ditemukan.'], 404);
        }

        return DB::transaction(function () use ($register, $localPath) {

            // upload foto ke Drive
            $destinationPath = "{$register->npp}/datadiri/{$register->foto}";
            $result = $this->googleDriveService->uploadFileAndGetUrl($localPath, $destinationPath);
            if (!$result || empty($result['file_id'])) {
                return back()->with('error', 'Gagal mengunggah foto ke Google Drive.');
            }

            // buat user
            $user = User::create([
                'npp'              => $register->npp,
                'email'            => $register->email,
                'password'         => $register->password,
                'status_keaktifan' => 'aktif',
                'role'             => $register->role,
            ]);

            // nomor_dokumen terakhir
            $lastDokumen = Dokumens::orderBy('nomor_dokumen', 'desc')->first();
            $lastNumber  = $lastDokumen ? (int) $lastDokumen->nomor_dokumen : 0;

            // dokumen foto utama
            $lastNumber++;
            $newId = str_pad($lastNumber, 7, '0', STR_PAD_LEFT);
            Dokumens::create([
                'nomor_dokumen'  => $newId,
                'path_file'      => $destinationPath,
                'file_id'        => $result['file_id'] ?? null,
                'view_url'       => $result['view_url'] ?? null,
                'download_url'   => $result['download_url'] ?? null,
                'preview_url'    => $result['preview_url'] ?? null,
                'id_user'        => $user->id_user,
                'tanggal_upload' => now()
            ]);

            //sertifikat dosen
            if ($register->tersertifikasi === 'sudah' && $register->serdos) {
                $lastNumber++;
                $newIdSerdos = str_pad($lastNumber, 7, '0', STR_PAD_LEFT);

                $serdosLocalPath = storage_path("app/private/register/{$register->serdos}");
                $serdosPath      = "{$register->npp}/datadiri/serdos/{$register->serdos}";
                if (file_exists($serdosLocalPath)) {
                    $serdosResult = $this->googleDriveService->uploadFileAndGetUrl($serdosLocalPath, $serdosPath);
                    if ($serdosResult && !empty($serdosResult['file_id'])) {
                        Dokumens::create([
                            'nomor_dokumen'  => $newIdSerdos,
                            'path_file'      => $serdosPath,
                            'file_id'        => $serdosResult['file_id'] ?? null,
                            'view_url'       => $serdosResult['view_url'] ?? null,
                            'download_url'   => $serdosResult['download_url'] ?? null,
                            'preview_url'    => $serdosResult['preview_url'] ?? null,
                            'id_user'        => $user->id_user,
                            'tanggal_upload' => now()
                        ]);
                    }
                }
            }
            // data diri
            DataDiri::create([
                'id_user'           => $user->id_user,
                'nuptk'             => $register->nuptk ?? null,
                'nip'               => $register->nip ?? null,
                'nidk'              => $register->nidk ?? null,
                'nidn'              => $register->nidn ?? null,
                'name'              => $register->name,
                'no_ktp'            => $register->no_ktp,
                'no_hp'             => $register->no_hp,
                'tanggal_lahir'     => $register->tanggal_lahir,
                'tempat_lahir'      => $register->tempat_lahir,
                'jenis_kelamin'     => $register->jenis_kelamin,
                'agama'             => $register->agama,
                'tanggal_bergabung' => $register->tanggal_bergabung,
                'alamat'            => $register->alamat,
                'rt'                => $register->rt,
                'rw'                => $register->rw,
                'desa'              => $register->desa,
                'kecamatan'         => $register->kecamatan,
                'kabupaten'         => $register->kabupaten,
                'provinsi'          => $register->provinsi,
                'foto'              => $newId,
                'tersertifikasi'    => $register->tersertifikasi,
                'serdos'            => $newIdSerdos ?? null
            ]);

            // dokumen pendidikan
            foreach (($register->registerPendidikan ?? []) as $pendidikan) {
                $jenjangName = $pendidikan->jenjang->nama_jenjang ?? 'Tanpa Jenjang';
                $jenjangName = str_replace('/', '-', $jenjangName);

                // ---- Ijazah (wajib di logikamu)
                $lastNumber++;
                $newIdIjazah = str_pad($lastNumber, 7, '0', STR_PAD_LEFT);

                $ijazahLocalPath = storage_path("app/private/pendidikan/ijazah/{$pendidikan->ijazah}");
                $ijazahPath      = "{$register->npp}/pendidikan/{$jenjangName}/{$pendidikan->ijazah}";
                if (file_exists($ijazahLocalPath)) {
                    $ijazahResult = $this->googleDriveService->uploadFileAndGetUrl($ijazahLocalPath, $ijazahPath);
                    if ($ijazahResult && !empty($ijazahResult['file_id'])) {
                        Dokumens::create([
                            'nomor_dokumen'  => $newIdIjazah,
                            'path_file'      => $ijazahPath,
                            'file_id'        => $ijazahResult['file_id'] ?? null,
                            'view_url'       => $ijazahResult['view_url'] ?? null,
                            'download_url'   => $ijazahResult['download_url'] ?? null,
                            'preview_url'    => $ijazahResult['preview_url'] ?? null,
                            'id_user'        => $user->id_user,
                            'tanggal_upload' => now()
                        ]);
                    }
                }

                // ---- Transkrip (opsional)
                $newIdT = null;
                if (!empty($pendidikan->transkip_nilai)) {
                    $lastNumber++;
                    $newIdT = str_pad($lastNumber, 7, '0', STR_PAD_LEFT);

                    $transkipLocalPath = storage_path("app/private/pendidikan/transkipNilai/{$pendidikan->transkip_nilai}");
                    $transkipPath      = "{$register->npp}/pendidikan/{$jenjangName}/{$pendidikan->transkip_nilai}";
                    if (file_exists($transkipLocalPath)) {
                        $transkipResult = $this->googleDriveService->uploadFileAndGetUrl($transkipLocalPath, $transkipPath);
                        if ($transkipResult && !empty($transkipResult['file_id'])) {
                            Dokumens::create([
                                'nomor_dokumen'  => $newIdT,
                                'path_file'      => $transkipPath,
                                'file_id'        => $transkipResult['file_id'] ?? null,
                                'view_url'       => $transkipResult['view_url'] ?? null,
                                'download_url'   => $transkipResult['download_url'] ?? null,
                                'preview_url'    => $transkipResult['preview_url'] ?? null,
                                'id_user'        => $user->id_user,
                                'tanggal_upload' => now()
                            ]);
                        }
                    }
                }

                // <-- fixed: create sekali (pakai $newIdT yang mungkin null)
                Pendidikans::create([
                    'id_jenjang'      => $pendidikan->id_jenjang,
                    'id_user'         => $user->id_user,
                    'institusi'       => $pendidikan->institusi,
                    'tahun_lulus'     => $pendidikan->tahun_lulus,
                    'ijazah'          => $newIdIjazah,
                    'transkip_nilai'  => $newIdT,   // null jika tidak ada transkrip
                    'gelar'           => $pendidikan->gelar,
                    'program_studi'   => $pendidikan->program_studi,
                ]);
            }

            // hapus file lokal setelah sukses
            if (file_exists($localPath)) {
                @unlink($localPath);
            }
            if (file_exists($register->role === 'dosen' && $serdosLocalPath)) {
                @unlink($serdosLocalPath);
            }
            foreach (($register->registerPendidikan ?? []) as $pendidikan) {
                $ijazahPathLocal = storage_path("app/private/pendidikan/ijazah/{$pendidikan->ijazah}");
                if (file_exists($ijazahPathLocal)) @unlink($ijazahPathLocal);

                if (!empty($pendidikan->transkip_nilai)) {
                    $transkipPathLocal = storage_path("app/private/pendidikan/transkipNilai/{$pendidikan->transkip_nilai}");
                    if (file_exists($transkipPathLocal)) @unlink($transkipPathLocal);
                }
            }

            $register->update(['status' => 'disetujui']);

            return redirect()->route('admin.pengajuan.akun')
                ->withHeaders([
                    // Hapus HTTP cache, Cache Storage, dan SW
                    'Clear-Site-Data' => '"cache", "storage", "executionContexts"',
                ])
                ->with('success', 'Pengajuan akun disetujui.');
        });
    }
    public function tolak(string $id)
    {
        $register = Registers::findOrFail($id);

        if ($register->foto && Storage::exists('register/' . $register->foto)) {
            Storage::delete('register/' . $register->foto);
        }
        if ($register->serdos && Storage::exists('register/' . $register->serdos)) {
            Storage::delete('register/' . $register->serdos);
        }

        $pendidikans = RegisterPendidikans::where('id_register', $register->id_register)->get();
        foreach ($pendidikans as $pendidikan) {
            if ($pendidikan->ijazah && Storage::exists('pendidikan/ijazah/' . $pendidikan->ijazah)) {
                Storage::delete('pendidikan/ijazah/' . $pendidikan->ijazah);
            }
            if ($pendidikan->transkip_nilai && Storage::exists('pendidikan/transkipNilai/' . $pendidikan->transkip_nilai)) {
                Storage::delete('pendidikan/transkipNilai/' . $pendidikan->transkip_nilai);
            }
        }

        $register->update(['status' => 'ditolak']);

        return redirect()->route('admin.pengajuan.akun')->with('success', 'Pengajuan akun ditolak.');
    }
}
