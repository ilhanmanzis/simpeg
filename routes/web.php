<?php

use App\Http\Controllers\Admin\Golongan;
use App\Http\Controllers\Admin\JabatanFungsional;
use App\Http\Controllers\Admin\JabatanStruktural;
use App\Http\Controllers\Admin\Jenjang;
use App\Http\Controllers\Admin\KategoriSertifikat;
use App\Http\Controllers\Admin\PengajuanAkun;
use App\Http\Controllers\Admin\Semester;
use App\Http\Controllers\Auth;
use App\Http\Controllers\Dashboard;
use App\Http\Controllers\FileController;

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
    // Dashboard
    Route::get('/', [Dashboard::class, 'admin'])->name('dashboard');

    // pengajuan akun
    Route::get('/akun', [PengajuanAkun::class, 'index'])->name('pengajuan.akun');
    Route::get('/akun/{id}', [PengajuanAkun::class, 'show'])->name('pengajuan.akun.show');
    Route::put('/akun/setuju/{id}', [PengajuanAkun::class, 'setuju'])->name('pengajuan.akun.setuju');
    Route::put('/akun/tolak/{id}', [PengajuanAkun::class, 'tolak'])->name('pengajuan.akun.tolak');

    // golongan
    Route::get('/golongan', [Golongan::class, 'index'])->name('golongan');
    Route::get('/golongan/create', [Golongan::class, 'create'])->name('golongan.create');
    Route::post('/golongan/store', [Golongan::class, 'store'])->name('golongan.store');
    Route::get('/golongan/{id}', [Golongan::class, 'edit'])->name('golongan.edit');
    Route::put('/golongan/{id}', [Golongan::class, 'update'])->name('golongan.update');
    Route::delete('/golongan/{id}', [Golongan::class, 'destroy'])->name('golongan.delete');

    // jabatan fungsional
    Route::get('/fungsional', [JabatanFungsional::class, 'index'])->name('fungsional');
    Route::get('/fungsional/create', [JabatanFungsional::class, 'create'])->name('fungsional.create');
    Route::post('/fungsional/store', [JabatanFungsional::class, 'store'])->name('fungsional.store');
    Route::get('/fungsional/{id}', [JabatanFungsional::class, 'edit'])->name('fungsional.edit');
    Route::put('/fungsional/{id}', [JabatanFungsional::class, 'update'])->name('fungsional.update');
    Route::delete('/fungsional/{id}', [JabatanFungsional::class, 'destroy'])->name('fungsional.delete');

    // jabatan struktural
    Route::get('/struktural', [JabatanStruktural::class, 'index'])->name('struktural');
    Route::get('/struktural/create', [JabatanStruktural::class, 'create'])->name('struktural.create');
    Route::post('/struktural/store', [JabatanStruktural::class, 'store'])->name('struktural.store');
    Route::get('/struktural/{id}', [JabatanStruktural::class, 'edit'])->name('struktural.edit');
    Route::put('/struktural/{id}', [JabatanStruktural::class, 'update'])->name('struktural.update');
    Route::delete('/struktural/{id}', [JabatanStruktural::class, 'destroy'])->name('struktural.delete');

    // kategori sertifikat
    Route::get('/kategorisertifikat', [KategoriSertifikat::class, 'index'])->name('kategorisertifikat');
    Route::get('/kategorisertifikat/create', [KategoriSertifikat::class, 'create'])->name('kategorisertifikat.create');
    Route::post('/kategorisertifikat/store', [KategoriSertifikat::class, 'store'])->name('kategorisertifikat.store');
    Route::get('/kategorisertifikat/{id}', [KategoriSertifikat::class, 'edit'])->name('kategorisertifikat.edit');
    Route::put('/kategorisertifikat/{id}', [KategoriSertifikat::class, 'update'])->name('kategorisertifikat.update');
    Route::delete('/kategorisertifikat/{id}', [KategoriSertifikat::class, 'destroy'])->name('kategorisertifikat.delete');

    // jenjang
    Route::get('/jenjang', [Jenjang::class, 'index'])->name('jenjang');
    Route::get('/jenjang/create', [Jenjang::class, 'create'])->name('jenjang.create');
    Route::post('/jenjang/store', [Jenjang::class, 'store'])->name('jenjang.store');
    Route::get('/jenjang/{id}', [Jenjang::class, 'edit'])->name('jenjang.edit');
    Route::put('/jenjang/{id}', [Jenjang::class, 'update'])->name('jenjang.update');
    Route::delete('/jenjang/{id}', [Jenjang::class, 'destroy'])->name('jenjang.delete');

    // semester
    Route::get('/semester', [Semester::class, 'index'])->name('semester');
    Route::get('/semester/create', [Semester::class, 'create'])->name('semester.create');
    Route::post('/semester/store', [Semester::class, 'store'])->name('semester.store');
    Route::get('/semester/{id}', [Semester::class, 'edit'])->name('semester.edit');
    Route::put('/semester/{id}', [Semester::class, 'update'])->name('semester.update');
    Route::delete('/semester/{id}', [Semester::class, 'destroy'])->name('semester.delete');
});
Route::middleware(['auth', 'role:dosen'])->prefix('dosen')->as('dosen.')->group(function () {
    Route::get('/', [Dashboard::class, 'dosen'])->name('dashboard');
});
Route::middleware(['auth', 'role:karyawan'])->prefix('karyawan')->as('karyawan.')->group(function () {
    Route::get('/', [Dashboard::class, 'karyawan'])->name('dashboard');
});

Route::middleware(['auth', 'role:admin,dosen,karyawan'])->group(function () {
    Route::get('/file/ijazah/{filename}', [FileController::class, 'showIjazah'])->name('file.ijazah');
    Route::get('/file/transkip/{filename}', [FileController::class, 'showTranskip'])->name('file.transkip');
    Route::get('/file/foto/{filename}', [FileController::class, 'showFoto'])->name('file.foto');
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
