{{-- resources/views/en/blog/portal/index.blade.php --}}
@extends('layouts.site')

@section('html_lang', $meta['html_lang'] ?? 'en')
@section('title', $meta['title'] ?? 'IA Tio Ben Blog')
@section('meta_description', $meta['description'] ?? '')
@section('canonical', $meta['canonical'] ?? url('/en/blog'))
@section('robots', $meta['robots'] ?? 'index,follow')

@section('hreflang')
  <link rel="alternate" hreflang="pt-BR" href="{{ url('/blog') }}"/>
  <link rel="alternate" hreflang="en" href="{{ url('/en/blog') }}"/>
  <link rel="alternate" hreflang="x-default" href="{{ url('/en/blog') }}"/>
@endsection

@section('og_title', $meta['og_title'] ?? ($meta['title'] ?? 'IA Tio Ben Blog'))
@section('og_description', $meta['og_description'] ?? ($meta['description'] ?? ''))
@section('og_url', $meta['og_url'] ?? ($meta['canonical'] ?? url('/en/blog')))
@section('og_image', $meta['og_image'] ?? (url('/og?title=IA%20Tio%20Ben%20Blog&description=Catholic%20reflections%2C%20saints%2C%20daily%20readings%20and%20rosary')))

@section('content')
  <div class="border-b bg-white">
    <div class="max-w-6xl mx-auto px-4 py-5">
      <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900">IA Tio Ben Blog</h1>
          <p class="text-sm md:text-base text-gray-600 mt-1">
            Daily readings, saints, rosary, and Catholic reflections.
          </p>
        </div>

        <div class="flex flex-wrap gap-2">
          <a href="{{ url('/en/blog/posts') }}"
             class="rounded-full bg-gray-900 text-white px-4 py-2 text-sm font-semibold hover:opacity-95">
            View all
          </a>

          <a href="{{ url('/en/blog/category/liturgy') }}"
             class="rounded-full px-4 py-2 text-sm font-semibold border border-amber-200 text-amber-900 hover:bg-amber-50">
            Liturgy
          </a>

          <a href="{{ url('/en/blog/category/saints') }}"
             class="rounded-full px-4 py-2 text-sm font-semibold border border-rose-200 text-rose-900 hover:bg-rose-50">
            Saints
          </a>

          <a href="{{ url('/en/blog/category/rosary') }}"
             class="rounded-full px-4 py-2 text-sm font-semibold border border-emerald-200 text-emerald-900 hover:bg-emerald-50">
            Rosary
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="max-w-6xl mx-auto px-4 py-8">
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <p class="text-sm text-slate-700">
        Minimal EN blog portal view created. Next: connect the Blade portal components and fetch posts from DB.
      </p>

      <div class="mt-4 flex flex-wrap gap-2">
        <a class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold hover:bg-slate-50"
           href="{{ url('/en/blog/posts') }}">Go to /en/blog/posts</a>
      </div>
    </div>
  </div>
@endsection
