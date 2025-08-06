<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriSertifikats;
use Illuminate\Http\Request;

class KategoriSertifikat extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $data = [
            'page' => 'Kategori Sertifikat',
            'selected' => 'Kategori Sertifikat',
            'title' => 'Data Kategori Sertifikat',
            'sertifikats' => KategoriSertifikats::orderBy('id_kategori_sertifikat', 'desc')->paginate(10)->withQueryString()

        ];
        return view('admin.kategorisertifikat.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'page' => 'Kategori Sertifikat',
            'selected' => 'Kategori Sertifikat',
            'title' => 'Tambah Kategori Sertifikat',
        ];

        return view('admin.kategorisertifikat.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'Nama Kategori Sertifikat harus diisi.',
        ]);

        KategoriSertifikats::create([
            'nama_kategori' => $request->name,
        ]);

        return redirect()->route('admin.kategorisertifikat')->with('success', 'Data berhasil ditambahkan');
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
        $data = [
            'page' => 'Kategori Sertifikat',
            'selected' => 'Kategori Sertifikat',
            'title' => 'Edit Kategori Sertifikat',
            'sertifikat' => KategoriSertifikats::where('id_kategori_sertifikat', $id)->first()
        ];

        // dd($data);

        return view('admin.kategorisertifikat.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'Nama Kategori Sertifikat harus diisi.',
        ]);

        $sertifikat = KategoriSertifikats::findOrFail($id);
        $sertifikat->update([
            'nama_kategori' => $request->name,
        ]);


        return redirect()->route('admin.kategorisertifikat')->with('success', 'Data berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $sertifikat = KategoriSertifikats::findOrFail($id);
        $sertifikat->delete();

        return redirect()->route('admin.kategorisertifikat')->with('success', 'Data berhasil dihapus');
    }
}
