<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Golongans;
use Illuminate\Http\Request;

class Golongan extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = [
            'page' => 'Golongan',
            'selected' => 'Golongan',
            'title' => 'Data Golongan',
            'golongans' => Golongans::orderBy('id_golongan', 'desc')->paginate(10)->withQueryString()

        ];
        return view('admin.golongan.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'page' => 'Golongan',
            'selected' => 'Golongan',
            'title' => 'Tambah Golongan',
        ];

        return view('admin.golongan.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'Nama golongan harus diisi.',
        ]);

        Golongans::create([
            'nama_golongan' => $request->name
        ]);

        return redirect()->route('admin.golongan')->with('success', 'Data berhasil ditambahkan');
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
            'page' => 'Golongan',
            'selected' => 'Golongan',
            'title' => 'Edit Golongan',
            'golongan' => Golongans::where('id_golongan', $id)->first()
        ];

        // dd($data);

        return view('admin.golongan.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'Nama golongan harus diisi.',
        ]);

        $golongan = Golongans::findOrFail($id);
        $golongan->update([
            'nama_golongan' => $request->name
        ]);


        return redirect()->route('admin.golongan')->with('success', 'Data berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $golongan = Golongans::findOrFail($id);
        $golongan->delete();

        return redirect()->route('admin.golongan')->with('success', 'Data berhasil dihapus');
    }
}
