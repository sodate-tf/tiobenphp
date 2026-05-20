<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostRelatedItem extends Model
{
    protected $table = 'post_related_items';

    public $timestamps = false; // sua tabela só tem created_at, não tem updated_at

    protected $fillable = [
        'post_id',
        'related_post_id',
        'sort_order',
        'created_at',
    ];

    protected $casts = [
        'sort_order' => 'int',
        'created_at' => 'datetime',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function relatedPost()
    {
        return $this->belongsTo(Post::class, 'related_post_id');
    }
}
