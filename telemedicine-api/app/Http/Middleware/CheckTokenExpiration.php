<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckTokenExpiration
{
    // Idle timeout: sesi berakhir jika tidak ada aktivitas selama X menit
    const IDLE_MINUTES = 30;

    public function handle(Request $request, Closure $next): mixed
    {
        if ($request->user() && $request->user()->currentAccessToken()) {
            $token = $request->user()->currentAccessToken();

            // Cek apakah token sudah expired (idle timeout tercapai)
            if ($token->expires_at && $token->expires_at->isPast()) {
                $token->delete();
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi telah berakhir, silakan login kembali.',
                ], 401);
            }

            // Perpanjang token setiap ada aktivitas (reset idle timer)
            $token->forceFill([
                'last_used_at' => now(),
                'expires_at'   => now()->addMinutes(self::IDLE_MINUTES),
            ])->save();
        }

        return $next($request);
    }
}