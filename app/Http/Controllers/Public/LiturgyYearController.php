<?php
// app/Http/Controllers/Public/LiturgyYearController.php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LiturgyYearController extends Controller
{
    private function parseYear(?string $raw): ?int
    {
        if ($raw === null) return null;
        if (!ctype_digit($raw)) return null;

        $year = (int) $raw;
        if ($year < 1900 || $year > 2100) return null;

        return $year;
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

    private function monthLabelPt(int $year, int $month): string
    {
        return Carbon::create($year, $month, 1)->locale('pt_BR')->translatedFormat('F \d\e Y');
    }

    private function monthLabelEn(int $year, int $month): string
    {
        return Carbon::create($year, $month, 1)->locale('en')->translatedFormat('F Y');
    }

    /**
     * 1º Domingo do Advento: domingo entre 27/11 e 03/12
     */
    private function firstSundayOfAdvent(int $year): Carbon
    {
        $d = Carbon::create($year, 11, 27)->startOfDay();

        $dow = $d->dayOfWeek; // 0=Sunday .. 6=Saturday
        $add = ($dow === Carbon::SUNDAY) ? 0 : (7 - $dow);

        return $d->copy()->addDays($add);
    }

    /**
     * Retorna informações do ano litúrgico para uma data:
     * - identifica o Advento que iniciou o ano litúrgico vigente
     * - mapeia o ciclo A/B/C pelo ano de início (Advento)
     *
     * Mapeamento:
     * Advento 2022 -> Ano A
     * Advento 2023 -> Ano B
     * Advento 2024 -> Ano C
     * Advento 2025 -> Ano A  (logo, em Fev/2026 ainda é Ano A)
     */
    private function liturgicalInfoForDate(Carbon $date): array
    {
        $date = $date->copy()->startOfDay();

        $adventThisYear = $this->firstSundayOfAdvent((int)$date->year);

        // Se ainda não chegou no Advento do ano civil atual,
        // o ano litúrgico vigente começou no Advento do ano anterior.
        $start = $date->lt($adventThisYear)
            ? $this->firstSundayOfAdvent((int)$date->year - 1)
            : $adventThisYear;

        $startYear = (int)$start->year;

        // 2022->A, 2023->B, 2024->C, 2025->A...
        $idx = $startYear % 3; // 0,1,2
        $letter = match ($idx) {
            0 => 'A',
            1 => 'B',
            2 => 'C',
        };

        // Ano em que o ano litúrgico termina (para exibição/SEO, se precisar)
        $endYear = $startYear + 1;

        return [
            'letter' => $letter,
            'start_year' => $startYear,
            'end_year' => $endYear,

            'advent_start_iso' => $start->toDateString(),
            'advent_start_human_pt' => $start->copy()->locale('pt_BR')->translatedFormat('d \\d\\e F \\d\\e Y'),
            'advent_start_human_en' => $start->copy()->locale('en')->translatedFormat('F j, Y'),
        ];
    }

    private function liturgicalCycleTextPt(string $letter): string
    {
        return match ($letter) {
            'A' => 'Ano A: foco no Evangelho de Mateus (complementado por João em tempos fortes). Destaca o discipulado, o Reino e a vivência concreta da fé.',
            'B' => 'Ano B: foco no Evangelho de Marcos (com João em momentos específicos). Linguagem direta e dinâmica: Jesus em ação, chamando à conversão e à confiança.',
            'C' => 'Ano C: foco no Evangelho de Lucas (complementado por João). Realça misericórdia, oração, Espírito Santo e o cuidado de Deus pelos pobres e marginalizados.',
            default => '',
        };
    }

    private function liturgicalCycleTextEn(string $letter): string
    {
        return match ($letter) {
            'A' => 'Year A: mainly the Gospel of Matthew (with John in key seasons). Emphasizes discipleship, the Kingdom, and living the faith.',
            'B' => 'Year B: mainly the Gospel of Mark (with John at specific times). Direct and dynamic: Jesus in action, calling to conversion and trust.',
            'C' => 'Year C: mainly the Gospel of Luke (with John). Highlights mercy, prayer, the Holy Spirit, and God’s care for the poor and the outcast.',
            default => '',
        };
    }

    public function ptYear(Request $request, string $ano)
    {
        $year = $this->parseYear($ano);
        abort_if(!$year, 404);
        $this->assertYearWithinWindow($year);

        $siteUrl = rtrim(config('app.url') ?: url('/'), '/');

        $canonicalPath = "/liturgia-diaria/ano/{$year}";
        $canonical = $siteUrl.$canonicalPath;

        $enPath = "/en/daily-mass-readings/year/{$year}";
        $ptPath = $canonicalPath;

        // Ano litúrgico relativo a HOJE (não ao parâmetro)
        $today = now();
        $litInfo = $this->liturgicalInfoForDate(Carbon::parse($today->toDateString()));

        $litLetter = $litInfo['letter'];                 // <-- agora fica correto (2026 => A)
        $litEnd    = $litInfo['end_year'];               // ano civil em que termina
        $adventStartHuman = $litInfo['advent_start_human_pt'];

        $months = collect(range(1, 12))->map(function ($m) use ($year) {
            return [
                'm' => $m,
                'href' => "/liturgia-diaria/ano/{$year}/".$this->pad2($m),
                'label' => $this->monthLabelPt($year, $m),
            ];
        })->all();

        // JSON-LD extra (ItemList) sem duplicar HTML
        $jsonLdMonths = [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => "Meses da Liturgia Diária em {$year}",
            'itemListElement' => array_map(function ($m, $idx) use ($siteUrl) {
                return [
                    '@type' => 'ListItem',
                    'position' => $idx + 1,
                    'name' => $m['label'],
                    'url' => $siteUrl.$m['href'],
                ];
            }, $months, array_keys($months)),
        ];

        $title = "Liturgia Diária {$year}: Calendário Anual, Leituras e Evangelho";
        $description = "Veja o calendário anual da Liturgia Diária {$year}: escolha o mês e acesse leituras da Missa, salmo responsorial e evangelho de cada dia.";

        $meta = [
            'html_lang' => 'pt-BR',
            'title' => $title,
            'description' => $description,
            'canonical' => $canonical,
            'robots' => 'index,follow',
            'hreflangs' => [
                'pt-BR' => $siteUrl.$ptPath,
                'en' => $siteUrl.$enPath,
                'x-default' => $siteUrl.$ptPath,
            ],
            'og_title' => $title,
            'og_description' => $description,
            'og_url' => $canonical,
            'og_image' => $siteUrl.'/og/liturgia.png',
            'jsonld_blocks' => [
                [
                    'id' => 'jsonld-breadcrumb',
                    'json' => [
                        '@context' => 'https://schema.org',
                        '@type' => 'BreadcrumbList',
                        'itemListElement' => [
                            ['@type'=>'ListItem','position'=>1,'name'=>'Liturgia Diária','item'=>$siteUrl.'/liturgia-diaria'],
                            ['@type'=>'ListItem','position'=>2,'name'=>(string)$year,'item'=>$canonical],
                        ],
                    ],
                ],
                [
                    'id' => 'jsonld-months',
                    'json' => $jsonLdMonths,
                ],
            ],
        ];

        $currentYear = (int) now()->format('Y');

        return view('liturgia.year', [
            'meta' => $meta,
            'year' => $year,
            'months' => $months,

            // janela permitida para navegar/pesquisar
            'yearMin' => $currentYear - 1,
            'yearMax' => $currentYear + 1,
            'yearPrev' => $year - 1,
            'yearNext' => $year + 1,

            // info do ano litúrgico (sempre relativo a hoje)
            'lit' => [
                'letter' => $litLetter,
                'end_year' => $litEnd,
                'advent_start_human' => $adventStartHuman,
                'textA' => $this->liturgicalCycleTextPt('A'),
                'textB' => $this->liturgicalCycleTextPt('B'),
                'textC' => $this->liturgicalCycleTextPt('C'),
            ],
        ]);
    }

    public function enYear(Request $request, string $year)
    {
        $yr = $this->parseYear($year);
        abort_if(!$yr, 404);
        $this->assertYearWithinWindow($yr);

        $siteUrl = rtrim(config('app.url') ?: url('/'), '/');

        $canonicalPath = "/en/daily-mass-readings/year/{$yr}";
        $canonical = $siteUrl.$canonicalPath;

        $ptPath = "/liturgia-diaria/ano/{$yr}";
        $enPath = $canonicalPath;

        $today = now();
        $litInfo = $this->liturgicalInfoForDate(Carbon::parse($today->toDateString()));

        $litLetter = $litInfo['letter'];               // <-- correto (2026 => A)
        $litEnd    = $litInfo['end_year'];
        $adventStartHuman = $litInfo['advent_start_human_en'];

        $months = collect(range(1, 12))->map(function ($m) use ($yr) {
            return [
                'm' => $m,
                'href' => "/en/daily-mass-readings/year/{$yr}/".$this->pad2($m),
                'label' => $this->monthLabelEn($yr, $m),
            ];
        })->all();

        $jsonLdMonths = [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => "Daily Mass Readings months in {$yr}",
            'itemListElement' => array_map(function ($m, $idx) use ($siteUrl) {
                return [
                    '@type' => 'ListItem',
                    'position' => $idx + 1,
                    'name' => $m['label'],
                    'url' => $siteUrl.$m['href'],
                ];
            }, $months, array_keys($months)),
        ];

        $title = "Daily Mass Readings {$yr}: Year Calendar, Readings and Gospel";
        $description = "See the yearly Daily Mass Readings calendar for {$yr}: choose a month and access Mass readings, responsorial psalm and Gospel for each day.";

        $meta = [
            'html_lang' => 'en',
            'title' => $title,
            'description' => $description,
            'canonical' => $canonical,
            'robots' => 'index,follow',
            'hreflangs' => [
                'pt-BR' => $siteUrl.$ptPath,
                'en' => $siteUrl.$enPath,
                'x-default' => $siteUrl.$ptPath,
            ],
            'og_title' => $title,
            'og_description' => $description,
            'og_url' => $canonical,
            'og_image' => $siteUrl.'/og/liturgia.png',
            'jsonld_blocks' => [
                [
                    'id' => 'jsonld-breadcrumb',
                    'json' => [
                        '@context' => 'https://schema.org',
                        '@type' => 'BreadcrumbList',
                        'itemListElement' => [
                            ['@type'=>'ListItem','position'=>1,'name'=>'Daily Mass Readings','item'=>$siteUrl.'/en/daily-mass-readings'],
                            ['@type'=>'ListItem','position'=>2,'name'=>(string)$yr,'item'=>$canonical],
                        ],
                    ],
                ],
                [
                    'id' => 'jsonld-months',
                    'json' => $jsonLdMonths,
                ],
            ],
        ];

        $currentYear = (int) now()->format('Y');

        return view('en.daily-mass-readings.year', [
            'meta' => $meta,
            'year' => $yr,
            'months' => $months,

            'yearMin' => $currentYear - 1,
            'yearMax' => $currentYear + 1,
            'yearPrev' => $yr - 1,
            'yearNext' => $yr + 1,

            'lit' => [
                'letter' => $litLetter,
                'end_year' => $litEnd,
                'advent_start_human' => $adventStartHuman,
                'textA' => $this->liturgicalCycleTextEn('A'),
                'textB' => $this->liturgicalCycleTextEn('B'),
                'textC' => $this->liturgicalCycleTextEn('C'),
            ],
        ]);
    }
}
