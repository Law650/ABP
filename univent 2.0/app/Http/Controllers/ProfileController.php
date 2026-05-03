<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View; // Import return type
use Illuminate\Http\RedirectResponse; // Import return type

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman profil pengguna.
     */
    // Fix: Tambahkan Return Type
    public function show(): View|RedirectResponse
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        // Fix: Pastikan user ada sebelum memanggil load()
        if (! $user) {
            return redirect()->route('login');
        }

        $user->load('profile');

        return view('profile', compact('user'));
    }

    /**
     * Menampilkan halaman edit profil.
     */
    public function edit(): View|RedirectResponse
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        $user->load('profile');

        return view('edit-profile', compact('user'));
    }

    /**
     * Update profil (avatar, birthday, phone)
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        // Fix: Guard clause untuk PHPStan (menangani User|null)
        if (! $user) {
            return redirect()->route('login');
        }

        // Validasi input
        $validated = $request->validate([
            'name' => 'required|string|max:255  ',
            'phone' => 'nullable|string|max:15|regex:/^(\+?\d{1,15})$/',
            'birthday' => 'nullable|date',
            'new_avatar_temp' => 'nullable|string',   // base64
            'remove_avatar' => 'nullable|boolean',
        ]);

        $user->name = $validated['name'];

        /*
        |--------------------------------------------------------------------------
        | HANDLE AVATAR
        |--------------------------------------------------------------------------
        */

        // Jika user klik remove
        if ($request->remove_avatar == 1) {
            $user->avatar = null;
        }

        // Jika user upload avatar baru
        // Fix: Gunakan $request->input() dan cast ke (string) agar PHPStan senang
        if ($request->filled('new_avatar_temp')) {

            $temp = $request->input('new_avatar_temp');
            $base64 = is_string($temp) ? $temp : '';
            // Jika format masih "data:image/png;base64,xxxx"
            // PHPStan sekarang tahu $base64 pasti string, jadi str_contains aman
            if (str_contains($base64, ',')) {
                $parts = explode(',', $base64);
                // Pastikan array key 1 ada (meskipun biasanya pasti ada di base64 image)
                $base64 = $parts[1] ?? $base64;
            }

            $user->avatar = $base64;
        }

        $user->save();

        /*
        |--------------------------------------------------------------------------
        | HANDLE PROFILE DETAIL (phone & birthday)
        |--------------------------------------------------------------------------
        */

        // Fix: User sudah dipastikan ada di atas, jadi akses $user->id aman
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'birthday' => $validated['birthday'] ?? null,
                'phone' => $validated['phone'] ?? null,
            ]
        );

        return redirect()->route('profile.edit')->with('success', 'Profile berhasil diperbarui!');
    }
}