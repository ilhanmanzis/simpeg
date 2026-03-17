<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class StatusPengajuanNotification extends Notification
{
    use Queueable;

    protected $pengajuan;
    protected $pengajuanName;
    protected $status;
    protected $url;
    protected $idPengajuan;

    public function __construct($pengajuan, $status, $pengajuanName, $url, $idPengajuan)
    {
        $this->pengajuan = $pengajuan;
        $this->pengajuanName = $pengajuanName;
        $this->status = $status;
        $this->url = $url;
        $this->idPengajuan = $idPengajuan;
    }

    public function via($notifiable)
    {
        return ['mail']; // kirim via email
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(
                $this->status == 'disetujui'
                    ? 'Pengajuan Disetujui'
                    : 'Pengajuan Ditolak'
            )
            ->view('status-pengajuan', [
                'nama' => $this->pengajuan->user->dataDiri->name,
                'jenis' => 'Pengajuan ' . $this->pengajuanName,
                'pengajuan' => $this->pengajuan,
                'status' => $this->status,
                'url' => $this->url,
                'idPengajuan'    => $this->idPengajuan
            ]);
    }
}
