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
        'moderation_status',
        'quality_report',
        'quality_checked_at',
        'auto_published',
        'review_notes',
        'rejection_reason',
        'reviewed_at',
        'reviewed_by',
    ];

    protected $casts = [
        'date' => 'date',
        'published_at' => 'datetime',
        'quality_checked_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'quality_report' => 'array',
        'auto_published' => 'boolean',
    ];
}
