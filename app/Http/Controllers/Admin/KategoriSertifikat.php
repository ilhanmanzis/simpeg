<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriSertifikats;
use Illuminate\Http\Request;

class KategoriSertifikat extends Controller
{
    public function index()
    {
        $data = [
            'page' => 'Kategori Sertifikat',
            'selected' => 'Kategori Sertifikat',
            'title' => 'Data Kategori Sertifikat',
            'kategoris' => KategoriSertifikats::orderBy('id_kategori', 'desc')->paginate(10)->withQueryString()

        ];
        return view('admin.master.sertifikat.index', $data);
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

        return view('admin.master.sertifikat.create', $data);
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
            'name' => $request->name
        ]);

        return redirect()->route('admin.kategori-sertifikat')->with('success', 'Data berhasil ditambahkan');
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
            'kategori' => KategoriSertifikats::where('id_kategori', $id)->first()
        ];

        // dd($data);

        return view('admin.master.sertifikat.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'Nama kategori sertifikat harus diisi.',
        ]);

        $kategori = KategoriSertifikats::findOrFail($id);
        $kategori->update([
            'name' => $request->name
        ]);


        return redirect()->route('admin.kategori-sertifikat')->with('success', 'Data berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $kategori = KategoriSertifikats::findOrFail($id);
        $kategori->delete();

        return redirect()->route('admin.kategori-sertifikat')->with('success', 'Data berhasil dihapus');
    }
}
