<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Konsultasi extends Model
{
    protected $table = 'konsultasi';

    protected $fillable = [
        'user_id',
        'local_id',
        'nama',
        'keluhan',
        'status',
        'jawaban_dokter',
        'dokter_id',
        'dijawab_at',
        'client_created_at',
        'nomor_antrian',
    ];

    protected $casts = [
        'client_created_at' => 'datetime',
        'dijawab_at'        => 'datetime',
    ];

    public function pasien()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function dokter()
    {
        return $this->belongsTo(User::class, 'dokter_id');
    }

    public function messages()
    {
        return $this->hasMany(KonsultasiMessage::class)->orderBy('created_at', 'asc');
    }

    protected static function booted()
    {
        static::creating(function ($konsultasi) {
            $today = \Carbon\Carbon::today();
            $maxNomor = self::whereDate('created_at', $today)->max('nomor_antrian');
            $konsultasi->nomor_antrian = $maxNomor ? $maxNomor + 1 : 1;
        });
    }
}