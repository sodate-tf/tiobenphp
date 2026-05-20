<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SitemapController extends Controller
{
    private string $baseUrl = 'https://www.iatioben.com.br';
    private string $tz = 'America/Sao_Paulo';

    /** @var array<string, bool> */
    private array $schemaCache = [];

    private function baseUrl(): string
    {
        $url = trim((string) config('app.url', $this->baseUrl));

        if ($url === '' || str_contains($url, 'localhost') || str_contains($url, '127.0.0.1')) {
            $url = $this->baseUrl;
        }

        return rtrim($url, '/');
    }

    private function xmlEscape(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    private function ymd(Carbon $date): string
    {
        return $date->copy()->timezone($this->tz)->format('Y-m-d');
    }

    private function now(): Carbon
    {
        return Carbon::now($this->tz);
    }

    private function currentDay(): Carbon
    {
        return $this->now()->startOfDay();
    }

    private function responseXml(string $xml): Response
    {
        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'Cache-Control' => 'public, max-age=900, s-maxage=3600, stale-while-revalidate=86400',
            'X-Robots-Tag' => 'noindex, follow',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | SITEMAP INDEX
    |--------------------------------------------------------------------------
    */

    public function index(): Response
    {
        $base = $this->baseUrl();
        $today = $this->currentDay();

        $sitemaps = [
            $this->sitemapNode("{$base}/sitemap-principal.xml", $today),
            $this->sitemapNode("{$base}/sitemap-today.xml", $today),
            $this->sitemapNode("{$base}/sitemap-liturgia-recent.xml", $today),
            $this->sitemapNode("{$base}/sitemap-liturgia-archive.xml", $today),
            $this->sitemapNode("{$base}/sitemap-en.xml", $today),
            $this->sitemapNode("{$base}/sitemap-blog.xml", $today),
            $this->sitemapNode("{$base}/sitemap-webstories.xml", $today),
            $this->sitemapNode("{$base}/sitemap-terco-webstories.xml", $today),
            $this->sitemapNode("{$base}/sitemap-recent.xml", $today),
        ];

        return $this->responseXml($this->buildSitemapIndexXml($sitemaps));
    }

    /*
    |--------------------------------------------------------------------------
    | SITEMAP PRINCIPAL: páginas fixas, hubs e arquivos PT
    |--------------------------------------------------------------------------
    */

    public function main(): Response
    {
        $base = $this->baseUrl();
        $today = $this->currentDay();
        $year = (int) $today->year;

        $urls = [];

        $urls[] = $this->node("{$base}/", $today, 'daily', '1.0');
        $urls[] = $this->node("{$base}/termo-de-responsabilidade", $today, 'yearly', '0.3');
        $urls[] = $this->node("{$base}/oracao-catolica-pratica", $today, 'daily', '0.85');
        $urls[] = $this->node("{$base}/vida-sacramental-pratica", $today, 'daily', '0.85');
        $urls[] = $this->node("{$base}/duvidas-da-fe-catolica", $today, 'daily', '0.85');

        $urls[] = $this->node("{$base}/liturgia-diaria", $today, 'daily', '1.0');
        $urls[] = $this->node("{$base}/liturgia-diaria/ano/{$year}", $today, 'weekly', '0.9');

        for ($month = 1; $month <= 12; $month++) {
            $mm = str_pad((string) $month, 2, '0', STR_PAD_LEFT);

            $urls[] = $this->node(
                "{$base}/liturgia-diaria/ano/{$year}/{$mm}",
                Carbon::createFromDate($year, $month, 1, $this->tz),
                'weekly',
                '0.85'
            );
        }

        foreach ($this->ptRosaryStaticPages() as $page) {
            $urls[] = $this->node("{$base}{$page['path']}", $today, $page['changefreq'], $page['priority']);
        }

        $urls = $this->dedupeByLocKeepNewestLastmod($urls);

        return $this->responseXml($this->buildSitemapXml($urls));
    }

    /*
    |--------------------------------------------------------------------------
    | SITEMAP TODAY: páginas diárias do dia atual PT + EN
    |--------------------------------------------------------------------------
    */

    public function todaySitemap(): Response
    {
        $base = $this->baseUrl();
        $today = $this->currentDay();

        $urls = [
            $this->node("{$base}/liturgia-diaria/{$today->format('d-m-Y')}", $today, 'daily', '1.0'),
            $this->node("{$base}/en/daily-mass-readings/{$today->format('m-d-Y')}", $today, 'daily', '1.0'),
        ];

        return $this->responseXml($this->buildSitemapXml($urls));
    }

    // Mantém compatibilidade caso sua rota antiga chame [SitemapController::class, 'today'].
    public function today(): Response
    {
        return $this->todaySitemap();
    }

    /*
    |--------------------------------------------------------------------------
    | LITURGIA RECENTE: últimos 60 dias sem repetir o dia atual
    |--------------------------------------------------------------------------
    */

    public function liturgyRecent(): Response
    {
        $base = $this->baseUrl();
        $today = $this->currentDay();
        $urls = [];

        for ($i = 1; $i <= 60; $i++) {
            $date = $today->copy()->subDays($i);

            $urls[] = $this->node("{$base}/liturgia-diaria/{$date->format('d-m-Y')}", $date, 'daily', '0.9');
            $urls[] = $this->node("{$base}/en/daily-mass-readings/{$date->format('m-d-Y')}", $date, 'daily', '0.9');
        }

        return $this->responseXml($this->buildSitemapXml($urls));
    }

    /*
    |--------------------------------------------------------------------------
    | LITURGIA ARCHIVE: ano atual sem hoje e sem os últimos 60 dias
    |--------------------------------------------------------------------------
    */

    public function liturgyArchive(): Response
    {
        $base = $this->baseUrl();
        $today = $this->currentDay();
        $year = (int) $today->year;
        $recentLimit = $today->copy()->subDays(60);

        $urls = [];
        $start = Carbon::createFromDate($year, 1, 1, $this->tz)->startOfDay();
        $end = Carbon::createFromDate($year, 12, 31, $this->tz)->startOfDay();

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            if ($date->isSameDay($today) || $date->greaterThan($recentLimit) && $date->lessThan($today)) {
                continue;
            }

            $isFuture = $date->greaterThan($today);

            $urls[] = $this->node(
                "{$base}/liturgia-diaria/{$date->format('d-m-Y')}",
                $date,
                $isFuture ? 'weekly' : 'monthly',
                $isFuture ? '0.75' : '0.6'
            );

            $urls[] = $this->node(
                "{$base}/en/daily-mass-readings/{$date->format('m-d-Y')}",
                $date,
                $isFuture ? 'weekly' : 'monthly',
                $isFuture ? '0.75' : '0.6'
            );
        }

        return $this->responseXml($this->buildSitemapXml($urls));
    }

    /*
    |--------------------------------------------------------------------------
    | SITEMAP EN: páginas fixas e hubs EN, sem duplicar páginas diárias
    |--------------------------------------------------------------------------
    */

    public function en(): Response
    {
        $base = $this->baseUrl();
        $today = $this->currentDay();
        $year = (int) $today->year;
        $urls = [];

        $urls[] = $this->node("{$base}/en", $today, 'daily', '1.0');
        $urls[] = $this->node("{$base}/en/terms-of-responsibility", $today, 'yearly', '0.3');
        $urls[] = $this->node("{$base}/en/practical-catholic-prayer", $today, 'daily', '0.85');
        $urls[] = $this->node("{$base}/en/practical-sacramental-life", $today, 'daily', '0.85');
        $urls[] = $this->node("{$base}/en/catholic-faith-questions", $today, 'daily', '0.85');
        $urls[] = $this->node("{$base}/en/daily-mass-readings", $today, 'daily', '0.9');
        $urls[] = $this->node("{$base}/en/daily-mass-readings/year/{$year}", $today, 'weekly', '0.8');

        for ($month = 1; $month <= 12; $month++) {
            $mm = str_pad((string) $month, 2, '0', STR_PAD_LEFT);

            $urls[] = $this->node(
                "{$base}/en/daily-mass-readings/year/{$year}/{$mm}",
                Carbon::createFromDate($year, $month, 1, $this->tz),
                'weekly',
                '0.7'
            );
        }

        foreach ($this->enRosaryStaticPages() as $page) {
            $urls[] = $this->node("{$base}{$page['path']}", $today, $page['changefreq'], $page['priority']);
        }

        $urls = $this->dedupeByLocKeepNewestLastmod($urls);

        return $this->responseXml($this->buildSitemapXml($urls));
    }

    /*
    |--------------------------------------------------------------------------
    | BLOG
    |--------------------------------------------------------------------------
    */

    public function blog(): Response
    {
        $base = $this->baseUrl();
        $today = $this->currentDay();
        $urls = [];

        $urls[] = $this->node("{$base}/blog", $today, 'daily', '0.9');
        $urls[] = $this->node("{$base}/blog/posts", $today, 'daily', '0.8');
        $urls[] = $this->node("{$base}/oracao-catolica-pratica", $today, 'daily', '0.85');
        $urls[] = $this->node("{$base}/vida-sacramental-pratica", $today, 'daily', '0.85');
        $urls[] = $this->node("{$base}/duvidas-da-fe-catolica", $today, 'daily', '0.85');
        $urls[] = $this->node("{$base}/en/blog", $today, 'daily', '0.9');
        $urls[] = $this->node("{$base}/en/practical-catholic-prayer", $today, 'daily', '0.9');
        $urls[] = $this->node("{$base}/en/practical-sacramental-life", $today, 'daily', '0.9');
        $urls[] = $this->node("{$base}/en/catholic-faith-questions", $today, 'daily', '0.9');
        $urls[] = $this->node("{$base}/en/blog/posts", $today, 'daily', '0.8');

        foreach ($this->ptBlogCategories() as $categorySlug) {
            $urls[] = $this->node("{$base}/blog/categoria/{$categorySlug}", $today, 'daily', '0.75');
        }

        foreach (['pt', 'en'] as $lang) {
            foreach ($this->getPublishedPostsForSitemap($lang) as $post) {
                $slug = trim((string) ($post->slug ?? ''));
                if ($slug === '') {
                    continue;
                }

                $path = $lang === 'en' ? "/en/blog/{$slug}" : "/blog/{$slug}";
                $lastmod = $this->postLastmod($post, $today);

                $urls[] = $this->node("{$base}{$path}", $lastmod, 'weekly', '0.7');
            }
        }

        $urls = $this->dedupeByLocKeepNewestLastmod($urls);

        return $this->responseXml($this->buildSitemapXml($urls));
    }

    /*
    |--------------------------------------------------------------------------
    | WEB STORIES
    |--------------------------------------------------------------------------
    */

    public function webstories(): Response
    {
        return $this->dailyWebstories('liturgia');
    }

    public function tercoWebstories(): Response
    {
        return $this->dailyWebstories('terco');
    }

    private function dailyWebstories(string $type): Response
    {
        $base = $this->baseUrl();
        $today = $this->currentDay();
        $year = (int) $today->year;

        $start = Carbon::createFromDate($year, 1, 1, $this->tz)->startOfDay();
        $end = Carbon::createFromDate($year, 12, 31, $this->tz)->startOfDay();
        $urls = [];

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $slug = $date->format('d-m-Y');

            $urls[] = $this->node(
                "{$base}/web-stories/{$type}-{$slug}/",
                $date,
                null,
                null
            );
        }

        return $this->responseXml($this->buildSitemapXml($urls, false));
    }

    /*
    |--------------------------------------------------------------------------
    | SITEMAP RECENT: descoberta rápida, com URLs mais importantes/recentes
    |--------------------------------------------------------------------------
    */

    public function recent(): Response
    {
        $base = $this->baseUrl();
        $today = $this->currentDay();
        $urls = [];

        $urls[] = $this->node("{$base}/", $today, 'daily', '1.0');
        $urls[] = $this->node("{$base}/liturgia-diaria", $today, 'daily', '0.9');
        $urls[] = $this->node("{$base}/liturgia-diaria/{$today->format('d-m-Y')}", $today, 'daily', '1.0');
        $urls[] = $this->node("{$base}/en/daily-mass-readings", $today, 'daily', '0.9');
        $urls[] = $this->node("{$base}/en/daily-mass-readings/{$today->format('m-d-Y')}", $today, 'daily', '1.0');
        $urls[] = $this->node("{$base}/blog", $today, 'daily', '0.9');
        $urls[] = $this->node("{$base}/oracao-catolica-pratica", $today, 'daily', '0.9');
        $urls[] = $this->node("{$base}/vida-sacramental-pratica", $today, 'daily', '0.9');
        $urls[] = $this->node("{$base}/duvidas-da-fe-catolica", $today, 'daily', '0.9');
        $urls[] = $this->node("{$base}/en/blog", $today, 'daily', '0.9');

        foreach (['pt', 'en'] as $lang) {
            foreach ($this->getPublishedPostsForSitemap($lang, 10) as $post) {
                $slug = trim((string) ($post->slug ?? ''));
                if ($slug === '') {
                    continue;
                }

                $path = $lang === 'en' ? "/en/blog/{$slug}" : "/blog/{$slug}";
                $urls[] = $this->node("{$base}{$path}", $this->postLastmod($post, $today), 'weekly', '0.7');
            }
        }

        $urls = $this->dedupeByLocKeepNewestLastmod($urls);

        return $this->responseXml($this->buildSitemapXml($urls));
    }

    /*
    |--------------------------------------------------------------------------
    | COMPATIBILIDADE: não gere /terco/{data}, pois essa rota não existe no PHP.
    |--------------------------------------------------------------------------
    */

    public function terco(): Response
    {
        $base = $this->baseUrl();
        $today = $this->currentDay();
        $urls = [];

        foreach ($this->ptRosaryStaticPages() as $page) {
            $urls[] = $this->node("{$base}{$page['path']}", $today, $page['changefreq'], $page['priority']);
        }

        foreach ($this->enRosaryStaticPages() as $page) {
            $urls[] = $this->node("{$base}{$page['path']}", $today, $page['changefreq'], $page['priority']);
        }

        return $this->responseXml($this->buildSitemapXml($this->dedupeByLocKeepNewestLastmod($urls)));
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS DE DADOS
    |--------------------------------------------------------------------------
    */

    private function getPublishedPostsForSitemap(string $lang, ?int $limit = null)
    {
        if (!class_exists(Post::class) || !$this->tableExists('posts')) {
            return collect();
        }

        $query = Post::query();

        if ($this->tableHasColumn('posts', 'lang')) {
            $query->where('lang', $lang);
        } elseif ($this->tableHasColumn('posts', 'language')) {
            $query->where('language', $lang);
        }

        if ($this->tableHasColumn('posts', 'is_active')) {
            $query->where('is_active', 1);
        }

        if ($this->tableHasColumn('posts', 'status')) {
            $query->whereIn('status', ['published', 'publish', 'ativo', 'active']);
        }

        if ($this->tableHasColumn('posts', 'published')) {
            $query->where('published', true);
        }

        if ($this->tableHasColumn('posts', 'publish_date')) {
            $query->whereNotNull('publish_date')
                ->where('publish_date', '<=', $this->now());
        }

        $orderColumn = $this->firstExistingColumn('posts', ['publish_date', 'updated_at', 'created_at', 'id']) ?? 'id';
        $select = $this->existingColumns('posts', ['slug', 'updated_at', 'publish_date', 'created_at']);

        if (!in_array('slug', $select, true)) {
            return collect();
        }

        $query->orderByDesc($orderColumn);

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query->get($select);
    }

    private function postLastmod($post, Carbon $fallback): Carbon
    {
        foreach (['updated_at', 'publish_date', 'created_at'] as $field) {
            if (!empty($post->{$field})) {
                try {
                    return Carbon::parse($post->{$field}, $this->tz);
                } catch (\Throwable) {
                    // Continua para o próximo campo.
                }
            }
        }

        return $fallback;
    }

    private function ptBlogCategories(): array
    {
        // Slugs reais usados no BlogController::buildSections().
        $fallback = ['liturgia', 'santos', 'terco', 'homilia', 'cotidiano', 'noticias'];

        if (!class_exists(Category::class) || !$this->tableExists('categories')) {
            return $fallback;
        }

        try {
            $categories = Category::query()
                ->orderBy($this->tableHasColumn('categories', 'name') ? 'name' : 'id')
                ->get($this->existingColumns('categories', ['slug', 'name', 'id']));

            $slugs = [];

            foreach ($categories as $category) {
                $raw = '';

                if ($this->tableHasColumn('categories', 'slug') && !empty($category->slug)) {
                    $raw = (string) $category->slug;
                } elseif ($this->tableHasColumn('categories', 'name') && !empty($category->name)) {
                    $raw = Str::slug((string) $category->name);
                }

                if ($raw !== '') {
                    $slugs[] = $raw;
                }
            }

            return array_values(array_unique(array_merge($fallback, $slugs)));
        } catch (\Throwable) {
            return $fallback;
        }
    }

    /** @return array<int, array{path: string, priority: string, changefreq: string}> */
    private function ptRosaryStaticPages(): array
    {
        return [
            ['path' => '/santo-terco', 'priority' => '0.95', 'changefreq' => 'weekly'],
            ['path' => '/santo-terco/como-rezar-o-terco', 'priority' => '0.92', 'changefreq' => 'weekly'],
            ['path' => '/santo-terco/misterios-gozosos', 'priority' => '0.9', 'changefreq' => 'monthly'],
            ['path' => '/santo-terco/misterios-dolorosos', 'priority' => '0.9', 'changefreq' => 'monthly'],
            ['path' => '/santo-terco/misterios-gloriosos', 'priority' => '0.9', 'changefreq' => 'monthly'],
            ['path' => '/santo-terco/misterios-luminosos', 'priority' => '0.9', 'changefreq' => 'monthly'],
        ];
    }

    /** @return array<int, array{path: string, priority: string, changefreq: string}> */
    private function enRosaryStaticPages(): array
    {
        return [
            ['path' => '/en/rosary', 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['path' => '/en/rosary/how-to-pray-the-rosary', 'priority' => '0.85', 'changefreq' => 'weekly'],
            ['path' => '/en/rosary/joyful-mysteries', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['path' => '/en/rosary/sorrowful-mysteries', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['path' => '/en/rosary/glorious-mysteries', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['path' => '/en/rosary/luminous-mysteries', 'priority' => '0.8', 'changefreq' => 'monthly'],
        ];
    }

    private function tableExists(string $table): bool
    {
        $key = "table:{$table}";

        if (!array_key_exists($key, $this->schemaCache)) {
            try {
                $this->schemaCache[$key] = Schema::hasTable($table);
            } catch (\Throwable) {
                $this->schemaCache[$key] = false;
            }
        }

        return $this->schemaCache[$key];
    }

    private function tableHasColumn(string $table, string $column): bool
    {
        $key = "column:{$table}.{$column}";

        if (!array_key_exists($key, $this->schemaCache)) {
            try {
                $this->schemaCache[$key] = Schema::hasColumn($table, $column);
            } catch (\Throwable) {
                $this->schemaCache[$key] = false;
            }
        }

        return $this->schemaCache[$key];
    }

    /** @param array<int, string> $columns */
    private function existingColumns(string $table, array $columns): array
    {
        return array_values(array_filter(
            $columns,
            fn (string $column): bool => $this->tableHasColumn($table, $column)
        ));
    }

    /** @param array<int, string> $columns */
    private function firstExistingColumn(string $table, array $columns): ?string
    {
        foreach ($columns as $column) {
            if ($this->tableHasColumn($table, $column)) {
                return $column;
            }
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS DE XML
    |--------------------------------------------------------------------------
    */

    private function sitemapNode(string $loc, Carbon $lastmod): array
    {
        return [
            'loc' => $loc,
            'lastmod' => $lastmod,
        ];
    }

    private function node(string $loc, Carbon $lastmod, ?string $changefreq, ?string $priority): array
    {
        return [
            'loc' => $loc,
            'lastmod' => $lastmod,
            'changefreq' => $changefreq,
            'priority' => $priority,
        ];
    }

    private function dedupeByLocKeepNewestLastmod(array $urls): array
    {
        $unique = [];

        foreach ($urls as $url) {
            $key = (string) ($url['loc'] ?? '');

            if ($key === '') {
                continue;
            }

            if (!isset($unique[$key])) {
                $unique[$key] = $url;
                continue;
            }

            $current = $unique[$key]['lastmod'] ?? null;
            $incoming = $url['lastmod'] ?? null;

            if ($current instanceof Carbon && $incoming instanceof Carbon && $incoming->gt($current)) {
                $unique[$key] = $url;
            }
        }

        return array_values($unique);
    }

    private function buildSitemapIndexXml(array $sitemaps): string
    {
        $items = [];

        foreach ($sitemaps as $sitemap) {
            $loc = $this->xmlEscape($sitemap['loc'] ?? '');
            $lastmod = $sitemap['lastmod'] instanceof Carbon
                ? $this->xmlEscape($this->ymd($sitemap['lastmod']))
                : null;

            if ($loc === '') {
                continue;
            }

            $items[] =
                "  <sitemap>\n" .
                "    <loc>{$loc}</loc>\n" .
                ($lastmod ? "    <lastmod>{$lastmod}</lastmod>\n" : '') .
                "  </sitemap>";
        }

        return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"
            . "<sitemapindex xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n"
            . implode("\n", $items)
            . "\n</sitemapindex>";
    }

    private function buildSitemapXml(array $urls, bool $includeChangefreqAndPriority = true): string
    {
        $items = [];

        foreach ($urls as $url) {
            $loc = $this->xmlEscape($url['loc'] ?? '');
            $lastmodValue = $url['lastmod'] ?? null;

            if ($loc === '' || !$lastmodValue instanceof Carbon) {
                continue;
            }

            $lastmod = $this->xmlEscape($this->ymd($lastmodValue));
            $changefreq = $url['changefreq'] ?? null;
            $priority = $url['priority'] ?? null;

            $items[] =
                "  <url>\n" .
                "    <loc>{$loc}</loc>\n" .
                "    <lastmod>{$lastmod}</lastmod>\n" .
                ($includeChangefreqAndPriority && $changefreq ? "    <changefreq>{$this->xmlEscape($changefreq)}</changefreq>\n" : '') .
                ($includeChangefreqAndPriority && $priority ? "    <priority>{$this->xmlEscape($priority)}</priority>\n" : '') .
                "  </url>";
        }

        return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"
            . "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n"
            . implode("\n", $items)
            . "\n</urlset>";
    }
}
