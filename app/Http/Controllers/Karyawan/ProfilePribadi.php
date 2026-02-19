<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\GoogleDriveService;
use Illuminate\Support\Facades\Auth;

class ProfilePribadi extends Controller
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

        $karyawan = User::where('id_user', $id)->with(['dataDiri'])->first();

        $data = [
            'page' => 'Profile Pribadi',
            'selected' => 'Profile Pribadi',
            'title' => 'Data Profile Pribadi',
            'karyawan' => $karyawan
        ];

        return view('karyawan.profile.index', $data);
    }
}
