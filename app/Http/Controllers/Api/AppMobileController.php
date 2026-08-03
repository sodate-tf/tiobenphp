<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\LiturgiaApiService;
use App\Services\LiturgiaNormalizer;
use App\Services\VaticanWordOfDayService;
use App\Support\LiturgiaDate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AppMobileController extends Controller
{
    public function __construct(
        private LiturgiaApiService $liturgyApi,
        private LiturgiaNormalizer $liturgyNormalizer,
        private VaticanWordOfDayService $vaticanWordOfDay,
    ) {}

    public function home(Request $request)
    {
        $lang = $this->lang($request);
        $today = Carbon::now('America/Sao_Paulo');
        $todaySlug = LiturgiaDate::slugFrom((int) $today->format('d'), (int) $today->format('m'), (int) $today->format('Y'));
        $rosarySet = $this->rosarySetForDate($today);

        return response()->json([
            'brand' => [
                'name' => 'IA Tio Ben',
                'subtitle' => $lang === 'en' ? 'Daily Liturgy & Catholic AI' : 'Liturgia diÃ¡ria e IA catÃ³lica',
                'logoUrl' => url('/images/logo-amp.webp'),
                'heroImageUrl' => url('/images/tio-ben-transparente.webp'),
            ],
            'hero' => [
                'title' => $lang === 'en'
                    ? 'Daily Mass Readings, Rosary and Catholic reflections'
                    : 'Liturgia diÃ¡ria, Santo TerÃ§o e reflexÃµes catÃ³licas',
                'description' => $lang === 'en'
                    ? 'Pray with the readings of the day, follow the Rosary and read Catholic formation content in one mobile experience.'
                    : 'Acompanhe as leituras do dia, reze o Santo TerÃ§o e leia conteÃºdos de formaÃ§Ã£o catÃ³lica em uma experiÃªncia mobile.',
            ],
            'quickActions' => [
                [
                    'key' => 'liturgy',
                    'label' => $lang === 'en' ? 'Today readings' : 'Liturgia de hoje',
                    'screen' => 'liturgy',
                    'path' => $lang === 'en' ? "/en/daily-mass-readings/{$todaySlug}" : "/liturgia-diaria/{$todaySlug}",
                ],
                [
                    'key' => 'rosary',
                    'label' => $lang === 'en' ? 'Pray Rosary' : 'Rezar o TerÃ§o',
                    'screen' => 'rosary',
                    'path' => $lang === 'en' ? '/en/rosary' : '/santo-terco',
                ],
                [
                    'key' => 'blog',
                    'label' => 'Blog',
                    'screen' => 'blog',
                    'path' => $lang === 'en' ? '/en/blog' : '/blog',
                ],
                [
                    'key' => 'chat',
                    'label' => $lang === 'en' ? 'Ask Tio Ben' : 'Perguntar ao Tio Ben',
                    'screen' => 'chat',
                    'path' => '/',
                ],
            ],
            'hubs' => $this->hubs($lang),
            'today' => [
                'dateSlug' => $todaySlug,
                'dateLabel' => $today->format('d/m/Y'),
                'rosarySet' => $this->rosaryPayload($rosarySet, $lang),
            ],
            'latestPosts' => $this->postQuery($lang)
                ->limit(6)
                ->get()
                ->map(fn (Post $post) => $this->postCard($post))
                ->values(),
        ]);
    }

    public function posts(Request $request)
    {
        $lang = $this->lang($request);
        $limit = max(1, min((int) $request->query('limit', 20), 50));
        $query = trim((string) $request->query('q', ''));

        $posts = $this->postQuery($lang)
            ->when($query !== '', function ($builder) use ($query) {
                $builder->where(function ($q) use ($query) {
                    $q->where('posts.title', 'like', "%{$query}%")
                        ->orWhere('posts.meta_description', 'like', "%{$query}%")
                        ->orWhere('posts.keywords', 'like', "%{$query}%");
                });
            })
            ->limit($limit)
            ->get()
            ->map(fn (Post $post) => $this->postCard($post))
            ->values();

        return response()->json([
            'items' => $posts,
            'query' => $query,
            'lang' => $lang,
        ]);
    }

    public function post(Request $request, string $slug)
    {
        $lang = $this->lang($request);
        $post = $this->postQuery($lang)
            ->where('posts.slug', $slug)
            ->first();

        if (!$post) {
            return response()->json(['message' => 'Post not found.'], 404);
        }

        return response()->json([
            'post' => array_merge($this->postCard($post), [
                'content' => $this->cleanArticleText((string) $post->content),
                'keywords' => $post->keywords ?? '',
            ]),
        ]);
    }

    public function liturgyToday(Request $request)
    {
        $today = Carbon::now('America/Sao_Paulo');
        $slug = LiturgiaDate::slugFrom((int) $today->format('d'), (int) $today->format('m'), (int) $today->format('Y'));

        return $this->liturgy($request, $slug);
    }

    public function liturgy(Request $request, string $date)
    {
        $parsed = LiturgiaDate::parseSlug($date);
        if (!$parsed) {
            return response()->json(['message' => 'Invalid date. Use dd-mm-yyyy.'], 422);
        }

        [$day, $month, $year] = $parsed;
        $raw = $this->liturgyApi->fetchByDate($day, $month, $year);
        $page = $this->liturgyNormalizer->normalize($raw, $day, $month, $year);
        $dateCarbon = Carbon::create($year, $month, $day, 12, 0, 0, 'America/Sao_Paulo');
        $reflection = $this->vaticanWordOfDay->forDate($dateCarbon);

        $payload = [
            'dateSlug' => $page['dateSlug'],
            'dateISO' => $page['dateISO'],
            'dateLabel' => $page['dateLabel'],
            'dateHuman' => $dateCarbon->locale('pt_BR')->translatedFormat('j \\d\\e F \\d\\e Y'),
            'weekday' => $page['weekday'],
            'celebration' => $page['celebration'],
            'color' => $page['color'],
            'summary' => $this->liturgySummary($page),
            'reflection' => $reflection,
            'reflectionStatus' => $dateCarbon->isSameDay(Carbon::now('America/Sao_Paulo'))
                ? ($reflection ? 'available' : 'unavailable')
                : 'today_only',
            'navigation' => [
                'previous' => $this->slugFromCarbon($dateCarbon->copy()->subDay()),
                'today' => $this->slugFromCarbon(Carbon::today('America/Sao_Paulo')),
                'next' => $this->slugFromCarbon($dateCarbon->copy()->addDay()),
            ],
            'tabs' => $this->liturgyTabs($page, $reflection),
        ];

        if ($request->boolean('debug_reflection')) {
            $payload['reflectionDebug'] = $this->vaticanWordOfDay->debugForDate($dateCarbon);
        }

        return response()->json($payload);
    }

    public function rosaryToday(Request $request)
    {
        $lang = $this->lang($request);
        $set = $this->rosarySetForDate(Carbon::now('America/Sao_Paulo'));

        return response()->json($this->rosaryPayload($set, $lang));
    }

    public function rosary(Request $request, string $set)
    {
        $lang = $this->lang($request);
        $set = in_array($set, ['gozosos', 'dolorosos', 'gloriosos', 'luminosos'], true) ? $set : 'gozosos';

        return response()->json($this->rosaryPayload($set, $lang));
    }

    private function postQuery(string $lang)
    {
        return Post::query()
            ->with('category')
            ->where('posts.is_active', true)
            ->where(function ($query) use ($lang) {
                $query->where('posts.lang', $lang)
                    ->orWhereNull('posts.lang');
            })
            ->orderByRaw('COALESCE(posts.publish_date, posts.created_at) DESC');
    }

    private function postCard(Post $post): array
    {
        return [
            'id' => (string) $post->id,
            'title' => $post->title,
            'slug' => $post->slug,
            'description' => $post->meta_description ?: Str::limit($this->cleanText((string) $post->content), 160),
            'category' => optional($post->category)->name ?: 'Blog',
            'coverImageUrl' => $this->coverUrl($post->cover_image_url),
            'publishedAt' => optional($post->publish_date ?? $post->created_at)->toISOString(),
            'webUrl' => url(($post->lang === 'en' ? '/en/blog/' : '/blog/') . $post->slug),
        ];
    }

    private function coverUrl(?string $url): ?string
    {
        $url = trim((string) $url);
        if ($url === '') {
            return null;
        }

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            $host = parse_url($url, PHP_URL_HOST);
            $path = parse_url($url, PHP_URL_PATH);
            $query = parse_url($url, PHP_URL_QUERY);

            if (in_array($host, ['localhost', '127.0.0.1'], true) && $path) {
                return url($path) . ($query ? '?' . $query : '');
            }

            return $url;
        }

        if (str_starts_with($url, '//')) {
            return 'https:' . $url;
        }

        if (str_starts_with($url, '/')) {
            return url($url);
        }

        if (str_starts_with($url, 'storage/')) {
            return url('/' . $url);
        }

        return Storage::disk('public')->url(ltrim($url, '/'));
    }

    private function liturgyTabs(array $page, ?array $reflection = null): array
    {
        $readings = $page['leiturasFull'] ?? [];
        $tabs = [];

        foreach ([
            ['primeiraLeitura', '1Âª Leitura', 'reading'],
            ['segundaLeitura', '2Âª Leitura', 'reading'],
            ['salmo', 'Salmo', 'psalm'],
            ['evangelho', 'Evangelho', 'gospel'],
            ['extras', 'Extras', 'extra'],
        ] as [$key, $label, $kind]) {
            if ($key === 'extras' && $reflection) {
                $tabs[] = $this->reflectionTab($reflection);
            }

            $items = $readings[$key] ?? [];
            if (!is_array($items) || count($items) === 0) {
                continue;
            }

            foreach (array_values($items) as $index => $item) {
                $tabs[] = [
                    'id' => $key . '-' . ($index + 1),
                    'label' => count($items) > 1 ? "{$label} " . ($index + 1) : $label,
                    'kind' => $kind,
                    'reference' => $item['referencia'] ?? null,
                    'title' => $item['titulo'] ?? null,
                    'text' => $this->cleanText((string) ($item['texto'] ?? '')),
                    'html' => filled($item['textoHtml'] ?? null) ? (string) $item['textoHtml'] : null,
                    'refrain' => filled($item['refrao'] ?? null) ? $this->cleanText((string) $item['refrao']) : null,
                    'sourceLabel' => null,
                    'sourceUrl' => null,
                ];
            }
        }

        if ($reflection && !collect($tabs)->contains(fn (array $tab) => $tab['kind'] === 'reflection')) {
            $tabs[] = $this->reflectionTab($reflection);
        }

        return $tabs;
    }

    private function reflectionTab(array $reflection): array
    {
        return [
            'id' => 'reflection-1',
            'label' => 'ReflexÃ£o',
            'kind' => 'reflection',
            'reference' => null,
            'title' => $reflection['title'] ?? 'ReflexÃ£o da Palavra',
            'text' => $this->cleanText((string) ($reflection['content'] ?? '')),
            'html' => null,
            'refrain' => null,
            'sourceLabel' => $reflection['sourceLabel'] ?? null,
            'sourceUrl' => $reflection['sourceUrl'] ?? null,
        ];
    }

    private function liturgySummary(array $page): string
    {
        $parts = array_filter([
            $page['evangelhoRef'] ? 'Evangelho ' . $page['evangelhoRef'] : null,
            $page['primeiraRef'] ? '1Âª leitura ' . $page['primeiraRef'] : null,
            $page['salmoRef'] ? 'Salmo ' . $page['salmoRef'] : null,
        ]);

        return $parts
            ? implode(', ', $parts)
            : 'Leituras, salmo e Evangelho do dia.';
    }

    private function rosaryPayload(string $set, string $lang): array
    {
        $sets = [
            'gozosos' => [
                'label' => $lang === 'en' ? 'Joyful Mysteries' : 'MistÃ©rios Gozosos',
                'days' => $lang === 'en' ? 'Monday and Saturday' : 'Segunda e sÃ¡bado',
                'theme' => $lang === 'en' ? 'joy, humility and trust' : 'alegria, humildade e confianÃ§a',
                'items' => [
                    ['title' => 'A AnunciaÃ§Ã£o do Senhor', 'reference' => 'Lc 1,26-38'],
                    ['title' => 'A VisitaÃ§Ã£o de Maria a Isabel', 'reference' => 'Lc 1,39-56'],
                    ['title' => 'O Nascimento de Jesus', 'reference' => 'Lc 2,1-20'],
                    ['title' => 'A ApresentaÃ§Ã£o do Menino Jesus no Templo', 'reference' => 'Lc 2,22-35'],
                    ['title' => 'A Perda e o Encontro de Jesus no Templo', 'reference' => 'Lc 2,41-52'],
                ],
            ],
            'dolorosos' => [
                'label' => $lang === 'en' ? 'Sorrowful Mysteries' : 'MistÃ©rios Dolorosos',
                'days' => $lang === 'en' ? 'Tuesday and Friday' : 'TerÃ§a e sexta',
                'theme' => $lang === 'en' ? 'conversion, patience and redemptive love' : 'conversÃ£o, paciÃªncia e amor redentor',
                'items' => [
                    ['title' => 'A Agonia de Jesus no Horto', 'reference' => 'Mt 26,36-46'],
                    ['title' => 'A FlagelaÃ§Ã£o de Jesus', 'reference' => 'Jo 19,1'],
                    ['title' => 'A CoroaÃ§Ã£o de Espinhos', 'reference' => 'Mt 27,27-31'],
                    ['title' => 'Jesus Carrega a Cruz', 'reference' => 'Lc 23,26-32'],
                    ['title' => 'A CrucificaÃ§Ã£o e Morte de Jesus', 'reference' => 'Lc 23,33-46'],
                ],
            ],
            'gloriosos' => [
                'label' => $lang === 'en' ? 'Glorious Mysteries' : 'MistÃ©rios Gloriosos',
                'days' => $lang === 'en' ? 'Wednesday and Sunday' : 'Quarta e domingo',
                'theme' => $lang === 'en' ? 'hope, resurrection and the Holy Spirit' : 'esperanÃ§a, ressurreiÃ§Ã£o e EspÃ­rito Santo',
                'items' => [
                    ['title' => 'A RessurreiÃ§Ã£o de Jesus', 'reference' => 'Mt 28,1-10'],
                    ['title' => 'A AscensÃ£o do Senhor', 'reference' => 'At 1,6-11'],
                    ['title' => 'A Vinda do EspÃ­rito Santo', 'reference' => 'At 2,1-13'],
                    ['title' => 'A AssunÃ§Ã£o de Maria', 'reference' => 'Ap 12,1-6'],
                    ['title' => 'A CoroaÃ§Ã£o de Maria como Rainha', 'reference' => 'Ap 12,1'],
                ],
            ],
            'luminosos' => [
                'label' => $lang === 'en' ? 'Luminous Mysteries' : 'MistÃ©rios Luminosos',
                'days' => $lang === 'en' ? 'Thursday' : 'Quinta-feira',
                'theme' => $lang === 'en' ? 'light, discipleship and Eucharist' : 'luz, discipulado e Eucaristia',
                'items' => [
                    ['title' => 'O Batismo de Jesus no JordÃ£o', 'reference' => 'Mt 3,13-17'],
                    ['title' => 'As Bodas de CanÃ¡', 'reference' => 'Jo 2,1-11'],
                    ['title' => 'O AnÃºncio do Reino e o Convite Ã  ConversÃ£o', 'reference' => 'Mc 1,14-15'],
                    ['title' => 'A TransfiguraÃ§Ã£o do Senhor', 'reference' => 'Lc 9,28-36'],
                    ['title' => 'A InstituiÃ§Ã£o da Eucaristia', 'reference' => 'Lc 22,14-20'],
                ],
            ],
        ];

        return array_merge(['key' => $set], $sets[$set]);
    }

    private function rosarySetForDate(Carbon $date): string
    {
        return match ((int) $date->dayOfWeek) {
            1, 6 => 'gozosos',
            2, 5 => 'dolorosos',
            3, 0 => 'gloriosos',
            default => 'luminosos',
        };
    }

    private function hubs(string $lang): array
    {
        if ($lang === 'en') {
            return [
                ['title' => 'Practical Catholic Prayer', 'description' => 'Simple guidance for daily prayer.', 'path' => '/en/practical-catholic-prayer'],
                ['title' => 'Practical Sacramental Life', 'description' => 'Live the sacraments with clarity.', 'path' => '/en/practical-sacramental-life'],
                ['title' => 'Catholic Faith Questions', 'description' => 'Questions and answers about the faith.', 'path' => '/en/catholic-faith-questions'],
            ];
        }

        return [
            ['title' => 'OraÃ§Ã£o CatÃ³lica PrÃ¡tica', 'description' => 'Guias simples para rezar melhor no dia a dia.', 'path' => '/oracao-catolica-pratica'],
            ['title' => 'Vida Sacramental PrÃ¡tica', 'description' => 'ConteÃºdo para viver melhor os sacramentos.', 'path' => '/vida-sacramental-pratica'],
            ['title' => 'DÃºvidas da FÃ© CatÃ³lica', 'description' => 'Perguntas e respostas sobre a fÃ©.', 'path' => '/duvidas-da-fe-catolica'],
            ['title' => 'CristÃ£o CatÃ³lico e FinanÃ§as', 'description' => 'Vida financeira com consciÃªncia cristÃ£.', 'path' => '/cristao-catolico-e-financas'],
        ];
    }

    private function lang(Request $request): string
    {
        return $request->query('lang') === 'en' ? 'en' : 'pt';
    }

    private function cleanText(string $text): string
    {
        $text = (string) preg_replace('/<\s*br\s*\/?>/i', "\n", $text);
        $text = (string) preg_replace('/<\s*\/\s*(p|div|h[1-6]|blockquote|section|article)\s*>/i', "\n\n", $text);
        $text = (string) preg_replace('/<\s*li\b[^>]*>/i', "\n- ", $text);
        $text = (string) preg_replace('/<\s*\/\s*li\s*>/i', "\n", $text);
        $text = html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = (string) preg_replace('/[ \t]+/', ' ', $text);
        $text = (string) preg_replace("/ *\n */", "\n", $text);
        $text = (string) preg_replace("/\n{3,}/", "\n\n", $text);

        return trim($text);
    }

    private function cleanArticleText(string $text): string
    {
        $text = (string) preg_replace('/<\s*h[1-6]\b[^>]*>/i', "\n\n## ", $text);
        $text = (string) preg_replace('/<\s*blockquote\b[^>]*>/i', "\n\n> ", $text);

        return $this->cleanText($text);
    }

    private function slugFromCarbon(Carbon $date): string
    {
        return LiturgiaDate::slugFrom((int) $date->format('d'), (int) $date->format('m'), (int) $date->format('Y'));
    }
}

