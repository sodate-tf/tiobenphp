{{-- resources/views/blog/posts/index.blade.php --}}
@extends('layouts.site')

@php
  $meta = $meta ?? [];

  $fmtDate = function ($dt) {
      try {
          return $dt ? \Carbon\Carbon::parse($dt)->format('d/m/Y') : '';
      } catch (\Throwable $e) {
          return '';
      }
  };

  $isoDate = function ($dt) {
      try {
          return $dt ? \Carbon\Carbon::parse($dt)->format('Y-m-d') : '';
      } catch (\Throwable $e) {
          return '';
      }
  };

  $coverUrl = function ($p) {
      $url = trim((string) ($p->cover_image_url ?? ''));

      if ($url === '') {
          return null;
      }

      if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
          $host = parse_url($url, PHP_URL_HOST);
          $path = parse_url($url, PHP_URL_PATH);
          $query = parse_url($url, PHP_URL_QUERY);

          if (in_array($host, ['localhost', '127.0.0.1'], true) && $path) {
              return url($path) . ($query ? '?' . $query : '');
          }

          return $url;
      }

      if (str_starts_with($url, '//')) {
          return 'https:' . $url;
      }

      if (str_starts_with($url, '/storage/')) {
          return url($url);
      }

      if (str_starts_with($url, 'storage/')) {
          return url('/' . $url);
      }

      if (str_starts_with($url, '/')) {
          return url($url);
      }

      $path = $url;
      $query = '';

      if (str_contains($url, '?')) {
          [$path, $query] = explode('?', $url, 2);
      }

      $final = \Illuminate\Support\Facades\Storage::disk('public')->url(ltrim($path, '/'));

      return $final . ($query ? '?' . $query : '');
  };

  $postUrl = fn ($p) => url('/blog/'.$p->slug);
@endphp

@section('html_lang', $meta['html_lang'] ?? 'pt-BR')
@section('title', $meta['title'] ?? 'Todos os posts — Blog IA Tio Ben')
@section('meta_description', $meta['description'] ?? 'Todos os posts do Blog IA Tio Ben.')
@section('canonical', $meta['canonical'] ?? url('/blog/posts'))
@section('robots', $meta['robots'] ?? 'index,follow')

@push('head')
<style>
  .blog-cover-placeholder {
    background:
      radial-gradient(900px 260px at 10% 0%, rgba(245, 158, 11, .18), transparent 60%),
      radial-gradient(800px 260px at 90% 10%, rgba(99, 102, 241, .16), transparent 60%),
      linear-gradient(135deg, #f8fafc, #e2e8f0);
  }

  .clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
</style>
@endpush

@section('content')
<main class="bg-slate-50/60 pb-16">
  <header class="border-b border-slate-200 bg-white">
    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
      <nav class="text-xs text-slate-500">
        <a href="{{ url('/') }}" class="hover:underline">Início</a>
        <span class="mx-2">›</span>
        <a href="{{ url('/blog') }}" class="hover:underline">Blog</a>
        <span class="mx-2">›</span>
        <span class="font-semibold text-slate-800">Todos os posts</span>
      </nav>

      <div class="mt-5 flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
        <div>
          <h1 class="text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl">
            Todos os posts
          </h1>

          <p class="mt-3 max-w-2xl text-base leading-relaxed text-slate-600">
            Lista ordenada por data de criação, do post mais recente para o mais antigo.
          </p>
        </div>

        <form action="{{ url('/blog/posts') }}" method="GET" class="w-full lg:w-[420px]" role="search">
          <div class="flex overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <input
              type="search"
              name="q"
              value="{{ $q ?? '' }}"
              placeholder="Buscar no blog..."
              class="min-w-0 flex-1 border-0 bg-white px-4 py-3 text-sm outline-none focus:ring-0"
            >
            <button type="submit" class="bg-slate-950 px-5 py-3 text-sm font-semibold text-white">
              Buscar
            </button>
          </div>
        </form>
      </div>

      @if(!empty($q))
        <div class="mt-4 rounded-2xl bg-amber-50 px-4 py-3 text-sm text-amber-900">
          Resultado da busca por: <strong>{{ $q }}</strong>
        </div>
      @endif
    </div>
  </header>

  <section class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
    @if($posts->count() === 0)
      <div class="rounded-3xl border border-slate-200 bg-white p-8 text-slate-700">
        Nenhum post encontrado.
      </div>
    @else
      <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($posts as $post)
          <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition hover:shadow-md">
            <a href="{{ $postUrl($post) }}" class="block">
              <div class="aspect-[16/9] overflow-hidden blog-cover-placeholder">
                @if($coverUrl($post))
                  <img
                    src="{{ $coverUrl($post) }}"
                    alt="{{ $post->title }}"
                    class="h-full w-full object-cover"
                    loading="lazy"
                    decoding="async"
                    onerror="this.remove()"
                  >
                @endif
              </div>

              <div class="p-4">
                <div class="flex flex-wrap items-center gap-2 text-[11px] text-slate-500">
                  <span>{{ optional($post->category)->name ?? 'Blog' }}</span>
                  <span>•</span>
                  <time datetime="{{ $isoDate($post->created_at) }}">
                    {{ $fmtDate($post->created_at) }}
                  </time>
                </div>

                <h2 class="mt-2 text-base font-extrabold leading-snug text-slate-950 clamp-2">
                  {{ $post->title }}
                </h2>

                @if($post->meta_description)
                  <p class="mt-2 text-sm leading-relaxed text-slate-600 clamp-3">
                    {{ $post->meta_description }}
                  </p>
                @endif

                <div class="mt-4 text-sm font-bold text-indigo-700">
                  Ler post →
                </div>
              </div>
            </a>
          </article>
        @endforeach
      </div>

      <div class="mt-10">
        {{ $posts->links() }}
      </div>
    @endif
  </section>
</main>
@endsection