<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Str;

class BlogPortalService
{
    // Ajuste se seus IDs forem outros
    private const CATEGORY_BY_ID = [
        '388b79e9-4a3a-4f9a-968c-2737cff74cce' => 'Liturgia',
        '7e0577dc-36c8-4f9d-b476-97af9dc1a077' => 'Homilia',
        '81f79787-be83-420f-b214-6b243da21cda' => 'Notícias',
        'ae6cd3b7-dbc9-4df2-bf7c-c372d6b7fe5a' => 'Cotidiano',
        'ba7adc02-de35-4405-b3f3-7391947d6281' => 'Santos',
        'da37f657-b94e-485f-be57-468815d712bd' => 'Terço',
    ];

    private const THEMES = [
        'liturgia' => [
            'key' => 'liturgia',
            'label' => 'Liturgia',
            'name' => 'Liturgia',
            'slug' => 'liturgia',
            'accentText' => 'text-amber-900',
            'accentBg' => 'bg-amber-50',
            'accentBorder' => 'border-amber-200',
            'accentUnderline' => 'bg-amber-500',
        ],
        'santos' => [
            'key' => 'santos',
            'label' => 'Santos',
            'name' => 'Santos',
            'slug' => 'santos',
            'accentText' => 'text-rose-900',
            'accentBg' => 'bg-rose-50',
            'accentBorder' => 'border-rose-200',
            'accentUnderline' => 'bg-rose-500',
        ],
        'terco' => [
            'key' => 'terco',
            'label' => 'Terço',
            'name' => 'Terço',
            'slug' => 'terco',
            'accentText' => 'text-emerald-900',
            'accentBg' => 'bg-emerald-50',
            'accentBorder' => 'border-emerald-200',
            'accentUnderline' => 'bg-emerald-500',
        ],
        'homilia' => [
            'key' => 'homilia',
            'label' => 'Homilia',
            'name' => 'Homilia',
            'slug' => 'homilia',
            'accentText' => 'text-indigo-900',
            'accentBg' => 'bg-indigo-50',
            'accentBorder' => 'border-indigo-200',
            'accentUnderline' => 'bg-indigo-500',
        ],
        'cotidiano' => [
            'key' => 'cotidiano',
            'label' => 'Vida Cristã',
            'name' => 'Cotidiano',
            'slug' => 'cotidiano',
            'accentText' => 'text-sky-900',
            'accentBg' => 'bg-sky-50',
            'accentBorder' => 'border-sky-200',
            'accentUnderline' => 'bg-sky-500',
        ],
        'noticias' => [
            'key' => 'noticias',
            'label' => 'Notícias',
            'name' => 'Notícias',
            'slug' => 'noticias',
            'accentText' => 'text-slate-900',
            'accentBg' => 'bg-slate-50',
            'accentBorder' => 'border-slate-200',
            'accentUnderline' => 'bg-slate-500',
        ],
    ];

    public function getPortalData(): array
    {
        $now = now();

        $posts = Post::query()
            ->where('is_active', true)
            ->where('publish_date', '<=', $now)
            ->where(function ($q) use ($now) {
                $q->whereNull('expiry_date')->orWhere('expiry_date', '>', $now);
            })
            ->orderByDesc('publish_date')
            ->get();

        $byCategoryName = [];
        foreach ($posts as $p) {
            $catId = (string) $p->category_id;
            $catName = self::CATEGORY_BY_ID[$catId] ?? 'Sem categoria';
            $byCategoryName[$catName] ??= [];
            $byCategoryName[$catName][] = $p;
        }

        $activePosts = $posts->values();
        $featured = $activePosts->first();
        $heroSecondary = $activePosts->slice(1, 6)->values();

        $sections = [];
        foreach (self::THEMES as $key => $theme) {
            $list = $byCategoryName[$theme['name']] ?? [];
            $sections[$key] = [
                'theme' => $theme,
                'categorySlug' => Str::slug($theme['name']),
                'posts' => $list,
            ];
        }

        return [
            'featured' => $featured,
            'heroSecondary' => $heroSecondary,
            'sections' => $sections,
        ];
    }
}
