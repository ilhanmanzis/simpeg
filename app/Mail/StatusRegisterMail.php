<?php
namespace App\Mail;

use Illuminate\Mail\Mailable;

class StatusRegisterMail extends Mailable
{
    public $register;
    public $status;

    public function __construct($register, $status)
    {
        $this->register = $register;
        $this->status = $status;
    }

    public function build()
    {
        return $this->subject('Status Pengajuan Akun')
            ->view('status-register');
    }
}