<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Jenjangs;
use App\Models\Pendidikans;
use App\Models\PengajuanPerubahanPendidikans;
use App\Services\GoogleDriveService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengajuanPendidikan extends Controller
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
            'page' => 'Pengajuan Pendidikan',
            'selected' => 'Pengajuan Pendidikan',
            'title' => 'Pengajuan Pendidikan',
            'pengajuans' => PengajuanPerubahanPendidikans::where('id_user', $id)->orderBy('updated_at', 'desc')->paginate(10)->withQueryString(),
            'pendidikans' => Pendidikans::where('id_user', $id)->with(['jenjang', 'dokumenIjazah', 'dokumenTranskipNilai'])->orderBy('id_jenjang', 'asc')->get()
        ];

        return view('karyawan.pengajuan.pendidikan.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'page' => 'Pengajuan Pendidikan',
            'selected' => 'Pengajuan Pendidikan',
            'title' => 'Tambah Pendidikan karyawan',
            'jenjangs' => Jenjangs::all()

        ];
        return view('karyawan.pengajuan.pendidikan.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenjang' => 'required|exists:jenjang,id_jenjang',
            'institusi' => 'required|string|max:255',
            'program_studi' => 'nullable|string|max:255',
            'gelar' => 'nullable|string|max:255',
            'tahun_lulus' => 'required|date_format:Y',
            'ijazah' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'transkip_nilai' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $user = Auth::user();

        $ijazahFile = $request->file("ijazah");
        $ijazahName = time() . '_' . $ijazahFile->getClientOriginalName();
        // Simpan ke storage/app/pendidikan/ijazah
        $ijazahFile->storeAs('pendidikan/ijazah', $ijazahName);

        $transkipNilaiName = null;
        if ($request->hasFile("transkip_nilai")) {
            $transkipFile = $request->file("transkip_nilai");
            $transkipNilaiName = time() . '_' . $transkipFile->getClientOriginalName();
            // Simpan ke storage/app/pendidikan/transkipNilai
            $transkipFile->storeAs('pendidikan/transkipNilai', $transkipNilaiName);
        }

        $pengajuan = PengajuanPerubahanPendidikans::create([
            'id_user' => $user->id_user,
            'id_jenjang' => $request->jenjang,
            'id_pendidikan' => null,
            'institusi' => $request->institusi,
            'program_studi' => $request->program_studi ?? null,
            'gelar' => $request->gelar ?? null,
            'tahun_lulus' => $request->tahun_lulus,
            'ijazah' => $ijazahName,
            'transkip_nilai' => $transkipNilaiName,
            'keterangan' => null,
            'jenis' => 'tambah',
            'status' => 'pending',
        ]);

        NotificationService::notifyAdmin(
            'Pengajuan Perubahan Data Pendidikan (Tambah) Baru',
            'Ada pengajuan perubahan data pendidikan (Tambah) dari '
                . $user->dataDiri->name,
            'admin.pengajuan.profile.show',
            [
                'id'    => $pengajuan->id_perubahan,
                'jenis' => 'pendidikan'
            ]
        );

        return redirect()->route('karyawan.pengajuan.pendidikan')->with('success', 'Tambah pendidikan berhasil diajukan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pengajuan = PengajuanPerubahanPendidikans::where('id_perubahan', $id)->with(['user.dataDiri', 'jenjang', 'pendidikan.dokumenIjazah', 'pendidikan.dokumenTranskipNilai', 'pendidikan.jenjang'])->first();

        $idUser = Auth::user()->id_user;
        if (!$pengajuan) {
            abort(404);
        }

        if ($idUser != $pengajuan->user->id_user) {
            return redirect()->route('karyawan.pengajuan.pendidikan')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }
        $data = [
            'page' => 'Pengajuan Pendidikan',
            'selected' => 'Pengajuan Pendidikan',
            'title' => 'Pengajuan Perubahan Pendidikan',
            'pengajuan' => $pengajuan,


        ];

        if ($pengajuan->status === 'pending') {
            return view('karyawan.pengajuan.pendidikan.show', $data);
        } else {
            return view('karyawan.pengajuan.pendidikan.riwayat', $data);
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = Auth::user()->id_user;
        $pendidikan = Pendidikans::where('id_pendidikan', $id)->with(['dokumenIjazah', 'dokumenTranskipNilai', 'user'])->firstOrFail();

        if ($user != $pendidikan->user->id_user) {
            return redirect()->route('karyawan.pengajuan.pendidikan')->with('error', 'Anda tidak memiliki akses untuk mengedit pendidikan milik orang lain.');
        }

        $data = [
            'page' => 'Pengajuan Pendidikan',
            'selected' => 'Pengajuan Pendidikan',
            'title' => 'Edit Pendidikan karyawan',
            'pendidikan' => $pendidikan,
            'jenjangs' => Jenjangs::all()

        ];
        return view('karyawan.pengajuan.pendidikan.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
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

        $user = Auth::user();
        $pendidikan = Pendidikans::where('id_pendidikan', $id)->with(['user'])->firstOrFail();

        if ($user->id_user != $pendidikan->user->id_user) {
            return redirect()->route('karyawan.pengajuan.pendidikan')->with('error', 'Anda tidak memiliki akses untuk mengedit pendidikan milik orang lain.');
        }

        $ijazahName = null;
        if ($request->hasFile("ijazah")) {
            $ijazahFile = $request->file("ijazah");
            $ijazahName = time() . '_' . $ijazahFile->getClientOriginalName();
            // Simpan ke storage/app/pendidikan/ijazah
            $ijazahFile->storeAs('pendidikan/ijazah', $ijazahName);
        }
        $transkipNilaiName = null;
        if ($request->hasFile("transkip_nilai")) {
            $transkipFile = $request->file("transkip_nilai");
            $transkipNilaiName = time() . '_' . $transkipFile->getClientOriginalName();
            // Simpan ke storage/app/pendidikan/transkipNilai
            $transkipFile->storeAs('pendidikan/transkipNilai', $transkipNilaiName);
        }

        $pengajuan = PengajuanPerubahanPendidikans::create([
            'id_user' => $user->id_user,
            'id_jenjang' => $request->jenjang,
            'id_pendidikan' => $id,
            'institusi' => $request->institusi,
            'program_studi' => $request->program_studi,
            'gelar' => $request->gelar ?? null,
            'tahun_lulus' => $request->tahun_lulus,
            'ijazah' => $ijazahName,
            'transkip_nilai' => $transkipNilaiName,
            'keterangan' => null,
            'jenis' => 'edit',
            'status' => 'pending',
        ]);
        NotificationService::notifyAdmin(
            'Pengajuan Perubahan Data Pendidikan (Edit) Baru',
            'Ada pengajuan perubahan data pendidikan (Edit) dari '
                . $user->dataDiri->name,
            'admin.pengajuan.profile.show',
            [
                'id'    => $pengajuan->id_perubahan,
                'jenis' => 'pendidikan'
            ]
        );

        return redirect()->route('karyawan.pengajuan.pendidikan')->with('success', 'Edit pendidikan berhasil diajukan.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = Auth::user();
        $pendidikan = Pendidikans::where('id_pendidikan', $id)->with(['user'])->firstOrFail();

        if ($user->id_user != $pendidikan->user->id_user) {
            return redirect()->route('karyawan.pengajuan.pendidikan')->with('success', 'pengajuan ditolak');
        }

        $pengajuan = PengajuanPerubahanPendidikans::create([
            'id_user' => $user->id_user,
            'id_jenjang' => null,
            'id_pendidikan' => $id,
            'institusi' => null,
            'program_studi' => null,
            'tahun_lulus' => null,
            'ijazah' => null,
            'transkip_nilai' => null,
            'keterangan' => null,
            'jenis' => 'delete',
            'status' => 'pending',
        ]);
        NotificationService::notifyAdmin(
            'Pengajuan Perubahan Data Pendidikan (Hapus) Baru',
            'Ada pengajuan perubahan data pendidikan (Hapus) dari '
                . $user->dataDiri->name,
            'admin.pengajuan.profile.show',
            [
                'id'    => $pengajuan->id_perubahan,
                'jenis' => 'pendidikan'
            ]
        );
        return redirect()->route('karyawan.pengajuan.pendidikan')->with('success', 'Hapus pendidikan berhasil diajukan.');
    }
}
