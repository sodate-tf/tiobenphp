<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MobileBetaTester extends Model
{
    protected $fillable = [
        'google_email',
        'whatsapp',
        'source_url',
        'link_sent',
        'link_sent_at',
    ];

    protected $casts = [
        'link_sent' => 'boolean',
        'link_sent_at' => 'datetime',
    ];
}
