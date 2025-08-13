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


class PengajuanAkun extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $googleDriveService;

    public function __construct(GoogleDriveService $googleDriveService)
    {
        $this->googleDriveService = $googleDriveService;
    }



    public function index()
    {
        $pengajuans = Registers::where('status', 'pending')->orderBy('created_at', 'desc')->paginate(10)->appends(request()->except('page', 'riwayat_page'));
        $riwayats = Registers::where('status', '!=', 'pending')->orderBy('updated_at', 'desc')
            ->paginate(10, ['*'], 'riwayat_page')
            // jangan bawa-bawa page default saat pindah halaman riwayat
            ->appends(request()->except('page'))
            // opsional: auto-scroll ke section riwayat
            ->fragment('riwayat');
        $data = [
            'selected' => 'Pengajuan',
            'page' => 'Pengajuan Akun',
            'title' => 'Pengajuan Akun',
            'pengajuans' => $pengajuans,
            'riwayats' =>    $riwayats
        ];

        return view('admin.pengajuan.akun.index', $data);
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
            'selected' => 'Pengajuan',
            'page' => 'Pengajuan Akun',
            'title' => 'Pengajuan Akun',
            'pengajuan' => Registers::where('id_register', $id)->with(['registerPendidikan.jenjang'])->first(),
        ];
        // dd($data);

        return view('admin.pengajuan.akun.show', $data);
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
    public function setuju(string $id)
    {
        $register = Registers::findOrFail($id);

        // Path file utama
        $localPath = storage_path("app/private/register/{$register->foto}");
        if (!file_exists($localPath)) {
            return response()->json([
                'success' => false,
                'message' => 'File tidak ditemukan.'
            ], 404);
        }

        $destinationPath = "{$register->npp}/datadiri/{$register->foto}";

        // Upload ke Google Drive
        $result = $this->googleDriveService->uploadFileAndGetUrl($localPath, $destinationPath);

        // Buat user
        $user = User::create([
            'npp' => $register->npp,
            'email' => $register->email,
            'password' => $register->password,
            'status_keaktifan' => 'aktif',
            'role' => $register->role,
        ]);

        // Ambil nomor dokumen terakhir
        $lastDokumen = Dokumens::orderBy('nomor_dokumen', 'desc')->first();
        $lastNumber = $lastDokumen ? (int) $lastDokumen->nomor_dokumen : 0;

        // Dokumen foto utama
        $lastNumber++;
        $newId = str_pad($lastNumber, 7, '0', STR_PAD_LEFT);
        $dokumenUser = Dokumens::create([
            'nomor_dokumen' => $newId,
            'path_file' => $destinationPath,
            'file_id' => $result['file_id'],
            'view_url' => $result['view_url'],
            'download_url' => $result['download_url'],
            'preview_url' => $result['preview_url'],
            'id_user' => $user->id_user,
            'tanggal_upload' => now()
        ]);

        // Data diri
        DataDiri::create([
            'id_user' => $user->id_user,
            'nuptk' => $register->nuptk ?? null,
            'nip' => $register->nip ?? null,
            'nidk' => $register->nidk ?? null,
            'nidn' => $register->nidn ?? null,
            'name' => $register->name,
            'no_ktp' => $register->no_ktp,
            'no_hp' => $register->no_hp,
            'tanggal_lahir' => $register->tanggal_lahir,
            'tempat_lahir' => $register->tempat_lahir,
            'jenis_kelamin' => $register->jenis_kelamin,
            'agama' => $register->agama,
            'tanggal_bergabung' => $register->tanggal_bergabung,
            'alamat' => $register->alamat,
            'rt' => $register->rt,
            'rw' => $register->rw,
            'desa' => $register->desa,
            'kecamatan' => $register->kecamatan,
            'kabupaten' => $register->kabupaten,
            'provinsi' => $register->provinsi,
            'foto' => $newId,
        ]);


        $newIdIjazah = null;
        $newIdT = null;
        // Dokumen pendidikan
        foreach ($register->registerPendidikan as $pendidikan) {


            // ==== Upload Ijazah ====
            $lastNumber++;
            $newIdIjazah = str_pad($lastNumber, 7, '0', STR_PAD_LEFT);

            $ijazahPath = "{$register->npp}/pendidikan/{$pendidikan->jenjang->nama_jenjang}/{$pendidikan->ijazah}";
            $ijazahLocalPath = storage_path("app/private/pendidikan/ijazah/{$pendidikan->ijazah}");

            if (file_exists($ijazahLocalPath)) {
                $ijazahResult = $this->googleDriveService->uploadFileAndGetUrl($ijazahLocalPath, $ijazahPath);

                Dokumens::create([
                    'nomor_dokumen' => $newIdIjazah,
                    'path_file' => $ijazahPath,
                    'file_id' => $ijazahResult['file_id'],
                    'view_url' => $ijazahResult['view_url'],
                    'download_url' => $ijazahResult['download_url'],
                    'preview_url' => $ijazahResult['preview_url'],
                    'id_user' => $user->id_user,
                    'tanggal_upload' => now()
                ]);
            }

            // ==== Upload Transkrip Nilai (jika ada) ====
            if (!empty($pendidikan->transkip_nilai)) {
                $lastNumber++;
                $newIdT = str_pad($lastNumber, 7, '0', STR_PAD_LEFT);

                $transkipPath = "{$register->npp}/pendidikan/{$pendidikan->jenjang->nama_jenjang}/{$pendidikan->transkip_nilai}";
                $transkipLocalPath = storage_path("app/private/pendidikan/transkipNilai/{$pendidikan->transkip_nilai}");

                if (file_exists($transkipLocalPath)) {
                    $transkipResult = $this->googleDriveService->uploadFileAndGetUrl($transkipLocalPath, $transkipPath);

                    Dokumens::create([
                        'nomor_dokumen' => $newIdT,
                        'path_file' => $transkipPath,
                        'file_id' => $transkipResult['file_id'],
                        'view_url' => $transkipResult['view_url'],
                        'download_url' => $transkipResult['download_url'],
                        'preview_url' => $transkipResult['preview_url'],
                        'id_user' => $user->id_user,
                        'tanggal_upload' => now()
                    ]);
                }
                Pendidikans::create([
                    'id_jenjang' => $pendidikan->id_jenjang,
                    'institusi' => $pendidikan->institusi,
                    'tahun_lulus' => $pendidikan->tahun_lulus,
                    'ijazah' => $newIdIjazah,
                    'transkip_nilai' =>  $newIdT,
                    'gelar' => $pendidikan->gelar,
                    'program_studi' => $pendidikan->program_studi,

                ]);
            }

            Pendidikans::create([
                'id_jenjang' => $pendidikan->id_jenjang,
                'institusi' => $pendidikan->institusi,
                'tahun_lulus' => $pendidikan->tahun_lulus,
                'ijazah' => $newIdIjazah,
                'transkip_nilai' =>  null,
                'gelar' => $pendidikan->gelar,
                'program_studi' => $pendidikan->program_studi,

            ]);
        }

        // Hapus foto utama setelah upload sukses
        if (file_exists($localPath)) {
            unlink($localPath);
        }

        foreach ($register->registerPendidikan as $pendidikan) {
            // Hapus ijazah
            $ijazahPath = storage_path("app/private/pendidikan/ijazah/{$pendidikan->ijazah}");
            if (file_exists($ijazahPath)) {
                unlink($ijazahPath);
            }

            // Hapus transkrip nilai (jika ada)
            if (!empty($pendidikan->transkip_nilai)) {
                $transkipPath = storage_path("app/private/pendidikan/transkipNilai/{$pendidikan->transkip_nilai}");
                if (file_exists($transkipPath)) {
                    unlink($transkipPath);
                }
            }
        }


        $register->update([
            'status' => 'disetujui'
        ]);

        return redirect()->route('admin.pengajuan.akun')->with('success', 'Pengajuan akun disetujui.');
    }
    public function tolak(string $id)
    {
        // Ambil data user
        $register = Registers::findOrFail($id);

        // Hapus file foto
        if ($register->foto && Storage::exists('register/' . $register->foto)) {
            Storage::delete('register/' . $register->foto);
        }

        // Ambil data pendidikan user
        $pendidikans = RegisterPendidikans::where('id_register', $register->id_register)->get();

        foreach ($pendidikans as $pendidikan) {
            // Hapus ijazah
            if ($pendidikan->ijazah && Storage::exists('pendidikan/ijazah/' . $pendidikan->ijazah)) {
                Storage::delete('pendidikan/ijazah/' . $pendidikan->ijazah);
            }

            // Hapus transkip nilai jika ada
            if ($pendidikan->transkip_nilai && Storage::exists('pendidikan/transkipNilai/' . $pendidikan->transkip_nilai)) {
                Storage::delete('pendidikan/transkipNilai/' . $pendidikan->transkip_nilai);
            }
        }

        // Update status menjadi 'ditolak'
        $register->status = 'ditolak';
        $register->save();

        return redirect()->route('admin.pengajuan.akun')->with('success', 'Pengajuan akun ditolak.');
    }
}
