<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Golongans;
use App\Models\JabatanFungsionals;
use Illuminate\Http\Request;

class JabatanFungsional extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $data = [
            'page' => 'Jabatan Fungsional',
            'selected' => 'Jabatan Fungsional',
            'title' => 'Data Jabatan Fungsional',
            'fungsionals' => JabatanFungsionals::with(['golongan'])->orderBy('id_fungsional', 'desc')->paginate(10)->withQueryString()

        ];
        return view('admin.fungsional.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'page' => 'Jabatan Fungsional',
            'selected' => 'Jabatan Fungsional',
            'title' => 'Tambah Jabatan Fungsional',
            'golongans' => Golongans::all()
        ];

        return view('admin.fungsional.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'golongan' => 'required'
        ], [
            'name.required' => 'Nama jabatan fungsional harus diisi.',
            'golongan.required' => 'Golongan minimal harus diisi.',
        ]);

        JabatanFungsionals::create([
            'nama_jabatan' => $request->name,
            'id_golongan' => $request->golongan
        ]);

        return redirect()->route('admin.fungsional')->with('success', 'Data berhasil ditambahkan');
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
            'page' => 'Jabatan Fungsional',
            'selected' => 'Jabatan Fungsional',
            'title' => 'Edit Jabatan Fungsional',
            'golongans' => Golongans::all(),
            'fungsional' => JabatanFungsionals::where('id_fungsional', $id)->first()
        ];

        // dd($data);

        return view('admin.fungsional.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'golongan' => 'required'
        ], [
            'name.required' => 'Nama jabatan fungsional harus diisi.',
            'golongan.required' => 'Golongan minimal harus diisi.',
        ]);

        $fungsional = JabatanFungsionals::findOrFail($id);
        $fungsional->update([
            'nama_jabatan' => $request->name,
            'id_golongan' => $request->golongan
        ]);


        return redirect()->route('admin.fungsional')->with('success', 'Data berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $fungsional = JabatanFungsionals::findOrFail($id);
        $fungsional->delete();

        return redirect()->route('admin.fungsional')->with('success', 'Data berhasil dihapus');
    }
}
