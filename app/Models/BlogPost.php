<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    // AJUSTE: nome real da sua tabela
    protected $table = 'posts';

    protected $guarded = [];

    protected $casts = [
        'publish_date' => 'datetime',
        'is_active' => 'number',
    ];
}
