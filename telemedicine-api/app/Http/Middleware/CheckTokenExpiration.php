<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Exception;

class CheckTokenExpiration
{
    // Idle timeout: sesi berakhir jika tidak ada aktivitas selama X menit
    const IDLE_MINUTES = 30;

    public function handle(Request $request, Closure $next): mixed
    {
        try {
            if ($request->user() && $request->user()->currentAccessToken()) {
                $token = $request->user()->currentAccessToken();

                // Cek apakah token sudah expired (idle timeout tercapai)
                if ($token->expires_at && $token->expires_at->isPast()) {
                    $token->delete();

                    // Return 401 dengan pesan yang akan ditangkap browser
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthenticated.',
                        'detail' => 'Token telah berakhir karena tidak ada aktivitas.',
                    ], 401);
                }

                // Perpanjang token setiap ada aktivitas (reset idle timer)
                // Token akan aktif lagi untuk 2 menit ke depan (untuk testing)
                $token->forceFill([
                    'last_used_at' => now(),
                    'expires_at' => now()->addMinutes(self::IDLE_MINUTES),
                ])->save();
            }
        } catch (Exception $e) {
            // Log error tapi jangan break request
            \Log::error('CheckTokenExpiration error: ' . $e->getMessage());
        }

        return $next($request);
    }
}