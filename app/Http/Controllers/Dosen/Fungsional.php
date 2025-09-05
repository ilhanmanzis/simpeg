<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\FungsionalUsers;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Fungsional extends Controller
{
    protected $googleDriveService;

    public function __construct(GoogleDriveService $googleDriveService)
    {
        $this->googleDriveService = $googleDriveService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $id = Auth::user()->id_user;
        $data = [
            'page' => 'Jabatan Fungsional',
            'selected' => 'Jabatan Fungsional',
            'title' => 'Data Jabatan Fungsional Dosen',
            'dosen' => FungsionalUsers::where('id_user', $id)->where('status', 'aktif')->with(['user.dataDiri', 'fungsional', 'dokumen'])->orderBy('id_fungsional_user', 'desc')->first(),

            'riwayats' => FungsionalUsers::where('id_user', $id)->where('status', 'nonaktif')->with(['user', 'fungsional', 'dokumen'])->orderBy('created_at', 'desc')->paginate(10)->withQueryString()
        ];

        return view('dosen.jabatan.fungsional.index', $data);
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
        //
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
