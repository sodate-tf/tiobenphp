{{-- resources/views/liturgia/partials/aside.blade.php --}}
@php
  $lang = $lang ?? (request()->is('en') || request()->is('en/*') ? 'en' : 'pt');
  $variant = $variant ?? 'desktop';
  $isEn = $lang === 'en';

  $pad2 = function(int $n) { return str_pad((string)$n, 2, '0', STR_PAD_LEFT); };
  $humanSlug = function(?string $s) { return $s ? str_replace('-', '/', $s) : ''; };

  $litBase  = $isEn ? '/en/daily-mass-readings' : '/liturgia-diaria';
  $blogBase = $isEn ? '/en/blog' : '/blog';

  $year  = $year  ?? (int) now('America/Sao_Paulo')->format('Y');
  $month = $month ?? (int) now('America/Sao_Paulo')->format('m');

  $monthHref = $isEn
    ? "{$litBase}/year/{$year}/".$pad2((int)$month)
    : "{$litBase}/ano/{$year}/".$pad2((int)$month);

  $yearHref = $isEn
    ? "{$litBase}/year/{$year}"
    : "{$litBase}/ano/{$year}";

  $todaySlug = $todaySlug ?? now('America/Sao_Paulo')->format('d-m-Y');
  $pageSlug  = $pageSlug  ?? $todaySlug;

  $prevSlug = $prevSlug ?? null;
  $nextSlug = $nextSlug ?? null;

  $todayHref = "{$litBase}/{$todaySlug}";
  $prevHref  = $prevSlug ? "{$litBase}/{$prevSlug}" : null;
  $nextHref  = $nextSlug ? "{$litBase}/{$nextSlug}" : null;

  $isHistoricalContext = !empty($pageSlug) && $pageSlug !== $todaySlug;

  $t = $isEn ? [
    'quick' => 'Quick access',
    'explore' => 'Explore',
    'fromBlog' => 'From the blog',
    'today' => 'Today',
    'todayCurrent' => 'Today (current date)',
    'prevDay' => 'Previous day',
    'nextDay' => 'Next day',
    'monthCalendar' => 'Monthly calendar',
    'yearCalendar' => 'Year calendar',
    'youAreOn' => 'You are on',
    'viewAll' => 'View all articles',
    'blogHint' => 'Content about Liturgy and prayer.',
    'howToUse' => 'How to use the Liturgy daily',
    'howToUseDesc' => 'A simple way to pray and prepare for Mass.',
    'litYear' => 'Liturgical year',
    'litYearDesc' => 'Seasons, colors and calendar.',
    'readingsGuide' => 'Guide to Mass readings',
    'readingsGuideDesc' => 'Readings, psalm, gospel and how to follow.',
    'pray5' => 'Pray with Liturgy in 5 minutes',
    'pray5Desc' => 'A step-by-step to build consistency.',
    'dailyVsGospel' => 'Daily Liturgy vs Gospel of the day',
    'dailyVsGospelDesc' => 'Differences and when to use each.',
    'ad' => 'Advertisement',
    'seriesKicker' => 'Special series',
    'seriesTitle' => 'Catholic Christian & Finances',
    'seriesDesc' => 'A Catholic view on money, work, and detachment.',
    'seriesCta' => 'See the series',
    'seriesHint' => 'PT only for now.',
  ] : [
    'quick' => 'Acesso rápido',
    'explore' => 'Explorar',
    'fromBlog' => 'Do blog',
    'today' => 'Hoje',
    'todayCurrent' => 'Hoje (data atual)',
    'prevDay' => 'Dia anterior',
    'nextDay' => 'Próximo dia',
    'monthCalendar' => 'Calendário do mês',
    'yearCalendar' => 'Calendário do ano',
    'youAreOn' => 'Você está em',
    'viewAll' => 'Ver todos os artigos',
    'blogHint' => 'Conteúdos sobre Liturgia e oração.',
    'howToUse' => 'Como usar a Liturgia no dia a dia',
    'howToUseDesc' => 'Um jeito simples de rezar e se preparar para a Missa.',
    'litYear' => 'Ano litúrgico',
    'litYearDesc' => 'Tempos, cores e calendário.',
    'readingsGuide' => 'Guia das leituras da Missa',
    'readingsGuideDesc' => 'Leituras, salmo, evangelho e como acompanhar.',
    'pray5' => 'Rezar com a Liturgia em 5 minutos',
    'pray5Desc' => 'Passo a passo para criar constância.',
    'dailyVsGospel' => 'Liturgia diária x Evangelho do dia',
    'dailyVsGospelDesc' => 'Diferenças e quando usar cada um.',
    'ad' => 'Anúncio',
    'seriesKicker' => 'Série especial',
    'seriesTitle' => 'Cristão Católico e Finanças',
    'seriesDesc' => 'Visão católica sobre dinheiro, trabalho e desapego.',
    'seriesCta' => 'Ver a série',
    'seriesHint' => '',
  ];

  $defaultBlogLinks = $isEn ? [
    ['href'=> "{$blogBase}/how-to-use-the-liturgy", 'title'=> 'How to use the Liturgy daily', 'desc'=> 'A simple way to pray and prepare for Mass.'],
    ['href'=> "{$blogBase}/liturgical-year", 'title'=> 'Liturgical year', 'desc'=> 'Seasons, colors and calendar.'],
    ['href'=> "{$blogBase}/mass-readings-guide", 'title'=> 'Guide to Mass readings', 'desc'=> 'Readings, psalm, gospel and how to follow.'],
    ['href'=> "{$blogBase}/pray-with-liturgy-in-5-minutes", 'title'=> 'Pray with Liturgy in 5 minutes', 'desc'=> 'Step-by-step to build consistency.'],
    ['href'=> "{$blogBase}/daily-liturgy-vs-gospel-of-the-day", 'title'=> 'Daily Liturgy vs Gospel of the day', 'desc'=> 'Differences and when to use each.'],
  ] : [
    ['href'=> "{$blogBase}/como-usar-a-liturgia", 'title'=> 'Como usar a Liturgia no dia a dia', 'desc'=> 'Um jeito simples de rezar e se preparar para a Missa.'],
    ['href'=> "{$blogBase}/ano-liturgico", 'title'=> 'Ano litúrgico', 'desc'=> 'Tempos, cores e calendário.'],
    ['href'=> "{$blogBase}/leituras-da-missa", 'title'=> 'Guia das leituras da Missa', 'desc'=> 'Leituras, salmo, evangelho e como acompanhar.'],
    ['href'=> "{$blogBase}/como-rezar-com-a-liturgia-em-5-minutos", 'title'=> 'Rezar com a Liturgia em 5 minutos', 'desc'=> 'Passo a passo para criar constância.'],
    ['href'=> "{$blogBase}/liturgia-diaria-ou-evangelho-do-dia", 'title'=> 'Liturgia diária x Evangelho do dia', 'desc'=> 'Diferenças e quando usar cada um.'],
  ];

  $effectiveBlogLinks = (isset($blogLinks) && is_array($blogLinks) && count($blogLinks))
    ? array_slice($blogLinks, 0, 5)
    : array_slice($defaultBlogLinks, 0, 5);

  $panel = "rounded-3xl bg-white/75 shadow-sm backdrop-blur-sm p-4 sm:p-5";
  $kicker = "text-[11px] font-bold uppercase tracking-wide text-amber-700";
  $subtle = "text-sm text-slate-600";
  $btn = "group flex items-start gap-3 rounded-2xl bg-white/70 px-4 py-3 hover:bg-white transition";
  $dot = "mt-1.5 h-2 w-2 rounded-full bg-amber-500/80";
  $title = "text-[15px] font-extrabold text-slate-900 leading-snug";

  $financeSeriesUrl = '/cristao-catolico-e-financas';
  $showFinanceSeries = !$isEn;

  $adClient = 'ca-pub-8819996017476509';

  $slotTop300x250 = $adsSlotDesktop300x250
    ?? ($ads['slot_sidebar_300x250'] ?? '8534838745');

  $slotBottom300x600 = $adsSlotDesktopSticky
    ?? ($ads['slot_sidebar_300x600'] ?? '9515073457');
@endphp

@once
  @push('head')
    @if(app()->environment('production'))
      <script async
        src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ $adClient }}"
        crossorigin="anonymous"></script>
    @endif
  @endpush
@endonce

<aside class="min-w-0 {{ $className ?? '' }}">
  <div class="sticky top-6 space-y-7">

    {{-- ANÚNCIO REAL - TOPO DO ASIDE --}}
    <section class="{{ $panel }}">
      <p class="{{ $kicker }}">{{ $t['ad'] }}</p>

      <div class="mt-3 overflow-hidden rounded-2xl bg-white/70 p-2 ring-1 ring-black/5" style="min-height:250px;">
        <ins class="adsbygoogle"
             style="display:block; min-width:300px; min-height:250px;"
             data-ad-client="{{ $adClient }}"
             data-ad-slot="{{ $slotTop300x250 }}"
             data-ad-format="rectangle"
             data-full-width-responsive="false"></ins>
      </div>
    </section>

    @push('scripts')
      <script>
        try {
          (window.adsbygoogle = window.adsbygoogle || []).push({});
        } catch (e) {}
      </script>
    @endpush

    {{-- ACESSO RÁPIDO --}}
    <section class="{{ $panel }}">
      <p class="{{ $kicker }}">{{ $t['quick'] }}</p>

      <div class="mt-4 space-y-2">
        <a href="{{ $todayHref }}" class="{{ $btn }}">
          <span class="{{ $dot }}"></span>
          <div class="min-w-0">
            <p class="{{ $title }}">{{ $t['today'] }}</p>
            <p class="mt-1 {{ $subtle }}">{{ $t['todayCurrent'] }} — {{ $humanSlug($todaySlug) }}</p>
          </div>
        </a>

        @if($prevHref)
          <a href="{{ $prevHref }}" class="{{ $btn }}">
            <span class="{{ $dot }}"></span>
            <div class="min-w-0">
              <p class="{{ $title }}">{{ $t['prevDay'] }}</p>
              <p class="mt-1 {{ $subtle }}">{{ $humanSlug($prevSlug) }}</p>
            </div>
          </a>
        @endif

        @if($nextHref)
          <a href="{{ $nextHref }}" class="{{ $btn }}">
            <span class="{{ $dot }}"></span>
            <div class="min-w-0">
              <p class="{{ $title }}">{{ $t['nextDay'] }}</p>
              <p class="mt-1 {{ $subtle }}">{{ $humanSlug($nextSlug) }}</p>
            </div>
          </a>
        @endif

        <a href="{{ $monthHref }}" class="{{ $btn }}">
          <span class="{{ $dot }}"></span>
          <div class="min-w-0">
            <p class="{{ $title }}">{{ $t['monthCalendar'] }}</p>
            <p class="mt-1 {{ $subtle }}">{{ $pad2((int)$month) }}/{{ (int)$year }}</p>
          </div>
        </a>

        <a href="{{ $yearHref }}" class="{{ $btn }}">
          <span class="{{ $dot }}"></span>
          <div class="min-w-0">
            <p class="{{ $title }}">{{ $t['yearCalendar'] }}</p>
            <p class="mt-1 {{ $subtle }}">{{ (int)$year }}</p>
          </div>
        </a>

        <div class="mt-4 rounded-2xl bg-white/60 px-4 py-3 ring-1 ring-black/5">
          <p class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $t['youAreOn'] }}</p>
          <p class="mt-1 text-sm font-extrabold text-slate-900">
            {{ $humanSlug($pageSlug) }}

            @if($isHistoricalContext)
              <span class="ml-2 inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-[11px] font-bold text-amber-800">
                arquivo
              </span>
            @endif
          </p>
        </div>
      </div>
    </section>

    {{-- SÉRIE ESPECIAL --}}
    @if($showFinanceSeries)
      <section class="{{ $panel }}">
        <p class="{{ $kicker }}">{{ $t['seriesKicker'] }}</p>

        <div class="mt-4 rounded-2xl bg-gradient-to-br from-amber-50 via-white to-indigo-50 p-4 ring-1 ring-black/5">
          <div class="flex items-start gap-3">
            <div class="mt-0.5 flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-900 text-white shadow-sm">
              <span class="text-lg font-black">₿</span>
            </div>

            <div class="min-w-0">
              <p class="text-base font-extrabold text-slate-900 leading-tight">{{ $t['seriesTitle'] }}</p>
              <p class="mt-1 text-sm text-slate-600">{{ $t['seriesDesc'] }}</p>

              <div class="mt-4">
                <a href="{{ $financeSeriesUrl }}"
                   class="group inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-slate-900 px-4 py-3 text-sm font-extrabold text-white shadow-sm hover:opacity-95">
                  <span>{{ $t['seriesCta'] }}</span>
                  <span class="inline-flex h-7 w-7 items-center justify-center rounded-xl bg-white/10 group-hover:bg-white/15">→</span>
                </a>

                @if(!empty($t['seriesHint']))
                  <p class="mt-2 text-center text-xs text-slate-600">{{ $t['seriesHint'] }}</p>
                @endif
              </div>
            </div>
          </div>
        </div>

        <div class="h-2 w-full bg-gradient-to-r from-amber-300 via-slate-200 to-indigo-300"></div>
      </section>
    @endif

    {{-- EXPLORAR --}}
    <section class="{{ $panel }}">
      <p class="{{ $kicker }}">{{ $t['explore'] }}</p>

      <div class="mt-4 space-y-2">
        <a href="{{ $isEn ? "{$blogBase}/how-to-use-the-liturgy" : "{$blogBase}/como-usar-a-liturgia" }}" class="{{ $btn }}">
          <span class="{{ $dot }}"></span>
          <div class="min-w-0">
            <p class="{{ $title }}">{{ $t['howToUse'] }}</p>
            <p class="mt-1 {{ $subtle }}">{{ $t['howToUseDesc'] }}</p>
          </div>
        </a>

        <a href="{{ $isEn ? "{$blogBase}/liturgical-year" : "{$blogBase}/ano-liturgico" }}" class="{{ $btn }}">
          <span class="{{ $dot }}"></span>
          <div class="min-w-0">
            <p class="{{ $title }}">{{ $t['litYear'] }}</p>
            <p class="mt-1 {{ $subtle }}">{{ $t['litYearDesc'] }}</p>
          </div>
        </a>

        <a href="{{ $isEn ? "{$blogBase}/mass-readings-guide" : "{$blogBase}/leituras-da-missa" }}" class="{{ $btn }}">
          <span class="{{ $dot }}"></span>
          <div class="min-w-0">
            <p class="{{ $title }}">{{ $t['readingsGuide'] }}</p>
            <p class="mt-1 {{ $subtle }}">{{ $t['readingsGuideDesc'] }}</p>
          </div>
        </a>

        <a href="{{ $isEn ? "{$blogBase}/pray-with-liturgy-in-5-minutes" : "{$blogBase}/como-rezar-com-a-liturgia-em-5-minutos" }}" class="{{ $btn }}">
          <span class="{{ $dot }}"></span>
          <div class="min-w-0">
            <p class="{{ $title }}">{{ $t['pray5'] }}</p>
            <p class="mt-1 {{ $subtle }}">{{ $t['pray5Desc'] }}</p>
          </div>
        </a>

        <a href="{{ $isEn ? "{$blogBase}/daily-liturgy-vs-gospel-of-the-day" : "{$blogBase}/liturgia-diaria-ou-evangelho-do-dia" }}" class="{{ $btn }}">
          <span class="{{ $dot }}"></span>
          <div class="min-w-0">
            <p class="{{ $title }}">{{ $t['dailyVsGospel'] }}</p>
            <p class="mt-1 {{ $subtle }}">{{ $t['dailyVsGospelDesc'] }}</p>
          </div>
        </a>
      </div>
    </section>

    {{-- DO BLOG --}}
    <section class="{{ $panel }}">
      <p class="{{ $kicker }}">{{ $t['fromBlog'] }}</p>
      <p class="mt-2 {{ $subtle }}">{{ $t['blogHint'] }}</p>

      <div class="mt-4 space-y-2">
        @foreach($effectiveBlogLinks as $it)
          <a href="{{ $it['href'] }}" class="{{ $btn }}">
            <span class="{{ $dot }}"></span>
            <div class="min-w-0">
              <p class="{{ $title }}">{{ $it['title'] }}</p>
              <p class="mt-1 {{ $subtle }}">{{ $it['desc'] }}</p>
            </div>
          </a>
        @endforeach
      </div>

      <div class="mt-4">
        <a href="{{ $blogBase }}"
           class="inline-flex w-full items-center justify-center rounded-2xl bg-slate-900 px-4 py-3 text-sm font-extrabold text-white shadow-sm hover:opacity-95">
          {{ $t['viewAll'] }}
        </a>
      </div>
    </section>

    {{-- ANÚNCIO REAL - FINAL DO ASIDE --}}
    <section class="{{ $panel }}">
      <p class="{{ $kicker }}">{{ $t['ad'] }}</p>

      <div class="mt-3 overflow-hidden rounded-2xl bg-white/70 p-2 ring-1 ring-black/5" style="min-height:600px;">
        <ins class="adsbygoogle"
             style="display:block; min-width:300px; min-height:600px;"
             data-ad-client="{{ $adClient }}"
             data-ad-slot="{{ $slotBottom300x600 }}"
             data-ad-format="auto"
             data-full-width-responsive="false"></ins>
      </div>
    </section>

    @push('scripts')
      <script>
        try {
          (window.adsbygoogle = window.adsbygoogle || []).push({});
        } catch (e) {}
      </script>
    @endpush

  </div>
</aside>