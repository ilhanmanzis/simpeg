<?php

namespace App\Http\Controllers;

use App\Models\Jenjangs;
use App\Models\RegisterPendidikans;
use App\Models\Registers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class Register extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = [
            'page' => 'Register',
            'selected' => 'Register',
            'title' => 'Register',

        ];
        return view('register.index', $data);
    }
    public function dosen()
    {
        $data = [
            'page' => 'Register Dosen',
            'selected' => 'Register Dosen',
            'title' => 'Register Dosen',
            'jenjangs' => Jenjangs::all()

        ];
        return view('register.dosen.index', $data);
    }
    public function karyawan()
    {
        $data = [
            'page' => 'Register Karyawan',
            'selected' => 'Register Karyawan',
            'title' => 'Register Karyawan',
            'jenjangs' => Jenjangs::all()

        ];
        return view('register.karyawan.index', $data);
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
    public function storeDosen(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|min:6',
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
            'npp' => 'required|max:30',
            'nuptk' => 'required|max:30',
            'nip' => 'nullable|max:30',
            'nidk' => 'nullable|max:30',
            'nidn' => 'nullable|max:30',
            'tanggal_bergabung' => 'required|date',

            // Pendidikan (array)
            'pendidikan' => 'required|array|min:1',
            'pendidikan.*.jenjang' => 'required|integer|max:255',
            'pendidikan.*.tahun_lulus' => 'required|max:5',
            'pendidikan.*.program_studi' => 'required|string|max:255',
            'pendidikan.*.gelar' => 'nullable|string|max:255',
            'pendidikan.*.institusi' => 'nullable|string|max:255',
            'pendidikan.*.ijazah' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'pendidikan.*.transkip_nilai' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',

            // Foto
            'foto' => 'required|image|max:2048',
            'serdos' => 'required|file|mimes:pdf|max:2048',
            'tersertifikasi' => 'required'
        ]);

        // ===== Simpan foto profil =====
        $originalName = $request->file('foto')->getClientOriginalName();
        $timestampedName = time() . '_' . $originalName;
        // Simpan ke storage/app/register
        $request->file('foto')->storeAs('register', $timestampedName);

        if ($request->hasFile('serdos') && $request->tersertifikasi === 'sudah') {
            $originalNameSerdos = $request->file('serdos')->getClientOriginalName();
            $timestampedNameSerdos = time() . '_' . $originalNameSerdos;
            // Simpan ke storage/app/register
            $request->file('serdos')->storeAs('register', $timestampedNameSerdos);
        }

        // ===== Simpan data utama =====
        $register = Registers::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'npp' => $request->npp,
            'nuptk' => $request->nuptk,
            'nip' => $request->nip,
            'nidk' => $request->nidk,
            'nidn' => $request->nidn,
            'name' => $request->name,
            'no_ktp' => $request->nik,
            'no_hp' => $request->no_hp,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'agama' => $request->agama,
            'alamat' => $request->alamat,
            'rt' => $request->rt,
            'rw' => $request->rw,
            'desa' => $request->desa,
            'kecamatan' => $request->kecamatan,
            'kabupaten' => $request->kabupaten,
            'provinsi' => $request->provinsi,
            'role' => 'dosen',
            'status' => 'pending',
            'tanggal_bergabung' => $request->tanggal_bergabung,
            'tersertifikasi' => $request->tersertifikasi,
            'foto' => $timestampedName,
            'serdos' => $timestampedNameSerdos ?? null
        ]);

        // ===== Simpan data pendidikan + upload ijazah dan transkip =====
        foreach ($request->pendidikan as $index => $pendidikan) {
            $ijazahFile = $request->file("pendidikan.$index.ijazah");
            $ijazahName = time() . '_' . $ijazahFile->getClientOriginalName();
            // Simpan ke storage/app/pendidikan/ijazah
            $ijazahFile->storeAs('pendidikan/ijazah', $ijazahName);

            $transkipNilaiName = null;
            if ($request->hasFile("pendidikan.$index.transkip_nilai")) {
                $transkipFile = $request->file("pendidikan.$index.transkip_nilai");
                $transkipNilaiName = time() . '_' . $transkipFile->getClientOriginalName();
                // Simpan ke storage/app/pendidikan/ijazah
                $transkipFile->storeAs('pendidikan/transkipNilai', $transkipNilaiName);
            }

            RegisterPendidikans::create([
                'id_register' => $register->id_register,
                'id_jenjang' => $pendidikan['jenjang'],
                'institusi' => $pendidikan['institusi'],
                'tahun_lulus' => $pendidikan['tahun_lulus'],
                'program_studi' => $pendidikan['program_studi'],
                'gelar' => $pendidikan['gelar'],
                'ijazah' => $ijazahName,
                'transkip_nilai' => $transkipNilaiName
            ]);
        }

        return redirect()->route('login')->with('success', 'Registrasi Berhasil, Status akun masih pending. Tunggu sampai Admin menyetujui');
    }
    public function storeKaryawan(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|min:6',
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
            'npp' => 'required|max:30',


            'tanggal_bergabung' => 'required|date',

            // Pendidikan (array)
            'pendidikan' => 'required|array|min:1',
            'pendidikan.*.jenjang' => 'required|integer|max:255',
            'pendidikan.*.tahun_lulus' => 'required|max:5',
            'pendidikan.*.program_studi' => 'required|string|max:255',
            'pendidikan.*.gelar' => 'nullable|string|max:255',
            'pendidikan.*.institusi' => 'nullable|string|max:255',
            'pendidikan.*.ijazah' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'pendidikan.*.transkip_nilai' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',

            // Foto
            'foto' => 'required|image|max:2048',
        ]);

        // ===== Simpan foto profil =====
        $originalName = $request->file('foto')->getClientOriginalName();
        $timestampedName = time() . '_' . $originalName;
        // Simpan ke storage/app/register
        $request->file('foto')->storeAs('register', $timestampedName);



        // ===== Simpan data utama =====
        $register = Registers::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'npp' => $request->npp,
            'name' => $request->name,
            'no_ktp' => $request->nik,
            'no_hp' => $request->no_hp,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'agama' => $request->agama,
            'alamat' => $request->alamat,
            'rt' => $request->rt,
            'rw' => $request->rw,
            'desa' => $request->desa,
            'kecamatan' => $request->kecamatan,
            'kabupaten' => $request->kabupaten,
            'provinsi' => $request->provinsi,
            'role' => 'karyawan',
            'status' => 'pending',
            'tanggal_bergabung' => $request->tanggal_bergabung,
            'foto' => $timestampedName
        ]);

        // ===== Simpan data pendidikan + upload ijazah dan transkip =====
        foreach ($request->pendidikan as $index => $pendidikan) {
            $ijazahFile = $request->file("pendidikan.$index.ijazah");
            $ijazahName = time() . '_' . $ijazahFile->getClientOriginalName();
            // Simpan ke storage/app/pendidikan/ijazah
            $ijazahFile->storeAs('pendidikan/ijazah', $ijazahName);

            $transkipNilaiName = null;
            if ($request->hasFile("pendidikan.$index.transkip_nilai")) {
                $transkipFile = $request->file("pendidikan.$index.transkip_nilai");
                $transkipNilaiName = time() . '_' . $transkipFile->getClientOriginalName();
                // Simpan ke storage/app/pendidikan/ijazah
                $transkipFile->storeAs('pendidikan/transkipNilai', $transkipNilaiName);
            }

            RegisterPendidikans::create([
                'id_register' => $register->id_register,
                'id_jenjang' => $pendidikan['jenjang'],
                'institusi' => $pendidikan['institusi'],
                'tahun_lulus' => $pendidikan['tahun_lulus'],
                'program_studi' => $pendidikan['program_studi'],
                'gelar' => $pendidikan['gelar'],
                'ijazah' => $ijazahName,
                'transkip_nilai' => $transkipNilaiName
            ]);
        }

        return redirect()->route('login')->with('success', 'Registrasi Berhasil, Status akun masih pending. Tunggu sampai Admin menyetujui');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
