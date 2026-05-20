{{-- resources/views/finance/hub.blade.php --}}
@extends('layouts.site')

@php
  $meta = $meta ?? [];
  $latest = collect($latest ?? []);
  $theme = $theme ?? [];
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
@section('title', $meta['title'] ?? 'Cristão Católico e Finanças — IA Tio Ben')
@section('meta_description', $meta['description'] ?? '')
@section('canonical', $meta['canonical'] ?? url('/cristao-catolico-e-financas'))
@section('robots', $meta['robots'] ?? 'index,follow,max-image-preview:large')
@section('og_title', $meta['og_title'] ?? 'Cristão Católico e Finanças — IA Tio Ben')
@section('og_description', $meta['og_description'] ?? ($meta['description'] ?? ''))
@section('og_url', $meta['og_url'] ?? url('/cristao-catolico-e-financas'))
@section('og_image', $meta['og_image'] ?? '')

@push('head')
  <script type="application/ld+json">
  {!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'CollectionPage',
    'name' => 'Cristão Católico e Finanças',
    'description' => $meta['description'] ?? '',
    'url' => $meta['canonical'] ?? url('/cristao-catolico-e-financas'),
  ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
  </script>
@endpush

@section('content')
<main class="bg-slate-50/60 pb-14">
  <header class="border-b bg-white">
    <div class="mx-auto max-w-6xl px-4 py-9">
      <nav class="text-xs text-slate-500" aria-label="Breadcrumb">
        <a href="{{ url('/') }}" class="hover:underline">Início</a>
        <span class="mx-1">›</span>
        <a href="{{ url('/blog') }}" class="hover:underline">Blog</a>
        <span class="mx-1">›</span>
        <span class="font-semibold text-slate-800">Cristão Católico e Finanças</span>
      </nav>

      <div class="mt-5 max-w-4xl">
        <p class="text-xs font-black uppercase tracking-[0.18em] text-amber-800">Hub especial</p>
        <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950 md:text-5xl">Cristão Católico e Finanças</h1>
        <p class="mt-4 text-base leading-relaxed text-slate-600 md:text-lg">
          Catecismo, Doutrina Social, santos e papas aplicados a orçamento, dívidas, investimento ético e generosidade. O foco aqui não é idolatrar dinheiro, mas ordenar a vida financeira pela fé.
        </p>
      </div>

      <div class="mt-5 flex flex-wrap gap-2">
        @foreach(['Catecismo', 'Doutrina Social', 'Orçamento', 'Dívidas', 'Investimento ético', 'Generosidade'] as $tag)
          <span class="rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-bold text-amber-950">{{ $tag }}</span>
        @endforeach
      </div>
    </div>
  </header>

  <section class="mx-auto max-w-6xl px-4 py-8">
    @if($featured)
      <article class="overflow-hidden rounded-[2rem] border border-amber-200 bg-white shadow-sm">
        <a href="{{ url('/blog/'.$featured->slug) }}" class="group grid gap-0 md:grid-cols-[minmax(0,1fr)_360px]">
          <div class="aspect-[16/9] bg-amber-100 md:aspect-auto">
            @if($cover($featured))
              <img src="{{ $cover($featured) }}" alt="{{ $featured->title }}" class="h-full w-full object-cover transition group-hover:scale-[1.02]" fetchpriority="high" />
            @endif
          </div>
          <div class="flex flex-col justify-center p-6 md:p-8">
            <p class="text-xs font-black uppercase tracking-[0.18em] text-amber-800">Comece por aqui</p>
            <h2 class="mt-2 text-2xl font-black leading-tight text-slate-950 md:text-3xl">{{ $featured->title }}</h2>
            @if($featured->meta_description)
              <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $featured->meta_description }}</p>
            @endif
            <span class="mt-5 text-sm font-black text-amber-900">Ler artigo →</span>
          </div>
        </a>
      </article>
    @endif

    <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
      @forelse($latest as $post)
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
      @empty
        <div class="col-span-full rounded-3xl border border-slate-200 bg-white p-8 text-slate-700">Nenhum post publicado neste hub ainda.</div>
      @endforelse
    </div>
  </section>
</main>
@endsection
