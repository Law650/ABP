<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\RedirectResponse;

class AuthService
{
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    // Helper private untuk membersihkan input agar PHPStan tidak rewel
    private function getStringInput(Request $request, string $key): string
    {
        $value = $request->input($key);
        return is_string($value) ? $value : '';
    }

    public function register(Request $request): RedirectResponse
    {
        // Fix: Gunakan helper untuk memastikan string
        $email = $this->getStringInput($request, 'email');
        $password = $this->getStringInput($request, 'password');

        $parts = explode('@', $email);
        $name = $parts[0] ?: 'User'; // Fallback jika kosong

        /** @var User $user */
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt($password),
            'is_active' => false,
        ]);

        $user->assignRole('user');
        $user->profile()->create();

        $this->otpService->generateAndSend($user);

        return redirect()->route('verification.notice', ['email' => $user->email])
            ->with('success', 'Akun berhasil dibuat. Silakan cek email untuk kode OTP.');
    }

    public function loginWithEmail(Request $request): RedirectResponse
    {
        /** @var User|null $user */
        $user = User::where('email', $request->email)->first();

        // Fix: Hash check dengan input yang dipastikan string
        $inputPassword = $this->getStringInput($request, 'password');
        $userPassword = $user ? (string) $user->password : '';

        if (! $user || ! Hash::check($inputPassword, $userPassword)) {
            return back()->withInput()->with('error', 'Email atau password salah');
        }
        $isTrusted = $request->cookie('device_verified_' . $user->id);

        if ($isTrusted === 'true') {
            Auth::login($user, $request->has('remember'));
            $request->session()->regenerate();
            return redirect()->route('dashboard')->with('success', 'Selamat datang kembali!');
        }
        // -----------------------

        // Jika tidak trusted, simpan status remember ke session dan kirim OTP
        $request->session()->put('auth.remember', $request->has('remember'));
        $this->otpService->generateAndSend($user);

        return redirect()->route('verification.notice', ['email' => $user->email])->with('success', 'Kode OTP telah dikirim ke email Anda');
    }

    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);

        /** @var User|null $user */
        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()->with('error', 'User tidak ditemukan');
        }

        $otp = $this->getStringInput($request, 'otp');

        if (! $this->otpService->verify($user, $otp)) {
            return back()->withInput()->with('error', 'OTP tidak valid atau sudah kadaluarsa');
        }

        $this->otpService->reset($user);

        $user->email_verified_at = now();
        $user->is_active = true;
        $user->save();

        $remember = (bool) $request->session()->pull('auth.remember', false);
                Auth::login($user, $remember);
        $request->session()->regenerate();
        $cookie = cookie('device_verified_' . $user->id, 'true', 43200);
        return redirect()->route('dashboard')->with('success', 'Verifikasi berhasil! Selamat datang.')->withCookie($cookie);;
    }

    public function redirectToGoogle(): \Symfony\Component\HttpFoundation\RedirectResponse|\Illuminate\Http\RedirectResponse
    {
        /** @var \Laravel\Socialite\Two\AbstractProvider $driver */
        $driver = Socialite::driver('google');

        return $driver->stateless()->redirect();
    }

    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        try {
            /** @var \Laravel\Socialite\Two\AbstractProvider $driver */
            $driver = Socialite::driver('google');

            /** @var \Laravel\Socialite\Contracts\User $googleUser */
            $googleUser = $driver->stateless()->user();

            /** @var User|null $user */
            $user = User::where('email', $googleUser->getEmail())->first();

            if (! $user) {
                $googleAvatar = null;
                $avatarUrl = $googleUser->getAvatar();

                if ($avatarUrl) {
                    $imageContent = file_get_contents($avatarUrl);
                    if ($imageContent !== false) {
                        $googleAvatar = base64_encode($imageContent);
                    }
                }

                /** @var User $user */
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleAvatar,
                    'password' => Hash::make(str()->random(16)),
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]);

                $user->assignRole('user');
                $user->profile()->create();
            } else {
                $updateData = [
                    'google_id' => $googleUser->getId(),
                    'name' => $googleUser->getName(),
                    'email_verified_at' => $user->email_verified_at ?? now(),
                    'is_active' => true,
                ];

                $currentAvatar = $user->avatar;
                $isLocalAvatar = $currentAvatar && ! str_starts_with($currentAvatar, 'http');

                if (! $isLocalAvatar) {
                    $avatarUrl = $googleUser->getAvatar();
                    if ($avatarUrl) {
                        $imageContent = file_get_contents($avatarUrl);
                        if ($imageContent !== false) {
                            $updateData['avatar'] = base64_encode($imageContent);
                        }
                    }
                }

                $user->update($updateData);
            }

            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->route('dashboard')->with('success', 'Login Google berhasil!');
        } catch (\Exception $e) {
            Log::error('Google Login Error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Login Google gagal: ' . $e->getMessage());
        }
    }

    public function forgotPassword(Request $request): RedirectResponse
    {
        /** @var User|null $user */
        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()->withInput()->with('error', 'Email tidak ditemukan');
        }

        $this->otpService->generateAndSend($user);

        return redirect()->route('password.reset.form', ['email' => $user->email])
            ->with('success', 'Kode reset password dikirim ke email Anda');
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        /** @var User|null $user */
        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()->withInput()->with('error', 'User tidak ditemukan');
        }

        $otp = $this->getStringInput($request, 'otp');

        if (! $this->otpService->verify($user, $otp)) {
            return back()->withInput()->with('error', 'OTP tidak valid atau kadaluarsa');
        }

        $password = $this->getStringInput($request, 'password');
        $user->password = Hash::make($password);
        $this->otpService->reset($user);
        $user->save();

        return redirect()->route('login')->with('success', 'Password berhasil direset. Silakan login dengan password baru.');
    }

    public function resendOtp(Request $request): RedirectResponse
    {
        /** @var User|null $user */
        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()->with('error', 'User tidak ditemukan');
        }

        try {
            // Panggil fungsi dengan limitasi
            $this->otpService->resendOtp($user);
            return back()->with('success', 'Kode OTP baru telah dikirim');
        } catch (\Exception $e) {
            // Jika kena limit, pesan "Anda telah mencapai batas..." akan dikirim ke view
            return back()->with('error', $e->getMessage());
        }
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }

    public function logoutGoogle(Request $request): RedirectResponse
    {
        return $this->logout($request);
    }
}