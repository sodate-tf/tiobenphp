@extends('layouts.site')

@php $meta=$meta??[]; $latest=collect($latest??[]); $cover=fn($p)=>!empty($p->cover_image_url)?$p->cover_image_url:null; @endphp
@section('html_lang','en')
@section('title',$meta['title'] ?? 'Practical Sacramental Life — IA Tio Ben')
@section('meta_description',$meta['description'] ?? '')
@section('canonical',$meta['canonical'] ?? url('/en/practical-sacramental-life'))
@section('robots',$meta['robots'] ?? 'index,follow,max-image-preview:large')
@section('og_title',$meta['og_title'] ?? 'Practical Sacramental Life — IA Tio Ben')
@section('og_description',$meta['og_description'] ?? ($meta['description'] ?? ''))
@section('og_url',$meta['og_url'] ?? url('/en/practical-sacramental-life'))
@section('og_image',$meta['og_image'] ?? '')
@section('hreflang')
  <link rel="alternate" hreflang="en" href="{{ url('/en/practical-sacramental-life') }}" />
  <link rel="alternate" hreflang="pt-BR" href="{{ url('/vida-sacramental-pratica') }}" />
  <link rel="alternate" hreflang="x-default" href="{{ url('/vida-sacramental-pratica') }}" />
@endsection
@section('content')
<main class="mx-auto max-w-6xl px-4 py-10">
  <h1 class="text-3xl font-black">Practical Sacramental Life</h1>
  <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
    @foreach($latest as $post)
      <a href="{{ url('/en/blog/'.$post->slug) }}" class="rounded-2xl border border-slate-200 bg-white p-4">
        @if($cover($post))<img src="{{ $cover($post) }}" alt="{{ $post->title }}" class="mb-3 aspect-[16/9] w-full rounded-xl object-cover" loading="lazy" />@endif
        <h2 class="text-base font-bold text-slate-900">{{ $post->title }}</h2>
      </a>
    @endforeach
  </div>
</main>
@endsection

