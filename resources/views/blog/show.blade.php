{{-- resources/views/blog/show.blade.php --}}
@extends('layouts.site')

@php
  $theme = $theme ?? [];
  $category = $category ?? null;
  $related = collect($related ?? []);
  $latest = collect($latest ?? []);
  $asideBlogLinks = $asideBlogLinks ?? [];
  $asideLatestPosts = $asideLatestPosts ?? [];
  $isFinance = $isFinance ?? false;

  $forceHttps = fn(?string $u) => $u ? preg_replace('#^http://#i', 'https://', $u) : $u;
  $canonical = $forceHttps(url('/blog/'.$post->slug));
  $desc = $post->meta_description ?: trim(str($post->content)->stripTags()->limit(155));
  $title = trim($post->title.' | IA Tio Ben');

  $fmtDate = fn($dt) => $dt ? optional($dt)->format('d/m/Y') : '';
  $isoDate = fn($dt) => $dt ? optional($dt)->toAtomString() : null;
  $readingTime = function (?string $html) {
      $text = trim(strip_tags((string) $html));
      if ($text === '') return null;
      return max(1, (int) ceil(str_word_count($text) / 200));
  };
  $rt = $readingTime($post->content ?? '');

  $cover = function ($p) use ($forceHttps) {
      $raw = trim((string)($p->cover_image_url ?? ''));
      if ($raw === '') return null;
      if (preg_match('#^https?://#i', $raw)) return $forceHttps($raw);
      if (str_starts_with($raw, '/')) return $forceHttps(url($raw));
      if (str_starts_with($raw, 'posts/')) return $forceHttps(asset('storage/'.$raw));
      return $forceHttps(asset(ltrim($raw, '/')));
  };
  $ogImage = $cover($post) ?: url('/og?title='.rawurlencode($post->title).'&description='.rawurlencode('Blog IA Tio Ben'));

  $catName = $theme['label'] ?? $category?->name;
  $catSlug = $theme['route_slug'] ?? $category?->slug;
  $themeKey = $theme['key'] ?? null;
  $hubLinks = [
    ['title' => 'Oração Católica Prática', 'href' => url('/oracao-catolica-pratica'), 'desc' => 'Terço, rotina e perseverança na oração.'],
    ['title' => 'Vida Sacramental Prática', 'href' => url('/vida-sacramental-pratica'), 'desc' => 'Missa, confissão e comunhão no cotidiano.'],
    ['title' => 'Dúvidas da Fé Católica', 'href' => url('/duvidas-da-fe-catolica'), 'desc' => 'Respostas claras para perguntas reais da fé.'],
  ];
  if ($themeKey === 'terco') {
    $hubLinks = array_values(array_filter($hubLinks, fn($h) => !str_contains($h['href'], '/oracao-catolica-pratica')));
    array_unshift($hubLinks, ['title' => 'Oração Católica Prática', 'href' => url('/oracao-catolica-pratica'), 'desc' => 'Aprofunde o tema com guias e aplicações concretas.']);
  }
  if ($themeKey === 'cotidiano') {
    $hubLinks = array_values(array_filter($hubLinks, fn($h) => !str_contains($h['href'], '/vida-sacramental-pratica')));
    array_unshift($hubLinks, ['title' => 'Vida Sacramental Prática', 'href' => url('/vida-sacramental-pratica'), 'desc' => 'Veja conteúdos focados em vida sacramental e rotina de fé.']);
  }
  if ($themeKey === 'homilia' || $themeKey === 'santos') {
    $hubLinks = array_values(array_filter($hubLinks, fn($h) => !str_contains($h['href'], '/duvidas-da-fe-catolica')));
    array_unshift($hubLinks, ['title' => 'Dúvidas da Fé Católica', 'href' => url('/duvidas-da-fe-catolica'), 'desc' => 'Explore respostas para dúvidas recorrentes da vida cristã.']);
  }

  $articleSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'BlogPosting',
    'headline' => $post->title,
    'description' => $desc,
    'image' => [$ogImage],
    'mainEntityOfPage' => $canonical,
    'datePublished' => $isoDate($post->publish_date),
    'dateModified' => $isoDate($post->updated_at ?: $post->publish_date),
    'author' => ['@type' => 'Organization', 'name' => 'IA Tio Ben'],
    'publisher' => ['@type' => 'Organization', 'name' => 'IA Tio Ben'],
  ];

  $breadcrumbSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => array_values(array_filter([
      ['@type' => 'ListItem', 'position' => 1, 'name' => 'Início', 'item' => url('/')],
      ['@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => url('/blog')],
      $catName && $catSlug ? ['@type' => 'ListItem', 'position' => 3, 'name' => $catName, 'item' => url('/blog/categoria/'.$catSlug)] : null,
      ['@type' => 'ListItem', 'position' => $catName && $catSlug ? 4 : 3, 'name' => $post->title, 'item' => $canonical],
    ])),
  ];
@endphp

@section('html_lang', 'pt-BR')
@section('title', $title)
@section('meta_description', $desc)
@section('canonical', $canonical)
@section('robots', 'index,follow,max-image-preview:large')
@section('og_type', 'article')
@section('og_title', $title)
@section('og_description', $desc)
@section('og_url', $canonical)
@section('og_image', $ogImage)
@section('tw_image', $ogImage)

@section('hreflang')
  <link rel="alternate" hreflang="pt-BR" href="{{ $canonical }}" />
  <link rel="alternate" hreflang="en" href="{{ url('/en/blog/'.($post->en_slug ?? $post->slug)) }}" />
  <link rel="alternate" hreflang="x-default" href="{{ $canonical }}" />
@endsection

@push('head')
  <script type="application/ld+json">{!! json_encode($articleSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
  <script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
<main class="bg-slate-50/60 pb-14">
  <header class="border-b bg-white">
    <div class="mx-auto max-w-6xl px-4 py-8">
      <nav class="flex flex-wrap items-center gap-2 text-xs text-slate-500" aria-label="Breadcrumb">
        <a href="{{ url('/') }}" class="hover:underline">Início</a>
        <span>›</span>
        <a href="{{ url('/blog') }}" class="hover:underline">Blog</a>
        @if($catName && $catSlug)
          <span>›</span>
          <a href="{{ url('/blog/categoria/'.$catSlug) }}" class="hover:underline">{{ $catName }}</a>
        @endif
      </nav>

      @if($catName)
        <a href="{{ $catSlug ? url('/blog/categoria/'.$catSlug) : url('/blog') }}" class="mt-5 inline-flex rounded-full border {{ $theme['accentBorder'] ?? 'border-slate-200' }} {{ $theme['accentBg'] ?? 'bg-slate-50' }} px-3 py-1 text-xs font-black uppercase tracking-[0.14em] {{ $theme['accentText'] ?? 'text-slate-700' }}">
          {{ $catName }}
        </a>
      @endif

      <h1 class="mt-4 max-w-4xl text-3xl font-black tracking-tight text-slate-950 md:text-5xl">{{ $post->title }}</h1>

      @if($desc)
        <p class="mt-4 max-w-3xl text-base leading-relaxed text-slate-600 md:text-lg">{{ $desc }}</p>
      @endif

      <div class="mt-5 flex flex-wrap items-center gap-3 text-sm text-slate-500">
        <span class="font-semibold text-slate-700">Por Tio Ben</span>
        @if($post->publish_date)
          <span>•</span>
          <time datetime="{{ optional($post->publish_date)->toAtomString() }}">{{ $fmtDate($post->publish_date) }}</time>
        @endif
        @if($rt)
          <span>•</span>
          <span>{{ $rt }} min de leitura</span>
        @endif
      </div>
    </div>
  </header>

  <div class="mx-auto grid max-w-6xl gap-8 px-4 py-8 lg:grid-cols-[minmax(0,1fr)_340px]">
    <article class="min-w-0 overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm">
      @if($cover($post))
        <img src="{{ $cover($post) }}" alt="{{ $post->title }}" class="aspect-[16/9] w-full object-cover" fetchpriority="high" />
      @endif

      <div class="p-5 md:p-8">
        @if($isFinance)
          <div class="mb-6 rounded-3xl border border-amber-200 bg-amber-50 p-5">
            <p class="text-sm leading-relaxed text-amber-950">
              Este artigo faz parte do hub <strong>Cristão Católico e Finanças</strong>.
              <a href="{{ route('finance.hub') }}" class="font-black underline">Ver todos os conteúdos do hub</a>.
            </p>
          </div>
        @endif

        <div class="prose prose-slate max-w-none prose-headings:font-black prose-a:font-bold prose-a:text-amber-800 hover:prose-a:text-amber-900 prose-img:rounded-3xl">
          {!! $post->content !!}
        </div>

        <section class="mt-10 rounded-3xl border border-amber-200 bg-amber-50 p-6">
          <h2 class="text-lg font-black text-amber-950">Continue sua rotina de oração</h2>
          <p class="mt-2 text-sm leading-relaxed text-amber-900">Acompanhe a Liturgia diária e reze com a Igreja de forma simples e organizada.</p>
          <div class="mt-4 flex flex-wrap gap-2">
            <a href="{{ url('/liturgia-diaria') }}" class="rounded-2xl bg-amber-800 px-4 py-2 text-sm font-black text-white hover:bg-amber-900">Abrir Liturgia</a>
            <a href="{{ url('/santo-terco') }}" class="rounded-2xl bg-white px-4 py-2 text-sm font-black text-amber-950 ring-1 ring-amber-200 hover:bg-amber-100">Rezar o Terço</a>
          </div>
        </section>

        <section class="mt-8 rounded-3xl border border-slate-200 bg-white p-6">
          <h2 class="text-lg font-black text-slate-950">Aprofunde por tema</h2>
          <p class="mt-2 text-sm leading-relaxed text-slate-600">Explore hubs com conteúdo organizado para avançar com mais profundidade.</p>
          <div class="mt-4 grid gap-3 sm:grid-cols-3">
            @foreach($hubLinks as $hub)
              <a href="{{ $hub['href'] }}" class="rounded-2xl border border-slate-200 bg-slate-50 p-4 hover:bg-slate-100">
                <h3 class="text-sm font-black text-slate-900">{{ $hub['title'] }}</h3>
                <p class="mt-1 text-xs leading-relaxed text-slate-600">{{ $hub['desc'] }}</p>
              </a>
            @endforeach
          </div>
        </section>
      </div>
    </article>

    <aside class="min-w-0">
      <div class="sticky top-20">
        @includeIf('blog.partials.aside', [
          'variant' => 'desktop',
          'todaySlug' => now()->format('d-m-Y'),
          'adsSlotRect' => '4921222321',
          'blogLinks' => $asideBlogLinks,
          'latestPosts' => $asideLatestPosts,
          'hideLatestPosts' => false,
        ])
      </div>
    </aside>
  </div>

  @if($related->count())
    <section class="mx-auto max-w-6xl px-4 pb-4">
      <div class="flex items-end justify-between gap-4">
        <div>
          <h2 class="text-2xl font-black text-slate-950">Posts relacionados</h2>
          <p class="mt-1 text-sm text-slate-600">Continue lendo conteúdos próximos a este tema.</p>
        </div>
        <a href="{{ url('/blog/posts') }}" class="text-sm font-bold text-slate-700 hover:text-slate-950">Ver todos →</a>
      </div>

      <div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($related->take(9) as $p)
          <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm hover:shadow-md">
            <a href="{{ url('/blog/'.$p->slug) }}" class="group block">
              <div class="aspect-[16/9] bg-slate-100">
                @if($cover($p))
                  <img src="{{ $cover($p) }}" alt="{{ $p->title }}" loading="lazy" class="h-full w-full object-cover transition group-hover:scale-[1.02]" />
                @endif
              </div>
              <div class="p-5">
                <p class="text-xs text-slate-500">{{ $fmtDate($p->publish_date) }}</p>
                <h3 class="mt-2 text-base font-black leading-snug text-slate-950 group-hover:underline">{{ $p->title }}</h3>
                @if($p->meta_description)
                  <p class="mt-2 text-sm leading-relaxed text-slate-600 line-clamp-3">{{ $p->meta_description }}</p>
                @endif
              </div>
            </a>
          </article>
        @endforeach
      </div>
    </section>
  @endif
</main>
@endsection
