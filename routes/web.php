<?php

use App\Http\Controllers\Admin\PengajuanAkun;
use App\Http\Controllers\Auth;
use App\Http\Controllers\Dashboard;
use App\Http\Controllers\Register;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth as FacadesAuth;


Route::get('/', function () {
    $user = FacadesAuth::user();
    if (!$user) {
        return redirect('login')->with('message', 'Silakan login terlebih dahulu');
    }
    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    } else if ($user->role === 'dosen') {
        return redirect()->route('dosen.dashboard');
    } else if ($user->role === 'karyawan') {
        return redirect()->route('karyawan.dashboard');
    } else {
        return redirect('login')->with('message', 'Silakan login terlebih dahulu');
    }
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->as('admin.')->group(function () {
    Route::get('/', [Dashboard::class, 'admin'])->name('dashboard');
    Route::get('/akun', [PengajuanAkun::class, 'index'])->name('pengajuan.akun');
    Route::get('/akun/{id}', [PengajuanAkun::class, 'show'])->name('pengajuan.akun.show');
});
Route::middleware(['auth', 'role:dosen'])->prefix('dosen')->as('dosen.')->group(function () {
    Route::get('/', [Dashboard::class, 'dosen'])->name('dashboard');
});
Route::middleware(['auth', 'role:karyawan'])->prefix('karyawan')->as('karyawan.')->group(function () {
    Route::get('/', [Dashboard::class, 'karyawan'])->name('dashboard');
});



// login
Route::get('/login', [Auth::class, 'index'])->middleware('guest')->name('login');
Route::post('/login', [Auth::class, 'store'])->name('auth.login');

// logout
Route::get('/logout', [Auth::class, 'destroy'])->name('auth.logout');

// register
Route::get('/register', [Register::class, 'index'])->middleware('guest')->name('register');
Route::get('/register/dosen', [Register::class, 'dosen'])->middleware('guest')->name('register.dosen');
Route::get('/register/karyawan', [Register::class, 'karyawan'])->middleware('guest')->name('register.karyawan');
Route::post('/register/dosen', [Register::class, 'storeDosen'])->middleware('guest')->name('register.dosen.store');
Route::post('/register/karyawan', [Register::class, 'storeKaryawan'])->middleware('guest')->name('register.karyawan.store');

Route::get('/check-email', function (Illuminate\Http\Request $request) {
    $email = $request->query('email');
    $exists = \App\Models\User::where('email', $email)->exists();

    return response()->json(['unique' => !$exists]);
});
