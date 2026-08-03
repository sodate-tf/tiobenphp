<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    private const SITE_URL = 'https://www.iatioben.com.br';

    private array $categoryThemes = [
        'liturgia' => [
            'label' => 'Liturgia',
            'name' => 'Liturgia',
            'accentText' => 'text-amber-900',
            'accentBorder' => 'border-amber-200',
            'accentBg' => 'bg-amber-50',
            'accentUnderline' => 'bg-amber-500',
        ],
        'santos' => [
            'label' => 'Santos',
            'name' => 'Santos',
            'accentText' => 'text-rose-900',
            'accentBorder' => 'border-rose-200',
            'accentBg' => 'bg-rose-50',
            'accentUnderline' => 'bg-rose-500',
        ],
        'terco' => [
            'label' => 'Terço',
            'name' => 'Terço',
            'accentText' => 'text-emerald-900',
            'accentBorder' => 'border-emerald-200',
            'accentBg' => 'bg-emerald-50',
            'accentUnderline' => 'bg-emerald-500',
        ],
        'homilia' => [
            'label' => 'Homilia',
            'name' => 'Homilia',
            'accentText' => 'text-indigo-900',
            'accentBorder' => 'border-indigo-200',
            'accentBg' => 'bg-indigo-50',
            'accentUnderline' => 'bg-indigo-500',
        ],
        'cotidiano' => [
            'label' => 'Vida Cristã',
            'name' => 'Cotidiano',
            'accentText' => 'text-sky-900',
            'accentBorder' => 'border-sky-200',
            'accentBg' => 'bg-sky-50',
            'accentUnderline' => 'bg-sky-500',
        ],
        'noticias' => [
            'label' => 'Notícias',
            'name' => 'Notícias',
            'accentText' => 'text-slate-900',
            'accentBorder' => 'border-slate-200',
            'accentBg' => 'bg-slate-50',
            'accentUnderline' => 'bg-slate-500',
        ],
    ];

    public function portal()
    {
        $baseQuery = $this->publicPostsQuery();

        $latestPosts = (clone $baseQuery)
            ->tap(fn (Builder $q) => $this->orderNewest($q))
            ->limit(30)
            ->get();

        $featured = $latestPosts->first();
        $heroSecondary = $latestPosts
            ->when($featured, fn ($items) => $items->where('id', '!=', $featured->id))
            ->take(6)
            ->values();

        $latest = $latestPosts
            ->when($featured, fn ($items) => $items->where('id', '!=', $featured->id))
            ->take(12)
            ->values();

        $sections = $this->buildSections();

        $categoryLinks = collect($this->categoryThemes)
            ->filter(function (array $theme): bool {
                return $this->publicPostsQuery()
                    ->whereHas('category', fn (Builder $q) => $q->where('name', $theme['name']))
                    ->exists();
            })
            ->map(fn ($theme, $slug) => [
                'slug' => $slug,
                'label' => $theme['label'],
                'theme' => $theme,
            ])
            ->values();

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

        return view('blog.portal', compact(
            'meta',
            'featured',
            'heroSecondary',
            'latest',
            'sections',
            'categoryLinks'
        ));
    }

    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $query = $this->publicPostsQuery();

        if ($q !== '') {
            $query->where(function (Builder $w) use ($q) {
                $w->where('title', 'like', "%{$q}%")
                    ->orWhere('meta_description', 'like', "%{$q}%")
                    ->orWhere('keywords', 'like', "%{$q}%")
                    ->orWhere('content', 'like', "%{$q}%");
            });
        }

        $posts = $this->orderNewest($query)
            ->paginate(12)
            ->withQueryString();

        $meta = [
            'html_lang' => 'pt-BR',
            'title' => $q !== '' ? "Busca por {$q} — Blog IA Tio Ben" : 'Todos os posts — Blog IA Tio Ben',
            'description' => 'Todos os artigos do Blog IA Tio Ben em ordem do mais recente para o mais antigo.',
            'canonical' => url('/blog/posts'),
            'robots' => 'index,follow',
        ];

        return view('blog.posts.index', compact('posts', 'q', 'meta'));
    }

    public function category(string $categorySlug)
    {
        $theme = $this->categoryTheme($categorySlug);
        $categoryName = $this->categoryNameBySlug($categorySlug);

        $posts = $this->orderNewest(
            $this->publicPostsQuery()
                ->whereHas('category', fn (Builder $q) => $q->where('name', $categoryName))
        )
            ->paginate(12)
            ->withQueryString();

        if ($posts->total() === 0) {
            return redirect()->route('blog.portal', [], 301);
        }

        return view('blog.category', [
            'categorySlug' => $categorySlug,
            'theme' => $theme,
            'posts' => $posts,
        ]);
    }

    public function show(string $slug)
    {
        $post = $this->publicPostsQuery()
            ->where('slug', $slug)
            ->firstOrFail();

        $alternateEn = null;
        if (!empty($post->uuid)) {
            $alternateEn = Post::query()
                ->where('is_active', 1)
                ->where('lang', 'en')
                ->where('uuid', $post->uuid)
                ->whereNotNull('publish_date')
                ->where('publish_date', '<=', now())
                ->where(function (Builder $q) {
                    $q->whereNull('expiry_date')->orWhere('expiry_date', '>', now());
                })
                ->orderByDesc('publish_date')
                ->first();
        }
        $post->setAttribute('en_slug', $alternateEn?->slug ?? $post->slug);
        $post->setAttribute('pt_slug', $post->slug);

        $latest = $this->orderNewest(
            $this->publicPostsQuery()
                ->where('id', '!=', $post->id)
        )
            ->take(6)
            ->get();

        $related = $this->orderNewest(
            $this->publicPostsQuery()
                ->where('id', '!=', $post->id)
                ->when($post->category_id, fn (Builder $q) => $q->where('category_id', $post->category_id))
        )
            ->take(9)
            ->get();

        $categorySlug = $post->category?->name
            ? Str::slug($post->category->name)
            : null;

        return view('blog.show', [
            'post' => $post,
            'latest' => $latest,
            'related' => $related,
            'category' => $post->category,
            'theme' => $this->categoryTheme($categorySlug),
        ]);
    }

    private function publicPostsQuery(): Builder
    {
        return Post::query()
            ->with('category:id,name')
            ->where('is_active', 1)
            ->where('lang', 'pt')
            ->whereNotNull('created_at')
            ->whereNotNull('publish_date')
            ->where('publish_date', '<=', now())
            ->where(function (Builder $q) {
                $q->whereNull('expiry_date')
                    ->orWhere('expiry_date', '>', now());
            });
    }

    private function orderNewest(Builder $query): Builder
    {
        return $query
            ->orderByDesc('created_at')
            ->orderByDesc('publish_date');
    }

    private function buildSections(): array
    {
        $sections = [];

        foreach ($this->categoryThemes as $slug => $theme) {
            $categoryName = $theme['name'];

            $posts = $this->orderNewest(
                $this->publicPostsQuery()
                    ->whereHas('category', fn (Builder $q) => $q->where('name', $categoryName))
            )
                ->limit(7)
                ->get();

            $sections[$slug] = [
                'categorySlug' => $slug,
                'theme' => $theme,
                'posts' => $posts,
            ];
        }

        return $sections;
    }

    private function categoryTheme(?string $slug): array
    {
        return $this->categoryThemes[$slug] ?? [
            'label' => 'Blog',
            'name' => 'Blog',
            'accentText' => 'text-slate-900',
            'accentBorder' => 'border-slate-200',
            'accentBg' => 'bg-slate-50',
            'accentUnderline' => 'bg-slate-500',
        ];
    }

    private function categoryNameBySlug(string $slug): string
    {
        return $this->categoryThemes[$slug]['name'] ?? Str::headline(str_replace('-', ' ', $slug));
    }
}
