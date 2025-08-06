<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RegisterPendidikans;
use App\Models\Registers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengajuanAkun extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pengajuans = Registers::where('status', 'pending')->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        $riwayats = Registers::where('status', '!=', 'pending')->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'riwayat_page')
            ->withQueryString();
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
        dd($id);
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
