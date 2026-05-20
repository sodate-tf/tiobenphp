<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class Category extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'slug',
        'description',
        'meta_title',
        'meta_description',
    ];

    protected static function booted(): void
    {
        static::creating(function (Category $model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }

            if (Schema::hasColumn('categories', 'slug') && empty($model->slug)) {
                $model->slug = static::uniqueSlug((string) $model->name);
            }
        });

        static::updating(function (Category $model) {
            if (Schema::hasColumn('categories', 'slug') && empty($model->slug)) {
                $model->slug = static::uniqueSlug((string) $model->name, (string) $model->id);
            }
        });
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'category_id');
    }

    private static function uniqueSlug(string $name, ?string $ignoreId = null): string
    {
        $base = Str::slug($name) ?: Str::uuid()->toString();
        $slug = $base;
        $i = 2;

        while (static::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }
}
