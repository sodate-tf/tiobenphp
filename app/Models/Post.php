<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Post extends Model
{
    protected $table = 'posts';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'title',
        'slug',
        'keywords',
        'meta_description',
        'cover_image_url',
        'content',
        'category_id',
        'is_active',
        'is_featured',
        'publish_date',
        'expiry_date',
        'lang',
        'uuid',
    ];

    protected $casts = [
        'id' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'publish_date' => 'datetime',
        'expiry_date' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Post $model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }

            if (empty($model->lang)) {
                $model->lang = 'pt';
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function relatedItems(): HasMany
    {
        return $this->hasMany(PostRelatedItem::class, 'post_id')
            ->orderBy('sort_order')
            ->orderByDesc('created_at');
    }

    public function relatedPosts(): BelongsToMany
    {
        return $this->belongsToMany(
            self::class,
            'post_related_items',
            'post_id',
            'related_post_id'
        )
            ->withPivot(['sort_order', 'created_at'])
            ->orderByPivot('sort_order')
            ->orderByDesc('posts.publish_date');
    }
}