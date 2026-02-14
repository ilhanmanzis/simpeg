<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\PengajuanMasukNotification;

class NotificationService
{
    public static function notifyAdmin(
        string $judul,
        string $pesan,
        string $routeName,
        array $params = []
    ) {
        $admins = User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            $admin->notify(
                new PengajuanMasukNotification(
                    $judul,
                    $pesan,
                    $routeName,
                    $params
                )
            );
        }
    }

    public static function notifyUser(
        User $user,
        string $judul,
        string $pesan,
        string $routeName,
        array $params = []
    ) {
        $user->notify(
            new PengajuanMasukNotification(
                $judul,
                $pesan,
                $routeName,
                $params
            )
        );
    }
}
