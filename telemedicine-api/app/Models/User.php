<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'no_hp',
        'alamat',
        'spesialisasi',
        'no_str',
        'foto_profil',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ── Helper ──
    public function isPasien(): bool
    {
        return $this->role === 'pasien';
    }
    public function isDokter(): bool
    {
        return $this->role === 'dokter';
    }
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // ── Relasi ──
    public function konsultasi()
    {
        return $this->hasMany(Konsultasi::class, 'user_id');
    }

    public function jadwalDokter()
    {
        return $this->hasMany(JadwalDokter::class, 'dokter_id');
    }

    public function bookings()
    {
        return $this->hasMany(BookingJadwal::class, 'pasien_id');
    }
}