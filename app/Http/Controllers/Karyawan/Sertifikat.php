<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\PengajuanSertifikats;
use App\Models\Sertifikats;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $judul = $request->get('judul');
        $id = Auth::user()->id_user;

        $data = [
            'page' => 'Sertifikat',
            'selected' => 'Sertifikat',
            'title' => 'Data Sertifikat',
            'sertifikats' => Sertifikats::where('id_user', $id)
                ->searchJudul($judul)
                ->orderBy('created_at', 'desc')
                ->paginate(10)
                ->appends(request()->except('page', 'riwayat_page')),
            'riwayats' => PengajuanSertifikats::where('id_user', $id)
                // where('status', '!=', 'disetujui')->
                ->orderBy('updated_at', 'desc')
                ->paginate(10, ['*'], 'riwayat_page')
                ->appends(request()->except('page'))
                ->fragment('riwayat'),
        ];

        // dd($data);

        return view('karyawan.sertifikat.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'page' => 'Sertifikat',
            'selected' => 'Sertifikat',
            'title' => 'Tambah Sertifikat',


        ];
        return view('karyawan.sertifikat.create', $data);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'penyelenggara' => 'required|string|max:255',
            'tanggal_diperoleh' => 'required|date',
            'tanggal_selesai' => 'nullable|date',
            'dokumen' => 'required|file|mimes:pdf|max:2048',
        ]);

        $user = Auth::user()->id_user;

        $dokumenFile = $request->file("dokumen");
        $dokumenName = time() . '_' . $dokumenFile->getClientOriginalName();
        // Simpan ke storage/app/sertifikat
        $dokumenFile->storeAs('sertifikat', $dokumenName);

        PengajuanSertifikats::create([
            'id_user' => $user,
            'nama_sertifikat' => $request->name,
            'kategori' => $request->kategori,
            'penyelenggara' => $request->penyelenggara,
            'tanggal_diperoleh' => $request->tanggal_diperoleh,
            'tanggal_selesai' => $request->tanggal_selesai ?? null,
            'dokumen' => $dokumenName,
            'keterangan' => null,
            'jenis' => 'tambah',
            'status' => 'pending',
        ]);

        return redirect()->route('karyawan.sertifikat')->with('success', 'Tambah sertifikat berhasil diajukan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $sertifikat = Sertifikats::where('id_sertifikat', $id)->with(['user.dataDiri', 'dokumenSertifikat'])->firstOrFail();

        if ($sertifikat->id_user !== Auth::user()->id_user) {
            abort(403); // Forbidden
        }

        $data = [
            'page' => 'Sertifikat',
            'selected' => 'Sertifikat',
            'title' => 'Data Sertifikat',
            'sertifikat' => $sertifikat,
        ];
        return view('karyawan.sertifikat.show', $data);
    }

    public function riwayat(string $id)
    {
        $pengajuan = PengajuanSertifikats::where('id_pengajuan', $id)->with(['user.dataDiri'])->firstOrFail();

        if ($pengajuan->id_user !== Auth::user()->id_user) {
            abort(403); // Forbidden
        }

        $data = [
            'page' => 'Sertifikat',
            'selected' => 'Sertifikat',
            'title' => 'Sertifikat',
            'pengajuan' => $pengajuan,
        ];
        return view('karyawan.sertifikat.riwayat', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = Auth::user()->id_user;
        $sertifikat = Sertifikats::where('id_sertifikat', $id)->with(['dokumenSertifikat', 'user.dataDiri'])->firstOrFail();

        if ($user !== $sertifikat->user->id_user) {
            return redirect()->route('karyawan.sertifikat');
        }

        $data = [
            'page' => 'Sertifikat',
            'selected' => 'Sertifikat',
            'title' => 'Edit Sertifikat',
            'sertifikat' => $sertifikat,

        ];
        return view('karyawan.sertifikat.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'kategori'   => 'nullable|string|max:255',
            'penyelenggara'           => 'required|string|max:255',
            'tanggal_diperoleh'     => 'required|date',
            'tanggal_selesai'     => 'nullable|date',
            'dokumen'          => 'nullable|file|mimes:pdf|max:2048',
        ]);

        $user = Auth::user()->id_user;
        $sertifikat = Sertifikats::where('id_sertifikat', $id)->with(['user'])->first();

        if ($user !== $sertifikat->user->id_user) {
            return redirect()->route('karyawan.sertifikat')->with('success', 'pengajuan ditolak');
        }

        $dokumenName = null;
        if ($request->hasFile("dokumen")) {
            $dokumenFile = $request->file("dokumen");
            $dokumenName = time() . '_' . $dokumenFile->getClientOriginalName();
            // Simpan ke storage/app/sertifikat
            $dokumenFile->storeAs('sertifikat', $dokumenName);
        }


        PengajuanSertifikats::create([
            'id_user'               => $user,
            'nama_sertifikat'       => $request->name,
            'id_sertifikat'         => $id,
            'kategori'              => $request->kategori,
            'penyelenggara'         => $request->penyelenggara,
            'tanggal_diperoleh'     => $request->tanggal_diperoleh ?? null,
            'tanggal_selesai'       => $request->tanggal_selesai ?? null,
            'dokumen'               => $dokumenName,
            'keterangan'            => null,
            'jenis'                 => 'edit',
            'status'                => 'pending',
        ]);

        return redirect()->route('karyawan.sertifikat')->with('success', 'Edit sertifikat berhasil diajukan.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = Auth::user()->id_user;
        $sertifikat = Sertifikats::where('id_sertifikat', $id)->with(['user'])->first();

        if ($user !== $sertifikat->user->id_user) {
            return redirect()->route('karyawan.sertifikat')->with('success', 'pengajuan ditolak');
        }

        PengajuanSertifikats::create([
            'id_user' => $user,
            'id_sertifikat' => $id,
            'nama_sertifikat' => $sertifikat->nama_sertifikat,
            'kategori' => null,
            'penyelenggara' => null,
            'tanggal_diperoleh' => null,
            'tanggal_selesai' => null,
            'dokumen' => null,
            'keterangan' => null,
            'jenis' => 'hapus',
            'status' => 'pending',
        ]);
        return redirect()->route('karyawan.sertifikat')->with('success', 'Hapus sertifikat berhasil diajukan.');
    }
}
