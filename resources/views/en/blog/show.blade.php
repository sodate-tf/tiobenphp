{{-- resources/views/en/blog/show.blade.php --}}
@extends('layouts.site')

@php
  /**
   * Expected from controller:
   * $post (required)
   * $category (optional)
   * $related (optional collection)
   * $latest (optional collection)
   * $liturgyDate (optional)
   * (optional) $adsSlots / $adsSlotDesktop300x250 / $adsSlotInArticle / etc.
   */

  // =========================
  // Helpers (DECLARE FIRST)
  // =========================
  $forceHttps = function (?string $u) {
    if (!$u) return $u;
    $u = trim((string)$u);
    if ($u === '') return $u;
    return preg_replace('#^http://#i', 'https://', $u);
  };

  $appUrl = rtrim(config('app.url') ?: url('/'), '/');
  $appUrl = $forceHttps($appUrl);

  $appHost = parse_url($appUrl, PHP_URL_HOST);

  $absUrl = function (?string $pathOrUrl) use ($appUrl, $appHost, $forceHttps) {
    if (!$pathOrUrl) return null;
    $u = trim((string)$pathOrUrl);
    if ($u === '') return null;

    if (preg_match('#^https?://#i', $u)) {
      return $forceHttps($u);
    }

    if ($appHost) {
      $hostPattern = preg_quote($appHost, '#');

      if (preg_match("#^{$hostPattern}(/|$)#i", $u)) {
        while (preg_match("#^{$hostPattern}/{$hostPattern}(/|$)#i", $u)) {
          $u = preg_replace("#^({$hostPattern}/)+#i", $appHost.'/', $u);
        }
        return 'https://' . ltrim($u, '/');
      }

      $u = preg_replace("#/({$hostPattern})/\\1/#i", "/{$appHost}/", $u);
    }

    if (str_starts_with($u, '/')) {
      return $forceHttps($appUrl . $u);
    }

    return $forceHttps($appUrl . '/' . ltrim($u, '/'));
  };

  $normalizeCoverPath = function (?string $raw) {
    if (!$raw) return null;
    $c = trim((string)$raw);
    if ($c === '') return null;

    if (
      preg_match('#^https?://#i', $c)
      || str_starts_with($c, '/')
      || preg_match('#^[a-z0-9.-]+\.[a-z]{2,}(/|$)#i', $c)
    ) {
      return $c;
    }

    if (str_starts_with($c, 'posts/')) {
      return '/storage/' . ltrim($c, '/');
    }

    return '/' . ltrim($c, '/');
  };

  // =========================
  // Main metadata
  // =========================
  $title = trim(($post->title ?? '') . ' | IA Tio Ben');

  $desc = $post->meta_description
    ?? $post->excerpt
    ?? 'Catholic reflections on the Gospel of the day, daily Mass readings, prayer, saints and spiritual life.';

  $canonical = $forceHttps(url('/en/blog/' . ($post->slug ?? '')));

  $defaultCover = 'data:image/svg+xml;utf8,' . rawurlencode(
    '<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="630">
      <defs>
        <linearGradient id="g" x1="0" x2="1">
          <stop offset="0" stop-color="#f1f5f9"/>
          <stop offset="1" stop-color="#e2e8f0"/>
        </linearGradient>
      </defs>
      <rect width="1200" height="630" fill="url(#g)"/>
      <text x="60" y="120" font-family="Inter, Arial" font-size="54" font-weight="800" fill="#0f172a">IA Tio Ben</text>
      <text x="60" y="190" font-family="Inter, Arial" font-size="30" font-weight="600" fill="#334155">Blog • Gospel, Liturgy & Christian Life</text>
    </svg>'
  );

  $coverUrl = function($p) use ($defaultCover, $absUrl, $normalizeCoverPath) {
    $raw = $p->cover_image_url ?? null;
    if (empty($raw)) return $defaultCover;

    $norm = $normalizeCoverPath($raw);
    $abs  = $absUrl($norm);

    return $abs ?: $defaultCover;
  };

  $ogImage = $coverUrl($post);

  $fmtDate = function ($dt) {
    try { return optional($dt)->format('F j, Y'); } catch (\Throwable $e) { return ''; }
  };

  $readingTime = function (?string $html) {
    $text = trim(strip_tags((string)$html));
    if ($text === '') return null;
    $words = str_word_count($text);
    $min = max(1, (int) ceil($words / 200));
    return $min;
  };

  $rt = $readingTime($post->content ?? null);

  $catName = $category->name ?? $category['name'] ?? null;
  $catSlug = $category->slug ?? $category['slug'] ?? null;
  $themeKey = $theme['key'] ?? null;
  $hubLinks = [
    ['title' => 'Practical Catholic Prayer', 'href' => url('/en/practical-catholic-prayer'), 'desc' => 'Rosary, routine and perseverance in prayer.'],
    ['title' => 'Practical Sacramental Life', 'href' => url('/en/practical-sacramental-life'), 'desc' => 'Mass, confession and communion in everyday life.'],
    ['title' => 'Catholic Faith Questions', 'href' => url('/en/catholic-faith-questions'), 'desc' => 'Clear answers to real Catholic questions.'],
  ];
  if ($themeKey === 'terco') {
    $hubLinks = array_values(array_filter($hubLinks, fn($h) => !str_contains($h['href'], '/en/practical-catholic-prayer')));
    array_unshift($hubLinks, ['title' => 'Practical Catholic Prayer', 'href' => url('/en/practical-catholic-prayer'), 'desc' => 'Go deeper with practical guides and daily steps.']);
  }
  if ($themeKey === 'cotidiano') {
    $hubLinks = array_values(array_filter($hubLinks, fn($h) => !str_contains($h['href'], '/en/practical-sacramental-life')));
    array_unshift($hubLinks, ['title' => 'Practical Sacramental Life', 'href' => url('/en/practical-sacramental-life'), 'desc' => 'Focused content for sacramental consistency and daily faith.']);
  }
  if ($themeKey === 'homilia' || $themeKey === 'santos') {
    $hubLinks = array_values(array_filter($hubLinks, fn($h) => !str_contains($h['href'], '/en/catholic-faith-questions')));
    array_unshift($hubLinks, ['title' => 'Catholic Faith Questions', 'href' => url('/en/catholic-faith-questions'), 'desc' => 'Explore clear answers to recurring faith concerns.']);
  }

  // =========================
  // LITURGY DATE
  // =========================
  $rawLitDate = $liturgyDate
    ?? ($post->liturgy_date ?? null)
    ?? ($post->liturgyDate ?? null)
    ?? ($post->liturgia_date ?? null)
    ?? ($post->date_ref ?? null)
    ?? ($post->ref_date ?? null);

  $litSlug = null;
  $litPretty = null;

  if ($rawLitDate) {
    try {
      if ($rawLitDate instanceof \DateTimeInterface) {
        $litSlug = $rawLitDate->format('d-m-Y');
        $litPretty = $rawLitDate->format('F j, Y');
      } else {
        $s = trim((string)$rawLitDate);

        if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $s)) {
          $litSlug = $s;
          $litPretty = $s;
        } elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $s)) {
          [$y,$m,$d] = explode('-', $s);
          $litSlug = "{$d}-{$m}-{$y}";
          $litPretty = "{$y}-{$m}-{$d}";
        }
      }
    } catch (\Throwable $e) {
      $litSlug = null;
      $litPretty = null;
    }
  }

  $litUrl = $litSlug ? $forceHttps(url('/en/daily-mass-readings/'.$litSlug)) : null;

  $hrefEn = $canonical;
  $ptSlug = trim((string) ($post->pt_slug ?? $post->slug ?? ''));
  $hrefPt = $forceHttps(url('/blog/' . $ptSlug));

  $datePublished = optional($post->publish_date)->toAtomString();
  $dateModified  = optional($post->updated_at)->toAtomString();

  $jsonLdImage = [];
  if (is_string($ogImage) && preg_match('#^https?://#i', $ogImage)) {
    $jsonLdImage = [$ogImage];
  }

  $ads = $adsSlots ?? [
    'top'      => '9515073457',
    'inCover'  => '9045864893',
    'mid'      => '7474884427',
    'after'    => '5602371881',
    'related'  => '2156366376',
    'sidebar'  => ($adsSlotDesktop300x250 ?? '4921222321'),
  ];

  $asideFeatured = $related?->first() ?? null;
@endphp

@section('title', $title)
@section('meta_description', $desc)
@section('canonical', $canonical)
@section('robots', 'index,follow,max-image-preview:large')

@push('head')

<link rel="alternate" hreflang="en" href="{{ $hrefEn }}" />
<link rel="alternate" hreflang="pt-BR" href="{{ $hrefPt }}" />
<link rel="alternate" hreflang="x-default" href="{{ $hrefPt }}" />

<meta property="og:type" content="article" />
<meta property="og:title" content="{{ $title }}" />
<meta property="og:description" content="{{ $desc }}" />
<meta property="og:url" content="{{ $canonical }}" />
<meta name="googlebot" content="max-image-preview:large" />
<meta property="og:site_name" content="IA Tio Ben" />
<meta property="og:image" content="{{ $ogImage }}" />
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">

@if(preg_match('#^https?://#i', $ogImage))
<meta property="og:image:secure_url" content="{{ $ogImage }}" />
@endif

<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="{{ $title }}" />
<meta name="twitter:description" content="{{ $desc }}" />
<meta name="twitter:image" content="{{ $ogImage }}" />

@if(preg_match('#^https?://#i', $ogImage))
<link rel="preload" as="image" href="{{ $ogImage }}">
@endif

@if($datePublished)
<meta property="article:published_time" content="{{ $datePublished }}" />
@endif

@if($dateModified)
<meta property="article:modified_time" content="{{ $dateModified }}" />
@endif

<meta property="article:author" content="Tio Ben IA" />
<meta property="article:section" content="Catholic Spirituality" />

<style>
.post-grid { display:block; }
@media (min-width:1024px){
.post-grid{
display:grid;
grid-template-columns:minmax(0,1fr) 360px;
gap:28px;
align-items:start;
}
}
</style>

{{-- JSON-LD --}}
<script type="application/ld+json">
{!! json_encode([
'@context'=>'https://schema.org',
'@type'=>'Article',
'headline'=>$post->title ?? '',
'description'=>$desc,
'image'=>$jsonLdImage,
'mainEntityOfPage'=>[
'@type'=>'WebPage',
'@id'=>$canonical,
],
'datePublished'=>$datePublished ?: null,
'dateModified'=>$dateModified ?: null,
'author'=>[
'@type'=>'Person',
'name'=>'Tio Ben',
],
'publisher'=>[
'@type'=>'Organization',
'name'=>'IA Tio Ben',
'logo'=>[
'@type'=>'ImageObject',
'url'=>$absUrl('/images/logo-amp.webp')
]
],
'wordCount'=>str_word_count(strip_tags($post->content ?? '')),
'timeRequired'=>$rt ? 'PT'.$rt.'M' : null
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>

@endpush

@section('content')

<div class="max-w-6xl mx-auto px-4 mt-6">
@includeIf('ads.display',['slot'=>$ads['top'],'class'=>'w-full','label'=>'Advertisement'])
</div>

<header class="mt-6 bg-gradient-to-b from-amber-100 to-white">

<div class="max-w-6xl mx-auto px-4 pt-6 pb-6">

<nav aria-label="Breadcrumb" class="text-xs text-slate-600 flex flex-wrap items-center gap-2">
<a href="{{ url('/en/blog') }}" class="font-semibold hover:underline text-slate-800">Blog</a>

@if($catName && $catSlug)
<span class="text-slate-300">›</span>
<a href="{{ url('/en/blog/category/'.$catSlug) }}" class="hover:underline text-slate-700">
{{ $catName }}
</a>
@endif

<span class="text-slate-300">›</span>
<span class="text-slate-500 line-clamp-1">{{ $post->title }}</span>

</nav>

@if($catName && $catSlug)
<div class="mt-3">
<a href="{{ url('/en/blog/category/'.$catSlug) }}"
class="inline-flex items-center rounded-full bg-white/70 text-slate-700 px-3 py-1 text-xs font-semibold ring-1 ring-slate-200 hover:bg-white transition">
{{ $catName }}
</a>
</div>
@endif

<h1 class="mt-3 text-2xl md:text-4xl font-extrabold tracking-tight text-slate-900 leading-tight">
{{ $post->title }}
</h1>

<div class="mt-4">
@includeIf('ads.in-article-fluid',[
'slot'=>$ads['mid'],
'class'=>'w-full',
'label'=>'Advertisement'
])
</div>

<div class="mt-3 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-slate-600">

@if($post->publish_date)

<span>
By <strong>Tio Ben</strong>
</span>

<time datetime="{{ optional($post->publish_date)->format('Y-m-d') }}">
{{ $fmtDate($post->publish_date) }}
</time>

@endif

@if($rt)

<span>
{{ $rt }} min read
</span>

@endif

</div>

<p class="mt-4 text-base md:text-lg text-slate-700 max-w-3xl leading-relaxed">
{{ $desc }}
</p>

@if($litUrl && $litPretty)

<div class="mt-5 rounded-2xl bg-amber-50 ring-1 ring-amber-200/60 p-4">

<div class="flex items-start gap-3">

<div class="mt-0.5 text-amber-700">
<svg width="18" height="18" viewBox="0 0 24 24" fill="none">
<path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2"/>
<path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="2"/>
<path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="2"/>
</svg>
</div>

<div class="min-w-0">

<p class="text-sm text-amber-900 font-semibold">
This post is connected with the Mass readings of {{ $litPretty }}.
</p>

<p class="mt-1 text-sm text-amber-900/80">

To pray with the Church and follow the readings, access:

<a href="{{ $litUrl }}" class="font-semibold underline underline-offset-2 hover:opacity-90">
Daily Mass Readings for {{ $litPretty }}
</a>.

</p>

</div>

</div>

</div>

@endif

</div>

</header>

<div class="max-w-6xl mx-auto px-4 py-8">

<div class="post-grid">

<main class="min-w-0">

<div class="rounded-3xl overflow-hidden bg-slate-100 shadow-sm ring-1 ring-slate-200/70">

<img
src="{{ $coverUrl($post) }}"
alt="{{ $post->title }}"
class="w-full h-auto object-cover"
loading="eager"
fetchpriority="high"
decoding="async"
width="1200"
height="630"
/>

</div>

<div class="mt-5">
@includeIf('ads.in-article-fluid',['slot'=>$ads['inCover'],'class'=>'w-full','label'=>'Advertisement'])
</div>

<div class="mt-8 prose prose-slate max-w-none">
{!! $post->content !!}
</div>

<div class="mt-8">
@includeIf('ads.display',['slot'=>$ads['after'],'class'=>'w-full','label'=>'Advertisement'])
</div>

<section class="mt-10 rounded-3xl bg-amber-50 ring-1 ring-amber-200/60 p-6">

<h2 class="text-lg font-extrabold text-amber-900">
Continue your prayer routine
</h2>

<p class="mt-1 text-sm text-amber-900/80">
Follow the daily Mass readings and pray with the Church.
</p>

<div class="mt-4 flex flex-wrap gap-2">

<a href="{{ url('/en/daily-mass-readings') }}"
class="rounded-xl bg-amber-700 text-white px-4 py-2 text-sm font-semibold hover:bg-amber-800">
Open Today’s Readings
</a>

<a href="{{ url('/en/rosary') }}"
class="rounded-xl bg-white px-4 py-2 text-sm font-semibold text-amber-900 ring-1 ring-amber-200 hover:bg-amber-50">
Pray the Rosary
</a>

</div>

</section>

<section class="mt-8 rounded-3xl border border-slate-200 bg-white p-6">
<h2 class="text-lg font-extrabold text-slate-900">
Deepen by Theme
</h2>
<p class="mt-2 text-sm text-slate-600 leading-relaxed">
Explore organized hubs to continue this topic with more depth.
</p>
<div class="mt-4 grid gap-3 sm:grid-cols-3">
@foreach($hubLinks as $hub)
<a href="{{ $hub['href'] }}" class="rounded-2xl border border-slate-200 bg-slate-50 p-4 hover:bg-slate-100">
<h3 class="text-sm font-extrabold text-slate-900">{{ $hub['title'] }}</h3>
<p class="mt-1 text-xs leading-relaxed text-slate-600">{{ $hub['desc'] }}</p>
</a>
@endforeach
</div>
</section>

<div class="mt-10">
@includeIf('ads.in-article-fluid',['slot'=>$ads['mid'],'class'=>'w-full','label'=>'Advertisement'])
</div>

@if(!empty($related) && $related->count())

<section class="mt-12">

<div class="flex items-end justify-between gap-4">

<h2 class="text-xl md:text-2xl font-extrabold text-slate-900">
Related Posts
</h2>

<a href="{{ url('/en/blog/posts') }}" class="text-sm font-semibold text-slate-700 hover:underline">
See all
</a>

</div>

<div class="mt-4">
@includeIf('ads.display',['slot'=>$ads['related'],'class'=>'w-full','label'=>'Advertisement'])
</div>

<div class="mt-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

@foreach($related->take(9) as $p)

<a href="{{ url('/en/blog/'.$p->slug) }}"
class="group rounded-3xl bg-white ring-1 ring-slate-200/70 overflow-hidden hover:shadow-sm transition">

<div class="aspect-[16/9] bg-slate-100 overflow-hidden">

<img
src="{{ $coverUrl($p) }}"
alt="{{ $p->title }}"
class="h-full w-full object-cover group-hover:scale-[1.02] transition"
loading="lazy"
/>

</div>

<div class="p-5">

<div class="text-xs text-slate-500">
@if($p->publish_date)
{{ $fmtDate($p->publish_date) }}
@endif
</div>

<span class="inline-flex items-center gap-2">
<svg width="16" height="16" viewBox="0 0 24 24" fill="none">
<path d="M20 21V19C20 16.7909 18.2091 15 16 15H8C5.79086 15 4 16.7909 4 19V21" stroke="currentColor" stroke-width="2"/>
<circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
</svg>
By Tio Ben
</span>

<div class="mt-1 text-base font-extrabold text-slate-900 leading-snug group-hover:underline line-clamp-2">
{{ $p->title }}
</div>

@if(!empty($p->meta_description))
<p class="mt-2 text-sm text-slate-700 leading-relaxed line-clamp-3">
{{ $p->meta_description }}
</p>
@endif

</div>

</a>

@endforeach

</div>

</section>

@endif

</main>

<aside class="mt-10 lg:mt-0 min-w-0">

<div class="sticky top-20 space-y-4">

@includeIf('blog.partials.aside',[
'featured'=>$asideFeatured,
'latest'=>$latest ?? collect(),
])

@if(!view()->exists('blog.partials.aside'))

<div class="rounded-3xl bg-white ring-1 ring-slate-200/70 p-5">

<div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">
Quick access
</div>

<div class="mt-3 space-y-2">

<a href="{{ url('/en/daily-mass-readings') }}"
class="block rounded-2xl bg-slate-50 ring-1 ring-slate-200/70 p-4 hover:bg-slate-100 transition">

<div class="text-sm font-extrabold text-slate-900">
Today’s Mass Readings
</div>

<div class="mt-1 text-xs text-slate-600">
Gospel, psalm and readings organized
</div>

</a>

<a href="{{ url('/en/rosary/how-to-pray-the-rosary') }}"
class="block rounded-2xl bg-slate-50 ring-1 ring-slate-200/70 p-4 hover:bg-slate-100 transition">

<div class="text-sm font-extrabold text-slate-900">
How to pray the Rosary
</div>

<div class="mt-1 text-xs text-slate-600">
Step-by-step prayer guide
</div>

</a>

</div>

</div>

@endif

@if(!empty($ads['sidebar']))

@includeIf('ads.sidebar-desktop-300x250',['slot'=>$ads['sidebar']])

@if(!view()->exists('ads.sidebar-desktop-300x250'))

<div class="rounded-3xl bg-white ring-1 ring-slate-200/70 p-4">

@includeIf('ads.display',['slot'=>$ads['sidebar'],'class'=>'w-full','label'=>'Advertisement'])

</div>

@endif

@endif

</div>

</aside>

</div>

</div>

@endsection
