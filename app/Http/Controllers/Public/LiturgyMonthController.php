<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LiturgyMonthController extends Controller
{
    private function parseYear(?string $raw): ?int
    {
        if ($raw === null || !ctype_digit($raw)) return null;
        $y = (int) $raw;
        return ($y >= 1900 && $y <= 2100) ? $y : null;
    }

    private function parseMonth(?string $raw): ?int
    {
        if ($raw === null || !ctype_digit($raw)) return null;
        $m = (int) $raw;
        return ($m >= 1 && $m <= 12) ? $m : null;
    }

    private function assertYearWithinWindow(int $year): void
    {
        $current = (int) now()->format('Y');
        abort_if($year < $current - 1 || $year > $current + 1, 404);
    }

    private function pad2(int $n): string
    {
        return str_pad((string)$n, 2, '0', STR_PAD_LEFT);
    }

    private function todaySaoPaulo(): Carbon
    {
        return Carbon::now('America/Sao_Paulo')->startOfDay();
    }

    private function monthLabelPt(int $year, int $month): string
    {
        return Carbon::create($year, $month, 1)->locale('pt_BR')->translatedFormat('F');
    }

    private function monthLabelEn(int $year, int $month): string
    {
        return Carbon::create($year, $month, 1)->locale('en')->translatedFormat('F');
    }

    private function slugFromDate(Carbon $d): string
    {
        return $d->format('d-m-Y');
    }

    private function labelFromSlug(string $slug): string
    {
        return str_replace('-', '/', $slug);
    }

    private function weekdayIndexMondayFirst(Carbon $date): int
    {
        // Carbon: dayOfWeek 0=Sunday..6=Saturday => seg=0..dom=6
        return ($date->dayOfWeek + 6) % 7;
    }

    private function getPrevMonth(int $year, int $month): array
    {
        return ($month === 1)
            ? ['year' => $year - 1, 'month' => 12]
            : ['year' => $year, 'month' => $month - 1];
    }

    private function getNextMonth(int $year, int $month): array
    {
        return ($month === 12)
            ? ['year' => $year + 1, 'month' => 1]
            : ['year' => $year, 'month' => $month + 1];
    }

    private function getSundaysInMonth(int $year, int $month, string $basePathDay): array
    {
        $total = Carbon::create($year, $month, 1)->daysInMonth;
        $out = [];
        for ($day = 1; $day <= $total; $day++) {
            $d = Carbon::create($year, $month, $day);
            if ($d->dayOfWeek === Carbon::SUNDAY) {
                $slug = sprintf('%s-%s-%d', $this->pad2($day), $this->pad2($month), $year);
                $out[] = ['day' => $day, 'slug' => $slug, 'href' => "{$basePathDay}/{$slug}"];
            }
        }
        return $out;
    }

    private function siteUrl(): string
    {
        return rtrim(config('app.url') ?: url('/'), '/');
    }

    private function buildBreadcrumbSchema(array $args): array
    {
        // args: baseName, baseUrl, year, yearUrl, monthName, monthUrl
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type'=>'ListItem','position'=>1,'name'=>$args['baseName'],'item'=>$args['baseUrl']],
                ['@type'=>'ListItem','position'=>2,'name'=>(string)$args['year'],'item'=>$args['yearUrl']],
                ['@type'=>'ListItem','position'=>3,'name'=>$args['monthName'],'item'=>$args['monthUrl']],
            ],
        ];
    }

    private function buildCollectionSchema(array $args): array
    {
        // args: lang, siteUrl, canonicalUrl, monthName, year, month, days[]
        return [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => ($args['lang'] === 'pt')
                ? "Calendário da Liturgia Diária de {$args['monthName']} {$args['year']}"
                : "Daily Mass Readings calendar for {$args['monthName']} {$args['year']}",
            'url' => $args['canonicalUrl'],
            'inLanguage' => ($args['lang'] === 'pt') ? 'pt-BR' : 'en',
            'isPartOf' => [
                '@type' => 'WebSite',
                'name' => 'IA Tio Ben',
                'url' => $args['siteUrl'],
            ],
            'about' => ($args['lang'] === 'pt')
                ? [
                    ['@type'=>'Thing','name'=>'Liturgia Diária'],
                    ['@type'=>'Thing','name'=>'Leituras da Missa'],
                    ['@type'=>'Thing','name'=>'Evangelho do dia'],
                    ['@type'=>'Thing','name'=>'Salmo responsorial'],
                ]
                : [
                    ['@type'=>'Thing','name'=>'Daily Mass Readings'],
                    ['@type'=>'Thing','name'=>'Mass readings'],
                    ['@type'=>'Thing','name'=>'Gospel of the day'],
                    ['@type'=>'Thing','name'=>'Responsorial Psalm'],
                ],
            'mainEntity' => [
                '@type' => 'ItemList',
                'itemListOrder' => 'https://schema.org/ItemListOrderAscending',
                'numberOfItems' => count($args['days']),
                'itemListElement' => array_map(function ($d, $idx) use ($args) {
                    $name = ($args['lang'] === 'pt')
                        ? "Liturgia Diária {$this->pad2($d['day'])}/{$this->pad2($args['month'])}/{$args['year']}"
                        : "Daily Mass Readings {$this->pad2($d['day'])}/{$this->pad2($args['month'])}/{$args['year']}";
                    return [
                        '@type' => 'ListItem',
                        'position' => $idx + 1,
                        'name' => $name,
                        'url' => $args['siteUrl'].$d['href'],
                    ];
                }, $args['days'], array_keys($args['days'])),
            ],
        ];
    }

    private function buildFaqSchema(array $args): array
    {
        // args: lang, year, monthName, todaySlug, baseDayPath
        $q1 = ($args['lang'] === 'pt') ? 'Como acessar a liturgia de hoje?' : 'How can I access today’s readings?';
        $a1 = ($args['lang'] === 'pt')
            ? "Use o botão “Liturgia de hoje” no topo do calendário ou acesse diretamente {$args['baseDayPath']}/{$args['todaySlug']}."
            : "Use the “Today” button at the top of the calendar or go directly to {$args['baseDayPath']}/{$args['todaySlug']}.";

        $q2 = ($args['lang'] === 'pt')
            ? "Esta página contém a liturgia completa de cada dia de {$args['monthName']} {$args['year']}?"
            : "Does this page include the full readings for each day of {$args['monthName']} {$args['year']}?";

        $a2 = ($args['lang'] === 'pt')
            ? "Sim. Este é um calendário mensal com links diretos para cada data. Em cada dia você encontra leituras, salmo e evangelho completos."
            : "Yes. This is a monthly calendar with direct links for each date. Each day includes the readings, psalm, and Gospel.";

        $q3 = ($args['lang'] === 'pt')
            ? 'Posso navegar para outros meses e anos?'
            : 'Can I browse other months and years?';

        $a3 = ($args['lang'] === 'pt')
            ? 'Sim. Você pode usar os botões de mês anterior/próximo e também acessar o calendário anual do mesmo ano.'
            : 'Yes. Use the previous/next month buttons and the yearly calendar for the same year.';

        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => [
                ['@type'=>'Question','name'=>$q1,'acceptedAnswer'=>['@type'=>'Answer','text'=>$a1]],
                ['@type'=>'Question','name'=>$q2,'acceptedAnswer'=>['@type'=>'Answer','text'=>$a2]],
                ['@type'=>'Question','name'=>$q3,'acceptedAnswer'=>['@type'=>'Answer','text'=>$a3]],
            ],
        ];
    }

    public function ptMonth(Request $request, string $ano, string $mes)
    {
        $year = $this->parseYear($ano);
        $month = $this->parseMonth($mes);
        abort_if(!$year || !$month, 404);
        $this->assertYearWithinWindow($year);

        $siteUrl = $this->siteUrl();

        $today = $this->todaySaoPaulo();
        $todaySlug = $this->slugFromDate($today);
        $todayLabel = $this->labelFromSlug($todaySlug);

        $isMonthOfToday = ((int)$today->year === $year && (int)$today->month === $month);

        $monthName = $this->monthLabelPt($year, $month);
        $monthNameYear = "{$monthName} {$year}";

        $canonicalPath = "/liturgia-diaria/ano/{$year}/".$this->pad2($month);
        $canonical = $siteUrl.$canonicalPath;

        $monthNameTitle = ucfirst($monthName);

        $title = "Liturgia Diária de {$monthNameTitle} {$year}: Calendário, Leituras e Evangelho";

        $description = $isMonthOfToday
            ? "Veja a Liturgia Diária de {$monthName} de {$year}: liturgia de hoje, calendário completo, leituras da Missa, salmo responsorial e evangelho de cada dia."
            : "Veja a Liturgia Diária de {$monthName} de {$year}: calendário completo com leituras da Missa, salmo responsorial e evangelho de cada dia.";

        $yearHref = "/liturgia-diaria/ano/{$year}";
        $enHref = "/en/daily-mass-readings/year/{$year}/".$this->pad2($month);

        $totalDays = Carbon::create($year, $month, 1)->daysInMonth;

        $days = [];
        for ($d = 1; $d <= $totalDays; $d++) {
            $slug = sprintf('%s-%s-%d', $this->pad2($d), $this->pad2($month), $year);
            $days[] = ['day' => $d, 'slug' => $slug, 'href' => "/liturgia-diaria/{$slug}"];
        }

        // grid (segunda->domingo)
        $firstDate = Carbon::create($year, $month, 1)->startOfDay();
        $startOffset = $this->weekdayIndexMondayFirst($firstDate);

        $cells = array_merge(array_fill(0, $startOffset, null), $days);
        $rem = count($cells) % 7;
        if ($rem !== 0) $cells = array_merge($cells, array_fill(0, 7 - $rem, null));

        $prevMonth = $this->getPrevMonth($year, $month);
        $nextMonth = $this->getNextMonth($year, $month);

        // baseDate p/ prev/next do aside (igual à lógica TS)
        $baseDate = $isMonthOfToday ? $today->copy() : Carbon::create($year, $month, 1);
        $prevSlug = $this->slugFromDate($baseDate->copy()->subDay());
        $nextSlug = $this->slugFromDate($baseDate->copy()->addDay());

        $sundays = $this->getSundaysInMonth($year, $month, '/liturgia-diaria');

        // JSON-LD
        $breadcrumb = $this->buildBreadcrumbSchema([
            'baseName' => 'Liturgia Diária',
            'baseUrl' => $siteUrl.'/liturgia-diaria',
            'year' => $year,
            'yearUrl' => $siteUrl.$yearHref,
            'monthName' => $monthName,
            'monthUrl' => $canonical,
        ]);

        $collection = $this->buildCollectionSchema([
            'lang' => 'pt',
            'siteUrl' => $siteUrl,
            'canonicalUrl' => $canonical,
            'monthName' => $monthName,
            'year' => $year,
            'month' => $month,
            'days' => $days,
        ]);

        $faq = $this->buildFaqSchema([
            'lang' => 'pt',
            'year' => $year,
            'monthName' => $monthName,
            'todaySlug' => $todaySlug,
            'baseDayPath' => '/liturgia-diaria',
        ]);

        $meta = [
            'html_lang' => 'pt-BR',
            'title' => $title,
            'description' => $description,
            'canonical' => $canonical,
            'robots' => 'index,follow',
            'hreflangs' => [
                'pt-BR' => $canonical,
                'en' => $siteUrl.$enHref,
                'x-default' => $canonical,
            ],
            'og_title' => $title,
            'og_description' => $description,
            'og_url' => $canonical,
            'og_image' => $siteUrl.'/og/liturgia.png',
            'jsonld_blocks' => [
                ['id' => 'jsonld-breadcrumb', 'json' => $breadcrumb],
                ['id' => 'jsonld-collection', 'json' => $collection],
                ['id' => 'jsonld-faq', 'json' => $faq],
            ],
        ];

        $currentYear = (int) now()->format('Y');

        return view('liturgia.month', [
            'meta' => $meta,

            'year' => $year,
            'month' => $month,
            'monthName' => $monthName,
            'monthNameYear' => $monthNameYear,

            'days' => $days,
            'cells' => $cells,
            'sundays' => $sundays,

            'todaySlug' => $todaySlug,
            'todayLabel' => $todayLabel,
            'isMonthOfToday' => $isMonthOfToday,

            'prevMonth' => $prevMonth,
            'nextMonth' => $nextMonth,

            'yearHref' => $yearHref,

            // janela permitida
            'yearMin' => $currentYear - 1,
            'yearMax' => $currentYear + 1,

            // para o switcher (URLs)
            'ptHref' => $canonicalPath,
            'enHref' => $enHref,

            // aside nav
            'prevSlug' => $prevSlug,
            'nextSlug' => $nextSlug,

            // ads
            'ads' => [
                'slot_aside_300x250' => '8534838745',
            ],
        ]);
    }

    public function enMonth(Request $request, string $year, string $month)
    {
        $yr = $this->parseYear($year);
        $mo = $this->parseMonth($month);
        abort_if(!$yr || !$mo, 404);
        $this->assertYearWithinWindow($yr);

        $siteUrl = $this->siteUrl();

        $today = $this->todaySaoPaulo(); // mantém consistência do “Hoje”
        $todaySlug = $this->slugFromDate($today);
        $todayLabel = $today->format('m/d/Y'); // opcional para EN (ou use labelFromSlug se preferir)

        $isMonthOfToday = ((int)$today->year === $yr && (int)$today->month === $mo);

        $monthName = $this->monthLabelEn($yr, $mo);
        $monthNameYear = "{$monthName} {$yr}";

        $canonicalPath = "/en/daily-mass-readings/year/{$yr}/".$this->pad2($mo);
        $canonical = $siteUrl.$canonicalPath;

        $title = "Daily Mass Readings for {$monthName} {$yr}: Calendar, Readings and Gospel";

        $description = $isMonthOfToday
            ? "See the Daily Mass Readings for {$monthName} {$yr}: today’s readings, monthly calendar, Mass readings, responsorial psalm and Gospel for each day."
            : "See the Daily Mass Readings for {$monthName} {$yr}: full monthly calendar with Mass readings, responsorial psalm and Gospel for each day.";

        $yearHref = "/en/daily-mass-readings/year/{$yr}";
        $ptHref = "/liturgia-diaria/ano/{$yr}/".$this->pad2($mo);

        $totalDays = Carbon::create($yr, $mo, 1)->daysInMonth;

        $days = [];
        for ($d = 1; $d <= $totalDays; $d++) {
            $slug = sprintf('%s-%s-%d', $this->pad2($d), $this->pad2($mo), $yr);
            // mantendo a mesma rota de dia (se a sua diária EN for outra, ajuste aqui)
            $days[] = ['day' => $d, 'slug' => $slug, 'href' => "/en/daily-mass-readings/{$slug}"];
        }

        $firstDate = Carbon::create($yr, $mo, 1)->startOfDay();
        $startOffset = $this->weekdayIndexMondayFirst($firstDate);

        $cells = array_merge(array_fill(0, $startOffset, null), $days);
        $rem = count($cells) % 7;
        if ($rem !== 0) $cells = array_merge($cells, array_fill(0, 7 - $rem, null));

        $prevMonth = $this->getPrevMonth($yr, $mo);
        $nextMonth = $this->getNextMonth($yr, $mo);

        $baseDate = $isMonthOfToday ? $today->copy() : Carbon::create($yr, $mo, 1);
        $prevSlug = $this->slugFromDate($baseDate->copy()->subDay());
        $nextSlug = $this->slugFromDate($baseDate->copy()->addDay());

        $sundays = $this->getSundaysInMonth($yr, $mo, '/en/daily-mass-readings');

        $breadcrumb = $this->buildBreadcrumbSchema([
            'baseName' => 'Daily Mass Readings',
            'baseUrl' => $siteUrl.'/en/daily-mass-readings',
            'year' => $yr,
            'yearUrl' => $siteUrl.$yearHref,
            'monthName' => $monthName,
            'monthUrl' => $canonical,
        ]);

        $collection = $this->buildCollectionSchema([
            'lang' => 'en',
            'siteUrl' => $siteUrl,
            'canonicalUrl' => $canonical,
            'monthName' => $monthName,
            'year' => $yr,
            'month' => $mo,
            'days' => $days,
        ]);

        $faq = $this->buildFaqSchema([
            'lang' => 'en',
            'year' => $yr,
            'monthName' => $monthName,
            'todaySlug' => $todaySlug,
            'baseDayPath' => '/en/daily-mass-readings',
        ]);

        $meta = [
            'html_lang' => 'en',
            'title' => $title,
            'description' => $description,
            'canonical' => $canonical,
            'robots' => 'index,follow',
            'hreflangs' => [
                'pt-BR' => $siteUrl.$ptHref,
                'en' => $canonical,
                'x-default' => $siteUrl.$ptHref,
            ],
            'og_title' => $title,
            'og_description' => $description,
            'og_url' => $canonical,
            'og_image' => $siteUrl.'/og/liturgia.png',
            'jsonld_blocks' => [
                ['id' => 'jsonld-breadcrumb', 'json' => $breadcrumb],
                ['id' => 'jsonld-collection', 'json' => $collection],
                ['id' => 'jsonld-faq', 'json' => $faq],
            ],
        ];

        $currentYear = (int) now()->format('Y');

        return view('en.daily-mass-readings.month', [
            'meta' => $meta,

            'year' => $yr,
            'month' => $mo,
            'monthName' => $monthName,
            'monthNameYear' => $monthNameYear,

            'days' => $days,
            'cells' => $cells,
            'sundays' => $sundays,

            'todaySlug' => $todaySlug,
            'todayLabel' => $todayLabel,
            'isMonthOfToday' => $isMonthOfToday,

            'prevMonth' => $prevMonth,
            'nextMonth' => $nextMonth,

            'yearHref' => $yearHref,

            'yearMin' => $currentYear - 1,
            'yearMax' => $currentYear + 1,

            'ptHref' => $ptHref,
            'enHref' => $canonicalPath,

            'prevSlug' => $prevSlug,
            'nextSlug' => $nextSlug,

            'ads' => [
                'slot_aside_300x250' => '8534838745',
            ],
        ]);
    }
}
