<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\FungsionalUsers;
use App\Models\Golongans;
use App\Models\GolonganUsers;
use App\Models\JabatanFungsionals;
use App\Models\PengajuanFungsionals;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengajuanFungsional extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $id = Auth::user()->id_user;

        $data = [
            'page' => 'Pengajuan Fungsional',
            'selected' => 'Pengajuan Fungsional',
            'title' => 'Pengajuan Kenaikan Jabatan Fungsional',
            'pengajuans' => PengajuanFungsionals::where('id_user', $id)->orderBy('updated_at', 'desc')->paginate(10)->withQueryString()
        ];

        return view('dosen.pengajuan.fungsional.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $id = Auth::user()->id_user;
        $data = [
            'page' => 'Pengajuan Fungsional',
            'selected' => 'Pengajuan Fungsional',
            'title' => 'Tambah Pengajuan Kenaikan Jabatan Fungsional',
            'user' => User::where('id_user', $id)->with('dataDiri')->first(),
            'dosen' => FungsionalUsers::where('id_user', $id)->where('status', 'aktif')->with(['fungsional'])->orderBy('id_fungsional_user', 'desc')->first(),
            'golongans' => Golongans::all(),
            'golongan' => GolonganUsers::where('id_user', $id)->where('status', 'aktif')->with(['golongan'])->orderBy('id_golongan_user', 'desc')->first(),
            'fungsionals' => JabatanFungsionals::with('golongan')->get()
        ];
        return view('dosen.pengajuan.fungsional.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'fungsional' => 'required',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date',
            'angka_kredit' => 'nullable',
            'sk' => 'required|file|mimes:pdf|max:2048'
        ]);

        $id = Auth::user()->id_user;
        $fungsional = JabatanFungsionals::findOrFail($request->fungsional);

        $timestampedName = null;
        if ($request->hasFile('sk')) {
            // ===== Simpan sk profil =====
            $originalName = $request->file('sk')->getClientOriginalName();
            $timestampedName = time() . '_' . $originalName;
            // Simpan ke storage/app/sk
            $request->file('sk')->storeAs('sk', $timestampedName);
        }

        PengajuanFungsionals::create([
            'id_user' => $id,
            'id_fungsional' => $fungsional->id_fungsional,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai ?? null,
            'angka_kredit' => $request->angka_kredit ?? null,
            'status' => 'pending',
            'sk' => $timestampedName
        ]);

        return redirect()->route('dosen.pengajuan.fungsional')->with('Kenaikan jabatan fungsional berhasil diajukan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pengajuan = PengajuanFungsionals::where('id_pengajuan_fungsional', $id)->with(['user.dataDiri', 'fungsional'])->first();
        $data = [
            'page' => 'Pengajuan Fungsional',
            'selected' => 'Pengajuan Fungsional',
            'title' => 'Pengajuan Kenaikan Jabatan Fungsional',
            'pengajuan' => $pengajuan,


        ];

        // dd($pengajuan);

        if ($pengajuan->status === 'pending') {
            return view('dosen.pengajuan.fungsional.show', $data);
        } else {
            return view('dosen.pengajuan.fungsional.riwayat', $data);
        }
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
