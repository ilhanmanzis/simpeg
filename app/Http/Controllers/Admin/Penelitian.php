<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penelitians;
use App\Models\User;
use Illuminate\Http\Request;

class Penelitian extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $keyword = $request->get('dosen');
        $data = [
            'page' => 'BKD Penelitian',
            'selected' => 'BKD Penelitian',
            'title' => 'Data BKD Penelitian',
            'dosens' => User::where('role', 'dosen')->with([
                'dataDiri',
            ])->when($keyword, function ($query) use ($keyword) {
                $query->searchDosen($keyword);
            })->orderBy('created_at', 'desc')->paginate(10)->withQueryString()
        ];

        return view('admin.bkd.penelitian.index', $data);
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
    public function all(Request $request, string $id)
    {
        $judul = $request->get('judul');

        $user = User::where('id_user', $id)->with('dataDiri')->firstOrFail();
        $data = [
            'page' => 'BKD Penelitian',
            'selected' => 'BKD Penelitian',
            'title' => 'BKD Penelitian ' . $user->dataDiri->name,
            'penelitians' => Penelitians::where('id_user', $id)
                ->searchJudul($judul)
                ->orderBy('created_at', 'desc')
                ->paginate(10)
                ->withQueryString(),
            'user' => $user
        ];

        return view('admin.bkd.penelitian.all', $data);
    }

    public function show(Request $request, string $id)
    {

        $penelitian = Penelitians::where('id_penelitian', $id)->with(['user.dataDiri'])->firstOrFail();

        $data = [
            'page' => 'BKD Penelitian',
            'selected' => 'BKD Penelitian',
            'title' => 'BKD Penelitian ' . $penelitian->user->dataDiri->name,
            'penelitian' => $penelitian,
        ];
        return view('admin.bkd.penelitian.show', $data);
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
        $penelitian = Penelitians::where('id_penelitian', $id)->with(['user'])->firstOrFail();
        $idUser = $penelitian->id_user;

        $penelitian->delete();

        return redirect()->route('admin.bkd.penelitian.all', ['id' => $idUser])->with('success', 'Data BKD Penelitian berhasil dihapus.');
    }
}
