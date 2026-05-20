{{-- resources/views/liturgia/en/month.blade.php --}}
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

  {{-- Perf: helps ads DNS/TLS handshake --}}
  <link rel="preconnect" href="https://pagead2.googlesyndication.com" crossorigin>
  <link rel="preconnect" href="https://googleads.g.doubleclick.net" crossorigin>
  <link rel="preconnect" href="https://tpc.googlesyndication.com" crossorigin>

  <style>
    .lit-grid { display:block; }
    .lit-aside-desktop { display:none; }
    .lit-aside-mobile { display:block; }

    @media (min-width: 1024px) {
      .lit-grid { display:grid; grid-template-columns:minmax(0,1fr) 320px; gap:24px; align-items:start; }
      .lit-aside-desktop { display:block; }
      .lit-aside-mobile { display:none; }
    }

    .lit-surface{
      background: linear-gradient(180deg, rgba(255,251,235,0.70) 0%, rgba(255,255,255,1) 55%);
      border-radius: 24px;
    }

    /* Minimal reserve to avoid CLS */
    .ads-slot { min-height: 120px; }
    .ad-container { width: 100%; }

    /* Calendar */
    .cal-grid { display:grid; grid-template-columns: repeat(7, minmax(0, 1fr)); }
    .cal-cell { min-height: 64px; }
    @media (min-width: 640px) { .cal-cell { min-height: 92px; } }
  </style>
@endpush

@section('content')
@php
  $dow = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];

  $prevHref = "/en/daily-mass-readings/year/{$prevMonth['year']}/".str_pad((string)$prevMonth['month'], 2, '0', STR_PAD_LEFT);
  $nextHref = "/en/daily-mass-readings/year/{$nextMonth['year']}/".str_pad((string)$nextMonth['month'], 2, '0', STR_PAD_LEFT);
  $todayHref = "/en/daily-mass-readings/{$todaySlug}";

  $prevLabel = \Carbon\Carbon::create($prevMonth['year'], $prevMonth['month'], 1)->locale('en')->translatedFormat('F')." {$prevMonth['year']}";
  $nextLabel = \Carbon\Carbon::create($nextMonth['year'], $nextMonth['month'], 1)->locale('en')->translatedFormat('F')." {$nextMonth['year']}";

  // ✅ AdSense
  $adClient = 'ca-pub-8819996017476509';

  /**
   * Recommended dedicated slots:
   * - slot_lit_month_top_responsive
   * - slot_lit_month_mid_responsive
   * - slot_lit_month_bottom_responsive
   * Fallback:
   * - slot_content_responsive
   *
   * Your existing slots (usable here too):
   * - BLOG_MOBILE_MID      9515073457
   * - BLOG_INFEED_1        4921222321
   * - BLOG_BOTTOM_BANNER   6552840528
   */
  $slotTop    = $ads['slot_lit_month_top_responsive']    ?? ($ads['slot_content_responsive'] ?? null);
  $slotMid    = $ads['slot_lit_month_mid_responsive']    ?? ($ads['slot_content_responsive'] ?? null);
  $slotBottom = $ads['slot_lit_month_bottom_responsive'] ?? ($ads['slot_content_responsive'] ?? null);

  // Mobile-only slots (use existing ones)
  $slotMobileMid     = $ads['slot_blog_mobile_mid'] ?? '9515073457';
  $slotMobileInfeed1 = $ads['slot_blog_infeed_1']   ?? '4921222321';
  $slotBottomBanner  = $ads['slot_blog_bottom_banner'] ?? '6552840528';

  // local helper to render an ad block
  $renderAd = function (?string $slot, string $label = 'Advertisement', string $visibilityClasses = '') use ($adClient) {
    if (empty($slot)) return '';
    ob_start(); @endphp
      <section class="mb-6 {{ $visibilityClasses }}">
        <div class="ads-slot rounded-2xl border border-slate-200 bg-white/90 p-4 shadow-sm">
          <p class="text-xs font-semibold tracking-wide text-amber-700 uppercase">{{ $label }}</p>

          <div class="ad-container mt-3 flex justify-center">
            <ins class="adsbygoogle"
                 style="display:block"
                 data-ad-client="{{ $adClient }}"
                 data-ad-slot="{{ $slot }}"
                 data-ad-format="auto"
                 data-full-width-responsive="true"></ins>
          </div>

          <script>
            (function () {
              function pushAd(){
                window.adsbygoogle = window.adsbygoogle || [];
                window.adsbygoogle.push({});
              }

              if (document.readyState === 'complete') {
                if ('requestIdleCallback' in window) requestIdleCallback(pushAd, { timeout: 1500 });
                else setTimeout(pushAd, 350);
              } else {
                window.addEventListener('load', function(){
                  if ('requestIdleCallback' in window) requestIdleCallback(pushAd, { timeout: 1500 });
                  else setTimeout(pushAd, 350);
                }, { once: true });
              }
            })();
          </script>
        </div>
      </section>
    @php
    return ob_get_clean();
  };
@endphp

<div class="pb-24 md:pb-0">
  <main class="lit-surface mx-auto w-full max-w-7xl px-3 sm:px-4 lg:px-6 py-4 sm:py-6 text-slate-900 leading-relaxed">
    <div class="lit-grid">

      <article class="min-w-0">
        {{-- Breadcrumbs --}}
        <nav aria-label="Breadcrumb" class="mb-4 text-sm text-slate-600">
          <ol class="flex flex-wrap items-center gap-2">
            <li><a href="{{ url('/en/daily-mass-readings') }}" class="hover:underline">Daily Mass Readings</a></li>
            <li aria-hidden="true">/</li>
            <li><a href="{{ $yearHref }}" class="hover:underline">{{ $year }}</a></li>
            <li aria-hidden="true">/</li>
            <li class="text-slate-900 font-semibold">{{ $monthName }}</li>
          </ol>
        </nav>

        <header class="mb-6">
          <div class="flex items-start justify-between gap-4">
            <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-slate-900">
              Daily Mass Readings Calendar — {{ $monthName }} {{ $year }}
            </h1>

            {{-- Language switch --}}
            <div class="shrink-0 flex items-center gap-1 text-slate-700">
              <a href="{{ url($ptHref) }}" class="rounded-lg px-2 py-1 text-xs font-bold text-amber-800 hover:bg-white/70">PT</a>
              <a href="{{ url($enHref) }}" class="rounded-lg px-2 py-1 text-xs font-bold text-amber-800 hover:bg-white/70">EN</a>
            </div>
          </div>

          <p class="mt-2 text-sm text-slate-700 max-w-3xl">
            Browse <strong>Daily Mass Readings</strong> for any date in <strong>{{ $monthName }} {{ $year }}</strong>.
            Each day includes the <strong>readings</strong>, <strong>responsorial psalm</strong>, and the <strong>Gospel</strong>.
          </p>

          <div class="mt-4 flex flex-wrap items-center gap-2">
            <a href="{{ $prevHref }}"
               class="rounded-xl border border-slate-200 bg-white/90 px-4 py-2 text-sm font-semibold hover:bg-white">
              ← {{ $prevLabel }}
            </a>

            <a href="{{ $nextHref }}"
               class="rounded-xl border border-slate-200 bg-white/90 px-4 py-2 text-sm font-semibold hover:bg-white">
              {{ $nextLabel }} →
            </a>

            <a href="{{ $todayHref }}"
               class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-2 text-sm font-semibold hover:bg-amber-100 text-amber-900">
              Today ({{ $todayLabel }})
            </a>

            <a href="{{ $yearHref }}"
               class="rounded-xl border border-slate-200 bg-white/90 px-4 py-2 text-sm font-semibold hover:bg-white">
              Yearly calendar {{ $year }}
            </a>
          </div>
        </header>

        {{-- ✅ AD #1: top (all devices, dedicated slot) --}}
        {!! $renderAd($slotTop, 'Advertisement') !!}

        {{-- About --}}
        <section class="mb-6 rounded-2xl border border-slate-200 bg-white/90 p-4 sm:p-5 shadow-sm">
          <h2 class="text-lg font-extrabold tracking-tight text-slate-900">
            About {{ $monthName }} {{ $year }}
          </h2>
          <p class="mt-2 text-sm text-slate-700 leading-7 max-w-3xl">
            This page is a <strong>monthly hub</strong> to access Daily Mass Readings by date.
            Use the calendar to quickly find a day. Each daily page gathers the <strong>readings</strong>,
            <strong>responsorial psalm</strong>, and the <strong>Gospel</strong>.
          </p>

          <div class="mt-3 text-sm text-slate-700">
            <p class="font-semibold text-slate-900">How to use this calendar</p>
            <ul class="mt-1 list-disc pl-5 space-y-1">
              <li>Use <strong>“Today”</strong> for quick access.</li>
              <li>Tap a day on the calendar to open that date.</li>
              <li>Use previous/next month navigation and the yearly calendar to browse more dates.</li>
            </ul>
          </div>
        </section>

        {{-- ✅ AD (mobile-only): after About (BLOG_MOBILE_MID) --}}
        {!! $renderAd($slotMobileMid, 'Advertisement', 'lg:hidden') !!}

        {{-- Sundays --}}
        @if(!empty($sundays))
          <section class="mb-6 rounded-2xl border border-slate-200 bg-white/90 p-4 sm:p-5 shadow-sm">
            <h2 class="text-lg font-extrabold tracking-tight text-slate-900">
              Sundays in {{ $monthName }} {{ $year }}
            </h2>
            <p class="mt-2 text-sm text-slate-700 max-w-3xl">
              Direct links to each Sunday in this month.
            </p>

            <ul class="mt-3 flex flex-wrap gap-2">
              @foreach($sundays as $x)
                <li>
                  <a href="{{ url($x['href']) }}"
                     class="inline-flex items-center rounded-xl border border-slate-200 bg-white/90 px-3 py-2 text-sm font-semibold hover:bg-white">
                    {{ str_pad((string)$x['day'],2,'0',STR_PAD_LEFT) }}/{{ str_pad((string)$month,2,'0',STR_PAD_LEFT) }}/{{ $year }}
                  </a>
                </li>
              @endforeach
            </ul>
          </section>
        @endif

        {{-- ✅ AD #2: mid (all devices, dedicated slot) --}}
        {!! $renderAd($slotMid, 'Advertisement') !!}

        {{-- ✅ AD (mobile-only): infeed before calendar (BLOG_INFEED_1) --}}
        {!! $renderAd($slotMobileInfeed1, 'Advertisement', 'lg:hidden') !!}

        {{-- Calendar --}}
        <section aria-label="Monthly calendar — {{ $monthName }} {{ $year }}"
          class="rounded-2xl border border-slate-200 overflow-hidden bg-white/90 shadow-sm">
          <div class="cal-grid bg-slate-50 border-b border-slate-200">
            @foreach($dow as $x)
              <div class="px-2 sm:px-3 py-2 text-[11px] sm:text-xs font-extrabold text-slate-700 uppercase tracking-wide">
                {{ $x }}
              </div>
            @endforeach
          </div>

          <div class="cal-grid">
            @foreach($cells as $idx => $cell)
              @if($cell === null)
                <div class="cal-cell border-b border-slate-100 border-r border-slate-100 bg-white/40"></div>
              @else
                @php
                  $isToday = ($cell['slug'] === $todaySlug);
                  $dtIso = $year.'-'.str_pad((string)$month,2,'0',STR_PAD_LEFT).'-'.str_pad((string)$cell['day'],2,'0',STR_PAD_LEFT);
                @endphp

                <a href="{{ url($cell['href']) }}"
                   class="cal-cell border-b border-slate-100 border-r border-slate-100 p-2 sm:p-3 hover:bg-slate-50
                          focus:outline-none focus:ring-2 focus:ring-slate-300 flex flex-col justify-between
                          {{ $isToday ? 'ring-1 ring-amber-300 bg-amber-50/40' : 'bg-white' }}"
                   aria-label="Open readings for {{ str_pad((string)$cell['day'],2,'0',STR_PAD_LEFT) }}/{{ str_pad((string)$month,2,'0',STR_PAD_LEFT) }}/{{ $year }}">

                  <div class="flex items-start justify-between gap-2">
                    <span class="text-sm sm:text-base font-extrabold text-slate-900">{{ $cell['day'] }}</span>
                    @if($isToday)
                      <span class="rounded-full border border-amber-200 bg-amber-50 px-2 py-0.5 text-[10px] font-bold text-amber-900">Today</span>
                    @endif
                  </div>

                  <time class="text-[10px] sm:text-[11px] text-slate-500" datetime="{{ $dtIso }}">
                    {{ str_pad((string)$month,2,'0',STR_PAD_LEFT) }}/{{ $year }}
                  </time>
                </a>
              @endif
            @endforeach
          </div>
        </section>

        {{-- ✅ AD #3: bottom (all devices, dedicated slot) --}}
        {!! $renderAd($slotBottom, 'Advertisement') !!}

        {{-- ✅ AD (all devices): bottom banner (BLOG_BOTTOM_BANNER) --}}
        {!! $renderAd($slotBottomBanner, 'Advertisement') !!}

        {{-- FAQ --}}
        <section class="mt-2 rounded-2xl border border-slate-200 bg-white/90 p-4 sm:p-5 shadow-sm">
          <h2 class="text-xl font-extrabold tracking-tight text-slate-900">FAQ</h2>

          <div class="mt-4 space-y-4">
            <div>
              <h3 class="text-sm font-extrabold text-slate-900">How can I access today’s readings?</h3>
              <p class="mt-1 text-sm text-slate-700">
                Use the <strong>“Today”</strong> button above or go
                <a class="underline hover:no-underline" href="{{ $todayHref }}"> directly to this page</a>.
              </p>
            </div>

            <div>
              <h3 class="text-sm font-extrabold text-slate-900">Does this page include the full readings for each day?</h3>
              <p class="mt-1 text-sm text-slate-700">
                Yes. This is a monthly calendar with direct links to each date. Each day includes the readings, psalm, and Gospel.
              </p>
            </div>

            <div>
              <h3 class="text-sm font-extrabold text-slate-900">Can I browse other months and years?</h3>
              <p class="mt-1 text-sm text-slate-700">
                Yes. Use the previous/next month buttons and the <strong>yearly calendar</strong>.
              </p>
            </div>
          </div>
        </section>

        {{-- MOBILE: Aside collapsible --}}
        <div class="lit-aside-mobile mt-6">
          <div class="rounded-2xl bg-white/90 p-4 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between">
              <p class="text-xs font-semibold tracking-wide text-amber-700 uppercase">Quick access</p>
              <button type="button" id="aside-toggle"
                class="text-xs font-semibold text-slate-600 hover:text-slate-900">Show</button>
            </div>

            <div id="aside-mobile" class="mt-3 hidden">
              @include('liturgia.partials.aside', [
                'year' => $year,
                'month' => $month,
                'todaySlug' => $todaySlug,
                'todayLabel' => $todayLabel,
                'prevSlug' => $prevSlug,
                'nextSlug' => $nextSlug,
                'variant' => 'mobile',
                'pageSlug' => null,
                'lang' => 'en',
              ])
            </div>
          </div>
        </div>

      </article>

      {{-- ASIDE Desktop --}}
      <aside class="lit-aside-desktop min-w-0">
        <div class="sticky top-20">
          @include('liturgia.partials.aside', [
            'year' => $year,
            'month' => $month,
            'todaySlug' => $todaySlug,
            'todayLabel' => $todayLabel,
            'prevSlug' => $prevSlug,
            'nextSlug' => $nextSlug,
            'adsSlotDesktop300x250' => $ads['slot_aside_300x250'] ?? null,
            'variant' => 'desktop',
            'pageSlug' => null,
            'lang' => 'en',
          ])
        </div>
      </aside>

    </div>
  </main>
</div>
@endsection

@push('scripts')
<script>
(function(){
  const toggle = document.getElementById('aside-toggle');
  const panel = document.getElementById('aside-mobile');
  if(toggle && panel){
    toggle.addEventListener('click', ()=>{
      const open = !panel.classList.contains('hidden');
      panel.classList.toggle('hidden', open);
      toggle.textContent = open ? 'Show' : 'Hide';
    });
  }
})();
</script>
@endpush