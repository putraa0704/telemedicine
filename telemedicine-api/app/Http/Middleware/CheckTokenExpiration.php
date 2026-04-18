<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckTokenExpiration
{
    public function handle(Request $request, Closure $next): mixed
    {
        if ($request->user() && $request->user()->currentAccessToken()) {
            $token = $request->user()->currentAccessToken();

            // Cek apakah token sudah expired
            if ($token->expires_at && $token->expires_at->isPast()) {
                $token->delete();
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi telah berakhir, silakan login kembali.',
                ], 401);
            }

            // Perpanjang token setiap ada request (idle timeout)
            $token->forceFill([
                'last_used_at' => now(),
                'expires_at'   => now()->addMinutes(2),
            ])->save();
        }

        return $next($request);
    }
}