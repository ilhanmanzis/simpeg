<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Hash;

class Auth extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = [
            'page' => 'Login',
            'selected' => 'Login',
            'title' => 'Login',
            'setting' => Settings::first()

        ];
        return view('auth.login', $data);
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

        $login = $request->input('email'); // Bisa npp atau email
        $password = $request->input('password');

        $user = User::with('dataDiri')
            ->where(function ($query) use ($login) {
                $query->where('email', $login)
                    ->orWhere('npp', $login);
            })
            ->first();




        if ($user && Hash::check($password, $user->password)) {
            FacadesAuth::login($user);
            $request->session()->regenerate();


            if ($user->status_keaktifan === 'nonaktif') {
                FacadesAuth::logout();
                return redirect()->route('login')->with('message', 'Akun anda telah di nonaktifkan, silahkan hubungi Admin untuk mengaktifkan akun anda.');
            }
            // Tentukan rute tujuan berdasar role
            $route = match ($user->role) {
                'admin'    => 'admin.dashboard',
                'dosen'    => 'dosen.dashboard',
                'karyawan' => 'karyawan.dashboard',
                default    => null,
            };

            if (!$route) {
                FacadesAuth::logout();
                return redirect()->route('login')->with('message', 'Role tidak dikenal.');
            }



            // 👉 MINTA BROWSER HAPUS CACHE
            return redirect()->route($route)->withHeaders([
                // Hapus HTTP cache, Cache Storage, dan SW
                'Clear-Site-Data' => '"cache", "storage", "executionContexts"',
            ]);
        } else {

            return redirect()->route('login')->with('message', 'Email/NPP dan password salah');
        }
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

        return redirect()->route('login');
    }
}
