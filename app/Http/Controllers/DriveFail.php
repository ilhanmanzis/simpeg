<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DriveFail extends Controller
{
    public function driveFail()
    {
        // Ambil pesan dari session (jika ada)
        $message = session('error') ?? 'Sistem belum terhubung dengan Google Drive.';
        return view('failDrive', [
            'title'   => 'Integrasi Google Drive Diperlukan',
            'message' => $message,
            'isAdmin' => Auth::check() && in_array(Auth::user()->role ?? null, ['admin']),
        ]);
    }
}
