<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JabatanFungsionals;
use App\Models\JabatanStrukturals;
use Illuminate\Http\Request;

class JabatanStruktural extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $data = [
            'page' => 'Jabatan Struktural',
            'selected' => 'Jabatan Struktural',
            'title' => 'Data Jabatan Struktural',
            'strukturals' => JabatanStrukturals::orderBy('id_struktural', 'desc')->paginate(10)->withQueryString()

        ];
        return view('admin.master.struktural.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'page' => 'Jabatan Struktural',
            'selected' => 'Jabatan Struktural',
            'title' => 'Tambah Jabatan Struktural',
        ];

        return view('admin.master.struktural.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'Nama jabatan struktural harus diisi.',
        ]);

        JabatanStrukturals::create([
            'nama_jabatan' => $request->name,
        ]);

        return redirect()->route('admin.struktural')->with('success', 'Data berhasil ditambahkan');
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
            'page' => 'Jabatan Struktural',
            'selected' => 'Jabatan Struktural',
            'title' => 'Edit Jabatan Struktural',
            'struktural' => JabatanStrukturals::where('id_struktural', $id)->first()
        ];

        // dd($data);

        return view('admin.master.struktural.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'Nama jabatan struktural harus diisi.',
        ]);

        $struktural = JabatanStrukturals::findOrFail($id);
        $struktural->update([
            'nama_jabatan' => $request->name,
        ]);


        return redirect()->route('admin.struktural')->with('success', 'Data berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $struktural = JabatanStrukturals::findOrFail($id);
        $struktural->delete();

        return redirect()->route('admin.struktural')->with('success', 'Data berhasil dihapus');
    }
}
