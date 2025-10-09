<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class Tendik extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $keyword = $request->get('tendik');
        $data = [
            'page' => 'Tendik',
            'title' => 'Data Tenaga Pendidik',
            'tendiks'   => User::where('role', 'karyawan')->with(['dataDiri'])
                ->when($keyword, function ($query) use ($keyword) {
                    $query->searchKaryawan($keyword);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10)
                ->withQueryString()
        ];

        return view('public/tendik', $data);
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
            'page' => 'Tenaga Pendidik',
            'title' => 'Data Tenaga Pendidik',
            'tendik'   => User::where('npp', $id)->where('role', 'karyawan')->with([
                'dataDiri.dokumen',
                'pendidikan' => function ($q) {
                    $q->orderBy('id_jenjang')
                        ->with('jenjang');
                },
            ])->first()
        ];


        return view('public/tendik-show', $data);
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
}
