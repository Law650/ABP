<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response; // Import Wajib

class CheckTokenExpiry
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (! $token) {
            return response()->json(['message' => 'Token tidak ditemukan'], 401);
        }

        $accessToken = PersonalAccessToken::findToken($token);

        if (! $accessToken) {
            return response()->json(['message' => 'Token tidak valid'], 401);
        }

        if ($accessToken->expires_at && Carbon::now()->greaterThan($accessToken->expires_at)) {
            return response()->json(['message' => 'Token sudah kedaluwarsa'], 401);
        }

        return $next($request);
    }
}
