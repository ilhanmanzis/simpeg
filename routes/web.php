<?php

use App\Http\Controllers\Admin\FungsionalUser;
use App\Http\Controllers\Admin\Golongan;
use App\Http\Controllers\Admin\GolonganUser;
use App\Http\Controllers\Admin\JabatanFungsional;
use App\Http\Controllers\Admin\JabatanStruktural;
use App\Http\Controllers\Admin\Jenjang;
use App\Http\Controllers\Admin\KategoriSertifikat;
use App\Http\Controllers\Admin\PegawaiDosen;
use App\Http\Controllers\Admin\PegawaiKaryawan;
use App\Http\Controllers\Admin\PengajuanAkun;
use App\Http\Controllers\Admin\PengajuanFungsional as AdminPengajuanFungsional;
use App\Http\Controllers\Admin\PengajuanGolongan as AdminPengajuanGolongan;
use App\Http\Controllers\Admin\PengajuanPendikan as AdminPengajuanPendikan;
use App\Http\Controllers\Admin\PengajuanProfilePribadi as AdminPengajuanProfilePribadi;
use App\Http\Controllers\Admin\Semester;
use App\Http\Controllers\Admin\StrukturalUser;
use App\Http\Controllers\Auth;
use App\Http\Controllers\Dashboard;
use App\Http\Controllers\Dosen\Fungsional;
use App\Http\Controllers\Dosen\Golongan as DosenGolongan;
use App\Http\Controllers\Dosen\Pendidikan;
use App\Http\Controllers\Dosen\PengajuanFungsional;
use App\Http\Controllers\Dosen\PengajuanGolongan;
use App\Http\Controllers\Dosen\PengajuanPendikan;
use App\Http\Controllers\Dosen\PengajuanProfilePribadi;
use App\Http\Controllers\Dosen\ProfilePribadi;
use App\Http\Controllers\Dosen\Struktural;
use App\Http\Controllers\FileController;
use App\Http\Controllers\Karyawan\Pendidikan as KaryawanPendidikan;
use App\Http\Controllers\Karyawan\PengajuanPendidikan;
use App\Http\Controllers\Karyawan\PengajuanProfilePribadi as KaryawanPengajuanProfilePribadi;
use App\Http\Controllers\Karyawan\ProfilePribadi as KaryawanProfilePribadi;
use App\Http\Controllers\ManajemenUser;
use App\Http\Controllers\Register;
use App\Models\StrukturalUsers;
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

    // dosen
    Route::get('/dosen', [PegawaiDosen::class, 'index'])->name('dosen');
    Route::get('/dosen/{id}/show', [PegawaiDosen::class, 'show'])->name('dosen.show');
    Route::get('/dosen/create', [PegawaiDosen::class, 'create'])->name('dosen.create');
    Route::post('/dosen/store', [PegawaiDosen::class, 'store'])->name('dosen.store');
    Route::get('/dosen/{id}/datadiri', [PegawaiDosen::class, 'dataDiri'])->name('dosen.datadiri');
    Route::put('/dosen/{id}/datadiri', [PegawaiDosen::class, 'dataDiriUpdate'])->name('dosen.datadiri.update');
    Route::get('/dosen/{id}/pendidikan/{idPendidikan}', [PegawaiDosen::class, 'pendidikan'])->name('dosen.pendidikan');
    Route::put('/dosen/{id}/pendidikan/{idPendidikan}', [PegawaiDosen::class, 'pendidikanUpdate'])->name('dosen.pendidikan.update');
    Route::get('/dosen/{id}/creatependidikan', [PegawaiDosen::class, 'createPendidikan'])->name('dosen.pendidikan.create');
    Route::post('/dosen/{id}/creatependidikan', [PegawaiDosen::class, 'storePendidikan'])->name('dosen.pendidikan.store');
    Route::delete('/dosen/{id}/pendidikan/{idPendidikan}', [PegawaiDosen::class, 'deletePendidikan'])->name('dosen.pendidikan.delete');
    Route::get('/dosen/{id}/password', [PegawaiDosen::class, 'password'])->name('dosen.password');
    Route::put('/dosen/{id}/password', [PegawaiDosen::class, 'passwordUpdate'])->name('dosen.password.update');
    Route::get('/dosen/{id}/npp', [PegawaiDosen::class, 'npp'])->name('dosen.npp');
    Route::put('/dosen/{id}/npp', [PegawaiDosen::class, 'nppUpdate'])->name('dosen.npp.update');
    Route::put('/dosen/{id}/status', [PegawaiDosen::class, 'status'])->name('dosen.status');
    Route::delete('/dosen/{id}', [PegawaiDosen::class, 'destroy'])->name('dosen.destroy');

    // karyawan
    Route::get('/karyawan', [PegawaiKaryawan::class, 'index'])->name('karyawan');
    Route::get('/karyawan/{id}/show', [PegawaiKaryawan::class, 'show'])->name('karyawan.show');
    Route::get('/karyawan/create', [PegawaiKaryawan::class, 'create'])->name('karyawan.create');
    Route::post('/karyawan/store', [PegawaiKaryawan::class, 'store'])->name('karyawan.store');
    Route::get('/karyawan/{id}/datadiri', [PegawaiKaryawan::class, 'dataDiri'])->name('karyawan.datadiri');
    Route::put('/karyawan/{id}/datadiri', [PegawaiKaryawan::class, 'dataDiriUpdate'])->name('karyawan.datadiri.update');
    Route::get('/karyawan/{id}/pendidikan/{idPendidikan}', [PegawaiKaryawan::class, 'pendidikan'])->name('karyawan.pendidikan');
    Route::put('/karyawan/{id}/pendidikan/{idPendidikan}', [PegawaiKaryawan::class, 'pendidikanUpdate'])->name('karyawan.pendidikan.update');
    Route::get('/karyawan/{id}/creatependidikan', [PegawaiKaryawan::class, 'createPendidikan'])->name('karyawan.pendidikan.create');
    Route::post('/karyawan/{id}/creatependidikan', [PegawaiKaryawan::class, 'storePendidikan'])->name('karyawan.pendidikan.store');
    Route::delete('/karyawan/{id}/pendidikan/{idPendidikan}', [PegawaiKaryawan::class, 'deletePendidikan'])->name('karyawan.pendidikan.delete');
    Route::get('/karyawan/{id}/password', [PegawaiKaryawan::class, 'password'])->name('karyawan.password');
    Route::put('/karyawan/{id}/password', [PegawaiKaryawan::class, 'passwordUpdate'])->name('karyawan.password.update');
    Route::get('/karyawan/{id}/npp', [PegawaiKaryawan::class, 'npp'])->name('karyawan.npp');
    Route::put('/karyawan/{id}/npp', [PegawaiKaryawan::class, 'nppUpdate'])->name('karyawan.npp.update');
    Route::put('/karyawan/{id}/status', [PegawaiKaryawan::class, 'status'])->name('karyawan.status');
    Route::delete('/karyawan/{id}', [PegawaiKaryawan::class, 'destroy'])->name('karyawan.destroy');


    // pengajuan perubahan profile pribadi
    Route::get('/perubahan-profile', [AdminPengajuanProfilePribadi::class, 'index'])->name('pengajuan.profile');
    Route::get('/perubahan-profile/{id}', [AdminPengajuanProfilePribadi::class, 'show'])->name('pengajuan.profile.show');
    Route::put('/perubahan-profile/setuju/{id}', [AdminPengajuanProfilePribadi::class, 'setuju'])->name('pengajuan.profile.setuju');
    Route::put('/perubahan-profile/tolak/{id}', [AdminPengajuanProfilePribadi::class, 'tolak'])->name('pengajuan.profile.tolak');

    // pengajuan perubahan pendidikan
    Route::get('/perubahan-pendidikan', [AdminPengajuanPendikan::class, 'index'])->name('pengajuan.pendidikan');
    Route::get('/perubahan-pendidikan/{id}', [AdminPengajuanPendikan::class, 'show'])->name('pengajuan.pendidikan.show');
    Route::get('/perubahan-pendidikan/{id}/riwayat', [AdminPengajuanPendikan::class, 'riwayat'])->name('pengajuan.pendidikan.riwayat');
    Route::put('/perubahan-pendidikan/setuju/{id}', [AdminPengajuanPendikan::class, 'setuju'])->name('pengajuan.pendidikan.setuju');
    Route::put('/perubahan-pendidikan/tolak/{id}', [AdminPengajuanPendikan::class, 'tolak'])->name('pengajuan.pendidikan.tolak');


    // golongan
    Route::get('/jabatan/golongan', [GolonganUser::class, 'index'])->name('jabatan.golongan');
    Route::get('/jabatan/golongan/{id}', [GolonganUser::class, 'show'])->name('jabatan.golongan.show');
    Route::get('/jabatan/golongan/{id}/mutasi', [GolonganUser::class, 'mutasi'])->name('jabatan.golongan.mutasi');
    Route::post('/jabatan/golongan/{id}/mutasi', [GolonganUser::class, 'mutasiStore'])->name('jabatan.golongan.mutasi.store');
    Route::delete('/jabatan/golongan/{id}', [GolonganUser::class, 'destroy'])->name('jabatan.golongan.mutasi.delete');

    // jabatan fungsional
    Route::get('/jabatan/fungsional', [FungsionalUser::class, 'index'])->name('jabatan.fungsional');
    Route::get('/jabatan/fungsional/{id}', [FungsionalUser::class, 'show'])->name('jabatan.fungsional.show');
    Route::get('/jabatan/fungsional/{id}/mutasi', [FungsionalUser::class, 'mutasi'])->name('jabatan.fungsional.mutasi');
    Route::post('/jabatan/fungsional/{id}/mutasi', [FungsionalUser::class, 'mutasiStore'])->name('jabatan.fungsional.mutasi.store');
    Route::delete('/jabatan/fungsional/{id}', [FungsionalUser::class, 'destroy'])->name('jabatan.fungsional.mutasi.delete');

    // jabatan struktural
    Route::get('/jabatan/struktural', [StrukturalUser::class, 'index'])->name('jabatan.struktural');
    Route::get('/jabatan/struktural/{id}', [StrukturalUser::class, 'show'])->name('jabatan.struktural.show');
    Route::get('/jabatan/struktural/{id}/mutasi', [StrukturalUser::class, 'mutasi'])->name('jabatan.struktural.mutasi');
    Route::post('/jabatan/struktural/{id}/mutasi', [StrukturalUser::class, 'mutasiStore'])->name('jabatan.struktural.mutasi.store');
    Route::delete('/jabatan/struktural/{id}', [StrukturalUser::class, 'destroy'])->name('jabatan.struktural.mutasi.delete');


    // pengajuan kenaikan golongan
    Route::get('/kenaikan-golongan', [AdminPengajuanGolongan::class, 'index'])->name('pengajuan.golongan');
    Route::get('/kenaikan-golongan/{id}', [AdminPengajuanGolongan::class, 'show'])->name('pengajuan.golongan.show');
    Route::get('/kenaikan-golongan/{id}/riwayat', [AdminPengajuanGolongan::class, 'riwayat'])->name('pengajuan.golongan.riwayat');
    Route::put('/kenaikan-golongan/setuju/{id}', [AdminPengajuanGolongan::class, 'setuju'])->name('pengajuan.golongan.setuju');
    Route::put('/kenaikan-golongan/tolak/{id}', [AdminPengajuanGolongan::class, 'tolak'])->name('pengajuan.golongan.tolak');

    // pengajuan kenaikan fungsional
    Route::get('/kenaikan-fungsional', [AdminPengajuanFungsional::class, 'index'])->name('pengajuan.fungsional');
    Route::get('/kenaikan-fungsional/{id}', [AdminPengajuanFungsional::class, 'show'])->name('pengajuan.fungsional.show');
    Route::get('/kenaikan-fungsional/{id}/riwayat', [AdminPengajuanFungsional::class, 'riwayat'])->name('pengajuan.fungsional.riwayat');
    Route::put('/kenaikan-fungsional/setuju/{id}', [AdminPengajuanFungsional::class, 'setuju'])->name('pengajuan.fungsional.setuju');
    Route::put('/kenaikan-fungsional/tolak/{id}', [AdminPengajuanFungsional::class, 'tolak'])->name('pengajuan.fungsional.tolak');
});



// dosen
Route::middleware(['auth', 'role:dosen'])->prefix('dosen')->as('dosen.')->group(function () {
    Route::get('/', [Dashboard::class, 'dosen'])->name('dashboard');

    // profile pribadi
    Route::get('/profile', [ProfilePribadi::class, 'index'])->name('profilepribadi');

    // pengajuan perubahan profile pribadi
    Route::get('/perubahan-profile', [PengajuanProfilePribadi::class, 'index'])->name('pengajuan.profile');
    Route::get('/perubahan-profile/create', [PengajuanProfilePribadi::class, 'create'])->name('pengajuan.profile.create');
    Route::post('/perubahan-profile/store', [PengajuanProfilePribadi::class, 'store'])->name('pengajuan.profile.store');
    Route::get('/perubahan-profile/{id}', [PengajuanProfilePribadi::class, 'show'])->name('pengajuan.profile.show');

    // pengajuan perubahan Pendidikan
    Route::get('/perubahan-pendidikan', [PengajuanPendikan::class, 'index'])->name('pengajuan.pendidikan');
    Route::get('/perubahan-pendidikan/create', [PengajuanPendikan::class, 'create'])->name('pengajuan.pendidikan.create');
    Route::post('/perubahan-pendidikan/store', [PengajuanPendikan::class, 'store'])->name('pengajuan.pendidikan.store');
    Route::get('/perubahan-pendidikan/pendidikan/{id}', [PengajuanPendikan::class, 'edit'])->name('pengajuan.pendidikan.edit');
    Route::put('/perubahan-pendidikan/pendidikan/{id}', [PengajuanPendikan::class, 'update'])->name('pengajuan.pendidikan.update');
    Route::delete('/perubahan-pendidikan/pendidikan/{id}', [PengajuanPendikan::class, 'destroy'])->name('pengajuan.pendidikan.delete');
    Route::get('/perubahan-pendidikan/{id}', [PengajuanPendikan::class, 'show'])->name('pengajuan.pendidikan.show');

    Route::get('/pendidikan', [Pendidikan::class, 'index'])->name('pendidikan');

    // golongan
    Route::get('/jabatan/golongan', [DosenGolongan::class, 'index'])->name('jabatan.golongan');
    // jabatan fungsioal
    Route::get('/jabatan/fungsional', [Fungsional::class, 'index'])->name('jabatan.fungsional');
    // jabatan struktural
    Route::get('/jabatan/struktural', [Struktural::class, 'index'])->name('jabatan.struktural');

    // kenaikan golongan
    Route::get('/kenaikan-golongan', [PengajuanGolongan::class, 'index'])->name('pengajuan.golongan');
    Route::get('/kenaikan-golongan/create', [PengajuanGolongan::class, 'create'])->name('pengajuan.golongan.create');
    Route::post('/kenaikan-golongan/store', [PengajuanGolongan::class, 'store'])->name('pengajuan.golongan.store');
    Route::get('/kenaikan-golongan/{id}', [PengajuanGolongan::class, 'show'])->name('pengajuan.golongan.show');

    // kenaikan fungsional
    Route::get('/kenaikan-fungsional', [PengajuanFungsional::class, 'index'])->name('pengajuan.fungsional');
    Route::get('/kenaikan-fungsional/create', [PengajuanFungsional::class, 'create'])->name('pengajuan.fungsional.create');
    Route::post('/kenaikan-fungsional/store', [PengajuanFungsional::class, 'store'])->name('pengajuan.fungsional.store');
    Route::get('/kenaikan-fungsional/{id}', [PengajuanFungsional::class, 'show'])->name('pengajuan.fungsional.show');
});


// karyawan
Route::middleware(['auth', 'role:karyawan'])->prefix('karyawan')->as('karyawan.')->group(function () {
    Route::get('/', [Dashboard::class, 'karyawan'])->name('dashboard');

    // profile pribadi
    Route::get('/profile', [KaryawanProfilePribadi
    ::class, 'index'])->name('profilepribadi');

    // pengajuan perubahan profile pribadi
    Route::get('/perubahan-profile', [KaryawanPengajuanProfilePribadi::class, 'index'])->name('pengajuan.profile');
    Route::get('/perubahan-profile/create', [KaryawanPengajuanProfilePribadi::class, 'create'])->name('pengajuan.profile.create');
    Route::post('/perubahan-profile/store', [KaryawanPengajuanProfilePribadi::class, 'store'])->name('pengajuan.profile.store');
    Route::get('/perubahan-profile/{id}', [KaryawanPengajuanProfilePribadi::class, 'show'])->name('pengajuan.profile.show');

    // pengajuan perubahan Pendidikan
    Route::get('/perubahan-pendidikan', [PengajuanPendidikan::class, 'index'])->name('pengajuan.pendidikan');
    Route::get('/perubahan-pendidikan/create', [PengajuanPendidikan::class, 'create'])->name('pengajuan.pendidikan.create');
    Route::post('/perubahan-pendidikan/store', [PengajuanPendidikan::class, 'store'])->name('pengajuan.pendidikan.store');
    Route::get('/perubahan-pendidikan/pendidikan/{id}', [PengajuanPendidikan::class, 'edit'])->name('pengajuan.pendidikan.edit');
    Route::put('/perubahan-pendidikan/pendidikan/{id}', [PengajuanPendidikan::class, 'update'])->name('pengajuan.pendidikan.update');
    Route::delete('/perubahan-pendidikan/pendidikan/{id}', [PengajuanPendidikan::class, 'destroy'])->name('pengajuan.pendidikan.delete');
    Route::get('/perubahan-pendidikan/{id}', [PengajuanPendidikan::class, 'show'])->name('pengajuan.pendidikan.show');

    Route::get('/pendidikan', [KaryawanPendidikan::class, 'index'])->name('pendidikan');
});




// get file
Route::middleware(['auth', 'role:admin,dosen,karyawan'])->group(function () {

    // edit profile
    Route::get('/users/edit', [ManajemenUser::class, 'index'])->name('users.index');
    Route::put('/users/edit', [ManajemenUser::class, 'update'])->name('users.update');

    Route::get('/file/ijazah/{filename}', [FileController::class, 'showIjazah'])->name('file.ijazah');
    Route::get('/file/sk/{filename}', [FileController::class, 'showSk'])->name('file.sk');
    Route::get('/file/transkip/{filename}', [FileController::class, 'showTranskip'])->name('file.transkip');
    Route::get('/file/foto/{filename}', [FileController::class, 'showFoto'])->name('file.foto');
    Route::get('/file/fotodrive/{id}', [FileController::class, 'showFotoDrive'])->name('file.foto.drive');
    Route::get('/file/fotoperubahan/{id}', [FileController::class, 'showFotoPerubahan'])->name('file.foto.perubahan');
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
