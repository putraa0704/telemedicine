<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalDokter extends Model
{
    protected $table = 'jadwal_dokter';

    protected $fillable = [
        'dokter_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'is_aktif',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
    ];

    // ── Relasi ──
    public function dokter()
    {
        return $this->belongsTo(User::class, 'dokter_id');
    }

    public function bookings()
    {
        return $this->hasMany(BookingJadwal::class, 'jadwal_id');
    }

    // ── Helper: cek apakah slot sudah terisi untuk tanggal tertentu ──
    public function sudahTerisi(string $tanggal): bool
    {
        return $this->bookings()
            ->where('tanggal', $tanggal)
            ->where('status', 'booked')
            ->exists();
    }
}