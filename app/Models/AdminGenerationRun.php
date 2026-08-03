<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AdminGenerationRun extends Model
{
    use HasUuids;

    protected $table = 'admin_generation_runs';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'topic',
        'status',
        'stage',
        'message',
        'pipeline_article_id',
        'created_by',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];
}

