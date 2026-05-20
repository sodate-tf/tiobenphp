<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BlogService
{
    public function listPublicPosts(int $perPage = 12): LengthAwarePaginator
    {
        $now = now();

        return Post::query()
            ->where('is_active', true)
            ->where('publish_date', '<=', $now)
            ->where(function ($q) use ($now) {
                $q->whereNull('expiry_date')->orWhere('expiry_date', '>', $now);
            })
            ->orderByDesc('publish_date')
            ->paginate($perPage);
    }
}
