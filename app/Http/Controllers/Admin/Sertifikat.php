<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dokumens;
use App\Models\Sertifikats;
use App\Models\User;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;

class Sertifikat extends Controller
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
        $keyword = $request->get('pegawai');
        $data = [
            'page' => 'Sertifikat',
            'selected' => 'Sertifikat',
            'title' => 'Data Sertifikat',
            'dosens' => User::where('role', '!=', 'admin')->with([
                'dataDiri',
            ])->when($keyword, function ($query) use ($keyword) {
                $query->searchPegawai($keyword);
            })->orderBy('created_at', 'desc')->paginate(10)->withQueryString()
        ];

        return view('admin.sertifikat.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $id)
    {
        $user = User::where('id_user', $id)->with('dataDiri')->firstOrFail();
        $data = [
            'page' => 'Sertifikat',
            'selected' => 'Sertifikat',
            'title' => 'Tambah Sertifikat ' . $user->dataDiri->name,
            'user' => $user
        ];

        return view('admin.sertifikat.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'penyelenggara' => 'required|string|max:255',
            'tanggal_diperoleh' => 'required|date',
            'tanggal_selesai' => 'nullable|date',
            'dokumen' => 'required|file|mimes:pdf|max:2048',
        ]);

        $user = User::where('id_user', $id)->with('dataDiri')->firstOrFail();

        $generateNomor = function () {
            $last = Dokumens::lockForUpdate()->orderBy('nomor_dokumen', 'desc')->first();
            $num  = $last ? ((int)$last->nomor_dokumen + 1) : 1;
            return str_pad($num, 7, '0', STR_PAD_LEFT);
        };


        $newIdDokumen = $generateNomor();
        $dokumenFile = $request->file("dokumen");
        $dokumenName = time() . '_' . $dokumenFile->getClientOriginalName();

        $destPath = "{$user->npp}/sertifikat/{$dokumenName}";
        $result   = $this->googleDriveService->uploadFileAndGetUrl($dokumenFile, $destPath);

        if ($result) {
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
        }

        Sertifikats::create([
            'id_user'             => $user->id_user,
            'nama_sertifikat'     => $request->name,
            'kategori'            => $request->kategori,
            'penyelenggara'       => $request->penyelenggara,
            'tanggal_selesai'     => $request->tanggal_selesai,
            'tanggal_diperoleh'   => $request->tanggal_diperoleh,
            'dokumen'             => $newIdDokumen,
        ]);

        return redirect()->route('admin.sertifikat.all', $user->id_user)->with('success', 'Sertifikat berhasil ditambahkan.');
    }



    /**
     * Display the specified resource.
     */

    public function all(Request $request, string $id)
    {
        $judul = $request->get('judul');

        $user = User::where('id_user', $id)->with('dataDiri')->firstOrFail();
        $data = [
            'page' => 'Sertifikat',
            'selected' => 'Sertifikat',
            'title' => 'Sertifikat ' . $user->dataDiri->name,
            'sertifikats' => Sertifikats::where('id_user', $id)
                ->searchJudul($judul)
                ->orderBy('created_at', 'desc')
                ->paginate(10)
                ->withQueryString(),
            'user' => $user
        ];

        return view('admin.sertifikat.all', $data);
    }
    public function show(string $id)
    {
        $sertifikat = Sertifikats::where('id_sertifikat', $id)->with(['user.dataDiri', 'dokumenSertifikat'])->firstOrFail();


        $data = [
            'page' => 'Sertifikat',
            'selected' => 'Sertifikat',
            'title' => 'Data Sertifikat ' . $sertifikat->user->dataDiri->name,
            'sertifikat' => $sertifikat,
        ];
        return view('admin.sertifikat.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $sertifikat = Sertifikats::where('id_sertifikat', $id)->with(['dokumenSertifikat', 'user.dataDiri'])->firstOrFail();



        $data = [
            'page' => 'Sertifikat',
            'selected' => 'Sertifikat',
            'title' => 'Edit Sertifikat ' . $sertifikat->user->dataDiri->name,
            'sertifikat' => $sertifikat,

        ];
        return view('admin.sertifikat.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name'                       => 'required|string|max:255',
            'kategori'                   => 'nullable|string|max:255',
            'penyelenggara'              => 'required|string|max:255',
            'tanggal_diperoleh'          => 'required|date',
            'tanggal_selesai'            => 'nullable|date',
            'dokumen'                    => 'nullable|file|mimes:pdf|max:2048',
        ]);

        $sertifikat = Sertifikats::where('id_sertifikat', $id)->with('user.dataDiri')->firstOrFail();
        $user = $sertifikat->user;


        if ($request->hasFile('dokumen')) {
            $dokumenFile = $request->file('dokumen');
            $dokumenName = time() . '_' . $dokumenFile->getClientOriginalName();
            $destPath = "{$user->npp}/sertifikat/{$dokumenName}";
            $result = $this->googleDriveService->uploadFileAndGetUrl($dokumenFile, $destPath);

            if ($result) {
                $sertifikat->dokumenSertifikat()->update([
                    'path_file'     => $destPath,
                    'file_id'        => $result['file_id'] ?? null,
                    'view_url'       => $result['view_url'] ?? null,
                    'download_url'   => $result['download_url'] ?? null,
                    'preview_url'    => $result['preview_url'] ?? null,
                ]);
            }
        }

        $sertifikat->update([
            'nama_sertifikat'     => $request->name,
            'kategori'            => $request->kategori,
            'penyelenggara'       => $request->penyelenggara,
            'tanggal_selesai'     => $request->tanggal_selesai,
            'tanggal_diperoleh'   => $request->tanggal_diperoleh,
        ]);


        return redirect()->route('admin.sertifikat.all', $user->id_user)->with('success', 'Sertifikat berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $sertifikat = Sertifikats::where('id_sertifikat', $id)->with(['user', 'dokumenSertifikat'])->firstOrFail();
        $idUser = $sertifikat->id_user;

        $this->googleDriveService->deleteById($sertifikat->dokumenSertifikat->file_id);
        $sertifikat->delete();

        return redirect()->route('admin.sertifikat.all', ['id' => $idUser])->with('success', 'Data Sertifikat berhasil dihapus.');
    }
}
