{{-- resources/views/liturgia/en/day.blade.php --}}

@extends('layouts.site')

@section('html_lang', $meta['html_lang'] ?? 'en')
@section('title', $meta['title'] ?? 'Daily Mass Readings — IA Tio Ben')
@section('meta_description', $meta['description'] ?? '')
@section('canonical', $meta['canonical'] ?? '')
@section('robots', $meta['robots'] ?? 'index,follow')

@section('hreflang')
  @if(!empty($meta['hreflangs']) && is_array($meta['hreflangs']))
    @foreach($meta['hreflangs'] as $langCode => $url)
      @if(!empty($langCode) && !empty($url))
        <link rel="alternate" hreflang="{{ $langCode }}" href="{{ $url }}"/>
      @endif
    @endforeach
  @endif
@endsection

@section('og_title', $meta['og_title'] ?? ($meta['title'] ?? ''))
@section('og_description', $meta['og_description'] ?? ($meta['description'] ?? ''))
@section('og_url', $meta['og_url'] ?? ($meta['canonical'] ?? ''))
@section('og_image', $meta['og_image'] ?? '')

@push('head')
  @if(!empty($meta['jsonld_blocks']) && is_array($meta['jsonld_blocks']))
    @foreach($meta['jsonld_blocks'] as $b)
      @if(!empty($b['id']) && !empty($b['json']))
        <script id="{{ $b['id'] }}" type="application/ld+json">{!! json_encode($b['json'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
      @endif
    @endforeach
  @endif

  <link rel="preconnect" href="https://pagead2.googlesyndication.com" crossorigin>
  <link rel="preconnect" href="https://googleads.g.doubleclick.net" crossorigin>
  <link rel="preconnect" href="https://tpc.googlesyndication.com" crossorigin>

  {{-- AdSense sem atraso --}}
  <script async
    src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-8819996017476509"
    crossorigin="anonymous"></script>

  <style>
    .lit-day-grid { display: block; }
    .lit-aside-desktop { display: none; }
    .lit-aside-mobile { display: block; }

    @media (min-width: 1024px) {
      .lit-day-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 340px;
        gap: 24px;
        align-items: start;
      }
      .lit-aside-desktop { display: block; }
      .lit-aside-mobile { display: none; }
    }

    .lit-surface {
      background: linear-gradient(180deg, rgba(255,251,235,0.70) 0%, rgba(255,255,255,1) 55%);
      border-radius: 24px;
    }

    .lit-bible{
      font-family: var(--font-sans, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, Helvetica, Arial, "Apple Color Emoji","Segoe UI Emoji");
      font-size: 18px;
      line-height: 1.9;
      color: #0f172a;
      text-rendering: optimizeLegibility;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }

    .lit-bible p { margin: 0.9em 0; }

    .lit-tab-btn { transition: background-color .15s ease, color .15s ease, box-shadow .15s ease; }

    .ads-slot {
      width: 100%;
      min-height: 280px;
    }

    .ads-slot-sidebar-top {
      min-height: 250px;
    }

    .ads-slot-sidebar-bottom {
      min-height: 600px;
    }
  </style>
@endpush

@section('content')
@php
  use App\Support\LiturgiaEnGlossary;

  $siteUrl = config('app.url') ?: url('/');
  $dateSlug = $page['dateSlug'] ?? '';
  $dateISO = $page['dateISO'] ?? '';
  $celebration = $page['celebration'] ?? '';
  $color = $page['color'] ?? '';
  $pageUrl = rtrim($siteUrl,'/').'/en/daily-mass-readings/'.$dateSlug;

  $lang = 'en';

  $celebrationEn = $celebration ? LiturgiaEnGlossary::translateCelebration($celebration) : '';
  $colorEn = $color ? LiturgiaEnGlossary::translateColor($color) : '';

  $slugFromIso = function(string $iso) {
    $c = \Carbon\Carbon::parse($iso, 'America/Sao_Paulo');
    return $c->format('d-m-Y');
  };

  $todayISO = \Carbon\Carbon::today('America/Sao_Paulo')->toDateString();
  $todaySlug = $todaySlug ?? $slugFromIso($todayISO);
  $todayLabel = $todayLabel ?? \Carbon\Carbon::parse($todayISO, 'America/Sao_Paulo')->format('d/m/Y');

  $baseISO = $dateISO ?: $todayISO;
  $prevSlug = $prevSlug ?? $slugFromIso(\Carbon\Carbon::parse($baseISO, 'America/Sao_Paulo')->subDay()->toDateString());
  $nextSlug = $nextSlug ?? $slugFromIso(\Carbon\Carbon::parse($baseISO, 'America/Sao_Paulo')->addDay()->toDateString());

  $carbon = $dateISO ? \Carbon\Carbon::parse($dateISO, 'America/Sao_Paulo') : \Carbon\Carbon::today('America/Sao_Paulo');
  $year = (int)$carbon->format('Y');
  $month = (int)$carbon->format('m');

  $tabs = [];

  $leituras = $page['leiturasFull'] ?? [];
  $oracoes  = $page['oracoesFull'] ?? [];
  $antif    = $page['antifonasFull'] ?? [];

  $primeiras = is_array($leituras['primeiraLeitura'] ?? null) ? $leituras['primeiraLeitura'] : [];
  $segundas  = is_array($leituras['segundaLeitura'] ?? null) ? $leituras['segundaLeitura'] : [];
  $salmos    = is_array($leituras['salmo'] ?? null) ? $leituras['salmo'] : [];
  $evangs    = is_array($leituras['evangelho'] ?? null) ? $leituras['evangelho'] : [];
  $extras    = is_array($leituras['extras'] ?? null) ? $leituras['extras'] : [];

  $hasText = function($x) {
    if (is_string($x)) return trim($x) !== '';
    if (!is_array($x)) return false;
    $t = trim((string)($x['texto'] ?? $x['text'] ?? ''));
    $h = trim((string)($x['textoHtml'] ?? $x['html'] ?? ''));
    return ($h !== '') || ($t !== '');
  };

  $readingGroups = [];
  if (count($primeiras)) $readingGroups[] = ['label'=>'First Reading','items'=>$primeiras];
  if (count($segundas))  $readingGroups[] = ['label'=>'Second Reading','items'=>$segundas];

  $leituraExtras = array_values(array_filter($extras, function($x){
    $tipo = mb_strtolower((string)($x['tipo'] ?? ''));
    return str_contains($tipo, 'leitura') || str_contains($tipo, 'epístola') || str_contains($tipo, 'epistola')
      || str_contains($tipo, 'reading') || str_contains($tipo, 'epistle');
  }));

  foreach ($leituraExtras as $idx => $x) {
    $label = (string)($x['tipo'] ?? $x['titulo'] ?? ('Reading '.($idx+3)));
    $readingGroups[] = ['label'=>$label,'items'=>[$x]];
  }

  $max = max(count($readingGroups), count($salmos));
  for ($i=0; $i<$max; $i++) {
    if (isset($readingGroups[$i])) {
      $tabs[] = ['id'=>'reading-'.($i+1), 'label'=>$readingGroups[$i]['label'], 'kind'=>'reading', 'payload'=>$readingGroups[$i]];
    }

    if (isset($salmos[$i])) {
      $tabs[] = [
        'id'=>'psalm-'.($i+1),
        'label'=>(count($salmos)>1 ? 'Psalm '.($i+1) : 'Psalm'),
        'kind'=>'psalm',
        'payload'=>$salmos[$i]
      ];
    }
  }

  if (count($evangs)) {
    $tabs[] = ['id'=>'gospel','label'=>'Gospel','kind'=>'gospel','payload'=>$evangs];
  }

  $outrosExtras = array_values(array_filter($extras, function($x){
    $tipo = mb_strtolower((string)($x['tipo'] ?? ''));
    return !(
      str_contains($tipo, 'leitura') || str_contains($tipo, 'epístola') || str_contains($tipo, 'epistola') ||
      str_contains($tipo, 'reading') || str_contains($tipo, 'epistle')
    );
  }));

  if (count($outrosExtras)) {
    $tabs[] = ['id'=>'extras','label'=>'Extras','kind'=>'extras','payload'=>$outrosExtras];
  }

  $hasAntEntrada = $hasText($antif['entrada'] ?? '');
  $hasAntCom = $hasText($antif['comunhao'] ?? '');

  if ($hasAntEntrada || $hasAntCom) {
    $tabs[] = ['id'=>'antiphons','label'=>'Antiphons','kind'=>'antiphons','payload'=>$antif];
  }

  $hasPrayers = false;

  if (!empty($oracoes['coleta']) || !empty($oracoes['oferendas']) || !empty($oracoes['comunhao'])) {
    $hasPrayers = true;
  }

  if (is_array($oracoes['extras'] ?? null) && count($oracoes['extras'])) {
    $hasPrayers = true;
  }

  if ($hasPrayers) {
    $tabs[] = ['id'=>'prayers','label'=>'Prayers','kind'=>'prayers','payload'=>$oracoes];
  }

  $defaultTab = $tabs[0]['id'] ?? '';

  $dateHumanEn = $dateISO
    ? \Carbon\Carbon::parse($dateISO, 'America/Sao_Paulo')->locale('en')->translatedFormat('F j, Y')
    : \Carbon\Carbon::parse($todayISO, 'America/Sao_Paulo')->locale('en')->translatedFormat('F j, Y');

  $defaultBlogLinks = [
    ['href' => url('/en/blog/how-to-use-the-liturgy'), 'title' => 'How to use the Liturgy daily', 'desc' => 'A simple way to pray and prepare for Mass.'],
    ['href' => url('/en/blog/liturgical-year'), 'title' => 'Liturgical year: seasons, colors and calendar', 'desc' => 'Understand what changes through the year and how to follow.'],
    ['href' => url('/en/blog/mass-readings-guide'), 'title' => 'Mass readings guide', 'desc' => 'First reading, psalm, Gospel and how to follow.'],
    ['href' => url('/en/blog/pray-with-the-liturgy-in-5-minutes'), 'title' => 'Pray with the Liturgy in 5 minutes', 'desc' => 'A practical step-by-step to build consistency.'],
    ['href' => url('/en/blog/daily-liturgy-vs-gospel-of-the-day'), 'title' => 'Daily Liturgy vs Gospel of the Day', 'desc' => 'What each includes and when to use.'],
  ];

  $effectiveBlogLinks = (!empty($blogLinks) && is_array($blogLinks)) ? $blogLinks : $defaultBlogLinks;
  $effectiveBlogLinks = array_slice($effectiveBlogLinks, 0, 5);

  $pageSlug = $dateSlug ?: null;

  $adClient = 'ca-pub-8819996017476509';

  $slotTop = $ads['slot_lit_en_day_top_responsive']
    ?? ($ads['slot_lit_day_top_responsive'] ?? ($ads['slot_content_responsive'] ?? '8534838745'));

  $slotMiddle = $ads['slot_lit_en_day_middle_responsive']
    ?? ($ads['slot_lit_day_middle_responsive'] ?? ($ads['slot_in_article'] ?? '5469336488'));

  $slotBottom = $ads['slot_lit_en_day_bottom_responsive']
    ?? ($ads['slot_lit_day_bottom_responsive'] ?? ($ads['slot_blog_bottom_banner'] ?? '6552840528'));

  $slotAsideTop = $ads['slot_lit_en_day_sidebar_300x250']
    ?? ($ads['slot_lit_day_sidebar_300x250'] ?? ($ads['slot_sidebar_300x250'] ?? ($ads['slot_desktop'] ?? '8534838745')));

  $slotAsideBottom = $ads['slot_lit_en_day_sidebar_300x600']
    ?? ($ads['slot_lit_day_sidebar_300x600'] ?? ($ads['slot_sidebar_300x600'] ?? '9515073457'));
@endphp

<div class="pb-24 md:pb-0">
  <article class="lit-surface mx-auto w-full max-w-7xl px-3 sm:px-4 lg:px-6 py-4 sm:py-6 text-slate-900 leading-relaxed">

    <div class="lit-day-grid gap-5 items-start">

      {{-- MAIN --}}
      <section class="min-w-0">
        <div class="relative mb-6 sm:mb-8 mt-2">
          <p class="text-xs font-semibold tracking-wide text-amber-700 uppercase">
            IA Tio Ben • Daily Mass Readings
          </p>

          <h1 class="mt-2 text-2xl sm:text-4xl font-extrabold tracking-tight">
            Daily Mass Readings for {{ $dateHumanEn }}: Gospel & Readings
          </h1>

          <div class="mt-2 flex flex-wrap items-center gap-2 text-sm text-slate-700">
            @if(!empty($celebrationEn))
              <span class="inline-flex items-center rounded-full bg-white/70 px-3 py-1 font-semibold ring-1 ring-slate-200/60">
                {{ $celebrationEn }}
              </span>
            @endif

            @if(!empty($colorEn))
              <span class="inline-flex items-center rounded-full bg-amber-100/70 px-3 py-1 font-semibold text-amber-900 ring-1 ring-amber-200/70">
                Color: {{ $colorEn }}
              </span>
            @endif
          </div>

          @if(!empty($dailyParagraph))
            <p class="mt-3 text-[15px] leading-7 text-slate-700">
              {{ $dailyParagraph }}
            </p>
          @endif

          <div class="mt-4 grid grid-cols-3 gap-2 sm:flex sm:flex-wrap sm:gap-2">
            <a href="{{ url('/en/daily-mass-readings/'.$prevSlug) }}"
               class="inline-flex justify-center rounded-xl bg-white/80 px-3 py-2 text-sm font-semibold text-slate-800 ring-1 ring-slate-200/60 hover:bg-white">
              Yesterday
            </a>

            <a href="{{ url('/en/daily-mass-readings/'.$todaySlug) }}"
               class="inline-flex justify-center rounded-xl bg-amber-600 text-white px-3 py-2 text-sm font-semibold hover:bg-amber-700 shadow-sm">
              Today
            </a>

            <a href="{{ url('/en/daily-mass-readings/'.$nextSlug) }}"
               class="inline-flex justify-center rounded-xl bg-white/80 px-3 py-2 text-sm font-semibold text-slate-800 ring-1 ring-slate-200/60 hover:bg-white">
              Tomorrow
            </a>
          </div>

          {{-- REAL ADSENSE - TOP --}}
          <section class="mt-5">
            <div class="ads-slot rounded-2xl border border-slate-200 bg-white/90 p-4 shadow-sm">
              <p class="text-xs font-semibold tracking-wide text-amber-700 uppercase">Ad</p>

              <div class="mt-3">
                <ins class="adsbygoogle"
                     style="display:block; width:100%; min-height:280px;"
                     data-ad-client="{{ $adClient }}"
                     data-ad-slot="{{ $slotTop }}"
                     data-ad-format="auto"
                     data-full-width-responsive="true"></ins>
              </div>

              <script>
                try {
                  (window.adsbygoogle = window.adsbygoogle || []).push({});
                } catch (e) {}
              </script>
            </div>
          </section>

          @if(!empty($blogComplement))
            <section class="mt-5 rounded-2xl bg-amber-50/70 p-4 sm:p-5 ring-1 ring-amber-200/60">
              <p class="text-[11px] font-semibold text-amber-700 uppercase tracking-wide">
                Reflection from IA Tio Ben Blog
              </p>

              <h2 class="mt-1 text-base sm:text-lg font-extrabold text-slate-900">
                {{ $blogComplement['title'] ?? '' }}
              </h2>

              <p class="mt-2 text-sm sm:text-[15px] leading-7 text-slate-700">
                {{ $blogComplement['paragraph'] ?? '' }}
              </p>

              <a href="{{ url('/en/blog/'.($blogComplement['slug'] ?? '')) }}"
                 class="mt-3 inline-flex text-sm font-semibold text-amber-700 hover:text-amber-800">
                Continue reading →
              </a>
            </section>
          @endif
        </div>

        <section
          class="rounded-2xl bg-white/90 shadow-sm ring-1 ring-slate-200/60 p-4 sm:p-5"
          data-page-url="{{ $pageUrl }}"
          data-default-tab="{{ $defaultTab }}"
          data-date-iso="{{ $dateISO ?: $todayISO }}"
          data-date-human="{{ $dateHumanEn }}"
          data-celebration="{{ $celebrationEn ?: 'Mass of the day' }}"
        >
          <div class="mb-4 flex flex-wrap items-center justify-between gap-3 rounded-xl bg-amber-50/60 px-3 py-2">
            <div class="flex items-center gap-2">
              <span class="text-xs font-semibold text-amber-800">Date</span>
              <input
                id="lit-date"
                type="date"
                value="{{ $dateISO ?: $todayISO }}"
                class="rounded-lg border border-amber-200 bg-white px-3 py-1.5 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-400"
                aria-label="Select date"
              />
            </div>

            <div class="flex items-center gap-1 text-slate-700">
              <button id="font-minus" type="button"
                class="rounded-lg px-2.5 py-1.5 font-semibold hover:bg-white/70 focus:outline-none focus:ring-2 focus:ring-amber-400"
                aria-label="Decrease font">A−</button>

              <button id="font-reset" type="button"
                class="rounded-lg px-2.5 py-1.5 text-xs font-semibold text-amber-900 hover:bg-white/70 focus:outline-none focus:ring-2 focus:ring-amber-400">
                Default
              </button>

              <button id="font-plus" type="button"
                class="rounded-lg px-2.5 py-1.5 font-semibold hover:bg-white/70 focus:outline-none focus:ring-2 focus:ring-amber-400"
                aria-label="Increase font">A+</button>

              <span class="mx-2 h-4 w-px bg-amber-200"></span>

              <button id="share-btn" type="button"
                class="rounded-lg bg-amber-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-amber-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-amber-400"
                aria-label="Share">
                Share
              </button>
            </div>
          </div>

          <div class="mt-2">
            <p class="text-sm font-semibold text-slate-800">
              Readings, Psalms and Gospel
            </p>

            <div class="mt-3 -mx-1 px-1 pt-1 overflow-x-auto overflow-y-visible">
              <div class="flex gap-2 pb-2 pt-1 snap-x snap-mandatory overflow-visible">
                @foreach($tabs as $t)
                  @php $isDefaultBtn = (($t['id'] ?? '') === ($defaultTab ?? '')); @endphp

                  <button type="button"
                    class="tab-btn lit-tab-btn snap-start shrink-0 rounded-full px-4 py-2 text-sm font-semibold
                      {{ $isDefaultBtn
                          ? 'bg-amber-600 text-white shadow-sm'
                          : 'bg-white/70 text-slate-700 ring-1 ring-slate-200 hover:bg-slate-50'
                      }}"
                    data-tab="{{ $t['id'] }}"
                    aria-selected="{{ $isDefaultBtn ? 'true' : 'false' }}">
                    {{ $t['label'] }}
                  </button>
                @endforeach
              </div>
            </div>
          </div>

          <div class="mt-5" id="tab-panels">
            @foreach($tabs as $t)
              @php
                $kind = $t['kind'] ?? 'reading';

                $kindBg = match($kind) {
                  'gospel' => 'bg-rose-50/40',
                  'psalm' => 'bg-emerald-50/40',
                  'reading' => 'bg-indigo-50/40',
                  'antiphons' => 'bg-amber-50/40',
                  'prayers' => 'bg-sky-50/40',
                  'extras' => 'bg-slate-50/60',
                  default => 'bg-white',
                };

                $barClass = match($kind) {
                  'gospel' => 'bg-rose-500',
                  'psalm' => 'bg-emerald-500',
                  'reading' => 'bg-indigo-500',
                  'antiphons' => 'bg-amber-500',
                  'prayers' => 'bg-sky-500',
                  'extras' => 'bg-slate-400',
                  default => 'bg-slate-400',
                };

                $isDefault = (($t['id'] ?? '') === ($defaultTab ?? ''));
              @endphp

              <div class="tab-panel {{ $isDefault ? '' : 'hidden' }}" data-panel="{{ $t['id'] }}">
                <div class="rounded-2xl {{ $kindBg }} overflow-visible">
                  <div class="h-1.5 {{ $barClass }}"></div>

                  <div class="p-4 sm:p-5">
                    <div class="lit-bible max-w-[68ch] mx-auto" data-font-body>
                      @if($t['kind'] === 'reading')
                        @php $items = $t['payload']['items'] ?? []; $cur = $items[0] ?? []; @endphp
                        @include('liturgia.partials.card-reading', [
                          'label' => $t['payload']['label'] ?? 'Reading',
                          'ref' => $cur['referencia'] ?? null,
                          'subtitle' => $cur['titulo'] ?? null,
                          'html' => $cur['textoHtml'] ?? null,
                          'text' => $cur['texto'] ?? null,
                          'kind' => 'reading',
                          'lang' => 'en',
                        ])
                      @elseif($t['kind'] === 'psalm')
                        @php $ps = $t['payload'] ?? []; @endphp
                        @include('liturgia.partials.card-psalm', [
                          'ref' => $ps['referencia'] ?? null,
                          'refrao' => $ps['refrao'] ?? null,
                          'html' => $ps['textoHtml'] ?? null,
                          'text' => $ps['texto'] ?? null,
                          'lang' => 'en',
                        ])
                      @elseif($t['kind'] === 'gospel')
                        @php $items = $t['payload'] ?? []; $cur = $items[0] ?? []; @endphp
                        @include('liturgia.partials.card-reading', [
                          'label' => 'Gospel',
                          'ref' => $cur['referencia'] ?? null,
                          'subtitle' => $cur['titulo'] ?? 'A reading from the holy Gospel',
                          'html' => $cur['textoHtml'] ?? null,
                          'text' => $cur['texto'] ?? null,
                          'kind' => 'gospel',
                          'lang' => 'en',
                        ])
                      @elseif($t['kind'] === 'extras')
                        @php $items = $t['payload'] ?? []; @endphp
                        <div class="space-y-5">
                          @foreach($items as $idx => $x)
                            @include('liturgia.partials.card-extra', [
                              'title' => (string)($x['titulo'] ?? $x['tipo'] ?? ('Extra '.($idx+1))),
                              'ref' => $x['referencia'] ?? null,
                              'html' => $x['textoHtml'] ?? null,
                              'text' => $x['texto'] ?? null,
                              'lang' => 'en',
                            ])
                          @endforeach
                        </div>
                      @elseif($t['kind'] === 'antiphons')
                        @php $a = $t['payload'] ?? []; @endphp
                        @include('liturgia.partials.card-antiphons', [
                          'entradaHtml' => $a['entradaHtml'] ?? null,
                          'entradaText' => $a['entrada'] ?? null,
                          'comunhaoHtml' => $a['comunhaoHtml'] ?? null,
                          'comunhaoText' => $a['comunhao'] ?? null,
                          'lang' => 'en',
                        ])
                      @elseif($t['kind'] === 'prayers')
                        @php $o = $t['payload'] ?? []; @endphp
                        @include('liturgia.partials.card-prayers', ['o' => $o, 'lang' => 'en'])
                      @endif
                    </div>
                  </div>

                  <div class="px-4 sm:px-5 pb-1">
                    <div class="h-px bg-slate-200/60"></div>
                  </div>

                  <div class="px-4 sm:px-5 py-3 text-xs text-slate-500 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>URL: <span class="font-semibold">/en/daily-mass-readings/{{ $dateSlug }}</span></div>
                    <div id="share-hint" class="text-slate-500"></div>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </section>

        {{-- REAL ADSENSE - MIDDLE / IN-ARTICLE --}}
        <section class="mt-8">
          <div class="ads-slot rounded-2xl border border-slate-200 bg-white/90 p-4 shadow-sm">
            <p class="text-xs font-semibold tracking-wide text-amber-700 uppercase">Ad</p>

            <div class="mt-3">
              <ins class="adsbygoogle"
                   style="display:block; width:100%; min-height:280px;"
                   data-ad-client="{{ $adClient }}"
                   data-ad-slot="{{ $slotMiddle }}"
                   data-ad-format="fluid"
                   data-ad-layout="in-article"></ins>
            </div>

            <script>
              try {
                (window.adsbygoogle = window.adsbygoogle || []).push({});
              } catch (e) {}
            </script>
          </div>
        </section>

        {{-- REAL ADSENSE - BOTTOM --}}
        <section class="mt-8">
          <div class="ads-slot rounded-2xl border border-slate-200 bg-white/90 p-4 shadow-sm">
            <p class="text-xs font-semibold tracking-wide text-amber-700 uppercase">Advertisement</p>

            <div class="mt-3">
              <ins class="adsbygoogle"
                   style="display:block; width:100%; min-height:280px;"
                   data-ad-client="{{ $adClient }}"
                   data-ad-slot="{{ $slotBottom }}"
                   data-ad-format="auto"
                   data-full-width-responsive="true"></ins>
            </div>

            <script>
              try {
                (window.adsbygoogle = window.adsbygoogle || []).push({});
              } catch (e) {}
            </script>
          </div>
        </section>

        <div class="lit-aside-mobile mt-6">
          <div class="rounded-2xl bg-white/90 p-4 shadow-sm ring-1 ring-slate-200/60">
            <div class="flex items-center justify-between">
              <p class="text-xs font-semibold tracking-wide text-amber-700 uppercase">
                Quick access
              </p>
              <button type="button" id="aside-toggle"
                class="text-xs font-semibold text-slate-600 hover:text-slate-900">
                Show
              </button>
            </div>

            <div id="aside-mobile" class="mt-3 hidden">
              @include('liturgia.partials.aside', [
                'year' => $year,
                'month' => $month,
                'todaySlug' => $todaySlug,
                'todayLabel' => $todayLabel,
                'prevSlug' => $prevSlug,
                'nextSlug' => $nextSlug,
                'adsSlotDesktop300x250' => $slotAsideTop,
                'adsSlotDesktopSticky' => $slotAsideBottom,
                'variant' => 'mobile',
                'pageSlug' => $pageSlug,
                'lang' => 'en',
                'blogLinks' => $effectiveBlogLinks,
              ])
            </div>
          </div>
        </div>

        <footer class="mt-8 border-t border-slate-200 pt-6">
          <p class="text-xs text-slate-500 break-words">
            Share the readings and help someone pray today.
          </p>
        </footer>
      </section>

      <aside class="lit-aside-desktop min-w-0">
        <div class="sticky top-20">
          @include('liturgia.partials.aside', [
            'year' => $year,
            'month' => $month,
            'todaySlug' => $todaySlug,
            'todayLabel' => $todayLabel,
            'prevSlug' => $prevSlug,
            'nextSlug' => $nextSlug,
            'adsSlotDesktop300x250' => $slotAsideTop,
            'adsSlotDesktopSticky' => $slotAsideBottom,
            'blogLinks' => $effectiveBlogLinks,
            'variant' => 'desktop',
            'pageSlug' => $pageSlug,
            'lang' => 'en',
          ])
        </div>
      </aside>

    </div>
  </article>
</div>
@endsection

@push('scripts')
<script>
(function(){
  const root = document.querySelector('[data-default-tab]');
  if(!root) return;

  const pageUrl = root.getAttribute('data-page-url') || window.location.href;
  const dateHuman = root.getAttribute('data-date-human') || '';
  const celebration = root.getAttribute('data-celebration') || 'Mass of the day';

  let fontStep = 0;
  const clamp = (n,min,max)=>Math.max(min,Math.min(max,n));
  const getFontPx = ()=> clamp(18 + fontStep * 1, 16, 24);

  function applyFont(){
    const px = getFontPx();
    document.querySelectorAll('#tab-panels [data-font-body]').forEach(el=>{
      el.style.fontSize = px+'px';
      el.style.lineHeight = '1.9';
    });
  }

  function setActive(tabId){
    document.querySelectorAll('.tab-panel').forEach(p=>{
      p.classList.toggle('hidden', p.getAttribute('data-panel') !== tabId);
    });

    document.querySelectorAll('.tab-btn').forEach(b=>{
      const on = b.getAttribute('data-tab') === tabId;
      b.setAttribute('aria-selected', on ? 'true' : 'false');

      b.className = on
        ? "tab-btn lit-tab-btn snap-start shrink-0 rounded-full px-4 py-2 text-sm font-semibold bg-amber-600 text-white shadow-sm"
        : "tab-btn lit-tab-btn snap-start shrink-0 rounded-full px-4 py-2 text-sm font-semibold bg-white/70 text-slate-700 ring-1 ring-slate-200 hover:bg-slate-50";
    });
  }

  document.addEventListener('click', (e)=>{
    const btn = e.target.closest && e.target.closest('.tab-btn');
    if(btn){
      const id = btn.getAttribute('data-tab');
      if(id) setActive(id);
    }
  });

  const minus = document.getElementById('font-minus');
  const plus = document.getElementById('font-plus');
  const reset = document.getElementById('font-reset');

  minus && minus.addEventListener('click', ()=>{ fontStep = clamp(fontStep-1, -2, 6); applyFont(); });
  plus  && plus.addEventListener('click',  ()=>{ fontStep = clamp(fontStep+1, -2, 6); applyFont(); });
  reset && reset.addEventListener('click', ()=>{ fontStep = 0; applyFont(); });

  applyFont();

  function goToDate(){
    const input = document.getElementById('lit-date');
    if(!input) return;

    const iso = input.value;
    if(!iso) return;

    const parts = iso.split('-');
    if(parts.length !== 3) return;

    const [y,m,d] = parts;
    if(!y||!m||!d) return;

    const slug = `${d}-${m}-${y}`;
    window.location.href = `/en/daily-mass-readings/${slug}`;
  }

  const input = document.getElementById('lit-date');
  input && input.addEventListener('change', ()=>{ goToDate(); });

  const shareBtn = document.getElementById('share-btn');

  async function doShare(){
    const shareTitle = `Daily Mass Readings for ${dateHuman}: Gospel & Readings`;
    const shareText =
      `Mass readings for ${dateHuman} (${celebration}). ` +
      `Read the Gospel and the complete readings here:`;

    try{
      if(navigator.share){
        await navigator.share({ title: shareTitle, text: shareText, url: pageUrl });
        return;
      }

      if(navigator.clipboard && navigator.clipboard.writeText){
        await navigator.clipboard.writeText(`${shareText}\n${pageUrl}`);
        if(shareBtn) shareBtn.textContent = "Copied ✓";
        setTimeout(()=>{ if(shareBtn) shareBtn.textContent="Share"; }, 1600);
        return;
      }

      window.prompt("Copy and share:", `${shareText}\n${pageUrl}`);
    }catch(e){}
  }

  shareBtn && shareBtn.addEventListener('click', doShare);

  const toggle = document.getElementById('aside-toggle');
  const panel = document.getElementById('aside-mobile');

  if(toggle && panel){
    toggle.addEventListener('click', ()=>{
      const open = !panel.classList.contains('hidden');
      panel.classList.toggle('hidden', open);
      toggle.textContent = open ? 'Show' : 'Hide';

      if (!open) {
        setTimeout(() => {
          try {
            panel.querySelectorAll('ins.adsbygoogle:not([data-adsbygoogle-status])').forEach(() => {
              (window.adsbygoogle = window.adsbygoogle || []).push({});
            });
          } catch (e) {}
        }, 50);
      }
    });
  }
})();
</script>
@endpush