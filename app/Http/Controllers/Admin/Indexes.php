<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Indexes as ModelsIndexes;
use Illuminate\Http\Request;

class Indexes extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = [
            'page' => 'Indexes',
            'selected' => 'Indexes',
            'title' => 'Data Indeks Publikasi',
            'indexes' => ModelsIndexes::orderBy('created_at', 'desc')->paginate(10)->withQueryString()
        ];


        // dd($data);


        return view('admin.master.indexes.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'page' => 'Indexes',
            'selected' => 'Indexes',
            'title' => 'Tambah Indeks Publikasi',
        ];

        return view('admin.master.indexes.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'Nama Indeks Publikasi harus diisi.',
        ]);

        ModelsIndexes::create([
            'name' => $request->name
        ]);

        return redirect()->route('admin.indexes')->with('success', 'Data berhasil ditambahkan');
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        if ($id == 1) {
            return redirect()->route('admin.indexes')->with('error', 'Indeks Publikasi ini tidak dapat diedit');
        }
        $data = [
            'page' => 'Indexes',
            'selected' => 'Indexes',
            'title' => 'Edit Indeks Publikasi',
            'indexes' => ModelsIndexes::where('id_index', $id)->firstOrFail()
        ];

        return view('admin.master.indexes.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if ($id == 1) {
            return redirect()->route('admin.indexes')->with('error', 'Indeks Publikasi ini tidak dapat diedit');
        }
        $request->validate([
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'Nama Indeks Publikasi harus diisi.',
        ]);

        $indexes = ModelsIndexes::findOrFail($id);
        $indexes->update([
            'name' => $request->name
        ]);


        return redirect()->route('admin.indexes')->with('success', 'Data berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if ($id == 1) {
            return redirect()->route('admin.indexes')->with('error', 'Indeks Publikasi ini tidak dapat dihapus');
        }
        $indexes = ModelsIndexes::findOrFail($id);
        $indexes->delete();

        return redirect()->route('admin.indexes')->with('success', 'Data berhasil dihapus');
    }
}
