<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dokumens;
use App\Models\Golongans;
use App\Models\GolonganUsers;
use App\Models\User;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use Yaza\LaravelGoogleDriveStorage\Gdrive;

class GolonganUser extends Controller
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
        $keyword = $request->get('dosen');
        $data = [
            'page' => 'Jabatan Golongan',
            'selected' => 'Jabatan Golongan',
            'title' => 'Data Golongan Dosen',
            'dosens' => User::where('role', 'dosen')->with([
                // Ambil histori golongan terbaru (urutan tanggal_mulai desc)
                'golongan' => function ($q) {
                    $q->orderByDesc('id_golongan_user')
                        ->with('golongan'); // untuk nama/kode golongan
                },
                // Jika nama ada di relasi dataDiri
                'dataDiri',
            ])->when($keyword, function ($query) use ($keyword) {
                $query->searchDosen($keyword);
            })->orderBy('created_at', 'desc')->paginate(10)->withQueryString()
        ];

        return view('admin.jabatan.golongan.index', $data);
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
            'page' => 'Jabatan Golongan',
            'selected' => 'Jabatan Golongan',
            'title' => 'Data Golongan Dosen',
            'user' => User::where('id_user', $id)->with('dataDiri')->first(),
            'dosen' => GolonganUsers::where('id_user', $id)->where('status', 'aktif')->with(['user.dataDiri', 'golongan', 'dokumen'])->orderBy('id_golongan_user', 'desc')->first(),

            'riwayats' => GolonganUsers::where('id_user', $id)->where('status', 'nonaktif')->with(['user', 'golongan', 'dokumen'])->orderBy('created_at', 'desc')->paginate(10)->withQueryString()
        ];

        return view('admin.jabatan.golongan.show', $data);
    }
    public function mutasi(string $id)
    {
        $data = [
            'page' => 'Jabatan Golongan',
            'selected' => 'Jabatan Golongan',
            'title' => 'Mutasi Golongan Dosen',
            'user' => User::where('id_user', $id)->with('dataDiri')->first(),
            'dosen' => GolonganUsers::where('id_user', $id)->where('status', 'aktif')->with(['golongan'])->orderBy('id_golongan_user', 'desc')->first(),
            'golongans' => Golongans::all()

        ];

        return view('admin.jabatan.golongan.mutasi', $data);
    }


    public function mutasiStore(Request $request, string $id)
    {
        $request->validate([
            'golongan' => 'required',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date',
            'sk' => 'required|file|mimes:pdf|max:2048'
        ]);

        $user = User::findOrFail($id);
        $golongan = Golongans::findOrFail($request->golongan);

        $golonganSebelumnya = GolonganUsers::where('id_user', $id)->where('status', 'aktif')->with(['golongan'])->orderBy('id_golongan_user', 'desc')->first();


        // Ambil nomor dokumen terakhir
        $lastDokumen = Dokumens::orderBy('nomor_dokumen', 'desc')->first();
        $lastNumber = $lastDokumen ? (int) $lastDokumen->nomor_dokumen : 0;

        // Dokumen foto utama
        $lastNumber++;
        $newId = str_pad($lastNumber, 7, '0', STR_PAD_LEFT);

        $originalName = $request->file('sk')->getClientOriginalName();
        $timestampedName = time() . '_' . $originalName;
        $namaGolongan = str_replace('/', '-', $golongan->nama_golongan);

        $destinationPath = "{$user->npp}/golongan/{$namaGolongan}/{$timestampedName}";


        // Upload ke Google Drive
        $result = $this->googleDriveService->uploadFileAndGetUrl($request->file('sk')->getPathname(), $destinationPath);

        $dokumen = Dokumens::create([
            'nomor_dokumen' => $newId,
            'path_file' => $destinationPath,
            'file_id' => $result['file_id'],
            'view_url' => $result['view_url'],
            'download_url' => $result['download_url'],
            'preview_url' => $result['preview_url'],
            'id_user' => $user->id_user,
            'tanggal_upload' => now()
        ]);

        GolonganUsers::create([
            'id_user' => $user->id_user,
            'id_golongan' => $request->golongan,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai ?? null,
            'status' => 'aktif',
            'sk' => $newId
        ]);



        if ($golonganSebelumnya) {
            $golonganSebelumnya->update([
                'status' => 'nonaktif',
                'tanggal_selesai' => $golonganSebelumnya->tanggal_selesai ?? now()
            ]);
        }

        return redirect()->route('admin.jabatan.golongan.show', $id)->with('success', 'Golongan Dosen berhasil dimutasi');
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
        $golongan = GolonganUsers::where('id_golongan_user', $id)->with(['dokumen', 'user'])->first();

        $user = $golongan->user->id_user;
        Gdrive::delete($golongan->dokumen->path_file);

        $golongan->delete();

        return redirect()->route('admin.jabatan.golongan.show', $user)->with('success', 'Data berhasil dihapus');
    }
}
