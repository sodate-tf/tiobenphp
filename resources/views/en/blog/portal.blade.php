{{-- resources/views/en/blog/portal.blade.php --}}
@extends('layouts.site')

@php

$defaultCover = asset('images/blog-cover-placeholder.jpg'); // create this file (or change the path)
$coverUrl = function($p) use ($defaultCover) {
  $url = $p->cover_image_url ?? null;
  return !empty($url) ? $url : $defaultCover;
};

  $SITE_URL = rtrim(config('app.url') ?? url('/'), '/');
  $PAGE_PATH = '/en/blog';
  $CANONICAL_URL = $SITE_URL.$PAGE_PATH;

  $fmtDate = function ($dt) {
    try { return optional($dt)->format('M d, Y'); } catch (\Throwable $e) { return ''; }
  };

  $isoDate = function ($dt) {
    try { return optional($dt)->format('Y-m-d'); } catch (\Throwable $e) { return ''; }
  };

  $postUrl = fn($p) => url('/en/blog/'.$p->slug);

  // ===== AdSense (REAL) =====
  $adClient = 'ca-pub-8819996017476509';

  // Slots you are already using in placeholders
  $adSlotTop  = '6026602273';
  $adSlotMid1 = '4649759585';
  $adSlotMid2 = '4921222321';
  $adSlotMid3 = '6552840528';

  // Data from controller:
  // $featured, $heroSecondary, $sections['liturgy'|'saints'|'rosary'|'homily'|'christian_life'|'news']
  $sections = $sections ?? [];

  $lit = $sections['liturgy'] ?? null;
  $san = $sections['saints'] ?? null;
  $ter = $sections['rosary'] ?? null;
  $hom = $sections['homily'] ?? null;
  $cot = $sections['christian_life'] ?? null;
  $not = $sections['news'] ?? null;

  $latest = collect($not['posts'] ?? [])->take(8);

  $todaySlug = now()->format('d-m-Y');

  // Fixed “Explore” links (SEO/internal linking)
  $asideBlogLinks = [
    ['href' => url('/en/blog/how-to-use-the-liturgy'), 'title' => 'How to use the Liturgy in daily life', 'desc' => 'A simple way to pray and prepare for Mass.'],
    ['href' => url('/en/blog/liturgical-year'), 'title' => 'Liturgical year: seasons, colors and calendar', 'desc' => 'Understand what changes throughout the year and how to follow it.'],
    ['href' => url('/en/blog/mass-readings-guide'), 'title' => 'Guide to the Mass readings', 'desc' => 'First reading, psalm, Gospel and how to follow along.'],
    ['href' => url('/en/blog/how-to-pray-with-the-liturgy-in-5-minutes'), 'title' => 'How to pray with the Liturgy in 5 minutes', 'desc' => 'A practical step-by-step to build a routine.'],
    ['href' => url('/en/blog/daily-liturgy-vs-gospel-of-the-day'), 'title' => 'Daily Liturgy vs Gospel of the day', 'desc' => 'Differences, benefits and when to use each.'],
  ];

  $asideLatestPosts = $latest
    ->map(fn($p)=>[
      'href' => $postUrl($p),
      'title' => $p->title,
      'desc' => $p->meta_description ?? null,
    ])
    ->values()
    ->all();

  // ====== SEO JSON-LD ======
  $jsonFlags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;

  $breadcrumbSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
      ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $SITE_URL.'/'],
      ['@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => $CANONICAL_URL],
    ],
  ];

  $itemList = [];
  $itemsForSchema = collect([$featured])->filter()->merge(collect($heroSecondary ?? []))->filter()->take(10);

  foreach ($itemsForSchema as $idx => $p) {
    $itemList[] = [
      '@type' => 'ListItem',
      'position' => $idx + 1,
      'url' => $postUrl($p),
      'name' => $p->title,
    ];
  }

  $itemListSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'ItemList',
    'name' => 'IA Tio Ben Blog Highlights',
    'itemListElement' => $itemList,
  ];

@endphp

@section('html_lang', 'en')
@section('title', 'IA Tio Ben Blog | Gospel, Daily Liturgy and Christian Life')
@section('meta_description', 'Catholic articles about the Gospel of the day, daily Mass readings, prayer, saints and practical Christian spirituality.')
@section('canonical', $CANONICAL_URL)
@section('robots', 'index,follow')

@push('head')
  <meta property="og:url" content="{{ $CANONICAL_URL }}" />
  <meta property="og:site_name" content="IA Tio Ben Blog" />

  {{-- ✅ AdSense loader (ONCE) --}}
  @once
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ $adClient }}" crossorigin="anonymous"></script>
  @endonce

  {{-- Modern layout: real grid without depending on lg: Tailwind --}}
 <style>
  /* General grid: 1 column on mobile, 2 columns on desktop */
  #blogGrid { display:grid; grid-template-columns:minmax(0,1fr); gap:28px; align-items:start; }
  #blogAsideDesktop { display:none; }
  #blogAsideMobile  { display:block; }

  @media (min-width: 1024px){
    #blogGrid { grid-template-columns:minmax(0,1fr) 360px; gap:32px; }
    #blogAsideDesktop { display:block; }
    #blogAsideMobile  { display:none; }
  }

  /* Softer cards: gentle ring, more breathing room */
  .card-soft {
    border: 0;
    box-shadow: 0 10px 30px rgba(15, 23, 42, .06);
    outline: 1px solid rgba(148, 163, 184, .35); /* slate-400/35 */
    outline-offset: 0;
    background: #fff;
  }

  .card-soft:hover {
    outline-color: rgba(148, 163, 184, .55);
    box-shadow: 0 14px 38px rgba(15, 23, 42, .08);
  }

  /* Default cover */
  .cover-placeholder {
    background:
      radial-gradient(1200px 300px at 20% 0%, rgba(245, 158, 11, .18), transparent 60%),
      radial-gradient(900px 260px at 80% 10%, rgba(99, 102, 241, .18), transparent 60%),
      linear-gradient(135deg, #f1f5f9, #e2e8f0);
  }
</style>

  <script type="application/ld+json">{!! json_encode($breadcrumbSchema, $jsonFlags) !!}</script>
  <script type="application/ld+json">{!! json_encode($itemListSchema, $jsonFlags) !!}</script>
@endpush

@section('content')
<main class="mx-auto w-full max-w-6xl px-3 pb-16 pt-4 sm:px-6 lg:px-8">

  {{-- Top utility: breadcrumb + search --}}
  <section class="mt-2">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <nav aria-label="Breadcrumb" class="text-xs text-slate-600">
        <ol class="flex items-center gap-2">
          <li><a href="{{ url('/en') }}" class="hover:underline">Home</a></li>
          <li aria-hidden="true">›</li>
          <li class="font-semibold text-slate-900">Blog</li>
        </ol>
      </nav>

      <form action="{{ url('/en/blog/posts') }}" method="GET" class="w-full sm:w-[420px]" role="search" aria-label="Search the blog">
        <div class="flex items-center gap-2 card-soft bg-white px-3 py-2 shadow-sm">
          <span aria-hidden="true">🔎</span>
          <input
            type="search"
            name="q"
            placeholder="Search: prayer, liturgy, rosary, saints..."
            class="w-full bg-transparent text-sm outline-none placeholder:text-slate-400"
          />
          <button type="submit" class="rounded-xl bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:opacity-95">
            Search
          </button>
        </div>
      </form>
    </div>
  </section>

  {{-- Modern hero: message + chips + CTAs --}}
  <header class="mt-5 card-soft bg-white p-5 shadow-sm sm:p-7">
    <div class="flex flex-col gap-4">
      <div>
        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">
          Catholic spirituality • quick reads for mobile
        </p>
        <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">
          Tio Ben Blog
        </h1>
        <p class="mt-2 max-w-2xl text-sm leading-relaxed text-slate-600 sm:text-base">
          Daily liturgy, saints, rosary and practical reflections to live the Gospel in everyday life.
        </p>
      </div>

      {{-- Editorial chips (strong internal linking) --}}
      <div class="flex flex-wrap gap-2" aria-label="Blog categories">
        <a href="{{ url('/en/blog/posts') }}" class="rounded-full bg-amber-50 px-4 py-2 text-xs font-semibold text-amber-900 hover:opacity-95">
          View all
        </a>

        @if($lit)
          <a href="{{ url('/en/blog/category/'.$lit['categorySlug']) }}"
             class="rounded-full border {{ $lit['theme']['accentBorder'] }} bg-white px-4 py-2 text-xs font-semibold {{ $lit['theme']['accentText'] }} hover:bg-amber-50">
            Liturgy
          </a>
        @endif

        @if($san)
          <a href="{{ url('/en/blog/category/'.$san['categorySlug']) }}"
             class="rounded-full border {{ $san['theme']['accentBorder'] }} bg-white px-4 py-2 text-xs font-semibold {{ $san['theme']['accentText'] }} hover:bg-rose-50">
            Saints
          </a>
        @endif

        @if($ter)
          <a href="{{ url('/en/blog/category/'.$ter['categorySlug']) }}"
             class="rounded-full border {{ $ter['theme']['accentBorder'] }} bg-white px-4 py-2 text-xs font-semibold {{ $ter['theme']['accentText'] }} hover:bg-emerald-50">
            Rosary
          </a>
        @endif

        <a href="{{ url('/en/practical-catholic-prayer') }}"
           class="rounded-full border border-emerald-200 bg-white px-4 py-2 text-xs font-semibold text-emerald-900 hover:bg-emerald-50">
          Practical Catholic Prayer
        </a>
        <a href="{{ url('/en/practical-sacramental-life') }}"
           class="rounded-full border border-indigo-200 bg-white px-4 py-2 text-xs font-semibold text-indigo-900 hover:bg-indigo-50">
          Practical Sacramental Life
        </a>
        <a href="{{ url('/en/catholic-faith-questions') }}"
           class="rounded-full border border-rose-200 bg-white px-4 py-2 text-xs font-semibold text-rose-900 hover:bg-rose-50">
          Catholic Faith Questions
        </a>
      </div>

      {{-- Main CTAs (UX) --}}
      <div class="flex flex-wrap gap-2">
        <a href="{{ url('/en/daily-mass-readings') }}"
           class="inline-flex items-center justify-center rounded-2xl bg-amber-600 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-amber-700">
          📖 Open Today’s Readings
        </a>
        <a href="{{ url('/en/rosary') }}"
           class="inline-flex items-center justify-center rounded-2xl border border-amber-200 bg-white px-4 py-3 text-sm font-semibold text-amber-900 shadow-sm hover:bg-amber-50">
          📿 Pray the Rosary
        </a>
        <a href="{{ url('/en') }}"
           class="inline-flex items-center justify-center bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-900 shadow-sm hover:opacity-95">
          🤖 Chat with Tio Ben
        </a>
      </div>
    </div>
  </header>

  {{-- Top Ad --}}
  <div class="mt-10 flex items-center justify-center w-auto">
    <div class="rounded-2xl card-soft bg-white p-3 w-full">
      <p class="px-1 pb-2 text-[11px] font-semibold text-slate-500">Advertisement</p>
      <ins class="adsbygoogle"
           style="display:block"
           data-ad-client="{{ $adClient }}"
           data-ad-slot="{{ $adSlotTop }}"
           data-ad-format="auto"
           data-full-width-responsive="true"></ins>
      <script>
        (function () {
          window.adsbygoogle = window.adsbygoogle || [];
          window.adsbygoogle.push({});
        })();
      </script>
    </div>
  </div>

  {{-- GRID: Content + Aside --}}
  <div id="blogGrid" class="mt-6">

    {{-- =========================
         MAIN
    ========================== --}}
    <section class="min-w-0 space-y-8">

      {{-- Main highlight + “Trending” (mobile-first) --}}
      @if($featured)
        <section aria-label="Highlights" class="grid grid-cols-1 gap-4 lg:grid-cols-[minmax(0,1fr)_minmax(0,0.9fr)] lg:gap-6">

          {{-- Featured --}}
          <article class="overflow-hidden rounded-[28px] card-soft transition">
            <a href="{{ $postUrl($featured) }}" class="block">
              @if(!empty($featured->cover_image_url))
                <div class="aspect-[16/9] w-full overflow-hidden cover-placeholder">
                  <img
                    src="{{ $coverUrl($featured) }}"
                    alt="{{ $featured->title }}"
                    class="h-full w-full object-cover"
                    loading="lazy"
                    decoding="async"
                  />
                </div>
              @else
                <div class="aspect-[16/9] w-full bg-gradient-to-br from-slate-100 to-slate-200"></div>
              @endif

              <div class="p-5 sm:p-6">
                <div class="flex flex-wrap items-center gap-2 text-[11px] uppercase tracking-[0.16em] text-slate-500">
                  <span class="font-semibold">Featured</span>
                  <span aria-hidden="true">•</span>
                  <time datetime="{{ $isoDate($featured->publish_date) }}">
                    {{ $fmtDate($featured->publish_date) }}
                  </time>
                </div>

                <h2 class="mt-2 text-xl font-extrabold leading-snug text-slate-900 sm:text-2xl">
                  {{ $featured->title }}
                </h2>

                @if($featured->meta_description)
                  <p class="mt-3 text-sm leading-relaxed text-slate-700 sm:text-base">
                    {{ $featured->meta_description }}
                  </p>
                @endif

                <div class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-indigo-700">
                  Read now <span aria-hidden="true">→</span>
                </div>
              </div>
            </a>
          </article>

          {{-- Trending (3 cards) --}}
          <aside class="space-y-3" aria-label="Trending">
            @foreach(collect($heroSecondary ?? [])->take(3) as $p)
             <article class="rounded-[22px] card-soft p-4 transition">
                  <div class="flex items-start gap-3">
                    <a href="{{ $postUrl($p) }}" class="block h-14 w-14 shrink-0 overflow-hidden rounded-2xl cover-placeholder">
                      <img
                        src="{{ $coverUrl($p) }}"
                        alt="{{ $p->title }}"
                        class="h-full w-full object-cover"
                        loading="lazy"
                        decoding="async"
                      />
                    </a>

                    <div class="min-w-0">
                      <div class="text-[11px] text-slate-500">
                        <time datetime="{{ $isoDate($p->publish_date) }}">{{ $fmtDate($p->publish_date) }}</time>
                      </div>

                      <a href="{{ $postUrl($p) }}" class="mt-1 block text-sm font-extrabold text-slate-900 leading-snug hover:underline line-clamp-2">
                        {{ $p->title }}
                      </a>

                      @if($p->meta_description)
                        <p class="mt-2 text-sm text-slate-700 line-clamp-2">{{ $p->meta_description }}</p>
                      @else
                        <p class="mt-2 text-sm text-slate-500 line-clamp-2">Read the full article to understand the topic and apply it in your life of faith.</p>
                      @endif
                    </div>
                  </div>
                </article>
            @endforeach

            {{-- micro-CTA to increase session depth --}}
            <div class="card-soft bg-slate-50 p-4 mb-10">
              <p class="text-sm font-semibold text-slate-900">Want consistency?</p>
              <p class="mt-1 text-xs text-slate-600">Open today’s readings, then read one article per day.</p>
              <div class="mt-3 flex gap-2">
                <a href="{{ url('/en/daily-mass-readings') }}" class="rounded-xl bg-slate-900 px-3 py-2 text-xs font-semibold text-amber-900">Readings</a>
                <a href="{{ url('/en/blog/posts') }}" class="card-soft bg-white px-3 py-2 text-xs font-semibold text-slate-900">All posts</a>
              </div>
            </div>
          </aside>
        </section>
      @endif

      <div class="rounded-2xl card-soft bg-white p-3 w-full">
        <p class="px-1 pb-2 text-[11px] font-semibold text-slate-500">Advertisement</p>
        <ins class="adsbygoogle"
             style="display:block"
             data-ad-client="{{ $adClient }}"
             data-ad-slot="{{ $adSlotMid1 }}"
             data-ad-format="auto"
             data-full-width-responsive="true"></ins>
        <script>
          (function () {
            window.adsbygoogle = window.adsbygoogle || [];
            window.adsbygoogle.push({});
          })();
        </script>
      </div>

      {{-- Editorial hubs (3 columns desktop, mobile 1) --}}
      <section aria-label="Featured categories" class="space-y-4 mt-10">
        <div class="flex items-end justify-between gap-4">
          <h2 class="text-xl font-extrabold text-slate-900 sm:text-2xl">Featured categories</h2>
          <p class="text-xs text-slate-500 hidden sm:block">Organized content for quick reading</p>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
          @foreach(['liturgy','saints','rosary'] as $k)
            @php($col = $sections[$k] ?? null)
            @if(!$col) @continue @endif

            @php($theme = $col['theme'])
            @php($posts = collect($col['posts'] ?? []))
            @php($top = $posts->first())
            @php($rest = $posts->slice(1,4))

            <section class="card-soft bg-white p-5 shadow-sm" aria-label="{{ $theme['label'] }}">
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="text-lg font-extrabold {{ $theme['accentText'] }}">{{ $theme['label'] }}</h3>
                  <div class="mt-2 h-1 w-14 rounded-full {{ $theme['accentUnderline'] }}"></div>
                </div>
                <a href="{{ url('/en/blog/category/'.$col['categorySlug']) }}" class="text-sm font-semibold {{ $theme['accentText'] }} hover:underline">
                  View all
                </a>
              </div>

              <div class="mt-4 space-y-3">
                @if($top)
                  <a href="{{ $postUrl($top) }}" class="block rounded-[22px] border {{ $theme['accentBorder'] }} bg-white p-4 hover:shadow-sm transition">
                    <p class="text-sm font-extrabold text-slate-900 leading-snug line-clamp-2">{{ $top->title }}</p>
                    @if($top->meta_description)
                      <p class="mt-2 text-xs text-slate-600 line-clamp-2">{{ $top->meta_description }}</p>
                    @endif
                    <p class="mt-2 text-[11px] text-slate-500">{{ $fmtDate($top->publish_date) }}</p>
                  </a>
                @endif

                <ul class="space-y-2">
                  @foreach($rest as $p)
                    <li>
                      <a href="{{ $postUrl($p) }}" class="block card-soft bg-white px-4 py-3 text-sm font-semibold text-slate-900 hover:border-slate-300 hover:shadow-sm transition line-clamp-2">
                        {{ $p->title }}
                      </a>
                    </li>
                  @endforeach
                </ul>
              </div>
            </section>
          @endforeach
        </div>
      </section>

      <div class="rounded-2xl card-soft bg-white p-3 w-full">
        <p class="px-1 pb-2 text-[11px] font-semibold text-slate-500">Advertisement</p>
        <ins class="adsbygoogle"
             style="display:block"
             data-ad-client="{{ $adClient }}"
             data-ad-slot="{{ $adSlotMid2 }}"
             data-ad-format="auto"
             data-full-width-responsive="true"></ins>
        <script>
          (function () {
            window.adsbygoogle = window.adsbygoogle || [];
            window.adsbygoogle.push({});
          })();
        </script>
      </div>

      {{-- Two rich sections (Homily + Christian life) --}}
      <section aria-label="Sections" class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        @foreach(['homily','christian_life'] as $key)
          @php($sec = $sections[$key] ?? null)
          @if(!$sec) @continue @endif

          @php($theme = $sec['theme'])
          @php($posts = collect($sec['posts'] ?? []))
          @php($top = $posts->first())
          @php($grid = $posts->slice(1,5))

          <section class="rounded-[28px] border {{ $theme['accentBorder'] }} {{ $theme['accentBg'] }} p-5 shadow-sm">
            <div class="flex items-start justify-between gap-4">
              <div>
                <h2 class="text-xl font-extrabold {{ $theme['accentText'] }}">{{ $theme['label'] }}</h2>
                <div class="mt-2 h-1 w-14 rounded-full {{ $theme['accentUnderline'] }}"></div>
              </div>

              <a href="{{ url('/en/blog/category/'.$sec['categorySlug']) }}"
                 class="rounded-full bg-white px-4 py-2 text-sm font-semibold border {{ $theme['accentBorder'] }} {{ $theme['accentText'] }} hover:shadow-sm">
                View all
              </a>
            </div>

            <div class="mt-4 space-y-3">
              @if($top)
                <a href="{{ $postUrl($top) }}" class="block rounded-[22px] border border-white/60 bg-white/90 p-4 hover:shadow-sm transition">
                  <div class="text-xs text-slate-500">
                    <time datetime="{{ $isoDate($top->publish_date) }}">{{ $fmtDate($top->publish_date) }}</time>
                  </div>
                  <p class="mt-1 text-base font-extrabold text-slate-900 leading-snug line-clamp-2">
                    {{ $top->title }}
                  </p>
                  @if($top->meta_description)
                    <p class="mt-2 text-sm text-slate-700 line-clamp-2">{{ $top->meta_description }}</p>
                  @endif
                </a>
              @endif

              <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                @foreach($grid as $p)
                  <a href="{{ $postUrl($p) }}" class="block rounded-2xl border border-white/60 bg-white/90 p-3 hover:shadow-sm transition">
                    <p class="text-sm font-bold text-slate-900 leading-snug line-clamp-2">{{ $p->title }}</p>
                    <p class="mt-2 text-[11px] text-slate-500">{{ $fmtDate($p->publish_date) }}</p>
                  </a>
                @endforeach
              </div>
            </div>
          </section>
        @endforeach
      </section>

      <div class="rounded-2xl card-soft bg-white p-3 w-full">
        <p class="px-1 pb-2 text-[11px] font-semibold text-slate-500">Advertisement</p>
        <ins class="adsbygoogle"
             style="display:block"
             data-ad-client="{{ $adClient }}"
             data-ad-slot="{{ $adSlotMid3 }}"
             data-ad-format="auto"
             data-full-width-responsive="true"></ins>
        <script>
          (function () {
            window.adsbygoogle = window.adsbygoogle || [];
            window.adsbygoogle.push({});
          })();
        </script>
      </div>

      {{-- Latest posts (scroll/CTR) --}}
      <section aria-label="Latest posts" class="card-soft bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between gap-4">
          <div>
            <h2 class="text-xl font-extrabold text-slate-900">Latest posts</h2>
            <p class="mt-1 text-sm text-slate-600">Quick and clear reading, great on mobile.</p>
          </div>
          <a href="{{ url('/en/blog/posts') }}" class="text-sm font-semibold text-slate-900 hover:underline">
            View all →
          </a>
        </div>

        <div class="mt-4 grid gap-3 sm:grid-cols-2">
          @forelse($latest as $p)
            <article class="rounded-[22px] card-soft p-4 hover:border-slate-300 hover:shadow-sm transition">
              <div class="text-[11px] text-slate-500">
                <time datetime="{{ $isoDate($p->publish_date) }}">{{ $fmtDate($p->publish_date) }}</time>
              </div>
              <a href="{{ $postUrl($p) }}" class="mt-1 block text-sm font-extrabold text-slate-900 leading-snug hover:underline line-clamp-2">
                {{ $p->title }}
              </a>
              @if($p->meta_description)
                <p class="mt-2 text-sm text-slate-700 line-clamp-2">{{ $p->meta_description }}</p>
              @endif
            </article>
          @empty
            <div class="text-slate-600">No posts yet.</div>
          @endforelse
        </div>
      </section>

      <section aria-label="Deepen by Theme" class="card-soft bg-white p-5 shadow-sm">
        <div class="flex items-end justify-between gap-4">
          <div>
            <h2 class="text-xl font-extrabold text-slate-900">Deepen by Theme</h2>
            <p class="mt-1 text-sm text-slate-600">Structured hubs to keep reading with focus and depth.</p>
          </div>
        </div>
        <div class="mt-4 grid gap-3 sm:grid-cols-3">
          <a href="{{ url('/en/practical-catholic-prayer') }}" class="rounded-2xl border border-slate-200 bg-slate-50 p-4 hover:bg-slate-100">
            <h3 class="text-sm font-extrabold text-slate-900">Practical Catholic Prayer</h3>
            <p class="mt-1 text-xs leading-relaxed text-slate-600">Rosary, routine and persevering in prayer.</p>
          </a>
          <a href="{{ url('/en/practical-sacramental-life') }}" class="rounded-2xl border border-slate-200 bg-slate-50 p-4 hover:bg-slate-100">
            <h3 class="text-sm font-extrabold text-slate-900">Practical Sacramental Life</h3>
            <p class="mt-1 text-xs leading-relaxed text-slate-600">Mass, confession and everyday fidelity.</p>
          </a>
          <a href="{{ url('/en/catholic-faith-questions') }}" class="rounded-2xl border border-slate-200 bg-slate-50 p-4 hover:bg-slate-100">
            <h3 class="text-sm font-extrabold text-slate-900">Catholic Faith Questions</h3>
            <p class="mt-1 text-xs leading-relaxed text-slate-600">Clear answers to common faith concerns.</p>
          </a>
        </div>
      </section>

    </section>

    {{-- =========================
         ASIDE
    ========================== --}}

    {{-- Mobile: collapsible aside --}}
    <div id="blogAsideMobile" class="min-w-0">
      <div class="rounded-2xl bg-white/90 p-4 shadow-sm ring-1 ring-slate-200/60">
        <div class="flex items-center justify-between">
          <p class="text-xs font-semibold tracking-wide text-amber-700 uppercase">Quick access</p>
          <button type="button" id="blog-aside-toggle" class="text-xs font-semibold text-slate-600 hover:text-slate-900">
            Show
          </button>
        </div>

        <div id="blog-aside-mobile-panel" class="mt-3 hidden">
          @include('blog.partials.aside', [
            'variant' => 'mobile',
            'todaySlug' => $todaySlug,
            'currentSlug' => $featured->slug ?? null,
            'adsSlotDesktop300x250' => '8534838745',
            'blogLinks' => $asideBlogLinks,
            'latestPosts' => $asideLatestPosts,
            'hideLatestPosts' => false,
          ])
        </div>
      </div>
    </div>

    {{-- Desktop: sticky aside --}}
    <aside id="blogAsideDesktop" class="min-w-0">
      <div class="sticky top-20">
        @include('blog.partials.aside', [
          'variant' => 'desktop',
          'todaySlug' => $todaySlug,
          'currentSlug' => $featured->slug ?? null,
          'adsSlotDesktop300x250' => '8534838745',
          'blogLinks' => $asideBlogLinks,
          'latestPosts' => $asideLatestPosts,
          'hideLatestPosts' => false,
        ])
      </div>
    </aside>

  </div>
</main>
@endsection

@push('scripts')
<script>
(function(){
  const toggle = document.getElementById('blog-aside-toggle');
  const panel = document.getElementById('blog-aside-mobile-panel');
  if(!toggle || !panel) return;

  toggle.addEventListener('click', ()=>{
    const isOpen = !panel.classList.contains('hidden');
    panel.classList.toggle('hidden', isOpen);
    toggle.textContent = isOpen ? 'Show' : 'Hide';
  });
})();
</script>
@endpush
