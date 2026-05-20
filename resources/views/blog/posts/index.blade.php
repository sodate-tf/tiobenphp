{{-- resources/views/blog/posts/index.blade.php --}}
@extends('layouts.site')

@section('html_lang', $meta['html_lang'] ?? 'pt-BR')
@section('title', $meta['title'] ?? 'Todos os posts — Blog Tio Ben')
@section('meta_description', $meta['description'] ?? '')
@section('canonical', $meta['canonical'] ?? url('/blog/posts'))
@section('robots', $meta['robots'] ?? 'index,follow')

@section('hreflang')
  <link rel="alternate" hreflang="pt-BR" href="{{ url('/blog/posts') }}"/>
  <link rel="alternate" hreflang="en" href="{{ url('/en/blog/posts') }}"/>
  <link rel="alternate" hreflang="x-default" href="{{ url('/blog/posts') }}"/>
@endsection

@section('og_title', $meta['og_title'] ?? ($meta['title'] ?? ''))
@section('og_description', $meta['og_description'] ?? ($meta['description'] ?? ''))
@section('og_url', $meta['og_url'] ?? ($meta['canonical'] ?? url('/blog/posts')))
@section('og_image', $meta['og_image'] ?? (url('/og?title=Blog%20Tio%20Ben&description=Todos%20os%20posts')))

@section('content')
  <div class="max-w-6xl mx-auto px-4 py-8">
    <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900">Todos os posts</h1>
    <p class="mt-2 text-gray-600">Página stub (listagem completa).</p>

    <div class="mt-6">
      <a class="text-sm font-semibold text-gray-900 hover:underline" href="{{ url('/blog') }}">← Voltar ao portal</a>
    </div>
  </div>
@endsection
