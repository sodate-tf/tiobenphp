<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Post;
use App\Support\BlogCatalog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PublicBlogService
{
    /** @var array<string, array<int, string>> */
    private array $categoryIdCache = [];

    public function publicQuery(string $lang = 'pt'): Builder
    {
        $now = now();

        return Post::query()
            ->with('category')
            ->where('is_active', true)
            ->where('lang', $lang)
            ->whereNotNull('publish_date')
            ->where('publish_date', '<=', $now)
            ->where(function (Builder $q) use ($now) {
                $q->whereNull('expiry_date')->orWhere('expiry_date', '>', $now);
            });
    }

    public function ordered(Builder $query, bool $featuredFirst = false): Builder
    {
        if ($featuredFirst && Schema::hasColumn('posts', 'is_featured')) {
            $query->orderByDesc('is_featured');
        }

        return $query
            ->orderByDesc('publish_date')
            ->orderByDesc('created_at')
            ->orderByDesc('id');
    }

    public function portalData(string $lang = 'pt'): array
    {
        $featured = $this->ordered($this->publicQuery($lang), true)->first();

        $heroSecondary = $this->ordered($this->publicQuery($lang))
            ->when($featured?->id, fn (Builder $q) => $q->where('id', '!=', $featured->id))
            ->limit(6)
            ->get();

        $latest = $this->ordered($this->publicQuery($lang))
            ->limit(10)
            ->get();

        $sections = [];
        $categories = [];

        foreach (BlogCatalog::portalSections() as $key => $rawTheme) {
            $theme = BlogCatalog::decorate(array_merge($rawTheme, ['key' => $key]), $lang);

            $posts = $this->ordered($this->applyCategory($this->publicQuery($lang), $theme))
                ->limit(7)
                ->get();

            $sections[$key] = [
                'key' => $key,
                'categorySlug' => $theme['route_slug'],
                'theme' => $theme,
                'posts' => $posts,
            ];

            $categories[$key] = $theme;
        }

        return [
            'featured' => $featured,
            'heroSecondary' => $heroSecondary,
            'latest' => $latest,
            'sections' => $sections,
            'categories' => $categories,
            'meta' => $this->portalMeta($lang),
        ];
    }

    public function paginatedPosts(string $lang = 'pt', ?string $search = null, int $perPage = 12): LengthAwarePaginator
    {
        $query = $this->publicQuery($lang);
        $search = trim((string) $search);

        if ($search !== '') {
            $query->where(function (Builder $q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('meta_description', 'like', "%{$search}%")
                    ->orWhere('keywords', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        return $this->ordered($query)
            ->paginate($perPage)
            ->withQueryString();
    }

    public function categoryData(string $routeSlug, string $lang = 'pt', int $perPage = 12): array
    {
        $theme = $this->themeByRouteSlug($routeSlug, $lang);

        if (!$theme) {
            abort(404);
        }

        $posts = $this->ordered($this->applyCategory($this->publicQuery($lang), $theme))
            ->paginate($perPage)
            ->withQueryString();

        if ($posts->total() === 0) {
            abort(404);
        }

        $category = $this->firstCategoryForTheme($theme);
        $theme['label'] = $category?->name && empty($theme['is_catalog_fallback'])
            ? $theme['label']
            : ($theme['label'] ?? $category?->name ?? 'Blog');

        return [
            'categorySlug' => $theme['route_slug'],
            'theme' => $theme,
            'category' => $category,
            'posts' => $posts,
            'meta' => $this->categoryMeta($theme, $category, $lang),
        ];
    }

    public function showData(string $slug, string $lang = 'pt'): array
    {
        $post = $this->publicQuery($lang)
            ->where('slug', $slug)
            ->firstOrFail();

        $alternate = $this->findAlternateLanguagePost($post, $lang);
        $post->setAttribute('pt_slug', $lang === 'pt' ? $post->slug : ($alternate?->slug ?? $post->slug));
        $post->setAttribute('en_slug', $lang === 'en' ? $post->slug : ($alternate?->slug ?? $post->slug));

        $category = $post->category;
        $theme = $this->themeForPost($post, $lang);

        $manualRelated = $this->manualRelatedPosts($post, $lang, 9);
        $related = $this->fillRelatedPosts($post, $manualRelated, $lang, 9);

        $latest = $this->ordered($this->publicQuery($lang))
            ->where('id', '!=', $post->id)
            ->limit(6)
            ->get();

        return [
            'post' => $post,
            'category' => $category,
            'theme' => $theme,
            'related' => $related,
            'latest' => $latest,
            'asideBlogLinks' => $this->asideLinks($lang),
            'asideLatestPosts' => $this->latestLinks($latest, $lang),
            'isFinance' => ($theme['key'] ?? null) === 'financas',
        ];
    }

    private function findAlternateLanguagePost(Post $post, string $lang): ?Post
    {
        $targetLang = $lang === 'en' ? 'pt' : 'en';
        $now = now();

        if (!empty($post->uuid)) {
            $byUuid = Post::query()
                ->where('lang', $targetLang)
                ->where('uuid', $post->uuid)
                ->where('is_active', true)
                ->whereNotNull('publish_date')
                ->where('publish_date', '<=', $now)
                ->where(function (Builder $q) use ($now) {
                    $q->whereNull('expiry_date')->orWhere('expiry_date', '>', $now);
                })
                ->orderByDesc('publish_date')
                ->first();

            if ($byUuid) {
                return $byUuid;
            }
        }

        return null;
    }

    public function financeHubData(): array
    {
        $theme = BlogCatalog::findByRouteSlug('cristao-catolico-e-financas', 'pt');
        $theme = $theme ?: BlogCatalog::defaultTheme('pt');

        $query = $this->applyCategory($this->publicQuery('pt'), $theme);

        $featured = $this->ordered(clone $query, true)->first();
        $latest = $this->ordered(clone $query)
            ->when($featured?->id, fn (Builder $q) => $q->where('id', '!=', $featured->id))
            ->limit(18)
            ->get();

        return [
            'featured' => $featured,
            'latest' => $latest,
            'theme' => $theme,
            'meta' => [
                'html_lang' => 'pt-BR',
                'title' => 'Cristão Católico e Finanças: dinheiro com fé e responsabilidade — IA Tio Ben',
                'description' => 'Finanças com consciência católica: Catecismo, Doutrina Social, santos e papas aplicados a orçamento, dívidas, investimento ético e generosidade.',
                'canonical' => url('/cristao-catolico-e-financas'),
                'robots' => 'index,follow,max-image-preview:large',
                'og_title' => 'Cristão Católico e Finanças — IA Tio Ben',
                'og_description' => 'Uma visão católica e responsável sobre dinheiro, sem teologia da prosperidade.',
                'og_url' => url('/cristao-catolico-e-financas'),
                'og_image' => url('/og?title=' . rawurlencode('Cristão Católico e Finanças') . '&description=' . rawurlencode('Catecismo, santos e vida real')),
            ],
        ];
    }

    public function prayerHubData(): array
    {
        $theme = BlogCatalog::findByRouteSlug('terco', 'pt');
        $theme = $theme ?: BlogCatalog::defaultTheme('pt');

        $query = $this->applyCategory($this->publicQuery('pt'), $theme);

        $featured = $this->ordered(clone $query, true)->first();
        $latest = $this->ordered(clone $query)
            ->when($featured?->id, fn (Builder $q) => $q->where('id', '!=', $featured->id))
            ->limit(18)
            ->get();

        return [
            'featured' => $featured,
            'latest' => $latest,
            'theme' => $theme,
            'meta' => [
                'html_lang' => 'pt-BR',
                'title' => 'Oração Católica Prática: terço, rotina e vida espiritual — IA Tio Ben',
                'description' => 'Guia prático de oração católica: terço, rotina diária, silêncio, perseverança e aplicação real da fé no cotidiano.',
                'canonical' => url('/oracao-catolica-pratica'),
                'robots' => 'index,follow,max-image-preview:large',
                'og_title' => 'Oração Católica Prática — IA Tio Ben',
                'og_description' => 'Terço, rotina de oração e vida espiritual com passos concretos para viver a fé todos os dias.',
                'og_url' => url('/oracao-catolica-pratica'),
                'og_image' => url('/og?title=' . rawurlencode('Oração Católica Prática') . '&description=' . rawurlencode('Terço, rotina e vida espiritual')),
            ],
        ];
    }

    public function sacramentalLifeHubData(): array
    {
        $theme = BlogCatalog::findByRouteSlug('cotidiano', 'pt');
        $theme = $theme ?: BlogCatalog::defaultTheme('pt');

        $query = $this->applyCategory($this->publicQuery('pt'), $theme);

        $featured = $this->ordered(clone $query, true)->first();
        $latest = $this->ordered(clone $query)
            ->when($featured?->id, fn (Builder $q) => $q->where('id', '!=', $featured->id))
            ->limit(18)
            ->get();

        return [
            'featured' => $featured,
            'latest' => $latest,
            'theme' => $theme,
            'meta' => [
                'html_lang' => 'pt-BR',
                'title' => 'Vida Sacramental Prática: missa, confissão e rotina de fé — IA Tio Ben',
                'description' => 'Como viver a vida sacramental no cotidiano: missa, confissão, comunhão, exame de consciência e perseverança concreta.',
                'canonical' => url('/vida-sacramental-pratica'),
                'robots' => 'index,follow,max-image-preview:large',
                'og_title' => 'Vida Sacramental Prática — IA Tio Ben',
                'og_description' => 'Missa, confissão e comunhão com passos concretos para viver a fé de modo consistente.',
                'og_url' => url('/vida-sacramental-pratica'),
                'og_image' => url('/og?title=' . rawurlencode('Vida Sacramental Prática') . '&description=' . rawurlencode('Missa, confissão e rotina de fé')),
            ],
        ];
    }

    public function catholicFaithQuestionsHubData(): array
    {
        $theme = BlogCatalog::findByRouteSlug('homilia', 'pt');
        $theme = $theme ?: BlogCatalog::defaultTheme('pt');

        $query = $this->applyCategory($this->publicQuery('pt'), $theme);

        $featured = $this->ordered(clone $query, true)->first();
        $latest = $this->ordered(clone $query)
            ->when($featured?->id, fn (Builder $q) => $q->where('id', '!=', $featured->id))
            ->limit(18)
            ->get();

        return [
            'featured' => $featured,
            'latest' => $latest,
            'theme' => $theme,
            'meta' => [
                'html_lang' => 'pt-BR',
                'title' => 'Dúvidas da Fé Católica: respostas claras para a vida real — IA Tio Ben',
                'description' => 'Perguntas católicas comuns sobre oração, sacramentos, sofrimento e discernimento, com respostas claras e aplicáveis.',
                'canonical' => url('/duvidas-da-fe-catolica'),
                'robots' => 'index,follow,max-image-preview:large',
                'og_title' => 'Dúvidas da Fé Católica — IA Tio Ben',
                'og_description' => 'Respostas claras para dúvidas comuns da fé católica e aplicação prática no dia a dia.',
                'og_url' => url('/duvidas-da-fe-catolica'),
                'og_image' => url('/og?title=' . rawurlencode('Dúvidas da Fé Católica') . '&description=' . rawurlencode('Respostas claras para a vida real')),
            ],
        ];
    }

    public function prayerHubDataEn(): array
    {
        return $this->hubDataByTheme('terco', 'en', '/en/practical-catholic-prayer', [
            'title' => 'Practical Catholic Prayer: rosary, routine and spiritual life — IA Tio Ben',
            'description' => 'Practical Catholic prayer guides: rosary, daily routine, silence and perseverance for real life.',
            'og_title' => 'Practical Catholic Prayer — IA Tio Ben',
            'og_description' => 'Rosary, prayer routine and practical spiritual life for everyday discipleship.',
        ]);
    }

    public function sacramentalLifeHubDataEn(): array
    {
        return $this->hubDataByTheme('cotidiano', 'en', '/en/practical-sacramental-life', [
            'title' => 'Practical Sacramental Life: Mass, confession and daily faith — IA Tio Ben',
            'description' => 'How to live a practical sacramental life: Mass, confession, communion and consistency in daily faith.',
            'og_title' => 'Practical Sacramental Life — IA Tio Ben',
            'og_description' => 'Mass, confession and communion with concrete steps for consistent Christian life.',
        ]);
    }

    public function catholicFaithQuestionsHubDataEn(): array
    {
        return $this->hubDataByTheme('homilia', 'en', '/en/catholic-faith-questions', [
            'title' => 'Catholic Faith Questions: clear answers for real life — IA Tio Ben',
            'description' => 'Common Catholic questions about prayer, sacraments, suffering and discernment with clear and practical answers.',
            'og_title' => 'Catholic Faith Questions — IA Tio Ben',
            'og_description' => 'Clear answers to common Catholic faith questions with practical application.',
        ]);
    }

    private function hubDataByTheme(string $themeSlug, string $lang, string $path, array $copy): array
    {
        $theme = BlogCatalog::findByRouteSlug($themeSlug, $lang);
        $theme = $theme ?: BlogCatalog::defaultTheme($lang);

        $query = $this->applyCategory($this->publicQuery($lang), $theme);
        $featured = $this->ordered(clone $query, true)->first();
        $latest = $this->ordered(clone $query)
            ->when($featured?->id, fn (Builder $q) => $q->where('id', '!=', $featured->id))
            ->limit(18)
            ->get();

        return [
            'featured' => $featured,
            'latest' => $latest,
            'theme' => $theme,
            'meta' => [
                'html_lang' => $lang === 'en' ? 'en' : 'pt-BR',
                'title' => $copy['title'],
                'description' => $copy['description'],
                'canonical' => url($path),
                'robots' => 'index,follow,max-image-preview:large',
                'og_title' => $copy['og_title'],
                'og_description' => $copy['og_description'],
                'og_url' => url($path),
                'og_image' => url('/og?title=' . rawurlencode($copy['og_title']) . '&description=' . rawurlencode($copy['og_description'])),
            ],
        ];
    }

    private function portalMeta(string $lang): array
    {
        if ($lang === 'en') {
            return [
                'html_lang' => 'en',
                'title' => 'IA Tio Ben Blog | Gospel, Daily Mass Readings and Christian Life',
                'description' => 'Catholic articles about the Gospel of the day, daily Mass readings, prayer, saints and practical Christian spirituality.',
                'canonical' => url('/en/blog'),
                'robots' => 'index,follow,max-image-preview:large',
                'og_title' => 'IA Tio Ben Blog | Gospel, Daily Mass Readings and Christian Life',
                'og_description' => 'Catholic articles about the Gospel of the day, daily Mass readings, prayer, saints and practical Christian spirituality.',
                'og_url' => url('/en/blog'),
                'og_image' => url('/og?title=' . rawurlencode('IA Tio Ben Blog') . '&description=' . rawurlencode('Gospel, daily Mass readings and Catholic spirituality')),
            ];
        }

        return [
            'html_lang' => 'pt-BR',
            'title' => 'Blog IA Tio Ben | Evangelho, Liturgia e Vida Cristã',
            'description' => 'Artigos católicos sobre Evangelho do dia, liturgia diária, oração, santos e espiritualidade para viver a fé no cotidiano.',
            'canonical' => url('/blog'),
            'robots' => 'index,follow,max-image-preview:large',
            'og_title' => 'Blog IA Tio Ben | Evangelho, Liturgia e Vida Cristã',
            'og_description' => 'Artigos católicos sobre Evangelho do dia, liturgia diária, oração, santos e espiritualidade para viver a fé no cotidiano.',
            'og_url' => url('/blog'),
            'og_image' => url('/og?title=' . rawurlencode('Blog IA Tio Ben') . '&description=' . rawurlencode('Evangelho, liturgia diária e espiritualidade católica')),
        ];
    }

    private function categoryMeta(array $theme, ?Category $category, string $lang): array
    {
        $label = $theme['label'] ?? $category?->name ?? 'Blog';
        $description = $category?->meta_description ?: ($theme['description'] ?? BlogCatalog::defaultTheme($lang)['description']);
        $path = $lang === 'en'
            ? '/en/blog/category/' . $theme['route_slug']
            : '/blog/categoria/' . $theme['route_slug'];

        if ($lang === 'en') {
            return [
                'html_lang' => 'en',
                'title' => ($category?->meta_title ?: $label . ' articles') . ' — IA Tio Ben Blog',
                'description' => $description,
                'canonical' => url($path),
                'robots' => 'index,follow,max-image-preview:large',
                'og_title' => $label . ' — IA Tio Ben Blog',
                'og_description' => $description,
                'og_url' => url($path),
                'og_image' => url('/og?title=' . rawurlencode($label) . '&description=' . rawurlencode($description)),
            ];
        }

        return [
            'html_lang' => 'pt-BR',
            'title' => ($category?->meta_title ?: $label . ': artigos católicos') . ' — Blog IA Tio Ben',
            'description' => $description,
            'canonical' => url($path),
            'robots' => 'index,follow,max-image-preview:large',
            'og_title' => $label . ' — Blog IA Tio Ben',
            'og_description' => $description,
            'og_url' => url($path),
            'og_image' => url('/og?title=' . rawurlencode($label) . '&description=' . rawurlencode($description)),
        ];
    }

    private function themeByRouteSlug(string $routeSlug, string $lang): ?array
    {
        $catalogTheme = BlogCatalog::findByRouteSlug($routeSlug, $lang);
        if ($catalogTheme) {
            return $catalogTheme;
        }

        $category = $this->categoryBySlug($routeSlug);
        if (!$category) {
            return null;
        }

        $slug = $category->slug ?: Str::slug($category->name);

        return array_merge(BlogCatalog::defaultTheme($lang), [
            'key' => $slug,
            'category_id' => $category->id,
            'category_name' => $category->name,
            'pt_slug' => $slug,
            'en_slug' => $slug,
            'route_slug' => $slug,
            'label' => $category->name,
            'description' => $category->description ?: BlogCatalog::defaultTheme($lang)['description'],
            'is_catalog_fallback' => true,
        ]);
    }

    private function themeForPost(Post $post, string $lang): array
    {
        $category = $post->category;
        if (!$category) {
            return BlogCatalog::defaultTheme($lang);
        }

        $known = BlogCatalog::findByRouteSlug($category->slug ?: Str::slug($category->name), $lang);
        if ($known) {
            return $known;
        }

        $slug = $category->slug ?: Str::slug($category->name);

        return array_merge(BlogCatalog::defaultTheme($lang), [
            'key' => $slug,
            'category_id' => $category->id,
            'category_name' => $category->name,
            'pt_slug' => $slug,
            'en_slug' => $slug,
            'route_slug' => $slug,
            'label' => $category->name,
            'description' => $category->description ?: BlogCatalog::defaultTheme($lang)['description'],
        ]);
    }

    private function applyCategory(Builder $query, array $theme): Builder
    {
        $ids = $this->categoryIdsForTheme($theme);

        if (empty($ids)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn('category_id', $ids);
    }

    /** @return array<int, string> */
    private function categoryIdsForTheme(array $theme): array
    {
        $cacheKey = md5(json_encode([
            $theme['category_id'] ?? null,
            $theme['category_name'] ?? null,
            $theme['pt_slug'] ?? null,
            $theme['en_slug'] ?? null,
            $theme['key'] ?? null,
        ]));

        if (isset($this->categoryIdCache[$cacheKey])) {
            return $this->categoryIdCache[$cacheKey];
        }

        $ids = [];
        if (!empty($theme['category_id'])) {
            $ids[] = (string) $theme['category_id'];
        }

        $hasSlug = Schema::hasColumn('categories', 'slug');

        $found = Category::query()
            ->where(function (Builder $q) use ($theme, $hasSlug) {
                if (!empty($theme['category_id'])) {
                    $q->orWhere('id', $theme['category_id']);
                }

                if (!empty($theme['category_name'])) {
                    $q->orWhere('name', $theme['category_name']);
                }

                if ($hasSlug) {
                    $slugs = array_filter([
                        $theme['pt_slug'] ?? null,
                        $theme['en_slug'] ?? null,
                        $theme['key'] ?? null,
                        !empty($theme['category_name']) ? Str::slug($theme['category_name']) : null,
                    ]);

                    if (!empty($slugs)) {
                        $q->orWhereIn('slug', array_values(array_unique($slugs)));
                    }
                }
            })
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->all();

        $ids = array_values(array_unique(array_filter(array_merge($ids, $found))));

        return $this->categoryIdCache[$cacheKey] = $ids;
    }

    private function firstCategoryForTheme(array $theme): ?Category
    {
        $ids = $this->categoryIdsForTheme($theme);
        if (empty($ids)) {
            return null;
        }

        return Category::query()->whereIn('id', $ids)->orderBy('name')->first();
    }

    private function categoryBySlug(string $slug): ?Category
    {
        $slug = Str::slug($slug);
        if ($slug === '') {
            return null;
        }

        $query = Category::query();

        if (Schema::hasColumn('categories', 'slug')) {
            $query->where('slug', $slug);
        } else {
            $query->where('name', str_replace('-', ' ', $slug));
        }

        return $query->first();
    }

    private function manualRelatedPosts(Post $post, string $lang, int $limit = 9): Collection
    {
        if (!Schema::hasTable('post_related_items')) {
            return collect();
        }

        $now = now();

        return Post::query()
            ->with('category')
            ->select('posts.*')
            ->join('post_related_items as pri', 'pri.related_post_id', '=', 'posts.id')
            ->where('pri.post_id', $post->id)
            ->where('posts.is_active', true)
            ->where('posts.lang', $lang)
            ->whereNotNull('posts.publish_date')
            ->where('posts.publish_date', '<=', $now)
            ->where(function (Builder $q) use ($now) {
                $q->whereNull('posts.expiry_date')->orWhere('posts.expiry_date', '>', $now);
            })
            ->orderBy('pri.sort_order')
            ->orderByDesc('posts.publish_date')
            ->limit($limit)
            ->get();
    }

    private function fillRelatedPosts(Post $post, Collection $manual, string $lang, int $limit = 9): Collection
    {
        $ids = $manual->pluck('id')->push($post->id)->map(fn ($id) => (string) $id)->all();
        $related = $manual;

        if ($related->count() < $limit && $post->category_id) {
            $sameCategory = $this->ordered($this->publicQuery($lang))
                ->where('category_id', $post->category_id)
                ->whereNotIn('id', $ids)
                ->limit($limit - $related->count())
                ->get();

            $related = $related->merge($sameCategory);
            $ids = $related->pluck('id')->push($post->id)->map(fn ($id) => (string) $id)->all();
        }

        if ($related->count() < $limit) {
            $fallback = $this->ordered($this->publicQuery($lang))
                ->whereNotIn('id', $ids)
                ->limit($limit - $related->count())
                ->get();

            $related = $related->merge($fallback);
        }

        return $related->take($limit)->values();
    }

    private function asideLinks(string $lang): array
    {
        if ($lang === 'en') {
            return [
                ['href' => url('/en/daily-mass-readings'), 'title' => 'Daily Mass Readings', 'desc' => 'Readings, psalm and Gospel organized by date.'],
                ['href' => url('/en/rosary'), 'title' => 'Holy Rosary', 'desc' => 'Mysteries and simple guide to pray with consistency.'],
                ['href' => url('/en/blog/category/saints'), 'title' => 'Saints', 'desc' => 'Examples of faith for practical Christian life.'],
            ];
        }

        return [
            ['href' => url('/cristao-catolico-e-financas'), 'title' => 'Cristão Católico e Finanças', 'desc' => 'Catecismo, santos e vida real: orçamento, dívidas e investimento ético.'],
            ['href' => url('/liturgia-diaria'), 'title' => 'Liturgia Diária', 'desc' => 'Leituras, salmo e Evangelho organizados por data.'],
            ['href' => url('/santo-terco'), 'title' => 'Santo Terço', 'desc' => 'Mistérios, guia e constância na oração.'],
            ['href' => url('/blog/categoria/santos'), 'title' => 'Santos', 'desc' => 'Histórias e ensinamentos para a vida cristã.'],
        ];
    }

    private function latestLinks(Collection $latest, string $lang): array
    {
        $prefix = $lang === 'en' ? '/en/blog/' : '/blog/';

        return $latest->map(fn (Post $post) => [
            'href' => url($prefix . $post->slug),
            'title' => $post->title,
            'desc' => $post->meta_description,
        ])->values()->all();
    }
}
