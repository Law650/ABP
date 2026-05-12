<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // 1. Pastikan user sudah login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // 2. Cek apakah user memiliki salah satu dari role yang diizinkan di rute
        $hasAccess = false;
        foreach ($roles as $role) {
            if (Auth::user()->hasRole($role)) {
                $hasAccess = true;
                break; // Jika cocok, hentikan pencarian
            }
        }

        // 3. Jika tidak punya akses, tolak.
        if (!$hasAccess) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk halaman ini.');
        }

        return $next($request);
    }
}