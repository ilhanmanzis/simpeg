<?php

use App\Http\Controllers\Admin\FungsionalUser;
use App\Http\Controllers\Admin\Golongan;
use App\Http\Controllers\Admin\GolonganUser;
use App\Http\Controllers\Admin\JabatanFungsional;
use App\Http\Controllers\Admin\JabatanStruktural;
use App\Http\Controllers\Admin\Jenjang;
use App\Http\Controllers\Admin\Laporan;
use App\Http\Controllers\Admin\PegawaiDosen;
use App\Http\Controllers\Admin\PegawaiKaryawan;
use App\Http\Controllers\Admin\Penelitian as AdminPenelitian;
use App\Http\Controllers\Admin\Pengabdian as AdminPengabdian;
use App\Http\Controllers\Admin\Pengajaran as AdminPengajaran;
use App\Http\Controllers\Admin\PengajuanAkun;
use App\Http\Controllers\Admin\PengajuanFungsional as AdminPengajuanFungsional;
use App\Http\Controllers\Admin\PengajuanGolongan as AdminPengajuanGolongan;
use App\Http\Controllers\Admin\PengajuanPendikan as AdminPengajuanPendikan;
use App\Http\Controllers\Admin\PengajuanPenelitian;
use App\Http\Controllers\Admin\PengajuanPengabdian;
use App\Http\Controllers\Admin\PengajuanPengajaran;
use App\Http\Controllers\Admin\PengajuanPenunjang;
use App\Http\Controllers\Admin\PengajuanProfilePribadi as AdminPengajuanProfilePribadi;
use App\Http\Controllers\Admin\PengajuanSerdos as AdminPengajuanSerdos;
use App\Http\Controllers\Admin\PengajuanSertifikat as AdminPengajuanSertifikat;
use App\Http\Controllers\Admin\Penunjang as AdminPenunjang;
use App\Http\Controllers\Admin\Semester;
use App\Http\Controllers\Admin\Sertifikat as AdminSertifikat;
use App\Http\Controllers\Admin\StrukturalUser;
use App\Http\Controllers\Auth;
use App\Http\Controllers\Dashboard;
use App\Http\Controllers\Dosen\Fungsional;
use App\Http\Controllers\Dosen\Golongan as DosenGolongan;
use App\Http\Controllers\Dosen\Laporan as DosenLaporan;
use App\Http\Controllers\Dosen\Pendidikan;
use App\Http\Controllers\Dosen\Penelitian;
use App\Http\Controllers\Dosen\Pengabdian;
use App\Http\Controllers\Dosen\Pengajaran;
use App\Http\Controllers\Dosen\PengajuanFungsional;
use App\Http\Controllers\Dosen\PengajuanGolongan;
use App\Http\Controllers\Dosen\PengajuanPendikan;
use App\Http\Controllers\Dosen\PengajuanProfilePribadi;
use App\Http\Controllers\Dosen\PengajuanSerdos;
use App\Http\Controllers\Dosen\PengajuanSertifikat;
use App\Http\Controllers\Dosen\Penunjang;
use App\Http\Controllers\Dosen\ProfilePribadi;
use App\Http\Controllers\Dosen\Sertifikat;
use App\Http\Controllers\Dosen\Struktural;
use App\Http\Controllers\DriveFail;
use App\Http\Controllers\FileController;
use App\Http\Controllers\GoogleOauthController;
use App\Http\Controllers\Karyawan\Pendidikan as KaryawanPendidikan;
use App\Http\Controllers\Karyawan\PengajuanPendidikan;
use App\Http\Controllers\Karyawan\PengajuanProfilePribadi as KaryawanPengajuanProfilePribadi;
use App\Http\Controllers\Karyawan\ProfilePribadi as KaryawanProfilePribadi;
use App\Http\Controllers\Karyawan\Sertifikat as KaryawanSertifikat;
use App\Http\Controllers\ManajemenUser;
use App\Http\Controllers\Register;
use App\Http\Controllers\Setting;
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

Route::middleware(['auth', 'role:admin'])->group(function () {
    // OAuth flow
    Route::get('/oauth/google/redirect', [GoogleOauthController::class, 'redirect'])->name('google.oauth.redirect');
    Route::get('/oauth/google/callback', [GoogleOauthController::class, 'callback'])->name('google.oauth.callback');
});
Route::middleware(['auth', 'role:admin'])->prefix('admin')->as('admin.')->group(function () {


    // Dashboard
    Route::get('/', [Dashboard::class, 'admin'])->name('dashboard');

    // pengajuan akun
    Route::get('/akun', [PengajuanAkun::class, 'index'])->name('pengajuan.akun');
    Route::get('/akun/{id}', [PengajuanAkun::class, 'show'])->name('pengajuan.akun.show');
    Route::put('/akun/setuju/{id}', [PengajuanAkun::class, 'setuju'])->name('pengajuan.akun.setuju');
    Route::put('/akun/tolak/{id}', [PengajuanAkun::class, 'tolak'])->name('pengajuan.akun.tolak');

    // laporan
    Route::get('/laporan', [Laporan::class, 'index'])->name('laporan');
    Route::post('/laporan/create', [Laporan::class, 'create'])->name('laporan.create');
    Route::get('/laporan/create/{id}', [Laporan::class, 'individu'])->name('laporan.individu');

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
    Route::get('/dosen/{id}/serdos', [PegawaiDosen::class, 'serdos'])->name('dosen.serdos');
    Route::put('/dosen/{id}/serdos', [PegawaiDosen::class, 'serdosUpdate'])->name('dosen.serdos.update');

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

    // pengajuan bkd penelitian
    Route::get('/pengajuan-penelitian', [PengajuanPenelitian::class, 'index'])->name('pengajuan.penelitian');
    Route::get('/pengajuan-penelitian/{id}', [PengajuanPenelitian::class, 'show'])->name('pengajuan.penelitian.show');
    Route::get('/pengajuan-penelitian/{id}/riwayat', [PengajuanPenelitian::class, 'riwayat'])->name('pengajuan.penelitian.riwayat');
    Route::put('/pengajuan-penelitian/setuju/{id}', [PengajuanPenelitian::class, 'setuju'])->name('pengajuan.penelitian.setuju');
    Route::put('/pengajuan-penelitian/tolak/{id}', [PengajuanPenelitian::class, 'tolak'])->name('pengajuan.penelitian.tolak');

    // pengajuan bkd pengabdian
    Route::get('/pengajuan-pengabdian', [PengajuanPengabdian::class, 'index'])->name('pengajuan.pengabdian');
    Route::get('/pengajuan-pengabdian/{id}', [PengajuanPengabdian::class, 'show'])->name('pengajuan.pengabdian.show');
    Route::get('/pengajuan-pengabdian/{id}/riwayat', [PengajuanPengabdian::class, 'riwayat'])->name('pengajuan.pengabdian.riwayat');
    Route::put('/pengajuan-pengabdian/setuju/{id}', [PengajuanPengabdian::class, 'setuju'])->name('pengajuan.pengabdian.setuju');
    Route::put('/pengajuan-pengabdian/tolak/{id}', [PengajuanPengabdian::class, 'tolak'])->name('pengajuan.pengabdian.tolak');

    // pengajuan bkd penunjang
    Route::get('/pengajuan-penunjang', [PengajuanPenunjang::class, 'index'])->name('pengajuan.penunjang');
    Route::get('/pengajuan-penunjang/{id}', [PengajuanPenunjang::class, 'show'])->name('pengajuan.penunjang.show');
    Route::get('/pengajuan-penunjang/{id}/riwayat', [PengajuanPenunjang::class, 'riwayat'])->name('pengajuan.penunjang.riwayat');
    Route::put('/pengajuan-penunjang/setuju/{id}', [PengajuanPenunjang::class, 'setuju'])->name('pengajuan.penunjang.setuju');
    Route::put('/pengajuan-penunjang/tolak/{id}', [PengajuanPenunjang::class, 'tolak'])->name('pengajuan.penunjang.tolak');

    // pengajuan bkd pengajaran
    Route::get('/pengajuan-pengajaran', [PengajuanPengajaran::class, 'index'])->name('pengajuan.pengajaran');
    Route::get('/pengajuan-pengajaran/{id}', [PengajuanPengajaran::class, 'show'])->name('pengajuan.pengajaran.show');
    Route::get('/pengajuan-pengajaran/{id}/riwayat', [PengajuanPengajaran::class, 'riwayat'])->name('pengajuan.pengajaran.riwayat');
    Route::put('/pengajuan-pengajaran/setuju/{id}', [PengajuanPengajaran::class, 'setuju'])->name('pengajuan.pengajaran.setuju');
    Route::put('/pengajuan-pengajaran/tolak/{id}', [PengajuanPengajaran::class, 'tolak'])->name('pengajuan.pengajaran.tolak');


    // bkd penelitian
    Route::get('/bkd/penelitian', [AdminPenelitian::class, 'index'])->name('bkd.penelitian');
    Route::get('/bkd/penelitian/{id}', [AdminPenelitian::class, 'all'])->name('bkd.penelitian.all');
    Route::get('/bkd/penelitian/{id}/show', [AdminPenelitian::class, 'show'])->name('bkd.penelitian.show');
    Route::delete('/bkd/penelitian/{id}', [AdminPenelitian::class, 'destroy'])->name('bkd.penelitian.delete');

    // bkd pengabdian
    Route::get('/bkd/pengabdian', [AdminPengabdian::class, 'index'])->name('bkd.pengabdian');
    Route::get('/bkd/pengabdian/{id}', [AdminPengabdian::class, 'all'])->name('bkd.pengabdian.all');
    Route::get('/bkd/pengabdian/{id}/show', [AdminPengabdian::class, 'show'])->name('bkd.pengabdian.show');
    Route::delete('/bkd/pengabdian/{id}', [AdminPengabdian::class, 'destroy'])->name('bkd.pengabdian.delete');

    // bkd pengajaran
    Route::get('/bkd/pengajaran', [AdminPengajaran::class, 'index'])->name('bkd.pengajaran');
    Route::get('/bkd/pengajaran/{id}', [AdminPengajaran::class, 'all'])->name('bkd.pengajaran.all');
    Route::get('/bkd/pengajaran/{id}/show', [AdminPengajaran::class, 'show'])->name('bkd.pengajaran.show');
    Route::delete('/bkd/pengajaran/{id}', [AdminPengajaran::class, 'destroy'])->name('bkd.pengajaran.delete');

    // bkd penunjang
    Route::get('/bkd/penunjang', [AdminPenunjang::class, 'index'])->name('bkd.penunjang');
    Route::get('/bkd/penunjang/{id}', [AdminPenunjang::class, 'all'])->name('bkd.penunjang.all');
    Route::get('/bkd/penunjang/{id}/show', [AdminPenunjang::class, 'show'])->name('bkd.penunjang.show');
    Route::delete('/bkd/penunjang/{id}', [AdminPenunjang::class, 'destroy'])->name('bkd.penunjang.delete');

    // pengajuan sertifikat
    Route::get('/pengajuan-sertifikat', [AdminPengajuanSertifikat::class, 'index'])->name('pengajuan.sertifikat');
    Route::get('/pengajuan-sertifikat/{id}', [AdminPengajuanSertifikat::class, 'show'])->name('pengajuan.sertifikat.show');
    Route::put('/pengajuan-sertifikat/setuju/{id}', [AdminPengajuanSertifikat::class, 'setuju'])->name('pengajuan.sertifikat.setuju');
    Route::put('/pengajuan-sertifikat/tolak/{id}', [AdminPengajuanSertifikat::class, 'tolak'])->name('pengajuan.sertifikat.tolak');

    // sertifikat
    Route::get('/sertifikat', [AdminSertifikat::class, 'index'])->name('sertifikat');
    Route::get('/sertifikat/{id}', [AdminSertifikat::class, 'all'])->name('sertifikat.all');
    Route::get('/sertifikat/{id}/show', [AdminSertifikat::class, 'show'])->name('sertifikat.show');
    Route::delete('/sertifikat/{id}', [AdminSertifikat::class, 'destroy'])->name('sertifikat.delete');
    Route::get('/sertifikat/create/{id}', [AdminSertifikat::class, 'create'])->name('sertifikat.create');
    Route::post('/sertifikat/store/{id}', [AdminSertifikat::class, 'store'])->name('sertifikat.store');
    Route::get('/sertifikat-edit/{id}', [AdminSertifikat::class, 'edit'])->name('sertifikat.edit');
    Route::put('/sertifikat/{id}', [AdminSertifikat::class, 'update'])->name('sertifikat.update');

    Route::get('/setting', [Setting::class, 'index'])->name('setting');
    Route::put('/setting', [Setting::class, 'update'])->name('setting.update');

    // serdos
    Route::get('/pengajuan-serdos', [AdminPengajuanSerdos::class, 'index'])->name('pengajuan.serdos');
    Route::get('/pengajuan-serdos/{id}', [AdminPengajuanSerdos::class, 'show'])->name('pengajuan.serdos.show');
    Route::put('/pengajuan-serdos/setuju/{id}', [AdminPengajuanSerdos::class, 'setuju'])->name('pengajuan.serdos.setuju');
    Route::put('/pengajuan-serdos/tolak/{id}', [AdminPengajuanSerdos::class, 'tolak'])->name('pengajuan.serdos.tolak');
});



// dosen
Route::middleware(['auth', 'role:dosen'])->prefix('dosen')->as('dosen.')->group(function () {
    Route::get('/', [Dashboard::class, 'dosen'])->name('dashboard');

    // profile pribadi
    Route::get('/profile', [ProfilePribadi::class, 'index'])->name('profilepribadi');

    // laporan
    Route::get('/laporan', [DosenLaporan::class, 'index'])->name('laporan');
    Route::post('/laporan/create', [DosenLaporan::class, 'create'])->name('laporan.create');

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

    // bkd penelitian
    Route::get('/bkd/penelitian', [Penelitian::class, 'index'])->name('penelitian');
    Route::get('/bkd/penelitian/create', [Penelitian::class, 'create'])->name('penelitian.create');
    Route::post('/bkd/penelitian/store', [Penelitian::class, 'store'])->name('penelitian.store');
    Route::get('/bkd/penelitian/{id}', [Penelitian::class, 'show'])->name('penelitian.show');
    Route::get('/bkd/penelitian-riwayat/{id}', [Penelitian::class, 'riwayat'])->name('penelitian.riwayat');

    // bkd pengabdian
    Route::get('/bkd/pengabdian', [Pengabdian::class, 'index'])->name('pengabdian');
    Route::get('/bkd/pengabdian/create', [Pengabdian::class, 'create'])->name('pengabdian.create');
    Route::post('/bkd/pengabdian/store', [Pengabdian::class, 'store'])->name('pengabdian.store');
    Route::get('/bkd/pengabdian/{id}', [Pengabdian::class, 'show'])->name('pengabdian.show');
    Route::get('/bkd/pengabdian-riwayat/{id}', [Pengabdian::class, 'riwayat'])->name('pengabdian.riwayat');

    // bkd penunjang
    Route::get('/bkd/penunjang', [Penunjang::class, 'index'])->name('penunjang');
    Route::get('/bkd/penunjang/create', [Penunjang::class, 'create'])->name('penunjang.create');
    Route::post('/bkd/penunjang/store', [Penunjang::class, 'store'])->name('penunjang.store');
    Route::get('/bkd/penunjang/{id}', [Penunjang::class, 'show'])->name('penunjang.show');
    Route::get('/bkd/penunjang-riwayat/{id}', [Penunjang::class, 'riwayat'])->name('penunjang.riwayat');

    // bkd pengajaran
    Route::get('/bkd/pengajaran', [Pengajaran::class, 'index'])->name('pengajaran');
    Route::get('/bkd/pengajaran/create', [Pengajaran::class, 'create'])->name('pengajaran.create');
    Route::post('/bkd/pengajaran/store', [Pengajaran::class, 'store'])->name('pengajaran.store');
    Route::get('/bkd/pengajaran/{id}', [Pengajaran::class, 'show'])->name('pengajaran.show');
    Route::get('/bkd/pengajaran-riwayat/{id}', [Pengajaran::class, 'riwayat'])->name('pengajaran.riwayat');

    // sertifikat
    Route::get('/sertifikat', [Sertifikat::class, 'index'])->name('sertifikat');
    Route::get('/sertifikat/create', [Sertifikat::class, 'create'])->name('sertifikat.create');
    Route::post('/sertifikat/store', [Sertifikat::class, 'store'])->name('sertifikat.store');
    Route::get('/sertifikat-edit/{id}', [Sertifikat::class, 'edit'])->name('sertifikat.edit');
    Route::put('/sertifikat/{id}', [Sertifikat::class, 'update'])->name('sertifikat.update');
    Route::delete('/sertifikat/{id}', [Sertifikat::class, 'destroy'])->name('sertifikat.delete');
    Route::get('/sertifikat/{id}', [Sertifikat::class, 'show'])->name('sertifikat.show');
    Route::get('/sertifikat-riwayat/{id}', [Sertifikat::class, 'riwayat'])->name('sertifikat.riwayat');

    // serdos
    Route::get('/serdos', [PengajuanSerdos::class, 'index'])->name('pengajuan.serdos');
    Route::get('/serdos/create', [PengajuanSerdos::class, 'create'])->name('pengajuan.serdos.create');
    Route::post('/serdos/store', [PengajuanSerdos::class, 'store'])->name('pengajuan.serdos.store');
    Route::get('/serdos/{id}', [PengajuanSerdos::class, 'show'])->name('pengajuan.serdos.show');
});


// karyawan
Route::middleware(['auth', 'role:karyawan'])->prefix('karyawan')->as('karyawan.')->group(function () {
    Route::get('/', [Dashboard::class, 'karyawan'])->name('dashboard');

    // profile pribadi
    Route::get('/profile', [KaryawanProfilePribadi::class, 'index'])->name('profilepribadi');

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
    // sertifikat
    Route::get('/sertifikat', [KaryawanSertifikat::class, 'index'])->name('sertifikat');
    Route::get('/sertifikat/create', [KaryawanSertifikat::class, 'create'])->name('sertifikat.create');
    Route::post('/sertifikat/store', [KaryawanSertifikat::class, 'store'])->name('sertifikat.store');
    Route::get('/sertifikat-edit/{id}', [KaryawanSertifikat::class, 'edit'])->name('sertifikat.edit');
    Route::put('/sertifikat/{id}', [KaryawanSertifikat::class, 'update'])->name('sertifikat.update');
    Route::delete('/sertifikat/{id}', [KaryawanSertifikat::class, 'destroy'])->name('sertifikat.delete');
    Route::get('/sertifikat/{id}', [KaryawanSertifikat::class, 'show'])->name('sertifikat.show');
    Route::get('/sertifikat-riwayat/{id}', [KaryawanSertifikat::class, 'riwayat'])->name('sertifikat.riwayat');
});




// get file
Route::middleware(['auth', 'role:admin,dosen,karyawan'])->group(function () {

    // edit profile
    Route::get('/users/edit', [ManajemenUser::class, 'index'])->name('users.index');
    Route::put('/users/edit', [ManajemenUser::class, 'update'])->name('users.update');

    Route::get('/file/ijazah/{filename}', [FileController::class, 'showIjazah'])->name('file.ijazah');
    Route::get('/file/register/{filename}', [FileController::class, 'register'])->name('file.register');
    Route::get('/file/sertifikat/{filename}', [FileController::class, 'sertifikat'])->name('file.sertifikat');
    Route::get('/file/sk/{filename}', [FileController::class, 'showSk'])->name('file.sk');
    Route::get('/file/bkd/{filename}', [FileController::class, 'showBkd'])->name('file.bkd');
    Route::get('/file/transkip/{filename}', [FileController::class, 'showTranskip'])->name('file.transkip');
    Route::get('/file/foto/{filename}', [FileController::class, 'showFoto'])->name('file.foto');
    Route::get('/file/fotodrive/{id}', [FileController::class, 'showFotoDrive'])->name('file.foto.drive');
    Route::get('/file/fotoperubahan/{id}', [FileController::class, 'showFotoPerubahan'])->name('file.foto.perubahan');
});



// login
Route::get('/login', [Auth::class, 'index'])->middleware('guest')->name('login');
Route::post('/login', [Auth::class, 'store'])->name('auth.login');

// logout
Route::post('/logout', [Auth::class, 'destroy'])->name('auth.logout');

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


// error google drive
Route::get('/drive-fail', [DriveFail::class, 'driveFail'])->name('drive.fail');
