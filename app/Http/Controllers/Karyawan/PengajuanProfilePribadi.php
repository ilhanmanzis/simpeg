<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\PengajuanPerubahanDatas;
use App\Models\User;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengajuanProfilePribadi extends Controller
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
        $id = Auth::user()->id_user;

        $data = [
            'page' => 'Pengajuan Profile Pribadi',
            'selected' => 'Pengajuan Profile Pribadi',
            'title' => 'Pengajuan Profile Pribadi',
            'pengajuans' => PengajuanPerubahanDatas::where('id_user', $id)->orderBy('updated_at', 'desc')->paginate(10)->withQueryString()
        ];

        return view('karyawan.pengajuan.profile.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $id = Auth::user()->id_user;
        $data = [
            'page' => 'Pengajuan Profile Pribadi',
            'selected' => 'Pengajuan Profile Pribadi',
            'title' => 'Tambah Pengajuan Profile Pribadi',
            'karyawan' => User::where('id_user', $id)->with(['dataDiri.dokumen'])->first()
        ];
        return view('karyawan.pengajuan.profile.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $id = Auth::user()->id_user;
        $request->validate([
            'name' => 'required|string|max:255',
            // 'email' => 'required|email|max:255|unique:users,email,' . $id . ',id_user',
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
            'tanggal_bergabung' => 'required|date',
            'golongan_darah' => 'required|in:A,B,AB,O,-',
            'bpjs'           => 'nullable',
            'anak'    => 'required|integer|min:0',
            'istri'   => 'required_if:jenis_kelamin,Laki-Laki|nullable|integer|min:0',


            // Foto
            'foto' => 'nullable|image|max:2048',
        ]);


        $timestampedName = null;
        if ($request->hasFile('foto')) {
            // ===== Simpan foto profil =====
            $originalName = $request->file('foto')->getClientOriginalName();
            $timestampedName = time() . '_' . $originalName;
            // Simpan ke storage/app/perubahanProfile
            $request->file('foto')->storeAs('perubahanProfile', $timestampedName);
        }


        PengajuanPerubahanDatas::create([
            'id_user' => $id,
            'name' => $request->input('name'),
            // 'email' => $request->input('email'),
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
            'nuptk' => null,
            'nip' => null,
            'nidk' => null,
            'nidn' => null,
            'tanggal_bergabung' => $request->input('tanggal_bergabung'),
            'bpjs'              => $request->input('bpjs') ?? null,
            'istri'             => $request->input('istri') ?? 0,
            'anak'              => $request->input('anak') ?? 0,
            'golongan_darah'              => $request->input('golongan_darah'),
            'foto' => $timestampedName ?? null,
        ]);

        return redirect()->route('karyawan.pengajuan.profile')->with('success', 'Perubahan Profile Pribadi Berhasil diajukan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = [
            'page' => 'Pengajuan Profile Pribadi',
            'selected' => 'Pengajuan Profile Pribadi',
            'title' => 'Pengajuan Profile Pribadi',
            'pengajuan' => PengajuanPerubahanDatas::where('id_perubahan', $id)->with(['user.dataDiri'])->first()
        ];

        return view('karyawan.pengajuan.profile.show', $data);
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
}
