<?php

namespace App\Services;

use App\Mail\SendOtpMail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Exception;

class OtpService
{
    public function generateAndSend(User $user, int $minutes = 5): void
    {
        $otp = (string) rand(100000, 999999);

        $user->otp_code = $otp;
        $user->otp_expires_at = now()->addMinutes($minutes);
        $user->save();

        Mail::to($user->email)->send(new SendOtpMail($otp));
    }

    public function resendOtp(User $user, int $minutes = 5): void
    {
        if ($user->resend_attempts >= 3) {
            throw new Exception('Batas maksimal terlampaui. Silakan coba lagi nanti.');
        }

        $otp = (string) rand(100000, 999999);

        $user->otp_code = $otp;
        $user->otp_expires_at = now()->addMinutes($minutes);
        $user->resend_attempts += 1;
        $user->save();

        Mail::to($user->email)->send(new SendOtpMail($otp));
    }

    public function verify(User $user, string $otp): bool
    {
        if (! $user->otp_code || ! $user->otp_expires_at) {
            return false; // belum ada OTP
        }

        if (now()->gt($user->otp_expires_at)) {
            return false; // OTP kadaluarsa
        }

        // Pastikan perbandingan string vs string (strict comparison)
        if ((string) $user->otp_code !== $otp) {
            return false; // OTP salah
        }

        return true;
    }

    // Fix: Tambahkan return type : void
    public function reset(User $user): void
    {
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->resend_attempts = 0;
        $user->save();
    }
}
