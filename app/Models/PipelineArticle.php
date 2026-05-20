<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PipelineArticle extends Model
{
    use HasUuids;

    protected $table = 'pipeline_articles';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id','topic','agent','language','focus_keywords','date',
        'source_text','liturgy_source',
        'content_raw','content_html',
        'title','slug','meta_description','keywords','cover_image_url',
        'published_at',
    ];

    protected $casts = [
        'date' => 'date',
        'published_at' => 'datetime',
    ];
}
