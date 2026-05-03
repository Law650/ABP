<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AuthController extends Controller
{
    // Fix: Definisikan tipe properti secara eksplisit
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    // --- Tampilkan Halaman (Views) ---

    public function showRegisterForm(): View
    {
        return view('auth.register');
    }

    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function showVerificationNotice(Request $request): View
    {
        return view('auth.email-verification', ['email' => $request->query('email')]);
    }

    public function showForgotPasswordForm(): View
    {
        return view('auth.forgot-password');
    }

    public function showResetPasswordForm(Request $request): View
    {
        return view('auth.reset-password', ['email' => $request->query('email')]);
    }

    // --- Aksi (Actions) ---

    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        return $this->authService->register($request);
    }

    public function loginWithEmail(Request $request): RedirectResponse
    {
        // $request->validate([
        //     'email' => 'required|email',
        //     'password' => 'required',
        // ]);

        // /** @var \App\Models\User|null $user */
        // $user = User::where('email', $request->email)->first();

        // // Fix: Pastikan parameter Hash::check adalah string.
        // // (string) $request->password memaksa input jadi string.
        // // $user?->password ?? '' menangani jika user null atau password null.
        // if (! $user || ! Hash::check($request->string('password')->toString(), $user->password ?? '')) {            return back()->withErrors(['login' => 'Email atau password salah']);
        // }

        // // Fix: Tambahkan pengecekan if($user) redundant tapi aman untuk PHPStan,
        // // meskipun sudah dicover di atas.
        // if ($user->isAdmin()) {
        //     Auth::login($user);
        //     return redirect()->route('dashboard');
        // }

        // return $this->authService->loginWithEmail($request);
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        /** @var \App\Models\User|null $user */
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->string('password')->toString(), $user->password ?? '')) {
            return back()->withErrors(['login' => 'Email atau password salah']);
        }

        // --- LOGIKA SKIP OTP (Jika perangkat sudah terpercaya) ---
        $isTrusted = $request->cookie('device_trusted_' . $user->id);

        if ($isTrusted === 'true' || $user->isAdmin()) {
            // Jika Admin atau perangkat terpercaya, langsung login
            Auth::login($user, $request->has('remember'));
            $request->session()->regenerate();

            return $user->isAdmin()
                ? redirect()->route('dashboard') // Sesuaikan route admin Anda
                : redirect()->route('dashboard');
        }

        // Jika user biasa dan perangkat baru, jalankan service OTP
        return $this->authService->loginWithEmail($request);
    }

    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        return $this->authService->verifyOtp($request);
    }

    public function forgotPassword(Request $request): RedirectResponse
    {
        $request->validate(['email' => 'required|email']);

        return $this->authService->forgotPassword($request);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
            'password' => 'required|min:8|confirmed',
        ]);

        return $this->authService->resetPassword($request);
    }

    public function resendOtp(Request $request): RedirectResponse
    {
        $request->validate(['email' => 'required|email']);

        return $this->authService->resendOtp($request);
    }

    public function logout(Request $request): RedirectResponse
    {
        return $this->authService->logout($request);
    }

    // --- Google Auth ---

    // Note: Return type di sini tergantung library Socialite,
    // tapi biasanya dia me-return RedirectResponse atau Symfony\Component\HttpFoundation\RedirectResponse
    public function redirectToGoogle(): \Symfony\Component\HttpFoundation\RedirectResponse|RedirectResponse
    {
        return $this->authService->redirectToGoogle();
    }

    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        return $this->authService->handleGoogleCallback($request);
    }
}
