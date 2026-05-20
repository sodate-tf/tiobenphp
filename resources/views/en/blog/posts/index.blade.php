{{-- resources/views/en/blog/posts/index.blade.php --}}
@extends('layouts.site')

@section('html_lang', $meta['html_lang'] ?? 'en')
@section('title', $meta['title'] ?? 'All posts — IA Tio Ben Blog')
@section('meta_description', $meta['description'] ?? '')
@section('canonical', $meta['canonical'] ?? url('/en/blog/posts'))
@section('robots', $meta['robots'] ?? 'index,follow')

@section('hreflang')
  <link rel="alternate" hreflang="pt-BR" href="{{ url('/blog/posts') }}"/>
  <link rel="alternate" hreflang="en" href="{{ url('/en/blog/posts') }}"/>
  <link rel="alternate" hreflang="x-default" href="{{ url('/en/blog/posts') }}"/>
@endsection

@section('og_title', $meta['og_title'] ?? ($meta['title'] ?? ''))
@section('og_description', $meta['og_description'] ?? ($meta['description'] ?? ''))
@section('og_url', $meta['og_url'] ?? ($meta['canonical'] ?? url('/en/blog/posts')))
@section('og_image', $meta['og_image'] ?? (url('/og?title=IA%20Tio%20Ben%20Blog&description=All%20posts')))

@section('content')
  <div class="max-w-6xl mx-auto px-4 py-8">
    <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900">All posts</h1>
    <p class="mt-2 text-gray-600">Stub page (full listing).</p>

    <div class="mt-6">
      <a class="text-sm font-semibold text-gray-900 hover:underline" href="{{ url('/en/blog') }}">← Back to portal</a>
    </div>
  </div>
@endsection
