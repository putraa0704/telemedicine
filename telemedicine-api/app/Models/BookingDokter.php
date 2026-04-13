<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingJadwal extends Model
{
    protected $table = 'booking_jadwal';

    protected $fillable = [
        'jadwal_id',
        'pasien_id',
        'konsultasi_id',
        'tanggal',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // ── Relasi ──
    public function jadwal()
    {
        return $this->belongsTo(JadwalDokter::class, 'jadwal_id');
    }

    public function pasien()
    {
        return $this->belongsTo(User::class, 'pasien_id');
    }

    public function konsultasi()
    {
        return $this->belongsTo(Konsultasi::class);
    }
}