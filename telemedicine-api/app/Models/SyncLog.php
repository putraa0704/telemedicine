<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncLog extends Model
{
    protected $fillable = [
        'local_id',
        'action',
        'result',
        'payload',
        'conflict_detail',
        'ip_address',
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}