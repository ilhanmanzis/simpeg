<?php

namespace App\Providers;

use App\Models\Presensi;
use App\Observers\PresensiObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Ambil token lengkap dari Cache (sesuai yang kamu simpan di gdrive:token)
        $tok = Cache::get('gdrive:token');

        // Set accessToken pada config disk 'google' kalau ada
        if (is_array($tok) && !empty($tok['access_token'])) {
            Config::set('filesystems.disks.google.accessToken', $tok['access_token']);
        } else {
            // fallback: kosongkan, biar driver/adaptor (kalau mendukung) pakai refresh_token untuk ambil token baru
            Config::set('filesystems.disks.google.accessToken', null);
        }
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $view->with('notifications', Auth::user()->unreadNotifications);
            }
        });
        Presensi::observe(PresensiObserver::class);
    }
}
