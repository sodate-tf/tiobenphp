{{-- resources/views/blog/category.blade.php --}}
@extends('layouts.site')

@php
  $meta = $meta ?? [];
  $theme = $theme ?? [];
  $label = $theme['label'] ?? ($category->name ?? 'Blog');
  $fmtDate = fn($dt) => $dt ? optional($dt)->format('d/m/Y') : '';
  $cover = function ($p) {
      $raw = trim((string)($p->cover_image_url ?? ''));
      if ($raw === '') return null;
      if (preg_match('#^https?://#i', $raw) || str_starts_with($raw, '/')) return $raw;
      if (str_starts_with($raw, 'posts/')) return asset('storage/'.$raw);
      return asset(ltrim($raw, '/'));
  };
@endphp

@section('html_lang', $meta['html_lang'] ?? 'pt-BR')
@section('title', $meta['title'] ?? ($label.' — Blog IA Tio Ben'))
@section('meta_description', $meta['description'] ?? '')
@section('canonical', $meta['canonical'] ?? url('/blog/categoria/'.$categorySlug))
@section('robots', $meta['robots'] ?? 'index,follow,max-image-preview:large')
@section('og_title', $meta['og_title'] ?? ($label.' — Blog IA Tio Ben'))
@section('og_description', $meta['og_description'] ?? ($meta['description'] ?? ''))
@section('og_url', $meta['og_url'] ?? url('/blog/categoria/'.$categorySlug))
@section('og_image', $meta['og_image'] ?? '')

@section('hreflang')
  <link rel="alternate" hreflang="pt-BR" href="{{ url('/blog/categoria/'.$categorySlug) }}" />
@endsection

@section('content')
<main class="bg-slate-50/60 pb-14">
  <header class="border-b bg-white">
    <div class="mx-auto max-w-6xl px-4 py-8">
      <nav class="text-xs text-slate-500" aria-label="Breadcrumb">
        <a href="{{ url('/') }}" class="hover:underline">Início</a>
        <span class="mx-1">›</span>
        <a href="{{ url('/blog') }}" class="hover:underline">Blog</a>
        <span class="mx-1">›</span>
        <span class="font-semibold text-slate-800">{{ $label }}</span>
      </nav>

      <p class="mt-5 text-xs font-black uppercase tracking-[0.18em] {{ $theme['accentText'] ?? 'text-slate-700' }}">Categoria</p>
      <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950 md:text-5xl">{{ $label }}</h1>
      <p class="mt-3 max-w-3xl text-base leading-relaxed text-slate-600">{{ $theme['description'] ?? ($category->description ?? 'Conteúdos recentes do Blog IA Tio Ben.') }}</p>
      <div class="mt-4 h-1 w-20 rounded-full {{ $theme['accentUnderline'] ?? 'bg-slate-500' }}"></div>
    </div>
  </header>

  <section class="mx-auto max-w-6xl px-4 py-8">
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
      @foreach($posts as $post)
        <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm hover:shadow-md">
          <a href="{{ url('/blog/'.$post->slug) }}" class="group block">
            <div class="aspect-[16/9] bg-slate-100">
              @if($cover($post))
                <img src="{{ $cover($post) }}" alt="{{ $post->title }}" loading="lazy" class="h-full w-full object-cover transition group-hover:scale-[1.02]" />
              @endif
            </div>
            <div class="p-5">
              <p class="text-xs text-slate-500">{{ $fmtDate($post->publish_date) }}</p>
              <h2 class="mt-2 text-base font-black leading-snug text-slate-950 group-hover:underline">{{ $post->title }}</h2>
              @if($post->meta_description)
                <p class="mt-2 text-sm leading-relaxed text-slate-600 line-clamp-3">{{ $post->meta_description }}</p>
              @endif
            </div>
          </a>
        </article>
      @endforeach
    </div>

    <div class="mt-8">{{ $posts->links() }}</div>
  </section>
</main>
@endsection
