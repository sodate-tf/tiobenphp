<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostCrossLink extends Model
{
    protected $table = 'post_cross_links';

    protected $fillable = [
        'post_id',
        'linked_post_id',
        'link_date',
        'paragraph',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'link_date' => 'date',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }
}
