{{-- resources/views/en/blog/category/index.blade.php --}}
@extends('layouts.site')

@section('html_lang', $meta['html_lang'] ?? 'en')
@section('title', ($meta['title'] ?? 'Category') . ' — IA Tio Ben Blog')
@section('meta_description', $meta['description'] ?? '')
@section('canonical', $meta['canonical'] ?? url('/en/blog/category/' . ($categorySlug ?? '')))
@section('robots', $meta['robots'] ?? 'index,follow')

@section('hreflang')
  <link rel="alternate" hreflang="pt-BR" href="{{ url('/blog/categoria/' . ($categorySlug ?? '')) }}"/>
  <link rel="alternate" hreflang="en" href="{{ url('/en/blog/category/' . ($categorySlug ?? '')) }}"/>
  <link rel="alternate" hreflang="x-default" href="{{ url('/en/blog/category/' . ($categorySlug ?? '')) }}"/>
@endsection

@section('og_title', $meta['og_title'] ?? ($meta['title'] ?? 'Category'))
@section('og_description', $meta['og_description'] ?? ($meta['description'] ?? ''))
@section('og_url', $meta['og_url'] ?? ($meta['canonical'] ?? url('/en/blog/category/' . ($categorySlug ?? ''))))
@section('og_image', $meta['og_image'] ?? (url('/og?title=Category&description=' . urlencode((string)($categorySlug ?? '')))))

@section('content')
  <div class="max-w-6xl mx-auto px-4 py-8">
    <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900">
      Category: {{ $categorySlug ?? '-' }}
    </h1>
    <p class="mt-2 text-gray-600">Stub page (listing by category).</p>

    <div class="mt-6">
      <a class="text-sm font-semibold text-gray-900 hover:underline" href="{{ url('/en/blog') }}">← Back to portal</a>
    </div>
  </div>
@endsection
