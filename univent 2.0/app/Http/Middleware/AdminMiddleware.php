<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response; // Import Wajib

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            /** @var \App\Models\User|null $user */
            $user = Auth::user();
            
            if ($user && $user->isAdmin()) {
                return $next($request);
            }
        }

        abort(403, 'Akses ditolak');
    }
}