<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Durasi token awal (harus sama dengan IDLE_MINUTES di CheckTokenExpiration)
    const TOKEN_MINUTES = 30;

    // Register pasien
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'role' => 'nullable|in:pasien,dokter',
            'spesialisasi' => 'nullable|string|max:100',
            'no_str' => 'nullable|string|max:100',
        ]);

        $targetRole = 'pasien';
        $requestedRole = $request->input('role');
        if ($requestedRole === 'dokter') {
            $actor = auth('sanctum')->user();
            if (!$actor || $actor->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya admin yang dapat membuat akun dokter.',
                ], 403);
            }
            $targetRole = 'dokter';
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $targetRole,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'spesialisasi' => $targetRole === 'dokter' ? $request->spesialisasi : null,
            'no_str' => $targetRole === 'dokter' ? $request->no_str : null,
        ]);

        $token = $user->createToken(
            'auth_token',
            ['*'],
            now()->addMinutes(self::TOKEN_MINUTES)
        )->plainTextToken;

        return response()->json([
            'success' => true,
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    // Register dokter (admin only)
    public function registerDokter(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'spesialisasi' => 'nullable|string|max:100',
            'no_str' => 'nullable|string|max:100',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'dokter',
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'spesialisasi' => $request->spesialisasi,
            'no_str' => $request->no_str,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Dokter berhasil ditambahkan',
            'user' => $user,
        ], 201);
    }

    // Login semua role
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        // Hapus token lama, buat baru
        $user->tokens()->delete();
        $token = $user->createToken(
            'auth_token',
            ['*'],
            now()->addMinutes(self::TOKEN_MINUTES)
        )->plainTextToken;

        return response()->json([
            'success' => true,
            'user' => $user,
            'token' => $token,
            'role' => $user->role,
        ]);
    }

    // Logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil',
        ]);
    }

    // Get current user
    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    // Update profile (nama tidak diubah di endpoint ini)
    public function updateProfile(Request $request)
    {
        $request->validate([
            'no_hp' => 'nullable|string|max:20',
            'foto_profil' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'remove_foto' => 'nullable|boolean',
        ]);

        $user = $request->user();
        $payload = [
            'no_hp' => $request->input('no_hp'),
        ];

        if ($request->hasFile('foto_profil')) {
            if ($user->foto_profil) {
                Storage::disk('public')->delete($user->foto_profil);
            }
            $payload['foto_profil'] = $request->file('foto_profil')->store('profile_photos', 'public');
        } elseif ($request->boolean('remove_foto')) {
            if ($user->foto_profil) {
                Storage::disk('public')->delete($user->foto_profil);
            }
            $payload['foto_profil'] = null;
        }

        $user->update($payload);

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui',
            'user' => $user->fresh(),
        ]);
    }

    // Ubah kata sandi user login
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Kata sandi saat ini tidak sesuai.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kata sandi berhasil diubah',
        ]);
    }
}