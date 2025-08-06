<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Semesters;
use Illuminate\Http\Request;

class Semester extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $data = [
            'page' => 'Semester',
            'selected' => 'Semester',
            'title' => 'Data Semester',
            'semesters' => Semesters::orderBy('id_semester', 'desc')->paginate(10)->withQueryString()

        ];
        return view('admin.semester.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'page' => 'Semester',
            'selected' => 'Semester',
            'title' => 'Tambah Semester',
        ];

        return view('admin.semester.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'Nama Semester harus diisi.',
        ]);

        Semesters::create([
            'nama_semester' => $request->name,
        ]);

        return redirect()->route('admin.semester')->with('success', 'Data berhasil ditambahkan');
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
            'page' => 'Semester',
            'selected' => 'Semester',
            'title' => 'Edit Semester',
            'semester' => Semesters::where('id_semester', $id)->first()
        ];

        // dd($data);

        return view('admin.semester.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'Nama Semester harus diisi.',
        ]);

        $semester = Semesters::findOrFail($id);
        $semester->update([
            'nama_semester' => $request->name,
        ]);


        return redirect()->route('admin.semester')->with('success', 'Data berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $semester = Semesters::findOrFail($id);
        $semester->delete();

        return redirect()->route('admin.semester')->with('success', 'Data berhasil dihapus');
    }
}
