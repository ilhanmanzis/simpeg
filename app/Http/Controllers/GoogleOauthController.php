<?php

namespace App\Http\Controllers;

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

        // penting agar dapat refresh_token
        $c->setAccessType('offline');
        $c->setPrompt('consent select_account');

        $c->setScopes(config('services.google_drive.scopes')); // drive.file
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

        $client = $this->newClient();
        $token  = $client->fetchAccessTokenWithAuthCode($request->query('code'));

        if (isset($token['error'])) {
            return redirect('/')->with('error', 'Google OAuth gagal: ' . ($token['error_description'] ?? $token['error']));
        }

        // struktur token yg konsisten dg service
        $payload = [
            'access_token'  => $token['access_token'] ?? null,
            'expires_in'    => (int)($token['expires_in'] ?? 3600),
            'refresh_token' => $token['refresh_token']
                ?? (Cache::get('gdrive:refresh_token') ?? config('services.google_drive.refresh_token')),
            'created'       => time(),
        ];

        // Simpan access token (TTL pendek, normal)
        Cache::put('gdrive:token', $payload, max(60, $payload['expires_in'] - 120));

        // SIMPAN refresh token SECARA PERSISTEN (tanpa TTL)
        if (!empty($payload['refresh_token'])) {
            Cache::forever('gdrive:refresh_token', $payload['refresh_token']);
        }

        return redirect('/')->with('success', 'Google Drive terhubung.');
    }
}
