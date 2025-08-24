<?php

namespace App\Http\Controllers;

use App\Services\GoogleDriveService;
use Google_Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class GoogleOauthController extends Controller
{
    private function newClient(): Google_Client
    {
        $c = new Google_Client();
        $c->setClientId(config('services.google_drive.client_id'));
        $c->setClientSecret(config('services.google_drive.client_secret'));
        $c->setRedirectUri(config('services.google_drive.redirect'));
        $c->setAccessType('offline');
        $c->setPrompt('consent');
        $c->setScopes(config('services.google_drive.scopes'));
        return $c;
    }

    public function redirect()
    {
        return redirect()->away($this->newClient()->createAuthUrl());
    }

    public function callback(Request $request)
    {
        if ($request->has('error')) {
            return redirect('/')->with('error', 'Google OAuth dibatalkan: ' . $request->query('error'));
        }
        if (!$request->has('code')) {
            return redirect('/')->with('error', 'Google OAuth: code tidak ditemukan.');
        }

        // (opsional) verifikasi state jika kamu aktifkan di redirect()
        // ...

        $client = $this->newClient();
        $token  = $client->fetchAccessTokenWithAuthCode($request->query('code'));

        if (isset($token['error'])) {
            return redirect('/')->with('error', 'Google OAuth gagal: ' . ($token['error_description'] ?? $token['error']));
        }

        $now       = now();
        $expiresIn = (int)($token['expires_in'] ?? 3600);
        $expiresAt = $now->copy()->addSeconds($expiresIn)->timestamp;
        $ttlSec    = max($expiresIn - 120, 60);

        // Simpan access_token ke cache
        Cache::put('google_drive_access_token', [
            'access_token' => $token['access_token'] ?? null,
            'expires_at'   => $expiresAt,
        ], $ttlSec);

        // Ambil refresh token: pakai yang baru kalau ada; kalau tidak ada, fallback ke env
        $refreshToken = $token['refresh_token']
            ?? Cache::get('google_drive_refresh_token')
            ?? config('services.google_drive.refresh_token');

        if (!empty($refreshToken)) {
            // simpan ke cache agar service selalu punya yang terbaru
            Cache::forever('google_drive_refresh_token', $refreshToken);
        }

        return redirect('/')->with('success', 'Google Drive terhubung.');
    }
}
