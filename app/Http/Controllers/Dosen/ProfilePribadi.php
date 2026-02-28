<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
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

        $dosen = User::where('id_user', $id)->with(['dataDiri.serdosen'])->first();

        $data = [
            'page' => 'Profile Pribadi',
            'selected' => 'Profile Pribadi',
            'title' => 'Data Profile Pribadi',
            'dosen' => $dosen
        ];

        return view('dosen.profile.index', $data);
    }
}
