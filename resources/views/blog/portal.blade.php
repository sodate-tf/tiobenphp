{{-- resources/views/blog/portal.blade.php --}}
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

  $postUrl = fn ($p) => url('/blog/' . $p->slug);

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

  $categoryLabel = fn ($p) => optional($p->category)->name ?? 'Blog';
@endphp

@section('html_lang', $meta['html_lang'] ?? 'pt-BR')
@section('title', $meta['title'] ?? 'Blog IA Tio Ben')
@section('meta_description', $meta['description'] ?? 'Artigos católicos, liturgia, oração, santos e vida cristã.')
@section('canonical', $meta['canonical'] ?? url('/blog'))
@section('robots', $meta['robots'] ?? 'index,follow')
@section('og_title', $meta['og_title'] ?? ($meta['title'] ?? 'Blog IA Tio Ben'))
@section('og_description', $meta['og_description'] ?? ($meta['description'] ?? 'Artigos católicos, liturgia, oração, santos e vida cristã.'))
@section('og_url', $meta['og_url'] ?? url('/blog'))
@section('og_image', $meta['og_image'] ?? url('/og?title=Blog%20IA%20Tio%20Ben'))

@push('head')
<style>
  .blog-card {
    border: 1px solid rgba(226, 232, 240, .95);
    background: #fff;
    box-shadow: 0 10px 28px rgba(15, 23, 42, .055);
  }

  .blog-card:hover {
    box-shadow: 0 16px 38px rgba(15, 23, 42, .09);
  }

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
      <nav aria-label="Breadcrumb" class="text-xs text-slate-500">
        <a href="{{ url('/') }}" class="hover:underline">Início</a>
        <span class="mx-2">›</span>
        <span class="font-semibold text-slate-800">Blog</span>
      </nav>

      <div class="mt-5 flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
        <div>
          <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-amber-700">
            IA Tio Ben
          </p>

          <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-950 sm:text-4xl">
            Blog do Tio Ben
          </h1>

          <p class="mt-3 max-w-2xl text-base leading-relaxed text-slate-600">
            Reflexões católicas, liturgia diária, santos, oração e vida cristã em ordem do mais recente para o mais antigo.
          </p>
        </div>

        <form action="{{ url('/blog/posts') }}" method="GET" class="w-full lg:w-[420px]" role="search">
          <div class="flex overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <input
              type="search"
              name="q"
              placeholder="Buscar no blog..."
              class="min-w-0 flex-1 border-0 bg-white px-4 py-3 text-sm outline-none focus:ring-0"
            >
            <button type="submit" class="bg-slate-950 px-5 py-3 text-sm font-semibold text-white">
              Buscar
            </button>
          </div>
        </form>
      </div>

      @if(!empty($categoryLinks))
        <div class="mt-6 flex flex-wrap gap-2">
          <a href="{{ url('/blog/posts') }}"
             class="rounded-full bg-slate-950 px-4 py-2 text-xs font-semibold text-white">
            Todos os posts
          </a>

          @foreach($categoryLinks as $cat)
            <a href="{{ url('/blog/categoria/'.$cat['slug']) }}"
               class="rounded-full border {{ $cat['theme']['accentBorder'] ?? 'border-slate-200' }} bg-white px-4 py-2 text-xs font-semibold {{ $cat['theme']['accentText'] ?? 'text-slate-800' }}">
              {{ $cat['label'] }}
            </a>
          @endforeach
        </div>
      @endif
    </div>
  </header>

  <section class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
    @if(!$featured)
      <div class="rounded-3xl border border-slate-200 bg-white p-8 text-slate-700">
        Nenhum post publicado ainda.
      </div>
    @else
      <div class="grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
        <article class="blog-card overflow-hidden rounded-[28px] transition">
          <a href="{{ $postUrl($featured) }}" class="block">
            <div class="aspect-[16/9] w-full overflow-hidden blog-cover-placeholder">
              @if($coverUrl($featured))
                <img
                  src="{{ $coverUrl($featured) }}"
                  alt="{{ $featured->title }}"
                  class="h-full w-full object-cover transition duration-300 hover:scale-[1.015]"
                  loading="eager"
                  decoding="async"
                  fetchpriority="high"
                  onerror="this.remove()"
                >
              @endif
            </div>

            <div class="p-5 sm:p-6">
              <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500">
                <span class="rounded-full bg-amber-50 px-3 py-1 font-semibold text-amber-800">
                  Destaque
                </span>
                <span>{{ $categoryLabel($featured) }}</span>
                <span>•</span>
                <time datetime="{{ $isoDate($featured->created_at) }}">
                  {{ $fmtDate($featured->created_at) }}
                </time>
              </div>

              <h2 class="mt-3 text-2xl font-extrabold leading-tight text-slate-950 sm:text-3xl">
                {{ $featured->title }}
              </h2>

              @if($featured->meta_description)
                <p class="mt-3 text-base leading-relaxed text-slate-600 clamp-3">
                  {{ $featured->meta_description }}
                </p>
              @endif

              <div class="mt-5 inline-flex items-center gap-2 text-sm font-bold text-indigo-700">
                Ler agora <span aria-hidden="true">→</span>
              </div>
            </div>
          </a>
        </article>

        <aside class="space-y-3">
          <div class="flex items-center justify-between">
            <h2 class="text-base font-extrabold text-slate-950">Mais recentes</h2>
            <a href="{{ url('/blog/posts') }}" class="text-xs font-semibold text-slate-600 hover:underline">
              Ver todos
            </a>
          </div>

          @foreach($heroSecondary as $p)
            <article class="blog-card rounded-3xl p-3 transition">
              <a href="{{ $postUrl($p) }}" class="flex gap-3">
                <div class="h-20 w-24 shrink-0 overflow-hidden rounded-2xl blog-cover-placeholder">
                  @if($coverUrl($p))
                    <img
                      src="{{ $coverUrl($p) }}"
                      alt="{{ $p->title }}"
                      class="h-full w-full object-cover"
                      loading="lazy"
                      decoding="async"
                      onerror="this.remove()"
                    >
                  @endif
                </div>

                <div class="min-w-0">
                  <div class="text-[11px] text-slate-500">
                    <time datetime="{{ $isoDate($p->created_at) }}">
                      {{ $fmtDate($p->created_at) }}
                    </time>
                  </div>

                  <h3 class="mt-1 text-sm font-extrabold leading-snug text-slate-950 clamp-2">
                    {{ $p->title }}
                  </h3>

                  <p class="mt-1 text-xs text-slate-500 clamp-2">
                    {{ $p->meta_description ?: 'Leia a reflexão completa no Blog IA Tio Ben.' }}
                  </p>
                </div>
              </a>
            </article>
          @endforeach
        </aside>
      </div>

      <div class="mt-10 grid grid-cols-1 gap-6 lg:grid-cols-3">
        @foreach(['liturgia', 'santos', 'terco'] as $key)
          @php($section = $sections[$key] ?? null)
          @if(!$section) @continue @endif

          @php($posts = collect($section['posts'] ?? [])->take(5))
          @php($theme = $section['theme'])

          <section class="blog-card rounded-[28px] p-5">
            <div class="flex items-center justify-between gap-4">
              <div>
                <h2 class="text-lg font-extrabold {{ $theme['accentText'] }}">
                  {{ $theme['label'] }}
                </h2>
                <div class="mt-2 h-1 w-14 rounded-full {{ $theme['accentUnderline'] }}"></div>
              </div>

              <a href="{{ url('/blog/categoria/'.$section['categorySlug']) }}"
                 class="text-sm font-semibold {{ $theme['accentText'] }} hover:underline">
                Ver todos
              </a>
            </div>

            <div class="mt-5 space-y-3">
              @forelse($posts as $p)
                <article>
                  <a href="{{ $postUrl($p) }}" class="grid grid-cols-[76px_minmax(0,1fr)] gap-3">
                    <div class="aspect-[4/3] overflow-hidden rounded-2xl blog-cover-placeholder">
                      @if($coverUrl($p))
                        <img
                          src="{{ $coverUrl($p) }}"
                          alt="{{ $p->title }}"
                          class="h-full w-full object-cover"
                          loading="lazy"
                          decoding="async"
                          onerror="this.remove()"
                        >
                      @endif
                    </div>

                    <div class="min-w-0">
                      <h3 class="text-sm font-extrabold leading-snug text-slate-950 clamp-2">
                        {{ $p->title }}
                      </h3>
                      <p class="mt-1 text-[11px] text-slate-500">
                        {{ $fmtDate($p->created_at) }}
                      </p>
                    </div>
                  </a>
                </article>
              @empty
                <p class="text-sm text-slate-500">Sem posts nesta editoria.</p>
              @endforelse
            </div>
          </section>
        @endforeach
      </div>

      <section class="mt-10 blog-card rounded-[28px] p-5 sm:p-6">
        <div class="flex items-end justify-between gap-4">
          <div>
            <h2 class="text-xl font-extrabold text-slate-950">Últimos posts</h2>
            <p class="mt-1 text-sm text-slate-600">
              Ordenados por data de criação, do mais recente para o mais antigo.
            </p>
          </div>

          <a href="{{ url('/blog/posts') }}" class="hidden text-sm font-semibold text-slate-900 hover:underline sm:block">
            Ver todos →
          </a>
        </div>

        <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
          @foreach($latest as $p)
            <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white transition hover:shadow-md">
              <a href="{{ $postUrl($p) }}" class="block">
                <div class="aspect-[16/9] overflow-hidden blog-cover-placeholder">
                  @if($coverUrl($p))
                    <img
                      src="{{ $coverUrl($p) }}"
                      alt="{{ $p->title }}"
                      class="h-full w-full object-cover"
                      loading="lazy"
                      decoding="async"
                      onerror="this.remove()"
                    >
                  @endif
                </div>

                <div class="p-4">
                  <div class="flex flex-wrap items-center gap-2 text-[11px] text-slate-500">
                    <span>{{ $categoryLabel($p) }}</span>
                    <span>•</span>
                    <time datetime="{{ $isoDate($p->created_at) }}">
                      {{ $fmtDate($p->created_at) }}
                    </time>
                  </div>

                  <h3 class="mt-2 text-base font-extrabold leading-snug text-slate-950 clamp-2">
                    {{ $p->title }}
                  </h3>

                  @if($p->meta_description)
                    <p class="mt-2 text-sm leading-relaxed text-slate-600 clamp-3">
                      {{ $p->meta_description }}
                    </p>
                  @endif
                </div>
              </a>
            </article>
          @endforeach
        </div>
      </section>
    @endif
  </section>
</main>
@endsection