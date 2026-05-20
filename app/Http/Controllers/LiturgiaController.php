<?php

namespace App\Http\Controllers;

use App\Services\LiturgiaApiService;
use App\Services\LiturgiaNormalizer;
use App\Services\Liturgia\NetBibleEnService;
use App\Support\LiturgiaDate;
use App\Support\Seo;

class LiturgiaController extends Controller
{
    public function __construct(
        private LiturgiaApiService $api,
        private LiturgiaNormalizer $norm,
        private NetBibleEnService $netBibleEn, // ✅ EN patcher
    ) {}

    /**
     * HUB /liturgia-diaria
     * (por enquanto redireciona para a liturgia de hoje — depois você pode trocar por um hub completo)
     */
    public function home()
    {
        $today = new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo'));
        $todaySlug = LiturgiaDate::slugFrom(
            (int) $today->format('d'),
            (int) $today->format('m'),
            (int) $today->format('Y')
        );

        return redirect()->route('liturgia.day', ['data' => $todaySlug]);
    }

    /**
     * /liturgia-diaria/ano/{ano}
     * Placeholder funcional (não quebra rota).
     * Depois você implementa a listagem por meses com SEO.
     */
    public function year(string $ano)
    {
        // Se quiser manter 404 até implementar:
        // abort(404);

        // Versão mínima útil: redireciona para janeiro do ano.
        return redirect()->route('liturgia.month', ['ano' => $ano, 'mes' => '01']);
    }

    /**
     * /liturgia-diaria/ano/{ano}/{mes}
     * Placeholder funcional (não quebra rota).
     * Depois você implementa listagem por dias com SEO.
     */
    public function month(string $ano, string $mes)
    {
        // Se quiser manter 404 até implementar:
        // abort(404);

        // Versão mínima útil: redireciona para o dia 01 do mês.
        $slug = LiturgiaDate::slugFrom(1, (int) $mes, (int) $ano);

        return redirect()->route('liturgia.day', ['data' => $slug]);
    }

    /**
     * PT-BR — /liturgia-diaria/{data}
     */
    public function day(string $data)
    {
        $parsed = LiturgiaDate::parseSlug($data);
        if (!$parsed) {
            $meta = [
                'html_lang' => 'pt-BR',
                'title' => 'Liturgia Diária — IA Tio Ben',
                'description' => 'Evangelho, leituras e salmo do dia. Acesse e reze com a Liturgia Diária no IA Tio Ben.',
                'canonical' => Seo::canonical('/liturgia-diaria'),
                'hreflangs' => Seo::hreflangs('/liturgia-diaria', '/en/daily-mass-readings'),
                'og_title' => 'Liturgia Diária — IA Tio Ben',
                'og_description' => 'Evangelho, leituras e salmo do dia. Acesse e reze com a Liturgia Diária no IA Tio Ben.',
                'og_url' => Seo::canonical('/liturgia-diaria'),
                'og_image' => Seo::ogImage('/og/liturgia.png'),
                'robots' => 'noindex,nofollow',
                'jsonld_blocks' => [],
            ];

            return response()
                ->view('liturgia.invalid-date', compact('meta'))
                ->setStatusCode(404);
        }

        [$dd, $mm, $yyyy] = $parsed;

        $raw = $this->api->fetchByDate($dd, $mm, $yyyy);
        $page = $this->norm->normalize($raw, $dd, $mm, $yyyy);

        $ptPath = "/liturgia-diaria/{$data}";
        $enPath = "/en/daily-mass-readings/{$data}";
        $canonical = Seo::canonical($ptPath);
        $ogImage = Seo::ogImage("/og/liturgia/{$data}.png");

        $todayForMeta = new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo'));
        $todayMetaSlug = LiturgiaDate::slugFrom(
            (int) $todayForMeta->format('d'),
            (int) $todayForMeta->format('m'),
            (int) $todayForMeta->format('Y')
        );
        $isTodayPage = ($data === $todayMetaSlug);

        $title = $this->buildTitle($dd, $mm, $yyyy, $isTodayPage);
        $description = $this->buildRefsDescriptionFromData($raw, $page, $dd, $mm, $yyyy, $isTodayPage);

        $dt = new \DateTimeImmutable(sprintf('%04d-%02d-%02d 12:00:00', $yyyy, $mm, $dd));
        $prev = $dt->modify('-1 day');
        $next = $dt->modify('+1 day');

        $prevSlug = LiturgiaDate::slugFrom(
            (int) $prev->format('d'),
            (int) $prev->format('m'),
            (int) $prev->format('Y')
        );

        $nextSlug = LiturgiaDate::slugFrom(
            (int) $next->format('d'),
            (int) $next->format('m'),
            (int) $next->format('Y')
        );

        $today = $todayForMeta;
        $todaySlug = LiturgiaDate::slugFrom(
            (int) $today->format('d'),
            (int) $today->format('m'),
            (int) $today->format('Y')
        );

        $todayLabel = sprintf(
            '%02d/%02d/%04d',
            (int) $today->format('d'),
            (int) $today->format('m'),
            (int) $today->format('Y')
        );

        $dailyParagraph = $this->buildDailyParagraphPT(
            $page['dateLabel'],
            $page['celebration'] ?: null,
            $page['color'] ?: null
        );

        $blogComplement = $this->getBlogComplementByDateISO($page['dateISO']);

        $blogUrl = $blogComplement
            ? Seo::canonical('/blog/' . $blogComplement['slug'])
            : null;

        $breadcrumbJsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'IA Tio Ben', 'item' => Seo::canonical('/')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Liturgia Diária', 'item' => Seo::canonical('/liturgia-diaria')],
                ['@type' => 'ListItem', 'position' => 3, 'name' => $page['dateLabel'], 'item' => $canonical],
            ],
        ];

        $articleJsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $title,
            'description' => $description,
            'mainEntityOfPage' => $canonical,
            'datePublished' => $page['dateISO'] . 'T06:00:00-03:00',
            'dateModified' => $page['dateISO'] . 'T06:00:00-03:00',
            'author' => ['@type' => 'Organization', 'name' => 'IA Tio Ben'],
            'publisher' => ['@type' => 'Organization', 'name' => 'IA Tio Ben'],
        ];

        if ($blogComplement && $blogUrl) {
            $articleJsonLd['relatedLink'] = [$blogUrl];
            $articleJsonLd['subjectOf'] = [
                '@type' => 'Article',
                '@id' => $blogUrl,
                'url' => $blogUrl,
                'headline' => $blogComplement['title'],
                'description' => $blogComplement['paragraph'],
                'isPartOf' => [
                    '@type' => 'Blog',
                    'name' => 'Blog IA Tio Ben',
                    'url' => Seo::canonical('/blog'),
                ],
            ];
        }

        $faqJsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => [
                [
                    '@type' => 'Question',
                    'name' => "Onde encontrar a liturgia diária completa de {$page['dateLabel']}?",
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => "Nesta página você encontra a liturgia diária de {$page['dateLabel']} com Primeira Leitura, Salmo, Evangelho e, quando houver, Segunda Leitura e leituras adicionais (por exemplo, na Vigília Pascal).",
                    ],
                ],
                [
                    '@type' => 'Question',
                    'name' => 'Quais partes normalmente aparecem na liturgia diária?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Em geral: Primeira Leitura, Salmo Responsorial, Evangelho e, quando houver, Segunda Leitura, antífonas e orações próprias.',
                    ],
                ],
                [
                    '@type' => 'Question',
                    'name' => 'Como acessar a liturgia de outra data?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Use a página /liturgia-diaria e o calendário por mês/ano para abrir qualquer data em /liturgia-diaria/dd-mm-aaaa.',
                    ],
                ],
            ],
        ];

        $meta = [
            'html_lang' => 'pt-BR',
            'title' => $title,
            'description' => $description,
            'canonical' => $canonical,
            'hreflangs' => Seo::hreflangs($ptPath, $enPath),
            'og_title' => $title,
            'og_description' => $description,
            'og_url' => $canonical,
            'og_image' => $ogImage,
            'robots' => 'index,follow',
            'jsonld_blocks' => [
                ['id' => 'jsonld-breadcrumb-liturgia-dia', 'json' => $breadcrumbJsonLd],
                ['id' => 'jsonld-article-liturgia-dia', 'json' => $articleJsonLd],
                ['id' => 'jsonld-faq-liturgia-dia', 'json' => $faqJsonLd],
            ],
        ];

        $ads = [
            'slot_desktop' => '8534838745',
            'slot_mobile' => '1573844576',
        ];

        return view('liturgia.day', compact(
            'meta',
            'page',
            'prevSlug',
            'nextSlug',
            'todaySlug',
            'todayLabel',
            'dailyParagraph',
            'blogComplement',
            'ads'
        ));
    }

    /**
     * EN — /en/daily-mass-readings/{data}
     * ✅ Aplica NET Bible apenas aqui.
     */
    public function dayEn(string $data)
    {
        $parsed = LiturgiaDate::parseSlug($data);
        if (!$parsed) {
            $meta = [
                'html_lang' => 'en',
                'title' => 'Daily Mass Readings — IA Tio Ben',
                'description' => 'Gospel, readings and psalm of the day. Pray with the Daily Mass Readings on IA Tio Ben.',
                'canonical' => Seo::canonical('/en/daily-mass-readings'),
                'hreflangs' => Seo::hreflangs('/liturgia-diaria', '/en/daily-mass-readings'),
                'og_title' => 'Daily Mass Readings — IA Tio Ben',
                'og_description' => 'Gospel, readings and psalm of the day. Pray with the Daily Mass Readings on IA Tio Ben.',
                'og_url' => Seo::canonical('/en/daily-mass-readings'),
                'og_image' => Seo::ogImage('/og/liturgia.png'),
                'robots' => 'noindex,nofollow',
                'jsonld_blocks' => [],
            ];

            return response()
                ->view('liturgia.invalid-date', compact('meta'))
                ->setStatusCode(404);
        }

        [$dd, $mm, $yyyy] = $parsed;

        // Base (API PT) + normalização
        $raw = $this->api->fetchByDate($dd, $mm, $yyyy);
        $page = $this->norm->normalize($raw, $dd, $mm, $yyyy);

        // ✅ EN: força substituir (não “tentar”) as leituras para EN via NET Bible
        $page = $this->applyNetBibleEnToPage_FORCE($page);

        // ✅ EN: evitar PT em tabs (até ter fonte EN para isso)
        $page = $this->stripPortugueseOnlyBlocksForEn($page);

        // canonical/hreflang
        $ptPath = "/liturgia-diaria/{$data}";
        $enPath = "/en/daily-mass-readings/{$data}";
        $canonical = Seo::canonical($enPath);
        $ogImage = Seo::ogImage("/og/liturgia/{$data}.png");

        $todayForMeta = new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo'));
        $todayMetaSlug = LiturgiaDate::slugFrom(
            (int) $todayForMeta->format('d'),
            (int) $todayForMeta->format('m'),
            (int) $todayForMeta->format('Y')
        );
        $isTodayPage = ($data === $todayMetaSlug);

        // title/description EN (otimizado para CTR)
        $title = $this->buildTitleEn($page, $isTodayPage);
        $description = $this->buildRefsDescriptionFromDataEn($raw, $page, $isTodayPage);

        // prev/next/today
        $dt = new \DateTimeImmutable(sprintf('%04d-%02d-%02d 12:00:00', $yyyy, $mm, $dd));
        $prev = $dt->modify('-1 day');
        $next = $dt->modify('+1 day');

        $prevSlug = LiturgiaDate::slugFrom((int) $prev->format('d'), (int) $prev->format('m'), (int) $prev->format('Y'));
        $nextSlug = LiturgiaDate::slugFrom((int) $next->format('d'), (int) $next->format('m'), (int) $next->format('Y'));

        $today = $todayForMeta;
        $todaySlug = LiturgiaDate::slugFrom((int) $today->format('d'), (int) $today->format('m'), (int) $today->format('Y'));
        $todayLabel = sprintf('%02d/%02d/%04d', (int) $today->format('d'), (int) $today->format('m'), (int) $today->format('Y'));

        $dailyParagraph = $this->buildDailyParagraphEn(
            $page['dateISO'] ?? null,
            $page['celebration'] ?: null,
            $page['color'] ?: null
        );

        $blogComplement = null;

        $dateLabelEn = $this->formatDateEnFromISO($page['dateISO'] ?? null) ?? ($page['dateLabel'] ?? $data);

        $breadcrumbJsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'IA Tio Ben', 'item' => Seo::canonical('/')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Daily Mass Readings', 'item' => Seo::canonical('/en/daily-mass-readings')],
                ['@type' => 'ListItem', 'position' => 3, 'name' => $dateLabelEn, 'item' => $canonical],
            ],
        ];

        $articleJsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $title,
            'description' => $description,
            'mainEntityOfPage' => $canonical,
            'datePublished' => ($page['dateISO'] ?? '') . 'T06:00:00-03:00',
            'dateModified' => ($page['dateISO'] ?? '') . 'T06:00:00-03:00',
            'author' => ['@type' => 'Organization', 'name' => 'IA Tio Ben'],
            'publisher' => ['@type' => 'Organization', 'name' => 'IA Tio Ben'],
        ];

        $faqJsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => [
                [
                    '@type' => 'Question',
                    'name' => "Where can I find the complete Daily Mass Readings for {$dateLabelEn}?",
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => "On this page you can read the First Reading, Psalm, Gospel and—when applicable—the Second Reading and additional texts for the celebration.",
                    ],
                ],
                [
                    '@type' => 'Question',
                    'name' => 'What usually appears in the Daily Mass Readings?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Typically: First Reading, Responsorial Psalm, Gospel, and when applicable: Second Reading, antiphons and proper prayers.',
                    ],
                ],
                [
                    '@type' => 'Question',
                    'name' => 'How do I open the readings for another date?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Use the date selector or open any date as /en/daily-mass-readings/dd-mm-yyyy.',
                    ],
                ],
            ],
        ];

        $meta = [
            'html_lang' => 'en',
            'title' => $title,
            'description' => $description,
            'canonical' => $canonical,
            'hreflangs' => Seo::hreflangs($ptPath, $enPath),
            'og_title' => $title,
            'og_description' => $description,
            'og_url' => $canonical,
            'og_image' => $ogImage,
            'robots' => 'index,follow',
            'jsonld_blocks' => [
                ['id' => 'jsonld-breadcrumb-liturgia-en-dia', 'json' => $breadcrumbJsonLd],
                ['id' => 'jsonld-article-liturgia-en-dia', 'json' => $articleJsonLd],
                ['id' => 'jsonld-faq-liturgia-en-dia', 'json' => $faqJsonLd],
            ],
        ];

        $ads = [
            'slot_desktop' => '8534838745',
            'slot_mobile' => '1573844576',
        ];

        return view('en.daily-mass-readings.day', compact(
            'meta',
            'page',
            'prevSlug',
            'nextSlug',
            'todaySlug',
            'todayLabel',
            'dailyParagraph',
            'blogComplement',
            'ads'
        ));
    }

    private function buildTitle(int $dd, int $mm, int $yyyy, bool $isToday = false): string
    {
        return sprintf(
            $isToday
                ? 'Liturgia de Hoje %02d/%02d/%04d: Evangelho, Salmo e Leituras'
                : 'Liturgia Diária %02d/%02d/%04d: Evangelho, Salmo e Leituras',
            $dd,
            $mm,
            $yyyy
        );
    }

    private function buildDescription(int $dd, int $mm, int $yyyy, bool $isToday = false): string
    {
        return sprintf(
            $isToday
                ? 'Veja a liturgia de hoje %02d/%02d/%04d com Evangelho do dia, leituras da Missa e salmo responsorial. Leia e reze no IA Tio Ben.'
                : 'Veja a Liturgia Diária de %02d/%02d/%04d com Evangelho do dia, leituras da Missa e salmo responsorial. Leia e reze no IA Tio Ben.',
            $dd,
            $mm,
            $yyyy
        );
    }

    private function trimMetaText(string $text, int $limit = 158): string
    {
        $text = trim((string) preg_replace('/\s+/u', ' ', $text));

        $len = function_exists('mb_strlen') ? mb_strlen($text, 'UTF-8') : strlen($text);
        if ($len <= $limit) {
            return $text;
        }

        $cut = function_exists('mb_substr')
            ? mb_substr($text, 0, $limit - 1, 'UTF-8')
            : substr($text, 0, $limit - 1);

        $cut = (string) preg_replace('/\s+\S*$/u', '', $cut);
        return rtrim($cut, " ,;:•-–") . '…';
    }

    private function buildDailyParagraphPT(string $dateLabel, ?string $celebration, ?string $color): string
    {
        $intro = "Hoje, {$dateLabel}" . ($celebration ? ", celebramos {$celebration}" : "") . ".";
        $middle = "Reserve alguns minutos para ler com atenção, guardar uma frase no coração e transformar isso em uma decisão concreta no seu dia.";
        $outro = "Se desejar, compartilhe esta liturgia com alguém e reze também em família.";
        return "{$intro} {$middle} {$outro}";
    }

    /**
     * EN paragraph (simples, estável e sem depender de tradução do "celebration").
     */
    private function buildDailyParagraphEn(?string $dateISO, ?string $celebration, ?string $color): string
    {
        $dateHuman = $this->formatDateEnFromISO($dateISO);
        $intro = $dateHuman ? "Today, {$dateHuman}." : "Today.";
        $middle = "Take a few minutes to read slowly, keep one line in your heart, and turn it into a concrete decision for your day.";
        $outro = "If you wish, share these readings with someone and pray together.";
        return "{$intro} {$middle} {$outro}";
    }

    /**
     * PT description com refs (já existia).
     */
    private function buildRefsDescriptionFromData(
        array $raw,
        array $norm,
        ?int $dd = null,
        ?int $mm = null,
        ?int $yyyy = null,
        bool $isToday = false
    ): string {
        $primeira = $norm['primeiraRef'] ?? null;
        $salmo = $norm['salmoRef'] ?? null;
        $segunda = $norm['segundaRef'] ?? null;
        $evangelho = $norm['evangelhoRef'] ?? null;

        $L = $raw['leituras'] ?? null;
        $primeiraRaw = $L['primeiraLeitura'][0]['referencia'] ?? null;
        $salmoRaw = $L['salmo'][0]['referencia'] ?? null;
        $segundaRaw = $L['segundaLeitura'][0]['referencia'] ?? null;
        $evangelhoRaw = $L['evangelho'][0]['referencia'] ?? null;

        $primeira = $primeira ?: $primeiraRaw;
        $salmo = $salmo ?: $salmoRaw;
        $segunda = $segunda ?: $segundaRaw;
        $evangelho = $evangelho ?: $evangelhoRaw;

        if ($dd && $mm && $yyyy) {
            $date = sprintf('%02d/%02d/%04d', $dd, $mm, $yyyy);
            $prefix = $isToday
                ? "Liturgia de hoje {$date}: "
                : "Liturgia Diária {$date}: ";
        } else {
            $prefix = $isToday ? 'Liturgia de hoje: ' : 'Liturgia Diária: ';
        }

        $parts = [];
        if ($evangelho) $parts[] = "Evangelho {$evangelho}";
        if ($primeira) $parts[] = "1ª leitura {$primeira}";
        if ($salmo) $parts[] = "Salmo {$salmo}";
        if (count($parts) < 3 && $segunda) $parts[] = "2ª leitura {$segunda}";

        if ($parts) {
            return $this->trimMetaText($prefix . implode(', ', $parts) . '. Leia as leituras da Missa e reze no IA Tio Ben.');
        }

        return $this->trimMetaText($this->buildDescription(
            $dd ?: 0,
            $mm ?: 0,
            $yyyy ?: 0,
            $isToday
        ));
    }

    /**
     * ✅ EN description com refs (equivalente ao PT).
     * Formato: "First Reading: ... • Psalm: ... • Second Reading: ... • Gospel: ..."
     */
    private function buildRefsDescriptionFromDataEn(array $raw, array $norm, bool $isToday = false): string
    {
        $primeira = $norm['primeiraRef'] ?? null;
        $salmo = $norm['salmoRef'] ?? null;
        $segunda = $norm['segundaRef'] ?? null;
        $evangelho = $norm['evangelhoRef'] ?? null;

        $L = $raw['leituras'] ?? null;
        $primeiraRaw = $L['primeiraLeitura'][0]['referencia'] ?? null;
        $salmoRaw = $L['salmo'][0]['referencia'] ?? null;
        $segundaRaw = $L['segundaLeitura'][0]['referencia'] ?? null;
        $evangelhoRaw = $L['evangelho'][0]['referencia'] ?? null;

        $primeira = $primeira ?: $primeiraRaw;
        $salmo = $salmo ?: $salmoRaw;
        $segunda = $segunda ?: $segundaRaw;
        $evangelho = $evangelho ?: $evangelhoRaw;

        $dateHuman = $this->formatDateEnFromISO($norm['dateISO'] ?? null);
        $prefix = $isToday
            ? ($dateHuman ? "Today’s Mass Readings {$dateHuman}: " : "Today’s Mass Readings: ")
            : ($dateHuman ? "Daily Mass Readings {$dateHuman}: " : "Daily Mass Readings: ");

        $parts = [];
        if ($evangelho) $parts[] = "Gospel {$evangelho}";
        if ($primeira) $parts[] = "First Reading {$primeira}";
        if ($salmo) $parts[] = "Psalm {$salmo}";
        if (count($parts) < 3 && $segunda) $parts[] = "Second Reading {$segunda}";

        if ($parts) {
            return $this->trimMetaText($prefix . implode(', ', $parts) . '. Read and pray on IA Tio Ben.');
        }

        return $this->trimMetaText(
            $isToday
                ? 'Today’s Mass Readings with Gospel, Mass readings and responsorial psalm. Read and pray on IA Tio Ben.'
                : 'Daily Mass Readings with Gospel, Mass readings and responsorial psalm. Read and pray on IA Tio Ben.'
        );
    }

    /**
     * ✅ EN Title com data human.
     */
    private function buildTitleEn(array $page, bool $isToday = false): string
    {
        $dateHuman = $this->formatDateEnFromISO($page['dateISO'] ?? null);

        if ($isToday) {
            return $dateHuman
                ? "Today’s Mass Readings {$dateHuman}: Gospel, Psalm and Readings"
                : 'Today’s Mass Readings: Gospel, Psalm and Readings';
        }

        return $dateHuman
            ? "Daily Mass Readings {$dateHuman}: Gospel, Psalm and Readings"
            : 'Daily Mass Readings: Gospel, Psalm and Readings';
    }

    private function formatDateEnFromISO(?string $iso): ?string
    {
        $iso = trim((string) ($iso ?? ''));
        if ($iso === '') return null;

        try {
            // Carbon é opcional no controller, mas você já está usando no projeto.
            $d = \Carbon\Carbon::parse($iso)->locale('en');
            return $d->translatedFormat('F j, Y');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function slugify(string $s): string
    {
        $s = mb_strtolower(trim($s));
        $s = preg_replace('/[^\pL\pN]+/u', '-', $s) ?? $s;
        $s = trim($s, '-');
        return $s;
    }

    private function getBlogComplementByDateISO(string $iso): ?array
    {
        $map = [
            // '2026-02-06' => ['title' => '...', 'paragraph' => '...'],
        ];

        if (!isset($map[$iso])) return null;

        $title = $map[$iso]['title'];
        return [
            'title' => $title,
            'paragraph' => $map[$iso]['paragraph'],
            'slug' => $this->slugify($title),
        ];
    }

    /**
     * ✅ EN: força substituir textoHtml/texto mesmo se a API já trouxe PT.
     * - Usa método forceApplyToReadingItem() (não applyToReadingItem).
     * - Salmo: remove refrao PT.
     * - HTML: mantém padrão do seu Blade (textoHtml com <p>..</p>).
     */
    private function applyNetBibleEnToPage_FORCE(array $page): array
    {
        if (empty($page['leiturasFull']) || !is_array($page['leiturasFull'])) {
            return $page;
        }

        $l = $page['leiturasFull'];

        foreach (['primeiraLeitura', 'segundaLeitura', 'evangelho'] as $k) {
            if (!empty($l[$k]) && is_array($l[$k])) {
                $l[$k] = array_map(function ($it) {
                    return is_array($it) ? $this->netBibleEn->forceApplyToReadingItem($it) : $it;
                }, $l[$k]);
            }
        }

        if (!empty($l['salmo']) && is_array($l['salmo'])) {
            $l['salmo'] = array_map(function ($ps) {
                if (!is_array($ps)) return $ps;

                $ref = trim((string) ($ps['referencia'] ?? ''));
                if ($ref !== '') {
                    $html = $this->netBibleEn->fetchNetBibleHtml($ref);
                    if ($html) {
                        // padroniza com <p>..</p> para o mesmo render da leitura
                        $ps['textoHtml'] = '<p>' . $html . '</p>';
                        $ps['texto'] = trim(strip_tags($html));
                    } else {
                        $text = $this->netBibleEn->fetchNetBibleText($ref);
                        if ($text) {
                            $ps['texto'] = $text;
                            $ps['textoHtml'] = null;
                        }
                    }
                }

                // ✅ remove refrão (vem PT)
                $ps['refrao'] = null;

                return $ps;
            }, $l['salmo']);
        }

        if (!empty($l['extras']) && is_array($l['extras'])) {
            $l['extras'] = array_map(function ($x) {
                return is_array($x) ? $this->netBibleEn->forceApplyToReadingItem($x) : $x;
            }, $l['extras']);
        }

        $page['leiturasFull'] = $l;

        return $page;
    }

    /**
     * ✅ EN: remove/neutraliza blocos que continuariam PT (antífonas/orações/celebration/cor).
     * Isso impede “vazamento” de português no EN até você ter fonte EN para esses textos.
     */
    private function stripPortugueseOnlyBlocksForEn(array $page): array
    {
        // Celebration vem PT na API: melhor esconder
        $page['celebration'] = '';

        // Color PT -> EN (mínimo)
        $page['color'] = $this->mapColorToEn((string) ($page['color'] ?? ''));

        // Antífonas e orações: como vêm PT, zera
        $page['antifonasFull'] = [
            'entrada' => null,
            'comunhao' => null,
        ];

        $page['oracoesFull'] = [
            'coleta' => null,
            'oferendas' => null,
            'comunhao' => null,
            'extras' => [],
        ];

        return $page;
    }

    private function mapColorToEn(string $color): string
    {
        $c = mb_strtolower(trim($color));
        return match ($c) {
            'verde' => 'Green',
            'vermelho' => 'Red',
            'branco' => 'White',
            'roxo' => 'Purple',
            'rosa' => 'Rose',
            'preto' => 'Black',
            default => $color ? ucfirst($color) : '',
        };
    }
}
