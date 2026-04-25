<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KonsultasiMessage extends Model
{
    protected $table = 'konsultasi_messages';

    protected $fillable = [
        'konsultasi_id',
        'sender_id',
        'message',
    ];

    public function konsultasi()
    {
        return $this->belongsTo(Konsultasi::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
