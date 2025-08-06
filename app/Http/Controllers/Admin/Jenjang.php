<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jenjangs;
use Illuminate\Http\Request;

class Jenjang extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = [
            'page' => 'Jenjang',
            'selected' => 'Jenjang',
            'title' => 'Data Jenjang',
            'jenjangs' => Jenjangs::orderBy('created_at', 'desc')->paginate(10)->withQueryString()
        ];


        // dd($data);


        return view('admin.jenjang.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'page' => 'Jenjang',
            'selected' => 'Jenjang',
            'title' => 'Tambah Jenjang',
        ];

        return view('admin.jenjang.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'Nama Jenjang harus diisi.',
        ]);

        Jenjangs::create([
            'nama_jenjang' => $request->name
        ]);

        return redirect()->route('admin.jenjang')->with('success', 'Data berhasil ditambahkan');
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
            'page' => 'Jenjang',
            'selected' => 'Jenjang',
            'title' => 'Edit Jenjang',
            'jenjang' => Jenjangs::where('id_jenjang', $id)->first()
        ];

        // dd($data);

        return view('admin.jenjang.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'Nama Jenjang harus diisi.',
        ]);

        $jenjang = Jenjangs::findOrFail($id);
        $jenjang->update([
            'nama_jenjang' => $request->name
        ]);


        return redirect()->route('admin.jenjang')->with('success', 'Data berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $jenjang = Jenjangs::findOrFail($id);
        $jenjang->delete();

        return redirect()->route('admin.jenjang')->with('success', 'Data berhasil dihapus');
    }
}
