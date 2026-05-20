<?php

namespace App\Http\Controllers;

use App\Services\LiturgiaApiService;
use App\Services\LiturgiaNormalizer;
use App\Services\Liturgia\NetBibleEnService;
use App\Support\LiturgiaDate;
use App\Support\Seo;

class DailyMassReadingsController extends Controller
{
    private const APP_TZ = 'America/Sao_Paulo';

    public function __construct(
        private LiturgiaApiService $api,
        private LiturgiaNormalizer $norm,
        private NetBibleEnService $netBibleEn,
    ) {}

    public function home()
    {
        $today = new \DateTimeImmutable('now', new \DateTimeZone(self::APP_TZ));

        $todaySlug = LiturgiaDate::slugFrom(
            (int) $today->format('d'),
            (int) $today->format('m'),
            (int) $today->format('Y')
        );

        return redirect()->route('en.liturgy.day', ['data' => $todaySlug]);
    }

    public function redirectDatePartsEn(string $a, string $b, string $c)
    {
        $normalized = LiturgiaDate::normalizeDaySlug("{$a}-{$b}-{$c}");

        if (!$normalized) {
            abort(404);
        }

        return redirect()
            ->route('en.liturgy.day', ['data' => $normalized['slug']], 301);
    }

    public function dayEn(string $data)
    {
        $normalized = LiturgiaDate::normalizeDaySlug($data);

        if (!$normalized) {
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

        $dd = $normalized['day'];
        $mm = $normalized['month'];
        $yyyy = $normalized['year'];
        $canonicalSlug = $normalized['slug'];

        if ($data !== $canonicalSlug) {
            return redirect()
                ->route('en.liturgy.day', ['data' => $canonicalSlug], 301);
        }

        $raw = $this->api->fetchByDate($dd, $mm, $yyyy);
        $page = $this->norm->normalize($raw, $dd, $mm, $yyyy);

        $page = $this->applyNetBibleEnToPage($page);

        $dateIso = $page['dateISO'] ?? sprintf('%04d-%02d-%02d', $yyyy, $mm, $dd);

        $ptPath = "/liturgia-diaria/{$canonicalSlug}";
        $enPath = "/en/daily-mass-readings/{$canonicalSlug}";
        $canonical = Seo::canonical($enPath);
        $ogImage = Seo::ogImage("/og/liturgia/{$canonicalSlug}.png");

        $title = $this->buildTitleEn($page);
        $description = $this->buildRefsDescriptionFromDataEn($raw, $page);

        $dt = new \DateTimeImmutable(
            sprintf('%04d-%02d-%02d 12:00:00', $yyyy, $mm, $dd),
            new \DateTimeZone(self::APP_TZ)
        );

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

        $today = new \DateTimeImmutable('now', new \DateTimeZone(self::APP_TZ));

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

        $dailyParagraph = $this->buildDailyParagraphEn(
            $dateIso,
            $page['celebration'] ?? null,
            $page['color'] ?? null
        );

        $blogComplement = null;

        $dateLabelEn = $this->formatDateEnFromISO($dateIso) ?? ($page['dateLabel'] ?? $canonicalSlug);

        $breadcrumbJsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => 'IA Tio Ben',
                    'item' => Seo::canonical('/'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => 'Daily Mass Readings',
                    'item' => Seo::canonical('/en/daily-mass-readings'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 3,
                    'name' => $dateLabelEn,
                    'item' => $canonical,
                ],
            ],
        ];

        $articleJsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $title,
            'description' => $description,
            'mainEntityOfPage' => $canonical,
            'datePublished' => $dateIso . 'T06:00:00-03:00',
            'dateModified' => $dateIso . 'T06:00:00-03:00',
            'author' => [
                '@type' => 'Organization',
                'name' => 'IA Tio Ben',
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'IA Tio Ben',
            ],
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
                        'text' => 'On this page you can read the First Reading, Psalm, Gospel and—when applicable—the Second Reading and additional texts for the celebration.',
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
                [
                    'id' => 'jsonld-breadcrumb-liturgia-en-dia',
                    'json' => $breadcrumbJsonLd,
                ],
                [
                    'id' => 'jsonld-article-liturgia-en-dia',
                    'json' => $articleJsonLd,
                ],
                [
                    'id' => 'jsonld-faq-liturgia-en-dia',
                    'json' => $faqJsonLd,
                ],
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

    private function buildDailyParagraphEn(?string $dateISO, ?string $celebration, ?string $color): string
    {
        $dateHuman = $this->formatDateEnFromISO($dateISO);
        $intro = $dateHuman ? "Today, {$dateHuman}." : 'Today.';
        $middle = 'Take a few minutes to read slowly, keep one line in your heart, and turn it into a concrete decision for your day.';
        $outro = 'If you wish, share these readings with someone and pray together.';

        return "{$intro} {$middle} {$outro}";
    }

    private function buildRefsDescriptionFromDataEn(array $raw, array $norm): string
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

        $parts = [];

        if ($primeira) {
            $parts[] = "First Reading: {$primeira}";
        }

        if ($salmo) {
            $parts[] = "Psalm: {$salmo}";
        }

        if ($segunda) {
            $parts[] = "Second Reading: {$segunda}";
        }

        if ($evangelho) {
            $parts[] = "Gospel: {$evangelho}";
        }

        $extras = $L['extras'] ?? [];

        if (is_array($extras) && count($extras) > 0) {
            $parts[] = 'Includes additional readings and proper texts for the celebration.';
        }

        $parts[] = 'Pray with the Daily Mass Readings on IA Tio Ben.';

        return implode(' • ', $parts);
    }

    private function buildTitleEn(array $page): string
    {
        $dateHuman = $this->formatDateEnFromISO($page['dateISO'] ?? null);
        $base = $dateHuman ? "Daily Mass Readings — {$dateHuman}" : 'Daily Mass Readings';

        return "{$base} | IA Tio Ben";
    }

    private function formatDateEnFromISO(?string $iso): ?string
    {
        $iso = trim((string) ($iso ?? ''));

        if ($iso === '') {
            return null;
        }

        try {
            $d = \Carbon\Carbon::parse($iso)->locale('en');

            return $d->translatedFormat('F j, Y');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function applyNetBibleEnToPage(array $page): array
    {
        if (empty($page['leiturasFull']) || !is_array($page['leiturasFull'])) {
            return $page;
        }

        $l = $page['leiturasFull'];

        foreach (['primeiraLeitura', 'segundaLeitura', 'evangelho'] as $k) {
            if (!empty($l[$k]) && is_array($l[$k])) {
                $l[$k] = array_map(function ($it) {
                    return is_array($it)
                        ? $this->netBibleEn->applyToReadingItem($it)
                        : $it;
                }, $l[$k]);
            }
        }

        if (!empty($l['salmo']) && is_array($l['salmo'])) {
            $l['salmo'] = array_map(function ($ps) {
                if (!is_array($ps)) {
                    return $ps;
                }

                $ref = trim((string) ($ps['referencia'] ?? ''));

                if ($ref !== '') {
                    $html = $this->netBibleEn->fetchNetBibleHtml($ref);
                    $text = $html ? null : $this->netBibleEn->fetchNetBibleText($ref);

                    if ($html) {
                        $ps['textoHtml'] = $html;
                        $ps['texto'] = strip_tags($html);
                    } elseif ($text) {
                        $ps['texto'] = $text;
                        $ps['textoHtml'] = null;
                    }
                }

                return $ps;
            }, $l['salmo']);
        }

        if (!empty($l['extras']) && is_array($l['extras'])) {
            $l['extras'] = array_map(function ($x) {
                return is_array($x)
                    ? $this->netBibleEn->applyToReadingItem($x)
                    : $x;
            }, $l['extras']);
        }

        $page['leiturasFull'] = $l;

        return $page;
    }
}