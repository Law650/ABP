<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    // Fix 1: Tambahkan tipe data 'string'
    public string $otp;

    // Fix 2: Tambahkan tipe data 'string' di parameter
    public function __construct(string $otp)
    {
        $this->otp = $otp;
    }

    // Fix 3: Tambahkan return type ': self' (artinya mengembalikan class ini sendiri)
    public function build(): self
    {
        return $this->subject('Kode OTP Anda')
            ->view('emails.otp')
            ->with(['otp' => $this->otp]);
    }
}
