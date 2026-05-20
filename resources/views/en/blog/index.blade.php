{{-- resources/views/en/blog/index.blade.php --}}
@extends('layouts.site')

@php
  $meta = $meta ?? [];
  $q = $q ?? '';
  $fmtDate = fn($dt) => $dt ? optional($dt)->format('M d, Y') : '';
  $cover = function ($p) {
      $raw = trim((string)($p->cover_image_url ?? ''));
      if ($raw === '') return null;
      if (preg_match('#^https?://#i', $raw) || str_starts_with($raw, '/')) return $raw;
      if (str_starts_with($raw, 'posts/')) return asset('storage/'.$raw);
      return asset(ltrim($raw, '/'));
  };
@endphp

@section('html_lang', $meta['html_lang'] ?? 'en')
@section('title', $meta['title'] ?? 'All Catholic articles — IA Tio Ben Blog')
@section('meta_description', $meta['description'] ?? '')
@section('canonical', $meta['canonical'] ?? url('/en/blog/posts'))
@section('robots', $meta['robots'] ?? 'index,follow,max-image-preview:large')
@section('og_title', $meta['og_title'] ?? ($meta['title'] ?? 'IA Tio Ben Blog'))
@section('og_description', $meta['og_description'] ?? ($meta['description'] ?? ''))
@section('og_url', $meta['og_url'] ?? url('/en/blog/posts'))

@section('content')
<main class="bg-slate-50/60 pb-14">
  <header class="border-b bg-white">
    <div class="mx-auto max-w-6xl px-4 py-7">
      <nav class="text-xs text-slate-500" aria-label="Breadcrumb">
        <a href="{{ url('/en') }}" class="hover:underline">Home</a>
        <span class="mx-1">›</span>
        <a href="{{ url('/en/blog') }}" class="hover:underline">Blog</a>
        <span class="mx-1">›</span>
        <span class="font-semibold text-slate-800">Articles</span>
      </nav>

      <div class="mt-4 grid gap-5 md:grid-cols-[minmax(0,1fr)_420px] md:items-end">
        <div>
          <h1 class="text-3xl font-black text-slate-950">{{ $q !== '' ? 'Results for “'.$q.'”' : 'All articles' }}</h1>
          <p class="mt-2 text-slate-600">Daily Mass readings, saints, rosary, homily and Christian life.</p>
        </div>
        <form action="{{ url('/en/blog/posts') }}" method="GET" class="flex gap-2">
          <input name="q" value="{{ $q }}" type="search" placeholder="Search the blog..." class="min-w-0 flex-1 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-slate-400" />
          <button class="rounded-2xl bg-slate-950 px-4 py-3 text-sm font-bold text-white">Search</button>
        </form>
      </div>
    </div>
  </header>

  <section class="mx-auto max-w-6xl px-4 py-8">
    <div class="mb-6 rounded-3xl border border-slate-200 bg-white p-5">
      <h2 class="text-lg font-extrabold text-slate-900">Deepen by Theme</h2>
      <p class="mt-1 text-sm text-slate-600">Explore curated hubs to expand your reading with clarity and continuity.</p>
      <div class="mt-4 grid gap-3 sm:grid-cols-3">
        <a href="{{ url('/en/practical-catholic-prayer') }}" class="rounded-2xl border border-slate-200 bg-slate-50 p-4 hover:bg-slate-100">
          <h3 class="text-sm font-extrabold text-slate-900">Practical Catholic Prayer</h3>
          <p class="mt-1 text-xs leading-relaxed text-slate-600">Rosary, routine and practical prayer life.</p>
        </a>
        <a href="{{ url('/en/practical-sacramental-life') }}" class="rounded-2xl border border-slate-200 bg-slate-50 p-4 hover:bg-slate-100">
          <h3 class="text-sm font-extrabold text-slate-900">Practical Sacramental Life</h3>
          <p class="mt-1 text-xs leading-relaxed text-slate-600">Mass, confession and sacramental consistency.</p>
        </a>
        <a href="{{ url('/en/catholic-faith-questions') }}" class="rounded-2xl border border-slate-200 bg-slate-50 p-4 hover:bg-slate-100">
          <h3 class="text-sm font-extrabold text-slate-900">Catholic Faith Questions</h3>
          <p class="mt-1 text-xs leading-relaxed text-slate-600">Answers to common Catholic faith questions.</p>
        </a>
      </div>
    </div>

    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
      @forelse($posts as $post)
        <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm hover:shadow-md">
          <a href="{{ url('/en/blog/'.$post->slug) }}" class="group block">
            <div class="aspect-[16/9] bg-slate-100">
              @if($cover($post))
                <img src="{{ $cover($post) }}" alt="{{ $post->title }}" loading="lazy" class="h-full w-full object-cover transition group-hover:scale-[1.02]" />
              @endif
            </div>
            <div class="p-5">
              <div class="flex items-center justify-between gap-2 text-xs text-slate-500">
                <time>{{ $fmtDate($post->publish_date) }}</time>
                @if($post->category?->name)
                  <span class="rounded-full bg-slate-100 px-2 py-1 font-semibold text-slate-700">{{ $post->category->name }}</span>
                @endif
              </div>
              <h2 class="mt-2 text-base font-black leading-snug text-slate-950 group-hover:underline">{{ $post->title }}</h2>
              @if($post->meta_description)
                <p class="mt-2 text-sm leading-relaxed text-slate-600 line-clamp-3">{{ $post->meta_description }}</p>
              @endif
            </div>
          </a>
        </article>
      @empty
        <div class="col-span-full rounded-3xl border border-slate-200 bg-white p-8 text-slate-700">No posts found.</div>
      @endforelse
    </div>

    <div class="mt-8">{{ $posts->links() }}</div>
  </section>
</main>
@endsection
