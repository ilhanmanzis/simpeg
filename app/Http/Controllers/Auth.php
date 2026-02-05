<?php

namespace App\Http\Controllers;

use App\Models\DataDiri;
use App\Models\Settings;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class Auth extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settings = Settings::first();

        return view('auth.login', [
            'page'     => 'Login',
            'selected' => 'Login',
            'title'    => 'Login',
            'setting'  => $settings,
        ]);
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
        $request->validate([
            'email' => 'required|max:255',
            'password' => 'required',
        ], [
            'email.required' => 'email/npp tidak boleh kosong',
            'password.required' => 'password tidak boleh kosong',
        ]);

        // Throttle (5 kali percobaan/menit per IP+login)
        $throttleKey = Str::lower($request->input('email')) . '|' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            throw ValidationException::withMessages([
                'email' => ['Terlalu banyak percobaan. Coba lagi dalam beberapa saat.'],
            ]);
        }

        $login    = $request->input('email'); // bisa email atau NPP
        $password = $request->input('password');

        // Coba autentikasi by email lalu by npp
        $ok = FacadesAuth::attempt(['email' => $login, 'password' => $password])
            || FacadesAuth::attempt(['npp'   => $login, 'password' => $password]);

        if (! $ok) {
            RateLimiter::hit($throttleKey, 60); // reset 1 menit
            return back()->with('message', 'Email/NPP dan password salah');
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();
        $user = FacadesAuth::user();

        if ($user->status_keaktifan === 'nonaktif') {
            FacadesAuth::logout();
            return redirect()->route('login')
                ->with('message', 'Akun anda telah di nonaktifkan, silahkan hubungi Admin untuk mengaktifkan akun anda.');
        }

        $route = match ($user->role) {
            'admin'    => 'admin.dashboard',
            'dosen'    => 'dosen.dashboard',
            'karyawan' => 'karyawan.dashboard',
            default    => null,
        };

        if (! $route) {
            FacadesAuth::logout();
            return redirect()->route('login')->with('message', 'Role tidak dikenal.');
        }

        return redirect()->route($route);
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
    public function destroy(Request $request)
    {
        FacadesAuth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Bersihkan cache/storage saat KELUAR, bukan saat login
        return redirect()->route('login')->withHeaders([
            'Clear-Site-Data' => '"cache", "storage", "executionContexts"',
        ]);
    }

    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $exists = User::where('email', $request->email)->exists();

        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'Email sudah terdaftar.' : 'Email tersedia.',
        ]);
    }

    public function checkNik(Request $request)
    {
        $request->validate([
            'nik' => ['required', 'digits:16']
        ]);

        $exists = DataDiri::where('no_ktp', $request->nik)->exists();

        return response()->json(['exists' => $exists]);
    }

    public function checkNpp(Request $request)
    {
        $request->validate(['npp' => 'required|string']);
        $exists = User::where('npp', $request->npp)->exists();
        return response()->json(['exists' => $exists]);
    }
}
