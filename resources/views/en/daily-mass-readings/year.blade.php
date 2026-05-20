{{-- resources/views/liturgia/en/year.blade.php --}}

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

  {{-- Perf: melhora handshake DNS/TLS do Ads (baixo risco) --}}
  <link rel="preconnect" href="https://pagead2.googlesyndication.com" crossorigin>
  <link rel="preconnect" href="https://googleads.g.doubleclick.net" crossorigin>
  <link rel="preconnect" href="https://tpc.googlesyndication.com" crossorigin>

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

    /* Reserva mínima para evitar salto, sem travar layout */
    .ads-slot { min-height: 120px; }

    .ad-container { width: 100%; }
  </style>
@endpush

@section('content')
@php
  $siteUrl = rtrim(config('app.url') ?: url('/'), '/');

  // Switch PT/EN keeping same year
  $enHref = url("/en/daily-mass-readings/year/{$year}");
  $ptHref = url("/liturgia-diaria/ano/{$year}");

  // For aside (uses TODAY as anchor)
  $todayISO = \Carbon\Carbon::today()->toDateString();
  $todaySlug = \Carbon\Carbon::parse($todayISO)->format('d-m-Y');
  $todayLabel = \Carbon\Carbon::parse($todayISO)->format('d/m/Y');
  $prevSlug = \Carbon\Carbon::parse($todayISO)->subDay()->format('d-m-Y');
  $nextSlug = \Carbon\Carbon::parse($todayISO)->addDay()->format('d-m-Y');

  $asideMonth = (int)\Carbon\Carbon::parse($todayISO)->format('m');

  $defaultBlogLinks = [
    ['href' => url('/en/blog/how-to-use-the-liturgy'), 'title' => 'How to use the Daily Liturgy', 'desc' => 'A simple way to pray and prepare for Mass.'],
    ['href' => url('/en/blog/liturgical-year'), 'title' => 'Liturgical year: seasons, colors and calendar', 'desc' => 'Understand what changes through the year and how to follow.'],
    ['href' => url('/en/blog/mass-readings-guide'), 'title' => 'Mass readings guide', 'desc' => 'First reading, psalm, Gospel and how to follow.'],
    ['href' => url('/en/blog/pray-with-the-liturgy-in-5-minutes'), 'title' => 'Pray with the Liturgy in 5 minutes', 'desc' => 'A practical step-by-step to build consistency.'],
    ['href' => url('/en/blog/daily-liturgy-vs-gospel-of-the-day'), 'title' => 'Daily Liturgy vs Gospel of the Day', 'desc' => 'What each includes and when to use.'],
  ];
  $effectiveBlogLinks = $defaultBlogLinks;

  // ✅ AdSense
  $adClient = 'ca-pub-8819996017476509';

  /**
   * Use um slot dedicado pro "year hub".
   * Ex.: $ads['slot_lit_year_top_responsive'] (ou outro nome)
   * Fallback: $ads['slot_content_responsive']
   */
  $slotTop = $ads['slot_lit_year_top_responsive'] ?? ($ads['slot_content_responsive'] ?? null);
@endphp

<div class="pb-24 md:pb-0">
  <article class="lit-surface mx-auto w-full max-w-7xl px-3 sm:px-4 lg:px-6 py-4 sm:py-6 text-slate-900 leading-relaxed">
    <div class="lit-day-grid gap-5 items-start">

      {{-- MAIN --}}
      <section class="min-w-0">

        {{-- Breadcrumbs --}}
        <nav aria-label="Breadcrumb" class="mb-4 text-sm text-slate-600">
          <ol class="flex flex-wrap items-center gap-2">
            <li>
              <a href="{{ url('/en/daily-mass-readings') }}" class="hover:underline">Daily Mass Readings</a>
            </li>
            <li aria-hidden="true">/</li>
            <li class="text-slate-900 font-semibold">{{ $year }}</li>
          </ol>
        </nav>

        <header class="mb-6">
          <div class="flex items-start justify-between gap-4">
            <div class="min-w-0">
              <p class="text-xs font-semibold tracking-wide text-amber-700 uppercase">
                IA Tio Ben • Daily Mass Readings
              </p>
              <h1 class="mt-2 text-2xl sm:text-4xl font-extrabold tracking-tight">
                Daily Mass Readings Calendar – Year {{ $year }}
              </h1>
            </div>

            {{-- Language switch --}}
            <div class="shrink-0 flex items-center gap-1 text-slate-700">
              <a href="{{ $ptHref }}" class="rounded-lg px-2 py-1 text-xs font-bold text-amber-800 hover:bg-white/70">PT</a>
              <a href="{{ $enHref }}" class="rounded-lg px-2 py-1 text-xs font-bold text-amber-800 hover:bg-white/70">EN</a>
            </div>
          </div>

          <p class="mt-3 text-[15px] leading-7 text-slate-700 max-w-3xl">
            This is the yearly calendar for <strong>Daily Mass Readings</strong> in <strong>{{ $year }}</strong>.
            Choose a month to access the full liturgy for each day, with <strong>Mass readings</strong>,
            the <strong>responsorial psalm</strong>, and the <strong>Gospel of the day</strong>.
          </p>

          {{-- Limited navigation + search --}}
          <div class="mt-4 flex flex-wrap items-center gap-2">
            @if($yearPrev >= $yearMin)
              <a href="{{ url("/en/daily-mass-readings/year/{$yearPrev}") }}"
                 class="rounded-xl border border-slate-200 bg-white/80 px-4 py-2 text-sm font-semibold hover:bg-white">
                ← Year {{ $yearPrev }}
              </a>
            @endif

            @if($yearNext <= $yearMax)
              <a href="{{ url("/en/daily-mass-readings/year/{$yearNext}") }}"
                 class="rounded-xl border border-slate-200 bg-white/80 px-4 py-2 text-sm font-semibold hover:bg-white">
                Year {{ $yearNext }} →
              </a>
            @endif

            <a href="{{ url('/en/daily-mass-readings') }}"
               class="rounded-xl border border-slate-200 bg-white/80 px-4 py-2 text-sm font-semibold hover:bg-white">
              Back to Daily Mass Readings
            </a>

            <div class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white/80 px-3 py-2">
              <span class="text-xs font-semibold text-slate-600">Search year</span>
              <input
                id="year-search"
                type="number"
                inputmode="numeric"
                min="{{ $yearMin }}"
                max="{{ $yearMax }}"
                value="{{ $year }}"
                class="w-24 rounded-lg border border-slate-200 bg-white px-2 py-1 text-sm font-semibold text-slate-800"
                aria-label="Search year"
              />
              <button id="year-go" type="button"
                class="rounded-lg bg-amber-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-amber-700">
                Go
              </button>
            </div>
          </div>
        </header>

        {{-- Liturgical Year (A/B/C) --}}
        <section class="rounded-2xl bg-white/90 p-4 sm:p-5 shadow-sm border border-slate-200">
          <p class="text-xs font-semibold tracking-wide text-amber-700 uppercase">
            Liturgical Year (A/B/C cycle)
          </p>

          <h2 class="mt-2 text-lg sm:text-xl font-extrabold tracking-tight text-slate-900">
            We are currently in Year {{ $lit['letter'] }} (2026)
          </h2>

          <p class="mt-2 text-sm text-slate-700 leading-7">
            The Liturgical Year begins on the <strong>First Sunday of Advent</strong>. In 2026, the current cycle is
            <strong>Year {{ $lit['letter'] }}</strong>, which started in Advent ({{ $lit['advent_start_human'] }}).
            This three-year rotation organizes the Sunday readings into <strong>A</strong>, <strong>B</strong> and <strong>C</strong>.
          </p>

          <div class="mt-4 grid grid-cols-1 gap-3">
            <div class="rounded-xl bg-amber-50/70 p-3 border border-amber-100">
              <p class="text-sm font-extrabold text-slate-900">Year A</p>
              <p class="mt-1 text-sm text-slate-700 leading-7">{{ $lit['textA'] }}</p>
            </div>
            <div class="rounded-xl bg-amber-50/70 p-3 border border-amber-100">
              <p class="text-sm font-extrabold text-slate-900">Year B</p>
              <p class="mt-1 text-sm text-slate-700 leading-7">{{ $lit['textB'] }}</p>
            </div>
            <div class="rounded-xl bg-amber-50/70 p-3 border border-amber-100">
              <p class="text-sm font-extrabold text-slate-900">Year C</p>
              <p class="mt-1 text-sm text-slate-700 leading-7">{{ $lit['textC'] }}</p>
            </div>
          </div>
        </section>

        {{-- ✅ AdSense real (slot único, antes do bloco de meses) --}}
        @if(!empty($slotTop))
          <section class="mt-6">
            <div class="ads-slot rounded-2xl border border-slate-200 bg-white/90 p-4 shadow-sm">
              <p class="text-xs font-semibold tracking-wide text-amber-700 uppercase">Advertisement</p>

              <div class="ad-container mt-3 flex justify-center">
                <ins class="adsbygoogle"
                     style="display:block"
                     data-ad-client="{{ $adClient }}"
                     data-ad-slot="{{ $slotTop }}"
                     data-ad-format="auto"
                     data-full-width-responsive="true"></ins>
              </div>

              <script>
                (function () {
                  function pushAd(){
                    window.adsbygoogle = window.adsbygoogle || [];
                    window.adsbygoogle.push({});
                  }

                  // Evita competir com LCP/INP
                  if ('requestIdleCallback' in window) {
                    requestIdleCallback(pushAd, { timeout: 1500 });
                  } else {
                    setTimeout(pushAd, 350);
                  }
                })();
              </script>
            </div>
          </section>
        @else
          {{-- Se quiser manter o “espaço” quando não houver slot --}}
          <section class="mt-6">
            <div class="ads-slot rounded-2xl border border-slate-200 bg-white/90 p-4 shadow-sm">
              <div class="text-xs text-slate-500">Ad space (Google AdSense)</div>
            </div>
          </section>
        @endif

        {{-- Months (single block; no duplication) --}}
        <section class="mt-6" aria-label="Months of year {{ $year }}">
          <h2 class="text-lg font-extrabold tracking-tight text-slate-900">
            Choose a month
          </h2>
          <p class="mt-2 text-sm text-slate-700 max-w-3xl">
            Select a month to open the monthly calendar and access any day.
          </p>

          <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($months as $x)
              <a href="{{ $x['href'] }}"
                 class="rounded-2xl border border-slate-200 bg-white/90 p-4 hover:bg-white transition"
                 aria-label="Open the Daily Mass Readings calendar for {{ $x['label'] }}">
                <p class="text-sm font-extrabold text-slate-900">{{ $x['label'] }}</p>
                <p class="mt-1 text-xs text-slate-700">
                  Open the month calendar and access any day
                </p>
              </a>
            @endforeach
          </div>
        </section>

        {{-- MOBILE: Aside collapsible --}}
        <div class="lit-aside-mobile mt-6">
          <div class="rounded-2xl bg-white/90 p-4 shadow-sm">
            <div class="flex items-center justify-between">
              <p class="text-xs font-semibold tracking-wide text-amber-700 uppercase">Quick access</p>
              <button type="button" id="aside-toggle"
                class="text-xs font-semibold text-slate-600 hover:text-slate-900">Show</button>
            </div>

            <div id="aside-mobile" class="mt-3 hidden">
              @include('liturgia.partials.aside', [
                'year' => $year,
                'month' => $asideMonth,
                'todaySlug' => $todaySlug,
                'todayLabel' => $todayLabel,
                'prevSlug' => $prevSlug,
                'nextSlug' => $nextSlug,
                'variant' => 'mobile',
                'pageSlug' => null,
                'lang' => 'en',
                'blogLinks' => $effectiveBlogLinks,
              ])
            </div>
          </div>
        </div>

        <footer class="mt-8 border-t border-slate-200 pt-6">
          <p class="text-xs text-slate-500 break-words">
            Share the calendar and help someone pray today.
          </p>
        </footer>
      </section>

      {{-- ASIDE Desktop --}}
      <aside class="lit-aside-desktop min-w-0">
        <div class="sticky top-20">
          @include('liturgia.partials.aside', [
            'year' => $year,
            'month' => $asideMonth,
            'todaySlug' => $todaySlug,
            'todayLabel' => $todayLabel,
            'prevSlug' => $prevSlug,
            'nextSlug' => $nextSlug,
            'adsSlotDesktop300x250' => $ads['slot_desktop'] ?? null,
            'blogLinks' => $effectiveBlogLinks,
            'variant' => 'desktop',
            'pageSlug' => null,
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
  // aside toggle
  const toggle = document.getElementById('aside-toggle');
  const panel = document.getElementById('aside-mobile');
  if(toggle && panel){
    toggle.addEventListener('click', ()=>{
      const open = !panel.classList.contains('hidden');
      panel.classList.toggle('hidden', open);
      toggle.textContent = open ? 'Show' : 'Hide';
    });
  }

  // year search (limited)
  const input = document.getElementById('year-search');
  const go = document.getElementById('year-go');
  const min = Number(input?.getAttribute('min'));
  const max = Number(input?.getAttribute('max'));

  function navigateYear(){
    if(!input) return;
    const y = Number(input.value || '');
    if(!Number.isFinite(y)) return;
    if(y < min || y > max){
      input.value = String({{ $year }});
      return;
    }
    window.location.href = `/en/daily-mass-readings/year/${y}`;
  }

  go && go.addEventListener('click', navigateYear);
  input && input.addEventListener('keydown', (e)=>{
    if(e.key === 'Enter') navigateYear();
  });
})();
</script>
@endpush