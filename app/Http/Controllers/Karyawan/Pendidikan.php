<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Pendidikans;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Pendidikan extends Controller
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
            'page' => 'Pendidikan',
            'selected' => 'Pendidikan',
            'title' => 'Pendidikan',
            'pendidikans' => Pendidikans::where('id_user', $id)->with(['jenjang', 'dokumenIjazah', 'dokumenTranskipNilai'])->get(),
        ];

        return view('karyawan.pendidikan.index', $data);
    }
}
