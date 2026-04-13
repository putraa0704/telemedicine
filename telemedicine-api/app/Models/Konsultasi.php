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
}