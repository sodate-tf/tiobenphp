<?php

// app/Http/Controllers/Public/BlogPortalController.php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Support\Str;

class BlogPortalController extends Controller
{
    private const SITE_URL = 'https://www.iatioben.com.br';

    // ✅ Mapa real (UUID -> nome)
    private const CATEGORY_BY_ID = [
        "388b79e9-4a3a-4f9a-968c-2737cff74cce" => "Liturgia",
        "7e0577dc-36c8-4f9d-b476-97af9dc1a077" => "Homilia",
        "81f79787-be83-420f-b214-6b243da21cda" => "Notícias",
        "ae6cd3b7-dbc9-4df2-bf7c-c372d6b7fe5a" => "Cotidiano",
        "ba7adc02-de35-4405-b3f3-7391947d6281" => "Santos",
        "da37f657-b94e-485f-be57-468815d712bd" => "Terço",
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
            'accentRing' => 'ring-amber-200',
            'accentUnderline' => 'bg-amber-500',
            'ogTint' => 'amber',
        ],
        'santos' => [
            'key' => 'santos',
            'label' => 'Santos',
            'name' => 'Santos',
            'slug' => 'santos',
            'accentText' => 'text-rose-900',
            'accentBg' => 'bg-rose-50',
            'accentBorder' => 'border-rose-200',
            'accentRing' => 'ring-rose-200',
            'accentUnderline' => 'bg-rose-500',
            'ogTint' => 'rose',
        ],
        'terco' => [
            'key' => 'terco',
            'label' => 'Terço',
            'name' => 'Terço',
            'slug' => 'terco',
            'accentText' => 'text-emerald-900',
            'accentBg' => 'bg-emerald-50',
            'accentBorder' => 'border-emerald-200',
            'accentRing' => 'ring-emerald-200',
            'accentUnderline' => 'bg-emerald-500',
            'ogTint' => 'emerald',
        ],
        'homilia' => [
            'key' => 'homilia',
            'label' => 'Homilia',
            'name' => 'Homilia',
            'slug' => 'homilia',
            'accentText' => 'text-indigo-900',
            'accentBg' => 'bg-indigo-50',
            'accentBorder' => 'border-indigo-200',
            'accentRing' => 'ring-indigo-200',
            'accentUnderline' => 'bg-indigo-500',
            'ogTint' => 'indigo',
        ],
        'cotidiano' => [
            'key' => 'cotidiano',
            'label' => 'Vida Cristã',
            'name' => 'Cotidiano',
            'slug' => 'cotidiano',
            'accentText' => 'text-sky-900',
            'accentBg' => 'bg-sky-50',
            'accentBorder' => 'border-sky-200',
            'accentRing' => 'ring-sky-200',
            'accentUnderline' => 'bg-sky-500',
            'ogTint' => 'sky',
        ],
        'noticias' => [
            'key' => 'noticias',
            'label' => 'Notícias',
            'name' => 'Notícias',
            'slug' => 'noticias',
            'accentText' => 'text-slate-900',
            'accentBg' => 'bg-slate-50',
            'accentBorder' => 'border-slate-200',
            'accentRing' => 'ring-slate-200',
            'accentUnderline' => 'bg-slate-500',
            'ogTint' => 'slate',
        ],
    ];

    private function normalize(string $str): string
    {
        $s = Str::lower($str);
        // remove acentos (boa o suficiente p/ slug)
        $s = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s) ?: $s;
        $s = preg_replace('/[^a-z0-9\s-]/', '', $s);
        return trim($s);
    }

    private function toCategorySlug(string $name): string
    {
        $n = $this->normalize($name);
        $n = preg_replace('/\s+/', '-', $n);
        $n = preg_replace('/-+/', '-', $n);
        return $n ?: 'sem-categoria';
    }

    private function getCategoryNameFromPost($post): string
    {
        $id = $post->category_id ?? null;
        if ($id && isset(self::CATEGORY_BY_ID[$id])) return self::CATEGORY_BY_ID[$id];

        $n = $post->name ?? ($post->category ?? null);
        return trim($n ?: 'Sem categoria');
    }

    private function groupByCategoryName($posts)
    {
        $map = [];
        foreach ($posts as $p) {
            $cat = $this->getCategoryNameFromPost($p);
            $map[$cat] = $map[$cat] ?? [];
            $map[$cat][] = $p;
        }
        return $map;
    }

    public function index()
    {
        // Se você tiver categories, faça join para trazer category_name (igual seu Next).
        $allPosts = Post::query()
            ->leftJoin('categories as c', 'posts.category_id', '=', 'c.id')
            ->select([
                'posts.*',
                'c.name as category_name',
            ])
            ->orderByRaw('COALESCE(posts.publish_date, posts.created_at) DESC')
            ->get();

        // ✅ Mesma lógica do filterActivePosts do Next
        $activePosts = $allPosts->filter(function ($post) {
            $now = now();
            $publishDate = $post->publish_date ? $post->publish_date : null;
            $expiryDate = $post->expiry_date ? $post->expiry_date : null;

            return (bool)$post->is_active
                && $publishDate
                && $publishDate->lte($now)
                && (!$expiryDate || $expiryDate->gt($now));
        })->values();

        $byCategory = $this->groupByCategoryName($activePosts);

        $themes = self::THEMES;

        $liturgia  = $byCategory[$themes['liturgia']['name']]  ?? [];
        $santos    = $byCategory[$themes['santos']['name']]    ?? [];
        $terco     = $byCategory[$themes['terco']['name']]     ?? [];
        $homilia   = $byCategory[$themes['homilia']['name']]   ?? [];
        $cotidiano = $byCategory[$themes['cotidiano']['name']] ?? [];
        $noticias  = $byCategory[$themes['noticias']['name']]  ?? [];

        $featured = $activePosts->first();
        $heroSecondary = $activePosts->slice(1, 6)->values(); // 1..6 (igual intenção)

        $meta = [
            'html_lang' => 'pt-BR',
            'title' => 'Blog IA Tio Ben | Evangelho, Liturgia e Vida Cristã',
            'description' => 'Artigos católicos sobre Evangelho do dia, liturgia diária, oração, santos e espiritualidade para viver a fé no cotidiano.',
            'canonical' => self::SITE_URL . '/blog',
            'robots' => 'index,follow',
            'og_title' => 'Blog IA Tio Ben | Evangelho, Liturgia e Vida Cristã',
            'og_description' => 'Artigos católicos sobre Evangelho do dia, liturgia diária, oração, santos e espiritualidade para viver a fé no cotidiano.',
            'og_url' => self::SITE_URL . '/blog',
            'og_image' => self::SITE_URL . '/og?title=Blog%20IA%20Tio%20Ben&description=Evangelho%2C%20liturgia%20di%C3%A1ria%20e%20espiritualidade%20cat%C3%B3lica',
        ];

        return view('blog.index', [
            'meta' => $meta,
            'siteUrl' => self::SITE_URL,
            'themes' => $themes,
            'categorySlug' => fn($name) => $this->toCategorySlug($name),

            'featured' => $featured,
            'heroSecondary' => $heroSecondary,

            'liturgia' => $liturgia,
            'santos' => $santos,
            'terco' => $terco,
            'homilia' => $homilia,
            'cotidiano' => $cotidiano,
            'noticias' => $noticias,
        ]);
    }
}
