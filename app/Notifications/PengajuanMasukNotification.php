<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PengajuanMasukNotification extends Notification
{
    use Queueable;

    protected string $judul;
    protected string $pesan;
    protected string $routeName;
    protected array $params;

    public function __construct(
        string $judul,
        string $pesan,
        string $routeName,
        array $params = []
    ) {
        $this->judul = $judul;
        $this->pesan = $pesan;
        $this->routeName = $routeName;
        $this->params = $params;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title'   => $this->judul,
            'message' => $this->pesan,
            'route'   => $this->routeName,
            'params'  => $this->params,
        ];
    }
}
