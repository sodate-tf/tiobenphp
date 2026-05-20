{{-- resources/views/blog/portal/index.blade.php --}}
@extends('layouts.site')

@section('html_lang', $meta['html_lang'] ?? 'pt-BR')
@section('title', $meta['title'] ?? 'Blog Tio Ben')
@section('meta_description', $meta['description'] ?? '')
@section('canonical', $meta['canonical'] ?? url('/blog'))
@section('robots', $meta['robots'] ?? 'index,follow')

@section('hreflang')
  <link rel="alternate" hreflang="pt-BR" href="{{ url('/blog') }}"/>
  <link rel="alternate" hreflang="en" href="{{ url('/en/blog') }}"/>
  <link rel="alternate" hreflang="x-default" href="{{ url('/blog') }}"/>
@endsection

@section('og_title', $meta['og_title'] ?? ($meta['title'] ?? 'Blog Tio Ben'))
@section('og_description', $meta['og_description'] ?? ($meta['description'] ?? ''))
@section('og_url', $meta['og_url'] ?? ($meta['canonical'] ?? url('/blog')))
@section('og_image', $meta['og_image'] ?? (url('/og?title=Blog%20Tio%20Ben&description=Liturgia%2C%20santos%2C%20ter%C3%A7o%20e%20reflex%C3%B5es')))

@section('content')
  <div class="border-b bg-white">
    <div class="max-w-6xl mx-auto px-4 py-5">
      <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900">Blog do Tio Ben</h1>
          <p class="text-sm md:text-base text-gray-600 mt-1">
            Liturgia diária, santos, terço e reflexões católicas.
          </p>
        </div>

        <div class="flex flex-wrap gap-2">
          <a href="{{ url('/blog/posts') }}"
             class="rounded-full bg-gray-900 text-white px-4 py-2 text-sm font-semibold hover:opacity-95">
            Ver todos
          </a>

          <a href="{{ url('/blog/categoria/liturgia') }}"
             class="rounded-full px-4 py-2 text-sm font-semibold border border-amber-200 text-amber-900 hover:bg-amber-50">
            Liturgia
          </a>

          <a href="{{ url('/blog/categoria/santos') }}"
             class="rounded-full px-4 py-2 text-sm font-semibold border border-rose-200 text-rose-900 hover:bg-rose-50">
            Santos
          </a>

          <a href="{{ url('/blog/categoria/terco') }}"
             class="rounded-full px-4 py-2 text-sm font-semibold border border-emerald-200 text-emerald-900 hover:bg-emerald-50">
            Terço
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="max-w-6xl mx-auto px-4 py-8">
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <p class="text-sm text-slate-700">
        View mínima do portal do blog criada com sucesso.
        Próximo passo: plugar os componentes Blade do portal (hero/three-cols/sections/strip/aside) e buscar posts no controller.
      </p>

      <div class="mt-4 flex flex-wrap gap-2">
        <a class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold hover:bg-slate-50"
           href="{{ url('/blog/posts') }}">Ir para /blog/posts</a>

        <a class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold hover:bg-slate-50"
           href="{{ url('/blog/categoria/liturgia') }}">Categoria Liturgia</a>
      </div>
    </div>
  </div>
@endsection
